<?php

class CP_Blog_Archive extends APP_View_Page {

	function __construct() {
		parent::__construct( 'index.php', __( 'Blog', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}
}


class CP_Ads_Home extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-ads-home.php', __( 'Ad Listings', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}
}


class CP_Add_New extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-add-new.php', __( 'Add New', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {
		global $cp_options;
		// if not logged in, redirect to login page
		appthemes_auth_redirect_login();
		// this is needed for IE to work with the go back button
		header( "Cache-control: private" );

		// if not meet membership requirement, redirect to membership purchase page
		cp_redirect_membership();
		
		// redirect to dashboard if can't renew
		if ( isset($_GET['renew']) )
			CP_Add_New::can_renew_ad();

		// needed for image uploading and deleting to work
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// include all the functions needed for this form
		require_once( get_template_directory() . '/includes/forms/step-functions.php' );

		// load up the validate and tinymce scripts
		add_action( 'wp_enqueue_scripts', 'cp_load_form_scripts' );
		// load app-plupload scripts
		if ( isset($_POST['cat']) && !isset($_POST['step1']) && $cp_options->ad_images && appthemes_plupload_is_enabled() )
			add_action( 'wp_enqueue_scripts', 'appthemes_plupload_enqueue_scripts' );
		
		add_action( 'appthemes_notices', array( $this, 'show_notice' ) );
	}

	function show_notice() {
		global $current_user;

		$membership_active = false;
		$membership = get_pack( $current_user->active_membership_pack );
		if ( $membership && cp_payments_is_enabled() )
			$membership_active = ( appthemes_days_between_dates($current_user->membership_expires) > 0 );
		if ( $membership_active && !isset( $_POST['step2'] ) )
			appthemes_display_notice( 'success', sprintf( __( 'You have active membership pack "%s". Membership benefit will apply on the review page before publishing an ad.', APP_TD ), $membership->pack_name ) );
	}

	function can_renew_ad() {
		global $cp_options;

		if ( isset($_GET['renew']) ) {
			if ( ! $cp_options->allow_relist )
				return CP_Add_New::redirect_dashboard('renew-disabled');

			if ( $_GET['renew'] != appthemes_numbers_only( $_GET['renew'] ) )
				return CP_Add_New::redirect_dashboard('renew-invalid-id');

			$post = get_post($_GET['renew']);
			if ( !$post )
				return CP_Add_New::redirect_dashboard('renew-invalid-id');

			if ( !in_array($post->post_status, array('draft', 'pending') ) )
				return CP_Add_New::redirect_dashboard('renew-not-expired');

			$expire_time = strtotime( get_post_meta( $post->ID, 'cp_sys_expire_date', true ) );
			if ( $expire_time > current_time( 'timestamp' ) )
				return CP_Add_New::redirect_dashboard('renew-not-expired');

		}
	}

	function redirect_dashboard($reason) {
		$redirect_url = add_query_arg( array( $reason => 'true' ), CP_DASHBOARD_URL );
		wp_redirect($redirect_url);
		exit();
	}

}


class CP_Membership extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-membership-purchase.php', __( 'Memberships', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {

		// redirect to dashboard if payments disabled
		if ( ! cp_payments_is_enabled() )
			wp_redirect( add_query_arg( array( 'payments-disabled' => 'true' ), CP_DASHBOARD_URL ) );

		// if not logged in, redirect to login page
		appthemes_auth_redirect_login();
		// this is needed for IE to work with the go back button
		header( "Cache-control: private" );

		// include all the functions needed for this form
		require_once( get_template_directory() . '/includes/forms/step-functions.php' );

		// load up the relavent javascript
		add_action( 'wp_enqueue_scripts', 'cp_load_form_scripts' );

	}
}


class CP_Edit_Item extends APP_View_Page {

	private $error;

	function __construct() {
		parent::__construct( 'tpl-edit-item.php', __( 'Edit Item', APP_TD ) );
		add_action( 'init', array( $this, 'update' ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function update() {
		global $cp_options;

		if ( !isset( $_POST['action'] ) || 'cp-edit-item' != $_POST['action'] || !current_user_can( 'edit_posts' ) )
			return;

		check_admin_referer( 'cp-edit-item' );

		// needed for image uploading and deleting to work
		include_once (ABSPATH . 'wp-admin/includes/file.php');
		include_once (ABSPATH . 'wp-admin/includes/image.php');

		// associate app-plupload images
		if ( isset( $_POST['app_attach_id'] ) ) {
			$attachments = $_POST['app_attach_id'];
			$titles = ( isset( $_POST['app_attach_title'] ) ) ? $_POST['app_attach_title'] : array();
			// associate the already uploaded images to the new ad and update titles
			$attach_id = appthemes_plupload_associate_images( $_POST['ad_id'], $attachments, $titles, false );
		}

		// delete any images checked
		if ( !empty( $_POST['image'] ) )
			cp_delete_image();

		// update the image alt text
		if ( !empty( $_POST['attachments'] ) )
			cp_update_alt_text();

		// check to see if an image needs to be uploaded, hack since we just check if array keys are empty for 6 keys
		$process_images = false;
		for ( $i = 0; $i <= 6; $i++ ) {
    	if( !empty( $_FILES['image']['tmp_name'][$i] ) ) {
				$process_images = true;
				break;
			}
		}

		if ( $process_images ) {

			// check for valid the image extensions and sizes
			$error_msg = cp_validate_image();
			if ( !$error_msg ) {

				$imagecount = cp_count_ad_images( $_POST['ad_id'] );
				$maximages = $cp_options->num_images;

				// only allow the max number of images to each ad. prevents page reloads adding more
				if ( $maximages > $imagecount ) {
					// create the array that will hold all the post values
					$postvals = array();
					// now upload the new image
					$postvals = cp_process_new_image( $_POST['ad_id'] );
					// associate the already uploaded images to the ad and create multiple image sizes
					$attach_id = cp_associate_images( $_POST['ad_id'], $postvals['attachment'] );
				}
			
			} else {
				// images didn't upload
				$this->error = $error_msg;
			}

		}

		$this->error = apply_filters( 'cp_listing_validate_fields', $this->error );

		// update an ad
		$post_id = ( empty( $this->error ) ) ? cp_update_listing() : false;
		if ( ! $post_id )
			$this->error[] = __( 'There was an error trying to update your ad.', APP_TD );
	}

	function template_redirect() {
		global $cp_options;

		appthemes_auth_redirect_login(); // if not logged in, redirect to login page
		nocache_headers();

		// redirect to dashboard if can't edit ad
		self::can_edit_ad();

		// include all the functions needed for form
		require_once( get_template_directory() . '/includes/forms/step-functions.php' );

		// add js files to wp_head. tiny_mce and validate
		add_action( 'wp_enqueue_scripts', 'cp_load_form_scripts' );
		// load app-plupload scripts
		if ( $cp_options->ad_images && appthemes_plupload_is_enabled() )
			add_action( 'wp_enqueue_scripts', 'appthemes_plupload_enqueue_scripts' );

		add_action( 'appthemes_notices', array( $this, 'show_notice' ) );
	}

	function show_notice() {
		if ( !empty( $this->error ) ) {
			foreach( $this->error as $error )
				appthemes_display_notice( 'error', $error );
		} elseif ( isset( $_POST['action'] ) && $_POST['action'] == 'cp-edit-item' ) {
			appthemes_display_notice( 'success', __( 'Your ad has been successfully updated.', APP_TD ) . ' <a href="' . CP_DASHBOARD_URL . '">' . __( 'Return to my dashboard', APP_TD ) . '</a>' );
		}
	}

	function can_edit_ad() {
		global $current_user, $cp_options;

		if ( ! isset( $_GET['aid'] ) || $_GET['aid'] != appthemes_numbers_only( $_GET['aid'] ) )
			return self::redirect_dashboard( 'edit-invalid-id' );

		if ( ! $cp_options->ad_edit )
			return self::redirect_dashboard( 'edit-disabled' );

		$post = get_post( $_GET['aid'] );
		if ( ! $post )
			return self::redirect_dashboard( 'edit-invalid-id' );

		if ( $post->post_status == 'pending' )
			return self::redirect_dashboard( 'edit-pending' );

		if ( $post->post_type != APP_POST_TYPE )
			return self::redirect_dashboard( 'edit-invalid-type' );

		if ( $post->post_author != $current_user->ID )
			return self::redirect_dashboard( 'edit-invalid-author' );

		$expire_time = strtotime( get_post_meta( $post->ID, 'cp_sys_expire_date', true ) );
		if ( current_time( 'timestamp' ) > $expire_time && $post->post_status == 'draft' )
			return self::redirect_dashboard( 'edit-expired' );

	}

	function redirect_dashboard( $reason ) {
		$redirect_url = add_query_arg( array( $reason => 'true' ), CP_DASHBOARD_URL );
		wp_redirect( $redirect_url );
		exit();
	}

}


class CP_User_Dashboard extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-dashboard.php', __( 'Dashboard', APP_TD ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {
		appthemes_auth_redirect_login(); // if not logged in, redirect to login page
		nocache_headers();

		// process actions if needed
		self::process_actions();

		add_action( 'appthemes_notices', array( $this, 'show_notice' ) );
	}

	function process_actions() {
		global $wpdb, $current_user;

		$allowed_actions = array( 'pause', 'restart', 'delete', 'setSold', 'unsetSold' );

		if ( ! isset( $_GET['action'] ) || ! in_array( $_GET['action'], $allowed_actions ) )
			return;

		if ( ! isset( $_GET['aid'] ) || ! is_numeric( $_GET['aid'] ) )
			return;

		$d = trim( $_GET['action'] );
		$aid = appthemes_numbers_only( $_GET['aid'] );

		// make sure author matches ad, and ad exist
		$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d AND post_author = %d AND post_type = %s", $aid, $current_user->ID, APP_POST_TYPE );
		$post = $wpdb->get_row( $sql );
		if ( $post == null )
			return;

		$expire_time = strtotime( get_post_meta( $post->ID, 'cp_sys_expire_date', true ) );
		$is_expired = ( current_time( 'timestamp' ) > $expire_time && $post->post_status == 'draft' );
		$is_pending = ( $post->post_status == 'pending' );

		if ( $d == 'pause' && ! $is_expired && ! $is_pending ) {
			wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'draft' ) );
			$redirect_url = add_query_arg( array( 'paused' => 'true' ), CP_DASHBOARD_URL );
			wp_redirect( $redirect_url );
			exit();

		} elseif ( $d == 'restart' && ! $is_expired && ! $is_pending ) {
			wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'publish' ) );
			$redirect_url = add_query_arg( array( 'restarted' => 'true' ), CP_DASHBOARD_URL );
			wp_redirect( $redirect_url );
			exit();

		} elseif ( $d == 'delete' ) {
			cp_delete_ad_listing( $post->ID );
			$redirect_url = add_query_arg( array( 'deleted' => 'true' ), CP_DASHBOARD_URL );
			wp_redirect( $redirect_url );
			exit();

		} elseif ( $d == 'setSold' ) {
			update_post_meta( $post->ID, 'cp_ad_sold', 'yes' );
			$redirect_url = add_query_arg( array( 'markedsold' => 'true' ), CP_DASHBOARD_URL );
			wp_redirect( $redirect_url );
			exit();

		} elseif ( $d == 'unsetSold' ) {
			update_post_meta( $post->ID, 'cp_ad_sold', 'no' );
			$redirect_url = add_query_arg( array( 'unmarkedsold' => 'true' ), CP_DASHBOARD_URL );
			wp_redirect( $redirect_url );
			exit();

		}

	}

	function show_notice() {
		if ( isset( $_GET['paused'] ) ) {
			appthemes_display_notice( 'success', __( 'Ad has been paused.', APP_TD ) );
		} elseif ( isset( $_GET['restarted'] ) ) {
			appthemes_display_notice( 'success', __( 'Ad has been published.', APP_TD ) );
		} elseif ( isset( $_GET['deleted'] ) ) {
			appthemes_display_notice( 'success', __( 'Ad has been deleted.', APP_TD ) );
		} elseif ( isset( $_GET['markedsold'] ) ) {
			appthemes_display_notice( 'success', __( 'Ad has been marked as sold.', APP_TD ) );
		} elseif ( isset( $_GET['unmarkedsold'] ) ) {
			appthemes_display_notice( 'success', __( 'Ad has been unmarked as sold.', APP_TD ) );
		} elseif ( isset( $_GET['renew-disabled'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not relist this ad. Relisting is currently disabled.', APP_TD ) );
		} elseif ( isset( $_GET['renew-invalid-id'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not relist this ad. Invalid ID of an ad.', APP_TD ) );
		} elseif ( isset( $_GET['renew-not-expired'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not relist this ad. Ad is not expired.', APP_TD ) );
		} elseif ( isset( $_GET['edit-invalid-id'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not edit this ad. Invalid ID of an ad.', APP_TD ) );
		} elseif ( isset( $_GET['edit-invalid-author'] ) ) {
			appthemes_display_notice( 'error', __( "You can not edit this ad. It's not your ad.", APP_TD ) );
		} elseif ( isset( $_GET['edit-invalid-type'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not edit this ad. This is not an ad.', APP_TD ) );
		} elseif ( isset( $_GET['edit-disabled'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not edit this ad. Editing is currently disabled.', APP_TD ) );
		} elseif ( isset( $_GET['edit-pending'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not edit this ad. Ad is not yet approved.', APP_TD ) );
		} elseif ( isset( $_GET['edit-expired'] ) ) {
			appthemes_display_notice( 'error', __( 'You can not edit this ad. Ad is expired.', APP_TD ) );
		} elseif ( isset( $_GET['payments-disabled'] ) ) {
			appthemes_display_notice( 'error', __( 'Payments are currently disabled. You cannot purchase anything.', APP_TD ) );
		}
	}

}


class CP_User_Profile extends APP_User_Profile {

	function __construct() {
		APP_View_Page::__construct( 'tpl-profile.php', __( 'Profile', APP_TD ) );
		add_action( 'init', array( $this, 'update' ) );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function template_redirect() {
		parent::template_redirect();

		wp_enqueue_script( 'jquery' );
	}

	function update() {
		if ( !isset( $_POST['action'] ) || 'app-edit-profile' != $_POST['action'] )
			return;
		
		check_admin_referer( 'app-edit-profile' );
		do_action( 'personal_options_update', $_POST['user_id'] );
		parent::update();
	}

}


class CP_Ads_Categories extends APP_View_Page {

	function __construct() {
		parent::__construct( 'tpl-categories.php', __( 'Categories', APP_TD ) );

		// Replace any children the "Categories" menu item might have with the category dropdown
		add_filter( 'wp_nav_menu_objects', array($this, 'disable_children'), 10, 2 );
		add_filter( 'walker_nav_menu_start_el', array($this, 'insert_dropdown'), 10, 4 );
	}

	static function get_id() {
		return parent::_get_id( __CLASS__ );
	}

	function disable_children( $items, $args ) {
		foreach ( $items as $key => $item ) {
			if ( $item->object_id == self::get_id() ) {
				$item->current_item_ancestor = false;
				$item->current_item_parent = false;
				$menu_id = $item->ID;
			}
		}

		if ( isset( $menu_id ) ) {
			foreach ( $items as $key => $item )
				if ( $item->menu_item_parent == $menu_id )
					unset( $items[$key] );
		}

		return $items;
	}

	function insert_dropdown( $item_output, $item, $depth, $args ) {
		if ( $item->object_id == self::get_id() && $item->object == 'page' ) {
			$item_output .= '<div class="adv_categories" id="adv_categories">' . cp_create_categories_list( 'menu' ) . '</div>';
		}
		return $item_output;
	}

}


class CP_Ad_Single extends APP_View {

	function condition() {
		return is_singular( APP_POST_TYPE );
	}

	function notices() {
		$status = get_post_status( get_queried_object() );

		if ( $status == 'pending' )
			appthemes_display_notice( 'success', __( 'This ad listing is currently pending and must be approved by an administrator.', APP_TD ) );
	}

}

