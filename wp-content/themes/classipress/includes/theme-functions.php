<?php
/**
 * ClassiPress core theme functions
 * This file is the backbone and includes all the core functions
 * Modifying this will void your warranty and could cause
 * problems with your instance of CP. Proceed at your own risk!
 *
 * @package ClassiPress
 * @author AppThemes
 *
 */

/**
 * The theme custom post type constant
 *
 * Override these values by defining
 * your own in functions.php
 *
 */
if ( !defined('APP_POST_TYPE') )
	define('APP_POST_TYPE', 'ad_listing');

if ( !defined('APP_TAX_CAT') )
	define('APP_TAX_CAT', 'ad_cat');

if ( !defined('APP_TAX_TAG') )
	define('APP_TAX_TAG', 'ad_tag');


// legacy classipress path variables
$upload_dir = wp_upload_dir();
define('UPLOADS_FOLDER', trailingslashit('classipress'));
define('CP_UPLOAD_DIR', trailingslashit($upload_dir['basedir']) . UPLOADS_FOLDER);

define( 'FAVICON', get_template_directory_uri() . '/images/favicon.ico' );
define( 'THE_POSITION', 3 );

// define the db tables we use
$app_db_tables = array($app_abbr.'_ad_fields', $app_abbr.'_ad_forms', $app_abbr.'_ad_geocodes', $app_abbr.'_ad_meta', $app_abbr.'_ad_packs', $app_abbr.'_ad_pop_daily', $app_abbr.'_ad_pop_total', $app_abbr.'_coupons', $app_abbr.'_order_info');

// register the db tables
foreach ( $app_db_tables as $app_db_table )
		scb_register_table($app_db_table);


// include all the core files
require_once( get_template_directory() . '/includes/views.php' );
require_once( get_template_directory() . '/includes/theme-hooks.php' );
require_once( get_template_directory() . '/includes/theme-cron.php' );
require_once( get_template_directory() . '/includes/theme-enqueue.php' );
require_once( get_template_directory() . '/includes/appthemes-functions.php' );
require_once( get_template_directory() . '/includes/theme-actions.php' );
require_once( get_template_directory() . '/includes/theme-widgets.php' );
require_once( get_template_directory() . '/includes/theme-sidebars.php' );
require_once( get_template_directory() . '/includes/theme-comments.php' );
require_once( get_template_directory() . '/includes/theme-profile.php' );
require_once( get_template_directory() . '/includes/theme-payments.php' );
require_once( APP_FRAMEWORK_DIR . '/admin/class-meta-box.php' );
require_once( get_template_directory() . '/includes/theme-security.php' );
require_once( get_template_directory() . '/includes/theme-emails.php' );
require_once( get_template_directory() . '/includes/theme-stats.php' );
require_once( get_template_directory() . '/includes/theme-deprecated.php' );

// include the new custom post type and taxonomy declarations.
// must be included on all pages to work with site functions
require_once( get_template_directory() . '/includes/admin/admin-post-types.php' );


// front-end includes
if ( !is_admin() ) :
	require_once( get_template_directory() . '/includes/theme-footer.php' );
	require_once( get_template_directory() . '/includes/theme-header.php' );
	require_once( get_template_directory() . '/includes/theme-refine.php' );

	cp_load_all_page_templates();
endif;

// admin-only functions
if ( is_admin() ) :
	require_once( get_template_directory() . '/includes/admin/install-script.php' ); // needs to be above admin-options otherwise install/upgrade script doesn't work correctly
	require_once( get_template_directory() . '/includes/admin/admin-updates.php' );
	require_once( get_template_directory() . '/includes/admin/admin-enqueue.php' );
	require_once( get_template_directory() . '/includes/admin/admin-dashboard.php' );
	require_once( get_template_directory() . '/includes/admin/admin-settings.php' );
	require_once( get_template_directory() . '/includes/admin/admin-system-info.php' );
	require_once( get_template_directory() . '/includes/admin/admin-options.php' );
	require_once( get_template_directory() . '/includes/admin/write-panel.php' );

	new CP_Theme_Settings_General( $cp_options );
	new CP_Theme_Settings_Emails( $cp_options );
	new CP_Theme_Settings_Pricing( $cp_options );
	new CP_Theme_System_Info();
endif;


new CP_Blog_Archive;
new CP_Ads_Home;
new CP_Ads_Categories;
new CP_Add_New;
new CP_Membership;
new CP_Edit_Item;
new CP_User_Dashboard;
new CP_User_Profile;
new CP_Ad_Single;

// set global path variables
define( 'CP_DASHBOARD_URL', get_permalink( CP_User_Dashboard::get_id() ) );
define( 'CP_PROFILE_URL', get_permalink( CP_User_Profile::get_id() ) );
define( 'CP_EDIT_URL', get_permalink( CP_Edit_Item::get_id() ) );
define( 'CP_ADD_NEW_URL', get_permalink( CP_Add_New::get_id() ) );
define( 'CP_MEMBERSHIP_PURCHASE_URL', get_permalink( CP_Membership::get_id() ) );

add_theme_support( 'app-versions', array(
	'update_page' => 'admin.php?page=app-settings&firstrun=1',
	'current_version' => $app_version,
	'option_key' => 'cp_version',
) );

add_theme_support( 'app-plupload', array(
	'max_file_size' => $cp_options->max_image_size,
	'allowed_files' => $cp_options->num_images,
	'disable_switch' => false,
) );

add_theme_support( 'app-wrapping' );

add_theme_support( 'app-login', array(
	'login' => 'tpl-login.php',
	'register' => 'tpl-registration.php',
	'recover' => 'tpl-password-recovery.php',
	'reset' => 'tpl-password-reset.php',
	'redirect' => $cp_options->disable_wp_login,
	'settings_page' => 'admin.php?page=app-settings&tab=advanced',
) );

add_theme_support( 'app-feed', array(
	'post_type' => APP_POST_TYPE,
	'blog_template' => 'index.php',
	'alternate_feed_url' => $cp_options->feedburner_url,
) );

add_theme_support( 'app-stats', array(
	'cache' => 'today',
	'table_daily' => 'cp_ad_pop_daily',
	'table_total' => 'cp_ad_pop_total',
	'meta_daily' => 'cp_daily_count',
	'meta_total' => 'cp_total_count',
) );

add_theme_support( 'app-payments', array(
	'items' => array(
		array(
			'type' => CP_ITEM_LISTING,
			'title' => __( 'Listing', APP_TD ),
			'meta' => array(),
		),
		array(
			'type' => CP_ITEM_MEMBERSHIP,
			'title' => __( 'Membership', APP_TD ),
			'meta' => array(),
		),
	),
	'items_post_types' => array( APP_POST_TYPE ),
	'options' => $cp_options,
) );

add_theme_support( 'app-price-format', array(
	'currency_default' => $cp_options->currency_code,
	'currency_identifier' => $cp_options->currency_identifier,
	'currency_position' => $cp_options->currency_position,
	'thousands_separator' => $cp_options->thousands_separator,
	'decimal_separator' => $cp_options->decimal_separator,
	'hide_decimals' => $cp_options->hide_decimals,
) );


// add query var for search functions
function cp_add_query_vars() {
	global $wp;
	$wp->add_query_var( 'scat' );
}
add_filter('init', 'cp_add_query_vars');


function cp_addnew_dropdown_child_categories() {
	global $cp_options;

	if ( 'POST' != $_SERVER['REQUEST_METHOD'] )
		die( json_encode( array( 'success' => false, 'message' => __( 'Sorry, only post method allowed.', APP_TD ) ) ) );

	$parent_cat = isset( $_POST['cat_id'] ) ? (int) $_POST['cat_id'] : 0;
	if ( $parent_cat < 1 )
		die( json_encode( array( 'success' => false, 'message' => __( 'Sorry, item does not exist.', APP_TD ) ) ) );

	$terms = (array) get_terms( APP_TAX_CAT, array( 'child_of' => $parent_cat, 'hide_empty' => 0 ) );
	if ( empty( $terms ) )
		die( json_encode( array( 'success' => false, 'message' => __( 'Sorry, no results found.', APP_TD ) ) ) );

	$args = array(
		'show_option_none' => __( 'Select one', APP_TD ),
		'class' => 'dropdownlist',
		'id' => 'ad_cat_id',
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0,
		'hierarchical' => 1,
		'depth' => 1,
		'echo' => 0,
		'taxonomy' => APP_TAX_CAT,
		'child_of' => $parent_cat,
	);

	if ( $cp_options->price_scheme == 'category' && cp_payments_is_enabled() ) {
		$result = cp_dropdown_categories_prices( $args );
	} else {
		$result = wp_dropdown_categories( $args ) . "\n" . '<div style="clear:both;">';
	}

	// return the result to the ajax post
	die( json_encode( array( 'success' => true, 'html' => $result ) ) );
}


// add AJAX functions
add_action( 'wp_ajax_nopriv_ajax-tag-search-front', 'cp_suggest' );
add_action( 'wp_ajax_ajax-tag-search-front', 'cp_suggest' );
add_action( 'wp_ajax_nopriv_dropdown-child-categories', 'cp_addnew_dropdown_child_categories' ); //keep for people who allow post before registration
add_action( 'wp_ajax_dropdown-child-categories', 'cp_addnew_dropdown_child_categories' );

// display the login message in the header
if (!function_exists('cp_login_head')) {
	function cp_login_head() {

		if (is_user_logged_in()) :
			global $current_user;
			$current_user = wp_get_current_user();
			$display_user_name = cp_get_user_name();
			$logout_url = cp_logout_url();
			?>
			<?php _e( 'Welcome,', APP_TD ); ?> <strong><?php echo $display_user_name; ?></strong> [ <a href="<?php echo CP_DASHBOARD_URL; ?>"><?php _e( 'My Dashboard', APP_TD ); ?></a> | <a href="<?php echo $logout_url; ?>"><?php _e( 'Log out', APP_TD ); ?></a> ]&nbsp;
		<?php else : ?>
			<?php _e( 'Welcome,', APP_TD ); ?> <strong><?php _e( 'visitor!', APP_TD ); ?></strong> [ <a href="<?php echo appthemes_get_registration_url(); ?>"><?php _e( 'Register', APP_TD ); ?></a> | <a href="<?php echo wp_login_url(); ?>"><?php _e( 'Login', APP_TD ); ?></a> ]&nbsp;
		<?php endif;

	}
}

// return user name depend of account type
function cp_get_user_name($user = false) {
	global $current_user;

	if (!$user && is_object($current_user))
		$user = $current_user;
	else if (is_numeric($user))
		$user = get_userdata($user);

	if (is_object($user)) {

		if ( 'fb-' == substr( $user->user_login, 0, 3 ) )
			$display_user_name = $user->display_name;
		else
			$display_user_name = $user->user_login;

		return $display_user_name;

	} else {
		return false;
	}
}

// return logout url depend of login type
function cp_logout_url( $url = '' ) {
	global $app_abbr;

	if ( !$url )
		$url = home_url();

	if ( is_user_logged_in() )
		return wp_logout_url($url);
	else
		return false;

}

// correct logout url in admin bar
function cp_admin_bar_render() {
	global $wp_admin_bar;

	if ( is_user_logged_in() ) {
		$wp_admin_bar->remove_menu('logout');
		$wp_admin_bar->add_menu( array(
			'parent' => 'user-actions',
			'id'     => 'logout',
			'title'  => __( 'Log out', APP_TD ),
			'href'   => cp_logout_url(),
		) );
	}

}
add_action( 'wp_before_admin_bar_render', 'cp_admin_bar_render' );

// get category id for search form
function cp_get_search_catid() {
	global $post;

	if ( is_tax(APP_TAX_CAT) ) {
		$ad_cat_array = get_term_by( 'slug', get_query_var(APP_TAX_CAT), APP_TAX_CAT, ARRAY_A );
		$catid = $ad_cat_array['term_id'];
	} else if ( is_singular(APP_POST_TYPE) ) {
		$term = wp_get_object_terms($post->ID, APP_TAX_CAT);
		if ( $term )
			$catid = $term[0]->term_id;
	} else if ( is_search() ) {
		$catid = get_query_var('scat');
	}

	if ( !isset($catid) || !is_numeric($catid) )
		$catid = 0;

	return $catid;
}


// get search term for refine results form
function cp_get_search_term() {

	$search_term = get_query_var('s');

	if ( empty($search_term) )
		$search_term = __( 'Enter ISBN # Of The Book You Want', APP_TD );

	return $search_term;
}


// processes the entire ad thumbnail logic within the loop
if ( !function_exists('cp_ad_loop_thumbnail') ) :
	function cp_ad_loop_thumbnail() {
		global $post, $cp_options;

		// go see if any images are associated with the ad
		$image_id = cp_get_featured_image_id($post->ID);

		// set the class based on if the hover preview option is set to "yes"
		$prevclass = ( $cp_options->ad_image_preview ) ? 'preview' : 'nopreview';

		if ( $image_id > 0 ) {

			// get 75x75 v3.0.5+ image size
			$adthumbarray = wp_get_attachment_image( $image_id, 'ad-thumb' );

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src( $image_id, 'large' );
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if ( $adthumbarray ) {
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumbarray.'</a>';

			// maybe a v3.0 legacy ad
			} else {
				$adthumblegarray = wp_get_attachment_image_src($image_id, 'thumbnail');
				$img_thumbleg_url_raw = $adthumblegarray[0];
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumblegarray.'</a>';
			}

		// no image so return the placeholder thumbnail
		} else {
			echo '<a href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '"><img class="attachment-medium" alt="" title="" src="' . appthemes_locate_template_uri('images/no-thumb-75.jpg') . '" /></a>';
		}

	}
endif;


// processes the entire ad thumbnail logic for featured ads
if ( !function_exists('cp_ad_featured_thumbnail') ) :
	function cp_ad_featured_thumbnail() {
		global $post, $cp_options;

		// go see if any images are associated with the ad
		$image_id = cp_get_featured_image_id($post->ID);

		// set the class based on if the hover preview option is set to "yes"
		$prevclass = ( $cp_options->ad_image_preview ) ? 'preview' : 'nopreview';

		if ( $image_id > 0 ) {

			// get 50x50 v3.0.5+ image size
			$adthumbarray = wp_get_attachment_image($image_id, 'sidebar-thumbnail');

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src($image_id, 'large');
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if($adthumbarray) {
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumbarray.'</a>';

			// maybe a v3.0 legacy ad
			} else {
				$adthumblegarray = wp_get_attachment_image_src($image_id, 'thumbnail');
				$img_thumbleg_url_raw = $adthumblegarray[0];
				echo '<a href="'. get_permalink() .'" title="'. the_title_attribute('echo=0') .'" class="'.$prevclass.'" data-rel="'.$img_large_url_raw.'">'.$adthumblegarray.'</a>';
			}

		// no image so return the placeholder thumbnail
		} else {
			echo '<a href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '"><img class="attachment-sidebar-thumbnail" alt="" title="" src="' . appthemes_locate_template_uri('images/no-thumb-sm.jpg') . '" /></a>';
		}

	}
endif;


// display all the custom fields on the single ad page, by default they are placed in the list area
if ( ! function_exists('cp_get_ad_details') ) {
	function cp_get_ad_details( $post_id, $category_id, $location = 'list' ) {
		global $wpdb;

		// see if there's a custom form first based on category id.
		$form_id = cp_get_form_id( $category_id );

		$post = get_post( $post_id );
		if ( ! $post )
			return;

		// if there's no form id it must mean the default form is being used
		if ( ! $form_id ) {

			// get all the custom field labels so we can match the field_name up against the post_meta keys
			$sql = "SELECT field_label, field_name, field_type FROM $wpdb->cp_ad_fields";

		} else {

			// now we should have the formid so show the form layout based on the category selected
			$sql = $wpdb->prepare( "SELECT f.field_label, f.field_name, f.field_type, m.field_pos FROM $wpdb->cp_ad_fields f "
				. "INNER JOIN $wpdb->cp_ad_meta m ON f.field_id = m.field_id WHERE m.form_id = %s ORDER BY m.field_pos ASC", $form_id );

		}

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			_e( 'No ad details found.', APP_TD );
			return;
		}

		// allows to hook before ad details
		cp_action_before_ad_details( $results, $post, $location );

		foreach ( $results as $result ) {

			// external plugins can modify or disable field
			$result = apply_filters( 'cp_ad_details_field', $result, $post, $location );
			if ( ! $result )
				continue;

			$disallow_fields = array( 'cp_price', 'cp_currency' );
			if ( in_array( $result->field_name, $disallow_fields ) )
				continue;

			$post_meta_val = get_post_meta( $post->ID, $result->field_name, true );
			if ( empty( $post_meta_val ) )
				continue;

			if ( $location == 'list' ) {
				if ( $result->field_type == 'text area' )
					continue;

				if ( $result->field_type == 'checkbox' ) {
					$post_meta_val = get_post_meta( $post->ID, $result->field_name, false );
					$post_meta_val = implode( ", ", $post_meta_val );
				}

				$args = array( 'value' => $post_meta_val, 'label' => $result->field_label, 'id' => $result->field_name, 'class' => '' );
				$args = apply_filters( 'cp_ad_details_' . $result->field_name, $args, $result, $post, $location );

				if ( $args )
					echo '<li id="' . $args['id'] . '" class="' . $args['class'] . '"><span>' . esc_html( translate( $args['label'], APP_TD ) ) . ':</span> ' . appthemes_make_clickable( $args['value'] ) . '</li>';

			} elseif ( $location == 'content' ) {
				if ( $result->field_type != 'text area' )
					continue;

				$args = array( 'value' => $post_meta_val, 'label' => $result->field_label, 'id' => $result->field_name, 'class' => 'custom-text-area dotted' );
				$args = apply_filters( 'cp_ad_details_' . $result->field_name, $args, $result, $post, $location );

				if ( $args )
					echo '<div id="' . $args['id'] . '" class="' . $args['class'] . '"><h3>' . esc_html( translate( $args['label'], APP_TD ) ) . '</h3> ' . appthemes_make_clickable( $args['value'] ) . '</div>';

			}
		}

		// allows to hook after ad details
		cp_action_after_ad_details( $results, $post, $location );
	}
}


// give us the custom form id based on category id passed in
// this is used on the single-default.php page to display the ad fields
function cp_get_form_id($catid) {
	global $wpdb;
	$fid = ''; // set to nothing to make WP notice happy

	// we first need to see if this ad is using a custom form
	// so lets search for a catid match and return the id if found
	$sql = "SELECT ID, form_cats FROM $wpdb->cp_ad_forms WHERE form_status = 'active'";

	$results = $wpdb->get_results($sql);

	if ( $results ) {

		foreach ($results as $result) :

			// put the form_cats into an array
			$catarray = unserialize($result->form_cats);

			// now search the array for the ad catid
			if ( in_array($catid, $catarray) )
				$fid = $result->ID; // when there's a catid match, grab the form id

		endforeach;

		// kick back the form id
		return $fid;

	}

}


// get the first medium image associated to the ad
// used on the home page, search, category, etc
// deprecated since 3.0.5.2
if ( !function_exists('cp_get_image') ) {
	function cp_get_image($post_id = '', $size = 'medium', $num = 1) {
		global $cp_options;

		$images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
		if ( $images ) {
			foreach ($images as $image) {
				$img_check = wp_get_attachment_image($image->ID, $size, $icon = false);
				// legacy since 3.0.5 which now includes image alt text editing
				//$post_title = get_the_title($post_id); // grab the post title so we can include in alt and title for SEO
				//$img_check = preg_replace('/title=\"(.*?)\"/','title="'.$post_title.'"', $img_check);
				//$img_check = preg_replace('/alt=\"(.*?)\"/','alt="'.$post_title.'"', $img_check);
			}
		} else {
			// show the placeholder image
			if ( $cp_options->ad_images )
				$img_check = '<img class="attachment-medium" alt="" title="" src="' . appthemes_locate_template_uri('images/no-thumb-75.jpg') . '" />';
		}
		echo $img_check;
	}
}


// get the main image associated to the ad used on the single page
if (!function_exists('cp_get_image_url')) {
	function cp_get_image_url() {
		global $post, $wpdb;

		// go see if any images are associated with the ad
		$images = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID') );

		if ($images) {

			// move over bacon
			$image = array_shift($images);

			$alt = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
			// see if this v3.0.5+ image size exists
			//$adthumbarray = wp_get_attachment_image($image->ID, 'medium');
			$adthumbarray = wp_get_attachment_image_src($image->ID, 'medium');
			$img_medium_url_raw = $adthumbarray[0];

			// grab the large image for onhover preview
			$adlargearray = wp_get_attachment_image_src($image->ID, 'large');
			$img_large_url_raw = $adlargearray[0];

			// must be a v3.0.5+ created ad
			if ( $adthumbarray )
				echo '<a href="'. $img_large_url_raw .'" class="img-main" data-rel="colorbox" title="'. the_title_attribute('echo=0') .'"><img src="'. $img_medium_url_raw .'" title="'. $alt .'" alt="'. $alt .'" /></a>';

		// no image so return the placeholder thumbnail
		} else {
			echo '<img class="attachment-medium" alt="" title="" src="' . appthemes_locate_template_uri('images/no-thumb.jpg') . '" />';
		}

	}
}


// get the image associated to the ad used in the loop-ad for hover previewing
if ( !function_exists('cp_get_image_url_raw') ) {
	function cp_get_image_url_raw($post_id = '', $size = 'medium', $class = '', $num = 1) {
		global $cp_options;

		$images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
		if ($images) {
			foreach ($images as $image) {
				$iarray = wp_get_attachment_image_src($image->ID, $size, $icon = false);
				$img_url_raw = $iarray[0];
			}
		} else {
			//if ( $cp_options->ad_images ) {$img_url_raw = appthemes_locate_template_uri('images/no-thumb.jpg'); }
		}
		return $img_url_raw;
	}
}


// get the image associated to the ad used on the home page
if ( !function_exists('cp_get_image_url_feat') ) {
	function cp_get_image_url_feat($post_id = '', $size = 'medium', $class = '', $num = 1) {
		global $cp_options;

		$images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));
		if ( $images ) {
			foreach ($images as $image) {
				$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
				$iarray = wp_get_attachment_image_src($image->ID, $size, $icon = false);
				$img_check = '<img class="'.$class.'" src="'.$iarray[0].'" width="'.$iarray[1].'" height="'.$iarray[2].'" alt="'.$alt.'" title="'.$alt.'" />';
			}
		} else {
			if ( $cp_options->ad_images )
				$img_check = '<img class="preview" alt="" title="" src="' . appthemes_locate_template_uri('images/no-thumb-sm.jpg') . '" />';
		}
		echo $img_check;
	}
}


// get all the small images for the ad and colorbox href
// important and used on the single page
if ( !function_exists('cp_get_image_url_single') ) {
	function cp_get_image_url_single($post_id = '', $size = 'medium', $title = '', $num = 1) {
		$images = get_posts(array('post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'post_parent' => $post_id, 'order' => 'ASC', 'orderby' => 'ID'));

		// remove the first image since it's already being shown as the main one
		$images = array_slice($images,1,count($images)-1);

		if ( $images ) {
			$i = 1;
			foreach ($images as $image) {
				$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
				if ( empty($alt) )
					$alt = $title . ' - ' . __( 'Image ', APP_TD ) . $i;
				$iarray = wp_get_attachment_image_src($image->ID, $size, $icon = false);
				$iarraylg = wp_get_attachment_image_src($image->ID, 'large', $icon = false);
				if ( $i == 1 ) $mainpicID = 'id="mainthumb"'; else $mainpicID = '';
				echo '<a href="'. $iarraylg[0] .'" id="thumb'. $i .'" class="post-gallery" data-rel="colorbox" title="'. $title .' - '. __( 'Image ', APP_TD ) . $i .'"><img src="'. $iarray[0] .'" alt="'. $alt .'" title="'. $alt .'" width="'. $iarray[1] .'" height="'. $iarray[2] .'" /></a>';
				$i++;
			}
		}
	}
}


// sets the thumbnail pic on the WP admin post
function cp_set_ad_thumbnail($post_id, $thumbnail_id) {
	$thumbnail_html = wp_get_attachment_image($thumbnail_id, 'thumbnail');
	if ( !empty($thumbnail_html) ) {
		update_post_meta($post_id, '_thumbnail_id', $thumbnail_id);
		die( _wp_post_thumbnail_html($thumbnail_id) );
	}
}


// deletes the thumbnail pic on the WP admin post
function cp_delete_ad_thumbnail($post_id) {
	delete_post_meta($post_id, '_thumbnail_id');
	die( _wp_post_thumbnail_html() );
}


// gets just the first raw image url
function cp_get_image_url_OLD($postID, $num=1, $order='ASC', $orderby='menu_order', $mime='image') {
	$images = get_posts( array( 'post_type' => 'attachment', 'numberposts' => $num, 'post_status' => null, 'order' => $order, 'orderby' => $orderby, 'post_mime_type' => $mime, 'post_parent' => $postID ) );
	if ( $images ) {
		foreach ($images as $image) {
			$single_url = wp_get_attachment_url($image->ID, false);
		}
	}
	echo $single_url;
}


// get the uploaded file extension and make sure it's an image
function cp_file_is_image($path) {
	$info = @getimagesize($path);
	if ( empty($info) )
		$result = false;
	elseif ( !in_array($info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)) )
		$result = false;
	else
		$result = true;

	return apply_filters('cp_file_is_image', $result, $path);
}


// format price with user settings, used for display prices
function cp_price_format( $price ) {
	global $cp_options;

	if ( is_numeric( $price ) ) {
		$decimals = ( $cp_options->hide_decimals || $price == 0 ) ? 0 : 2;
		$decimal_separator = $cp_options->decimal_separator;
		$thousands_separator = $cp_options->thousands_separator;

		$price = number_format( $price, $decimals, $decimal_separator, $thousands_separator );
	}

	return $price;
}


// displays passed price, with user defined format and currency symbol
function cp_display_price($price, $price_type = '', $echo = true) {

	$price = cp_price_format($price);
	$price = cp_pos_currency($price, $price_type);

	if ( $echo )
		echo $price;

	return $price;
}


// get the ad price and position the currency symbol
if ( !function_exists('cp_get_price') ) {
	function cp_get_price($postid, $meta_field) {
		global $cp_options;

		if ( get_post_meta($postid, $meta_field, true) ) {

			$price_out = get_post_meta($postid, $meta_field, true);
			$price_out = cp_price_format($price_out);
			$price_out = cp_pos_currency($price_out, 'ad');

		} else {
			if ( $cp_options->force_zeroprice )
				$price_out = cp_pos_currency(0, 'ad');
			else
				$price_out = '&nbsp;';
		}

		echo $price_out;
	}
}


// figure out the position of the currency symbol and return it with the price
function cp_pos_currency( $price_out, $price_type = '' ) {
	global $post, $cp_options;

	$price = $price_out;

	if ( $price_type == 'ad' )
		$curr_symbol = $cp_options->curr_symbol;
	else
		$curr_symbol = $price_type;

	//if ad have custom currency, display it instead of default one
	if ( $price_type == 'ad' && isset( $post ) && is_object( $post ) ) {
		$custom_curr = get_post_meta( $post->ID, 'cp_currency', true );
		if ( ! empty( $custom_curr ) )
			$curr_symbol = $custom_curr;
	}

	//possition the currency symbol
	if ( current_theme_supports( 'app-price-format' ) )
		$price_out = _appthemes_format_display_price( $price_out, $curr_symbol, $cp_options->currency_position );
	else
		$price_out = $price_out . '&nbsp;' . $curr_symbol;

	return apply_filters( 'cp_currency_position', $price_out, $price, $curr_symbol, $price_type );
}


// on ad submission form, check images for valid file size and type
function cp_validate_image() {
	global $cp_options;

	$error_msg  = array();
	$max_size = ( $cp_options->max_image_size * 1024 ); // 1024 K = 1 MB. convert into bytes so we can compare file size to max size. 1048576 bytes = 1MB.

	while(list($key,$value) = each($_FILES['image']['name'])) {
		$value = strtolower($value); // added for 3.0.1 to force image names to lowercase. some systems throw an error otherwise
		if ( !empty($value) ) {
			if ( $max_size < $_FILES['image']['size'][$key] ) {
				$size_diff = number_format(($_FILES['image']['size'][$key] - $max_size)/1024);
				$max_size_fmt = number_format( $cp_options->max_image_size );
				$error_msg[] = '<strong>' . $_FILES['image']['name'][$key] . '</strong> ' . sprintf( __( 'exceeds the %1$s KB limit by %2$s KB. Please go back and upload a smaller image.', APP_TD ), $max_size_fmt, $size_diff );
			} elseif ( !cp_file_is_image($_FILES['image']['tmp_name'][$key]) ) {
				$error_msg[] = '<strong>' . $_FILES['image']['name'][$key] . '</strong> ' . __( 'is not a valid image type (.gif, .jpg, .png). Please go back and upload a different image.', APP_TD );
			}
		}
	}
	return $error_msg;
}


// process each image that's being uploaded
function cp_process_new_image() {
	global $wpdb;
	$postvals = '';

	for ( $i=0; $i < count( $_FILES['image']['tmp_name'] ); $i++ ) {
		if ( !empty($_FILES['image']['tmp_name'][$i]) ) {
			// rename the image to a random number to prevent junk image names from coming in
			$renamed = mt_rand( 1000,1000000 ).".".appthemes_find_ext( $_FILES['image']['name'][$i] );

			//Hack since WP can't handle multiple uploads as of 2.8.5
			$upload = array( 'name' => $renamed,'type' => $_FILES['image']['type'][$i],'tmp_name' => $_FILES['image']['tmp_name'][$i],'error' => $_FILES['image']['error'][$i],'size' => $_FILES['image']['size'][$i] );

			// need to set this in order to send to WP media
			$overrides = array( 'test_form' => false );

			// check and make sure the image has a valid extension and then upload it
			$file = cp_image_upload( $upload );

			if ( $file ) // put all these keys into an array and session so we can associate the image to the post after generating the post id
				$postvals['attachment'][$i] = array( 'post_title' => $renamed, 'post_content' => '', 'post_excerpt' => '', 'post_mime_type' => $file['type'], 'guid' => $file['url'], 'file' => $file['file'] );
		}
	}
	return $postvals;
}


// this ties the uploaded files to the correct ad post and creates the multiple image sizes.
function cp_associate_images( $post_id, $files, $print = false ) {
	$i = 1;
	$image_count = count( $files );
	if ( $image_count > 0 && $print )
		echo html( 'p', __( 'Your ad images are now being processed...', APP_TD ) );

	foreach ( $files as $key => $file ) {
		$post_title = esc_attr( get_the_title( $post_id ) );
		$attachment = array( 'post_title' => $post_title, 'post_content' => $file['post_content'], 'post_excerpt' => $file['post_excerpt'], 'post_mime_type' => $file['post_mime_type'], 'guid' => $file['guid'] );
		$attach_id = wp_insert_attachment( $attachment, $file['file'], $post_id );

		// create multiple sizes of the uploaded image via WP controls
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file['file'] ) );

		if ( $print )
			echo html( 'p', sprintf( __( 'Image number %1$d of %2$s has been processed.', APP_TD ), $i, $image_count ) );
		$i++;
	}
}


// get all the images associated to the ad and display the
// thumbnail with checkboxes for deleting them
// used on the ad edit page
if ( !function_exists('cp_get_ad_images') ) {
	function cp_get_ad_images($ad_id) {
		$args = array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $ad_id, 'order' => 'ASC', 'orderby' => 'ID');

		// get all the images associated to this ad
		$images = get_posts($args);

		// print_r($images); // for debugging

		// get the total number of images already on this ad
		// we need it to figure out how many upload fields to show
		$imagecount = count($images);

		// make sure we have images associated to the ad
		if ( $images ) :

			$i = 1;
			$media_dims = '';
			foreach ($images as $image) :

				// go get the width and height fields since they are stored in meta data
				$meta = wp_get_attachment_metadata( $image->ID );
				if ( is_array($meta) && array_key_exists('width', $meta) && array_key_exists('height', $meta) )
					$media_dims = "<span id='media-dims-".$image->ID."'>{$meta['width']}&nbsp;&times;&nbsp;{$meta['height']}</span> ";
			?>
				<li class="images">
					<div class="labelwrapper">
						<label><?php _e( 'Image', APP_TD ); ?> <?php echo $i; ?>:</label>
					</div>

					<div class="thumb-wrap-edit">
						<?php echo cp_get_attachment_link($image->ID); ?>
					</div>

					<div class="image-meta">
						<p class="image-delete"><input class="checkbox" type="checkbox" name="image[]" value="<?php echo $image->ID; ?>">&nbsp;<?php _e( 'Delete Image', APP_TD ); ?></p>
						<p class="image-meta"><strong><?php _e( 'Upload Date:', APP_TD ); ?></strong> <?php echo appthemes_display_date( $image->post_date, 'date' ); ?></p>
						<p class="image-meta"><strong><?php _e( 'File Info:', APP_TD ); ?></strong> <?php echo $media_dims; ?> <?php echo $image->post_mime_type; ?></p>
					</div>

					<div class="clr"></div>

				<?php // get the alt text and print out the field
					$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true); ?>
					<p class="alt-text">
						<div class="labelwrapper">
							<label><?php _e( 'Alt Text:', APP_TD ); ?></label>
						</div>
						<input type="text" class="text" name="attachments[<?php echo $image->ID; ?>][image_alt]" id="image_alt" value="<?php if(count($alt)) echo esc_attr(stripslashes($alt)); ?>" />
					</p>

					<div class="clr"></div>
				</li>
			<?php
				$i++;
			endforeach;

		endif;

		// returns a count of array keys so we know how many images currently
		// are being used with this ad. this value is needed for cp_ad_edit_image_input_fields()
		return $imagecount;
	}
}


// gets the image link for each ad. used in the edit-ads page template
function cp_get_attachment_link($id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false) {
	$id = intval($id);
	$_post = & get_post( $id );

	// print_r($_post);

	if ( ('attachment' != $_post->post_type) || !$url = wp_get_attachment_url($_post->ID) )
		return __( 'Missing Attachment', APP_TD );

	if ( $permalink )
		$url = get_attachment_link($_post->ID);

	$post_title = esc_attr($_post->post_title);

	if ( $text ) {
		$link_text = esc_attr($text);
	} elseif ( ( is_int($size) && $size != 0 ) or ( is_string($size) && $size != 'none' ) or $size != false ) {
		$link_text = wp_get_attachment_image($id, $size, $icon);
	} else {
		$link_text = '';
	}

	if( trim($link_text) == '' )
		$link_text = $_post->post_title;

	return apply_filters( 'cp_get_attachment_link', "<a target='_blank' href='$url' alt='' class='post-gallery' data-rel='colorbox' title='$post_title'>$link_text</a>", $id, $size, $permalink, $icon, $text );
}


// gives us a count of how many images are associated to an ad
function cp_count_ad_images($ad_id) {
    $args = array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $ad_id, 'order' => 'ASC', 'orderby' => 'ID');

    // get all the images associated to this ad
    $images = get_posts($args);

    // get the total number of images already on this ad
    // we need it to figure out how many upload fields to show
    $imagecount = count($images);

    // returns a count of array keys so we know how many images currently
    // are being used with this ad.
    return $imagecount;
}


// calculates total number of image input upload boxes
// minus the number of existing images
function cp_ad_edit_image_input_fields($imagecount) {
	global $cp_options;

    $disabled = '';

    // get the max number of images allowed option
    $maximages = $cp_options->num_images;

    // figure out how many image upload fields we need
    $imageboxes = ($maximages - $imagecount);

    // now loop through and print out the upload fields
    for ( $i = 0; $i < $imageboxes; $i++ ) :
		$next = $i + 1;
		if ( $i > 0 ) $disabled = 'disabled="disabled"';
    ?>
        <li>
            <div class="labelwrapper">
				<label><?php _e( 'Add Image', APP_TD ); ?>:</label>
			</div>
				<?php echo "<input type=\"file\" name=\"image[]\" id=\"upload$i\" class=\"fileupload\" onchange=\"enableNextImage(this,$next)\" $disabled" . ' />'; ?>
            <div class="clr"></div>
        </li>
    <?php
    endfor;
    ?>

    <p class="small"><?php printf( __( 'You are allowed %s image(s) per ad.', APP_TD ), $maximages ); ?> <?php echo $cp_options->max_image_size; ?><?php _e( 'KB max file size per image.', APP_TD ); ?> <?php _e( 'Check the box next to each image you wish to delete.', APP_TD ); ?></p>
    <div class="clr"></div>

<?php
}


// make sure it's an image file and then upload it
function cp_image_upload($upload) {
    if ( cp_file_is_image( $upload['tmp_name'] ) ) {
        $overrides = array( 'test_form' => false );
        // move image to the WP defined upload directory and set correct permissions
        $file = wp_handle_upload( $upload, $overrides );
    }
    return $file;
}


// delete the image from WordPress
function cp_delete_image() {
    foreach ( (array) $_POST['image'] as $img_id_del ) {
        $img_del = & get_post($img_id_del);

        if ( $img_del )
            if ( $img_del->post_type == 'attachment' )
                if ( !wp_delete_attachment($img_id_del, true) )
                    wp_die( __( 'Error in deleting the image.', APP_TD ) );
    }
}

// update the image alt and title text on edit ad page. since v3.0.5
function cp_update_alt_text() {
	foreach ($_POST['attachments'] as $attachment_id => $attachment) :
		if (isset($attachment['image_alt'])) {
			$image_alt = esc_html(get_post_meta($attachment_id, '_wp_attachment_image_alt', true));

			if ($image_alt != esc_html($attachment['image_alt'])) {
				$image_alt = wp_strip_all_tags(esc_html($attachment['image_alt']), true);

        $image_data = & get_post($attachment_id);
          if($image_data):
  				// update the image alt text for based on the id
  				update_post_meta($attachment_id, '_wp_attachment_image_alt', addslashes($image_alt));

  				// update the image title text. it's stored as a post title so it's different to update
  				$post = array();
  				$post['ID'] = $attachment_id;
  				$post['post_title'] = $image_alt;
  				wp_update_post($post);
        endif;
			}
		}
	endforeach;
}


// checks if a user is logged in, if not redirect them to the login page
function auth_redirect_login() {
    $user = wp_get_current_user();
    if ( $user->ID == 0 ) {
        nocache_headers();
        wp_redirect( wp_login_url($_SERVER['REQUEST_URI']) );
        exit();
    }
}


// gets the ad tags
function cp_get_the_term_list( $id = 0, $taxonomy, $before = '', $sep = '', $after = '' ) {
    $terms = get_the_terms( $id, $taxonomy );

    if (is_wp_error($terms))
        return $terms;

    if (empty($terms))
        return false;

    foreach ($terms as $term) {
        $link = get_term_link($term, $taxonomy);
        if (is_wp_error($link))
            return $link;
        $term_links[] = $term->name . ', ';
    }

    $term_links = apply_filters( "term_links-$taxonomy", $term_links );

    return $before . join( $sep, $term_links ) . $after;
}


// change ad to draft if it's expired
function cp_has_ad_expired( $post_id ) {
	global $wpdb;

	$expire_time = strtotime( get_post_meta($post_id, 'cp_sys_expire_date', true) );

	// if current date is past the expires date, change post status to draft
	if ( current_time('timestamp') > $expire_time ) {
		$my_post = array();
		$my_post['ID'] = $post_id;
		$my_post['post_status'] = 'draft';
		wp_update_post( $my_post );

		return true;
	}

	return false;
}


// saves the ad on the tpl-edit-item.php page template
function cp_update_listing() {
	global $wpdb, $cp_options;

	// check to see if html is allowed
	if ( ! $cp_options->allow_html )
		$post_content = appthemes_filter($_POST['post_content']);
	else
		$post_content = $_POST['post_content'];

	// keep only numeric, commas or decimal values
	if ( !empty($_POST['cp_price']) )
		$_POST['cp_price'] = appthemes_clean_price( $_POST['cp_price'] );

	// keep only values and insert/strip commas if needed and put into an array
	if ( !empty($_POST['tags_input']) ) {
		$_POST['tags_input'] = appthemes_clean_tags( $_POST['tags_input'] );
		$new_tags = explode( ',', $_POST['tags_input'] );
	}

	// put all the ad elements into an array
	// these are the minimum required fields for WP (except tags)
	$update_ad                      = array();
	$update_ad['ID']                = trim( $_POST['ad_id'] );
	$update_ad['post_title']        = appthemes_filter( $_POST['post_title'] );
	$update_ad['post_content']      = trim( $post_content );
	//$update_ad['post_category']   = array((int)appthemes_filter($_POST['cat'])); // maybe use later if we decide to let users change categories

	//print_r($update_ad).' <- new ad array<br>'; // for debugging

	// update the ad and return the ad id
	$post_id = wp_update_post( $update_ad );


	if ( $post_id ) {

		//update post custom taxonomy "ad_tags"
		// keep only values and insert/strip commas if needed and put into an array
		if ( !empty($_POST['tags_input']) ) {
			$_POST['tags_input'] = appthemes_clean_tags( $_POST['tags_input'] );
			$new_tags = explode( ',', $_POST['tags_input'] );
			$settags = wp_set_object_terms( $post_id, $new_tags, APP_TAX_TAG );
			//echo 'Update Tags or Erro:'.print_r($settags, true);
		}

		// assemble the comma separated hidden fields back into an array so we can save them.
		$metafields = explode( ',', $_POST['custom_fields_vals'] );

		// loop through all custom meta fields and update values
		foreach ( $metafields as $name ) {

			if ( !isset($_POST[$name]) ) {
				delete_post_meta($post_id, $name);
			} else if ( is_array($_POST[$name]) ) {
				delete_post_meta($post_id, $name);
				foreach ( $_POST[$name] as $checkbox_value )
					add_post_meta( $post_id, $name, $checkbox_value );
			} else {
				update_post_meta( $post_id, $name, $_POST[$name] );
			}

		}


		$result = $post_id;
		cp_action_update_listing( $post_id );

	} else {
		// the ad wasn't updated
		$result = false;

	}

	return $result;

}


// shows how much time is left before the ad expires
function cp_timeleft( $date_time ) {
	if ( is_string( $date_time ) )
		$date_time = strtotime( $date_time );

	$timeLeft = $date_time - current_time( 'timestamp' );

	$days_label = __( 'days', APP_TD );
	$day_label = __( 'day', APP_TD );
	$hours_label = __( 'hours', APP_TD );
	$hour_label = __( 'hour', APP_TD );
	$mins_label = __( 'mins', APP_TD );
	$min_label = __( 'min', APP_TD );
	$secs_label = __( 'secs', APP_TD );
	$r_label = __( 'remaining', APP_TD );
	$expired_label = __( 'This ad has expired', APP_TD );

	if ( $timeLeft > 0 ) {
		$days = floor($timeLeft/60/60/24);
		$hours = $timeLeft/60/60%24;
		$mins = $timeLeft/60%60;
		$secs = $timeLeft%60;

		if ( $days == 01 ) { $d_label = $day_label; } else { $d_label = $days_label; }
		if ( $hours == 01 ) { $h_label = $hour_label; } else { $h_label = $hours_label; }
		if ( $mins == 01 ) { $m_label = $min_label; } else { $m_label = $mins_label; }

		if ( $days ) {
			$theText = $days . " " . $d_label;
			if ( $hours ) { $theText .= ", " .$hours . " " . $h_label; }
		} elseif ( $hours ) {
			$theText = $hours . " " . $h_label;
			if ( $mins ) { $theText .= ", " .$mins . " " . $m_label; }
		} elseif ( $mins ) {
			$theText = $mins . " " . $m_label;
			if ( $secs ) { $theText .= ", " .$secs . " " . $secs_label; }
		} elseif ( $secs ) {
			$theText = $secs . " " . $secs_label;
		}
	} else {
		$theText = $expired_label;
	}
	return $theText;
}


// Breadcrumb for the top of pages
function cp_breadcrumb() {
	global $app_abbr, $post;

	$delimiter = '&raquo;';
	$currentBefore = '<span class="current">';
	$currentAfter = '</span>';

	if ( !is_front_page() || is_paged() ) :
		$flag = 1;
		echo '<div id="crumbs">';
		echo '<a href="' . home_url('/') . '">' . __( 'Home', APP_TD ) . '</a> ' . $delimiter . ' ';

		// figure out what to display
		switch ($flag) :

			case is_tax(APP_TAX_TAG):
				echo $currentBefore . __( 'Ads tagged with', APP_TD ) . ' &#39;' . single_tag_title('', false) . '&#39;' . $currentAfter;
			break;

			case is_tax():
				// get the current ad category
				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
				// get the current ad category parent id
				$parent = $term->parent;
				// WP doesn't have a function to grab the top-level term id so we need to
				// climb up the tree and create a list of all the ad cat parents
				while ($parent):
					$parents[] = $parent;
					$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
					$parent = $new_parent->parent;
				endwhile;

				// if parents are found display them
				if(!empty($parents)):
					// flip the array over so we can print out descending
					$parents = array_reverse($parents);
					// for each parent, create a breadcrumb item
					foreach ($parents as $parent):
						$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
						$url = get_term_link( $item->slug, APP_TAX_CAT );
						echo '<a href="'.$url.'">'.$item->name.'</a> ' . $delimiter . ' ';
					endforeach;
				endif;
				echo $currentBefore . $term->name . $currentAfter;
			break;

			case is_singular(APP_POST_TYPE):
				// get the ad category array
				$term = wp_get_object_terms($post->ID, APP_TAX_CAT);
        if(!empty($term)):
  				// get the first ad category parent id
  				$parent = $term[0]->parent;
  				// get the first ad category id and put into array
  				$parents[] = $term[0]->term_id;
  				// WP doesn't have a function to grab the top-level term id so we need to
  				// climb up the tree and create a list of all the ad cat parents
  				while ($parent):
  					$parents[] = $parent;
  					$new_parent = get_term_by( 'id', $parent, APP_TAX_CAT );
  					$parent = $new_parent->parent;
  				endwhile;
  				// if parents are found display them
  				if(!empty($parents)):
  					// flip the array over so we can print out descending
  					$parents = array_reverse($parents);
  					// for each parent, create a breadcrumb item
  					foreach ($parents as $parent):
  						$item = get_term_by( 'id', $parent, APP_TAX_CAT );
  						$url = get_term_link( $item->slug, APP_TAX_CAT );

  						echo '<a href="'.$url.'">'.$item->name.'</a> ' . $delimiter . ' ';
  					endforeach;
  				endif;
        endif;
				echo $currentBefore . the_title() . $currentAfter;
			break;

			case is_single():
				$cat = get_the_category();
				if ( ! empty( $cat ) ) {
					$cat = $cat[0];
					echo get_category_parents( $cat, true, " $delimiter " );
				}
				echo $currentBefore . the_title() . $currentAfter;
			break;

			case is_category():
				global $wp_query;
				$cat_obj = $wp_query->get_queried_object();
				$thisCat = $cat_obj->term_id;
				$thisCat = get_category($thisCat);
				$parentCat = get_category($thisCat->parent);
				if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
				echo $currentBefore . single_cat_title() . $currentAfter;
			break;

			case is_page():
				// get the parent page id
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				if ($parent_id > 0 ) :
					// now loop through and put all parent pages found above current one in array
					while ($parent_id) {
						$page = get_page($parent_id);
						$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
						$parent_id  = $page->post_parent;
					}
					$breadcrumbs = array_reverse($breadcrumbs);
					foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
				endif;
				echo $currentBefore . the_title() . $currentAfter;
			break;

			case ( is_home() && get_option( 'show_on_front' ) == 'page' ):
				$home_page = get_page( get_queried_object_id() );
				echo $currentBefore . get_the_title( $home_page->ID ) . $currentAfter;
			break;

			case is_search():
				echo $currentBefore . __( 'Search results for', APP_TD ) . ' &#39;' . get_search_query() . '&#39;' . $currentAfter;
			break;

			case is_tag():
				echo $currentBefore . __( 'Posts tagged with', APP_TD ) . ' &#39;' . single_tag_title('', false) . '&#39;' . $currentAfter;
			break;

			case is_author():
				global $author;
				$userdata = get_userdata($author);
				echo $currentBefore . __( 'About', APP_TD ) . '&nbsp;' . $userdata->display_name . $currentAfter;
			break;

			case is_day():
				echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
				echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
				echo $currentBefore . get_the_time('d') . $currentAfter;
			break;

			case is_month():
				echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
				echo $currentBefore . get_the_time('F') . $currentAfter;
			break;

			case is_year():
				echo $currentBefore . get_the_time('Y') . $currentAfter;
			break;

			case is_archive():
        if( !empty($_GET['sort']) && $_GET['sort'] == 'random' )
  				  echo $currentBefore . __( 'Random Ads', APP_TD ) . $currentAfter;
        elseif( !empty($_GET['sort']) && $_GET['sort'] == 'popular' )
  				  echo $currentBefore . __( 'Popular Ads', APP_TD ) . $currentAfter;
				else
  				  echo $currentBefore . __( 'Latest Ads', APP_TD ) . $currentAfter;
			break;

			case is_404():
				echo $currentBefore . __( 'Page not found', APP_TD ) . $currentAfter;
			break;

		endswitch;

		if ( get_query_var('paged') ) {
		  if ( is_home() || is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_archive() || is_tax() ) echo ' (';
			echo __( 'Page', APP_TD ) . ' ' . get_query_var('paged');
		  if ( is_home() || is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() || is_archive() || is_tax() ) echo ')';
		}

		echo '</div>';

	endif;

}


// return most popular ads for use in loop
function cp_get_popular_ads() {
	global $cp_has_next_page;

	$popular = new CP_Popular_Posts_Query();

	if ( ! $popular->have_posts() )
		return false;

	$cp_has_next_page = ( $popular->max_num_pages > 1 );

	return $popular;
}


// query popular ads & posts
class CP_Popular_Posts_Query extends WP_Query {

	public $stats;
	public $stats_table;
	public $today_date;

	function __construct( $args = array(), $stats = 'total' ) {
		global $wpdb;

		$this->stats = $stats;
		$this->stats_table = ( $stats == 'today' ) ? $wpdb->cp_ad_pop_daily : $wpdb->cp_ad_pop_total;
		$this->today_date = date( 'Y-m-d', current_time( 'timestamp' ) );

		$defaults = array(
			'post_type' => APP_POST_TYPE,
			'post_status' => 'publish',
			'paged' => ( get_query_var('paged') ) ? get_query_var('paged') : 1,
			'suppress_filters' => false,
		);
		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( 'cp_popular_posts_args', $args );

		add_filter( 'posts_join', array( $this, 'posts_join' ) );
		add_filter( 'posts_where', array( $this, 'posts_where' ) );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );

		parent::__construct( $args );

		// remove filters to don't affect any other queries
		remove_filter( 'posts_join', array( $this, 'posts_join' ) );
		remove_filter( 'posts_where', array( $this, 'posts_where' ) );
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );
	}

	function posts_join( $sql ) {
		global $wpdb;
		return $sql . " INNER JOIN $this->stats_table ON ($wpdb->posts.ID = $this->stats_table.postnum) ";
	}

	function posts_where( $sql ) {
		global $wpdb;
		$sql = $sql . " AND $this->stats_table.postcount > 0 ";

		if ( $this->stats == 'today' )
			$sql .= " AND $this->stats_table.time = '$this->today_date' ";

		if ( $this->get( 'date_start' ) )
			$sql .= " AND $wpdb->posts.post_date > '" . $this->get( 'date_start' ) . "' ";

		return $sql;
	}

	function posts_orderby( $sql ) {
		return "$this->stats_table.postcount DESC";
	}

}


/**
 * Returns ads which are marked as featured for slider
 */
function cp_get_featured_slider_ads() {

	$args = array(
		'post_type' => APP_POST_TYPE,
		'post_status' => 'publish',
		'post__in' => get_option('sticky_posts'),
		'posts_per_page' => 15,
		'orderby' => 'rand',
		'suppress_filters' => false,
	);

	$args = apply_filters( 'cp_featured_slider_args', $args );

	$featured = new WP_Query( $args );

	if ( ! $featured->have_posts() )
		return false;

	return $featured;
}


// custom related posts function based on tags
// not being used in 3.0 yet
function cp_related_posts($postID, $width, $height) {
    global $wpdb, $post, $cp_options;
    $output = '';

    if ( ! $cp_options->similar_items ) {

// if (!$post_id) { $post_id = $post->ID; }

        $q = "SELECT DISTINCT object_id, post_title, post_content ".
                "FROM $wpdb->term_relationships r, $wpdb->term_taxonomy t, $wpdb->posts p ".
                "WHERE t.term_id IN (".
                "SELECT t.term_id FROM $wpdb->term_relationships r, $wpdb->term_taxonomy t ".
                "WHERE r.term_taxonomy_id = t.term_taxonomy_id ".
                "AND t.taxonomy = 'category' ".
                "AND r.object_id = $postID".
                ") ".
                "AND r.term_taxonomy_id = t.term_taxonomy_id ".
                "AND p.post_status = 'publish' ".
                "AND p.ID = r.object_id ".
                "AND object_id <> $postID ".
                "AND p.post_type = '".APP_POST_TYPE."' ".
                "ORDER BY RAND() LIMIT 5";

        $entries = $wpdb->get_results($q);

//$output .= '<h3>'. __( 'Similar Items', APP_TD ) . '</h3>';
        $output .= '<div id="similar-items">';

        if ($entries) {

            $output .= '<ul>';

            foreach ($entries as $post) {
                $output .= '<li class="clearfix">';
                $output .= '<div class="list_ad_img"><img src="'.cp_single_image_raw($post->object_id, $width, $height).'" /></div>';
                $output .= '<span class="list_ad_wrap_wide"><a class="list_ad_link_wide" href="'.get_permalink($post->object_id).'">'. $post->post_title. '</a><br/>';
                $output .= substr(strip_tags($post->post_content), 0, 165).'...</span>';
                $output .= '</li>';
            }

        } else {
            $output .= '<p>' . __( 'No matches found', APP_TD ) . '</p>';
        }
        $output .= '</ul>';
        $output .= '</div>';

        return $output;
    }
}


// show category with price dropdown
if (!function_exists('cp_dropdown_categories_prices')) {
	function cp_dropdown_categories_prices( $args = '' ) {
		$defaults = array( 'show_option_all' => '', 'show_option_none' => '', 'orderby' => 'ID', 'order' => 'ASC', 'show_last_update' => 0, 'show_count' => 0, 'hide_empty' => 1, 'child_of' => 0, 'exclude' => '', 'echo' => 1, 'selected' => 0, 'hierarchical' => 0, 'name' => 'cat', 'class' => 'postform', 'depth' => 0, 'tab_index' => 0 );

		$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;
		$r = wp_parse_args( $args, $defaults );
		$r['include_last_update_time'] = $r['show_last_update'];
		extract( $r );

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 )
			$tab_index_attribute = " tabindex=\"$tab_index\"";

		$categories = get_categories( $r );
		$name = esc_attr( $name );
		$class = esc_attr( $class );
		$id = $id ? esc_attr( $id ) : $name;

		$output = '';
		if ( ! empty( $categories ) ) {
			$output = "<select name='$name' id='$id' class='$class' $tab_index_attribute>\n";

			if ( $show_option_all ) {
				$show_option_all = apply_filters( 'list_cats', $show_option_all );
				$selected = ( '0' === strval($r['selected']) ) ? " selected='selected'" : '';
				$output .= "\t<option value='0'$selected>$show_option_all</option>\n";
			}

			if ( $show_option_none ) {
				$show_option_none = apply_filters( 'list_cats', $show_option_none );
				$selected = ( '-1' === strval($r['selected']) ) ? " selected='selected'" : '';
				$output .= "\t<option value='-1'$selected>$show_option_none</option>\n";
			}

			if ( $hierarchical )
				$depth = $r['depth'];  // Walk the full depth.
			else
				$depth = -1; // Flat.

			$output .= cp_category_dropdown_tree( $categories, $depth, $r );
			$output .= "</select>\n";
		}

		$output = apply_filters( 'wp_dropdown_cats', $output );

		if ( $echo )
			echo $output;

		return $output;
	}
}

// needed for the cp_dropdown_categories_prices function
function cp_category_dropdown_tree() {
	$args = func_get_args();

	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
		$walker = new cp_CategoryDropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

// needed for the cp_category_dropdown_tree function
class cp_CategoryDropdown extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );

	function start_el( &$output, $category, $depth, $args, $current_object_id = 0 ) {
		global $cp_options;

		$prices = $cp_options->price_per_cat;
		$cat_price = ( isset( $prices[ $category->cat_ID ] ) ) ? (float) $prices[ $category->cat_ID ] : 0;
		$pad = str_repeat('&nbsp;', $depth * 3);
		$cat_name = apply_filters( 'list_cats', $category->name, $category );

		$output .= "\t<option class=\"level-$depth\" value=\"" . $category->term_id . "\">";
		$output .= $pad . $cat_name;
		$output .= ' - ' . appthemes_get_price( $cat_price ) . '</option>'."\n";
	}
}


// categories list display
function cp_create_categories_list( $location = 'menu' ) {
	global $cp_options;

	$prefix = 'cat_' . $location . '_';

	$args['menu_cols'] = ( $location == 'menu' ? 3 : $cp_options->{$prefix . 'cols'} );
	$args['menu_depth'] = $cp_options->{$prefix . 'depth'};
	$args['menu_sub_num'] = $cp_options->{$prefix . 'sub_num'};
	$args['cat_parent_count'] = $cp_options->{$prefix . 'count'};
	$args['cat_child_count'] = $cp_options->{$prefix . 'count'};
	$args['cat_hide_empty'] = $cp_options->{$prefix . 'hide_empty'};
	$args['cat_nocatstext'] = true;
	$args['cat_order'] = 'ASC';
	$args['taxonomy'] = APP_TAX_CAT;

	return appthemes_categories_list( $args );
}


// delete transient to refresh cat menu
function cp_edit_term_delete_transient() {
	delete_transient( 'cp_cat_menu' );
}
add_action( 'edit_term', 'cp_edit_term_delete_transient' );




// If you want to automatically resize youtube videos uncomment the filter
function cp_resize_youtube($content) {
    return str_replace('width="640" height="385"></embed>', 'width="480" height="295"></embed>', $content);
}
//add_filter('the_content', 'cp_resize_youtube', 999);



// ajax auto-complete search
function cp_suggest() {
    global $wpdb;

	$s = $_GET['term']; // is this slashed already?

    if ( isset($_GET['tax']) )
            $taxonomy = sanitize_title($_GET['tax']);
    else
            die('no taxonomy');

    if ( false !== strpos( $s, ',' ) ) {
        $s = explode( ',', $s );
        $s = $s[count( $s ) - 1];
    }
    $s = trim( $s );
    if ( strlen( $s ) < 2 ) {
        die( __( 'need at least two characters', APP_TD ) ); // require 2 chars for matching
	}

	$terms = $wpdb->get_col( "
		SELECT t.slug FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id ".
		"WHERE tt.taxonomy = '$taxonomy' AND tt.count > 0 ".
		"AND t.name LIKE (
			'%$s%'
		)" .
		"LIMIT 50"
		);
	if(empty($terms)){
		//$results[0] = {"name":"no results"};
		echo json_encode($terms);
		die;
	}else{
		$i = 0;
		foreach ($terms as $term) {
			$results[$i] = get_term_by( 'slug', $term, $taxonomy );
			$i++;
		}
		echo json_encode($results);
		die;
	}
}


/**
 * Custom ClassiPress search engine to search
 * and include custom fields
 * @global <type> $wpdb
 * @param <type> $join
 * @return <type>
 *
 *
 */



// exclude pages and blog entries from search results if option is set
// not using yet since still using custom where statement below
// since 3.0.5
function appthemes_exclude_search_types( $query ) {
	global $cp_options;

	if ( $query->is_search ) {

		if ( $cp_options->search_ex_blog )
			$query->set('post_type', APP_POST_TYPE);
		else
			$query->set( 'post_type', array( 'post', APP_POST_TYPE ) );

	}
	return $query;
}

//if ( $cp_options->search_ex_pages )
//add_filter('pre_get_posts', 'appthemes_exclude_search_types');



// search only ads and not pages
function cp_is_type_page() {
	global $post;

	return ( $post->post_type == 'page' );
}


// get all custom field names so we can use them for search
function cp_custom_search_fields() {
	global $wpdb;

	$custom_fields = array();

	$sql = "SELECT field_name FROM $wpdb->cp_ad_fields p WHERE p.field_name LIKE 'cp_%' ";
	$results = $wpdb->get_results( $sql );

	if ( $results ) {
		foreach ( $results as $result ) {
			// put the fields into an array
			$custom_fields[] = $result->field_name;
		}
	}

	return $custom_fields;
}


// search on custom fields
function custom_search_groupby( $groupby ) {
	global $wpdb, $wp_query;

	if ( is_search() && isset( $_GET['s'] ) ) {
		$groupby = "$wpdb->posts.ID";

		remove_filter( 'posts_groupby', 'custom_search_groupby' );
	}

	return $groupby;
}


// search on custom fields
function custom_search_join( $join ) {
	global $wpdb, $wp_query;

	if ( is_search() && isset( $_GET['s'] ) ) {

		$join  = " LEFT JOIN $wpdb->term_relationships AS r ON ($wpdb->posts.ID = r.object_id) ";
		$join .= " LEFT JOIN $wpdb->term_taxonomy AS x ON (r.term_taxonomy_id = x.term_taxonomy_id) ";
		$join .= " AND (x.taxonomy = '".APP_TAX_TAG."' OR x.taxonomy = '".APP_TAX_CAT."' OR 1=1) ";

		// if an ad category is selected, limit results to that cat only
		$catid = get_query_var('scat');

		if ( ! empty( $catid ) ) {

			// put the catid into an array
			(array) $include_cats[] = $catid;

			// get all sub cats of catid and put them into the array
			$descendants = get_term_children( (int) $catid, APP_TAX_CAT );

			foreach ( $descendants as $key => $value )
				$include_cats[] = $value;

			// take catids out of the array and separate with commas
			$include_cats = "'" . implode( "', '", $include_cats ) . "'";

			// add the category filter to show anything within this cat or it's children
			$join .= " INNER JOIN $wpdb->term_relationships AS tr2 ON ($wpdb->posts.ID = tr2.object_id) ";
			$join .= " INNER JOIN $wpdb->term_taxonomy AS tt2 ON (tr2.term_taxonomy_id = tt2.term_taxonomy_id) ";
			$join .= " AND tt2.term_id IN ($include_cats) ";

		}

		$join .= " INNER JOIN $wpdb->postmeta AS m ON ($wpdb->posts.ID = m.post_id) ";
		$join .= " LEFT JOIN $wpdb->terms AS t ON x.term_id = t.term_id ";

		remove_filter( 'posts_join', 'custom_search_join' );
	}

	return $join;
}


// search on custom fields
function custom_search_where( $where ) {
	global $wpdb, $wp_query, $cp_options;

	$old_where = $where; // intercept the old where statement

	if ( is_search() && isset( $_GET['s'] ) ) {

		if ( $cp_options->search_custom_fields ) {
			// get the custom fields to add to search
			$customs = cp_custom_search_fields();
			// add some internal custom fields to search
			$customs = array_merge( $customs, array( 'cp_sys_ad_conf_id' ) );
		}

		$query = '';

		$var_q = stripslashes( $_GET['s'] );
		//empty the s parameter if set to default search text
		if ( __( 'Enter ISBN # Of The Book You Want', APP_TD ) == $var_q )
			$var_q = '';

		if ( isset( $_GET['sentence'] ) || $var_q == '' ) {
			$search_terms = array($var_q);
		} else {
			preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $var_q, $matches);
			$search_terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);
		}

		if ( ! isset( $_GET['exact'] ) )
			$_GET['exact'] = '';

		$n = ( $_GET['exact'] ) ? '' : '%';

		$searchand = '';

		foreach ( (array) $search_terms as $term ) {
			$term = addslashes_gpc($term);

			$query .= "{$searchand}(";
			$query .= "($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
			$query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
			$query .= " OR ((t.name LIKE '{$n}{$term}{$n}')) OR ((t.slug LIKE '{$n}{$term}{$n}'))";

			if ( isset( $customs ) ) {
				foreach ( $customs as $custom ) {
					$query .= " OR (";
					$query .= "(m.meta_key = '$custom')";
					$query .= " AND (m.meta_value  LIKE '{$n}{$term}{$n}')";
					$query .= ")";
				}
			}

			$query .= ")";
			$searchand = ' AND ';
		}

		$term = esc_sql( $var_q );

		if ( ! isset( $_GET['sentence'] ) && count( $search_terms ) > 1 && $search_terms[0] != $var_q ) {
			$query .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
			$query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
		}

		if ( ! empty( $query ) ) {

			$where = " AND ({$query}) AND ($wpdb->posts.post_status = 'publish') ";

			// setup the array for post types
			$post_type_array = array();

			// always include the ads post type
			$post_type_array[] = APP_POST_TYPE;

			// check to see if we include blog posts
			if ( ! $cp_options->cp_search_ex_blog )
				$post_type_array[] = 'post';

			// check to see if we include pages
			if ( ! $cp_options->cp_search_ex_pages )
				$post_type_array[] = 'page';

			// build the post type filter sql from the array values
			$post_type_filter = "'" . implode( "','", $post_type_array ) . "'";

			// return the post type sql to complete the where clause
			$where .= " AND ($wpdb->posts.post_type IN ($post_type_filter)) ";

		}

		remove_filter( 'posts_where', 'custom_search_where' );
	}

	return $where;
}

// load filters only on frontend
if ( ! is_admin() ) {
	add_filter( 'posts_join', 'custom_search_join' );
	add_filter( 'posts_where', 'custom_search_where' );
	add_filter( 'posts_where', 'custom_search_refine_where' );
	add_filter( 'posts_join', 'custom_search_refine_join' );
	add_filter( 'posts_groupby', 'custom_search_groupby' );
}



// if an ad is created and doesn't have an expiration date,
// make sure to insert one based on the Ad Listing Period option.
// all ads need an expiration date otherwise they will automatically
// expire. this is common when customers manually create an ad through
// the WP admin new post or when using an automated scrapper script
function cp_check_expire_date($post_id) {
	global $wpdb, $cp_options;

	// we don't want to add the expires date to blog posts
	if ( get_post_type() != APP_POST_TYPE )  {

		// do nothing

	} else {

		// add default expiration date if the expired custom field is blank or empty
		$ad_expire_date = get_post_meta($post_id, 'cp_sys_expire_date', true);
		if ( empty($ad_expire_date) ) :
			$ad_length = $cp_options->prun_period;
			if ( !$ad_length || !is_numeric($ad_length) ) $ad_length = '365'; // if the prune days is empty, set it to one year
			$ad_expire_date = appthemes_mysql_date( current_time( 'mysql' ), $ad_length );
			add_post_meta( $post_id, 'cp_sys_expire_date', $ad_expire_date, true );
		endif;

	}

}

/**
 * RENEW AD LISTINGS : @SC - Allowing free ads to be relisted, call this
 * function and send the ads post id. We will check to make sure its free
 * and relist the ad for the same duration it
 */
if ( !function_exists('cp_renew_ad_listing') ) :
function cp_renew_ad_listing ( $ad_id ) {
	global $cp_options;

	$listfee = (float)get_post_meta($ad_id, 'cp_sys_total_ad_cost', true);

	// protect against false URL attempts to hack ads into free renewal
	if ( $listfee == 0 )	{
		$ad_length = get_post_meta($ad_id, 'cp_sys_ad_duration', true);
		if ( empty($ad_length) )
			$ad_length = $cp_options->prun_period;

		if ( !$ad_length || !is_numeric($ad_length) ) $ad_length = '365'; // if the prune days is empty, set it to one year

		// set the ad listing expiration date
		$ad_expire_date = appthemes_mysql_date( current_time( 'mysql' ), $ad_length );

		//now update the expiration date on the ad
		update_post_meta($ad_id, 'cp_sys_expire_date', $ad_expire_date);
		wp_update_post( array( 'ID' => $ad_id, 'post_date' => current_time('mysql') ) );
		return true;
	}

	//attempt to relist a paid ad
	else {	return false;	}
}
endif;


// delete ad listings together with associated attachments
function cp_delete_ad_listing( $postid ) {
	global $wpdb;

	$attachments_query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type='attachment'", $postid);
	$attachments = $wpdb->get_results($attachments_query);

	// delete all associated attachments
	if ( $attachments )
		foreach( $attachments as $attachment )
			wp_delete_attachment( $attachment->ID, true );

	// delete geo location
	cp_delete_geocode( $postid );

	// delete post and it's revisions, comments, meta
	if ( wp_delete_post( $postid, true ) )
		return true;
	else
		return false;
}


// runs when a post is published, or is edited and status is "published"
add_filter('publish_post', 'cp_check_expire_date', 9, 3);



// creates the charts on the dashboard
function cp_dashboard_charts() {
	global $wpdb, $cp_options;

	$sql = "SELECT COUNT(post_title) as total, post_date FROM $wpdb->posts WHERE post_type = %s AND post_date > %s GROUP BY DATE(post_date) DESC";
	$results = $wpdb->get_results( $wpdb->prepare( $sql, APP_POST_TYPE, appthemes_mysql_date( current_time( 'mysql' ), -30 ) ) );

	$listings = array();

	// put the days and total posts into an array
	foreach ( (array) $results as $result ) {
		$the_day = date( 'Y-m-d', strtotime( $result->post_date ) );
		$listings[ $the_day ] = $result->total;
	}

	// setup the last 30 days
	for ( $i = 0; $i < 30; $i++ ) {
		$each_day = date( 'Y-m-d', strtotime( '-' . $i . ' days' ) );
		// if there's no day with posts, insert a goose egg
		if ( ! in_array( $each_day, array_keys( $listings ) ) )
			$listings[ $each_day ] = 0;
	}

	// sort the values by date
	ksort( $listings );

	// Get sales - completed orders with a cost
	$results = array();
	$currency_symbol = $cp_options->curr_symbol;
	if ( current_theme_supports( 'app-payments' ) ) {
		$sql = "SELECT sum( m.meta_value ) as total, p.post_date FROM $wpdb->postmeta m INNER JOIN $wpdb->posts p ON m.post_id = p.ID WHERE m.meta_key = 'total_price' AND p.post_status IN ( '" . APPTHEMES_ORDER_COMPLETED . "', '" . APPTHEMES_ORDER_ACTIVATED . "' ) AND p.post_date > %s GROUP BY DATE(p.post_date) DESC";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, appthemes_mysql_date( current_time( 'mysql' ), -30 ) ) );
		$currency_symbol = APP_Currencies::get_current_symbol();
	}

	$sales = array();

	// put the days and total posts into an array
	foreach ( (array) $results as $result ) {
		$the_day = date( 'Y-m-d', strtotime( $result->post_date ) );
		$sales[ $the_day ] = $result->total;
	}

	// setup the last 30 days
	for ( $i = 0; $i < 30; $i++ ) {
		$each_day = date( 'Y-m-d', strtotime( '-' . $i . ' days' ) );
		// if there's no day with posts, insert a goose egg
		if ( ! in_array( $each_day, array_keys( $sales ) ) )
			$sales[ $each_day ] = 0;
	}

	// sort the values by date
	ksort( $sales );
?>

<div id="placeholder"></div>

<script language="javascript" type="text/javascript">
// <![CDATA[
jQuery(function() {

	var posts = [
		<?php
		foreach ( $listings as $day => $value ) {
			$sdate = strtotime( $day );
			$sdate = $sdate * 1000; // js timestamps measure milliseconds vs seconds
			$newoutput = "[$sdate, $value],\n";
			echo $newoutput;
		}
		?>
	];

	var sales = [
		<?php
		foreach ( $sales as $day => $value ) {
			$sdate = strtotime( $day );
			$sdate = $sdate * 1000; // js timestamps measure milliseconds vs seconds
			$newoutput = "[$sdate, $value],\n";
			echo $newoutput;
		}
		?>
	];


	var placeholder = jQuery("#placeholder");

	var output = [
		{
			data: posts,
			label: "<?php _e( 'New Ad Listings', APP_TD ); ?>",
			symbol: ''
		},
		{
			data: sales,
			label: "<?php _e( 'Total Sales', APP_TD ); ?>",
			symbol: '<?php echo $currency_symbol; ?>',
			yaxis: 2
		}
	];

	var options = {
		series: {
			lines: { show: true },
			points: { show: true }
		},
		grid: {
			tickColor:'#f4f4f4',
			hoverable: true,
			clickable: true,
			borderColor: '#f4f4f4',
			backgroundColor:'#FFFFFF'
		},
		xaxis: {
			mode: 'time',
			timeformat: "%m/%d"
		},
		yaxis: {
			min: 0
		},
		y2axis: {
			min: 0,
			tickFormatter: function(v, axis) {
				return "<?php echo $currency_symbol; ?>" + v.toFixed(axis.tickDecimals)
			}
		},
		legend: {
			position: 'nw'
		}
	};

	jQuery.plot(placeholder, output, options);

	// reload the plot when browser window gets resized
	jQuery(window).resize(function() {
		jQuery.plot(placeholder, output, options);
	});

	function showChartTooltip(x, y, contents) {
		jQuery('<div id="charttooltip">' + contents + '</div>').css( {
			position: 'absolute',
			display: 'none',
			top: y + 5,
			left: x + 5,
			opacity: 1
		} ).appendTo("body").fadeIn(200);
	}

	var previousPoint = null;
	jQuery("#placeholder").bind("plothover", function (event, pos, item) {
		jQuery("#x").text(pos.x.toFixed(2));
		jQuery("#y").text(pos.y.toFixed(2));
		if (item) {
			if (previousPoint != item.datapoint) {
				previousPoint = item.datapoint;

				jQuery("#charttooltip").remove();
				var x = new Date(item.datapoint[0]), y = item.datapoint[1];
				var xday = x.getDate(), xmonth = x.getMonth()+1; // jan = 0 so we need to offset month
				showChartTooltip(item.pageX, item.pageY, xmonth + "/" + xday + " - <b>" + item.series.symbol + y + "</b> " + item.series.label);
			}
		} else {
			jQuery("#charttooltip").remove();
			previousPoint = null;
		}
	});
});
// ]]>
</script>


<?php
}


// activate theme support items
if (function_exists('add_theme_support')) { // added in 2.9

	// this theme uses post thumbnails
	add_theme_support('post-thumbnails', array('post', 'page'));
	set_post_thumbnail_size(100, 100); // normal post thumbnails

	// add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );
}

// setup different image sizes
if ( function_exists( 'add_image_size' ) ) {
	add_image_size('blog-thumbnail', 150, 150); // blog post thumbnail size, box crop mode
	add_image_size('sidebar-thumbnail', 50, 50, true); // sidebar blog thumbnail size, box crop mode

	// create special sizes for the ads
	add_image_size('ad-thumb', 75, 75, true);
	add_image_size('ad-small', 100, 100, true);
	add_image_size('ad-medium', 250, 250, true);
	//add_image_size('ad-large', 500, 500);
}

// Set the content width based on the theme's design and stylesheet.
// Used to set the width of images and content. Should be equal to the width the theme
// is designed for, generally via the style.css stylesheet.
if (!isset($content_width))
	$content_width = 500;


// This theme supports native menu options, and uses wp_nav_menu() in one location for top navigation.
function appthemes_register_menus() {
	register_nav_menus(array(
		'primary' => __( 'Primary Navigation', APP_TD ),
		'secondary' => __( 'Footer Navigation', APP_TD ),
		'theme_dashboard' => __( 'Theme Dashboard', APP_TD )
	));
}
add_action( 'init', 'appthemes_register_menus' );


/**
* return all the order values we plan on using as hidden payment fields
*
* @since 3.1
*
*/
function cp_get_order_vals( $order_vals ) {
	global $cp_options;
	// figure out the number of days this ad was listed for
	if ( get_post_meta( $order_vals['post_id'], 'cp_sys_ad_duration', true ) )
		$order_vals['prune_period'] = get_post_meta( $order_vals['post_id'], 'cp_sys_ad_duration', true );
	else
		$order_vals['prune_period'] = $cp_options->prun_period;

	$order_vals['item_name'] = sprintf( __( 'Classified ad listing on %1$s for %2$s days', APP_TD ), get_bloginfo('name'), $order_vals['prune_period'] );
	$order_vals['item_number'] = get_post_meta( $order_vals['post_id'], 'cp_sys_ad_conf_id', true );
	$order_vals['item_amount'] =  get_post_meta( $order_vals['post_id'], 'cp_sys_total_ad_cost', true );
	$order_vals['notify_url'] = add_query_arg( array( 'invoice' => $order_vals['item_number'], 'aid' => $order_vals['post_id'] ), home_url('/') );
	$order_vals['return_url'] = add_query_arg( array( 'pid' => $order_vals['item_number'], 'aid' => $order_vals['post_id'] ), CP_ADD_NEW_CONFIRM_URL );
	$order_vals['return_text'] = sprintf( __( 'Click here to publish your ad on %s', APP_TD ), get_bloginfo('name') );

	return $order_vals;
}


/**
* return all the order pack values we plan on using as hidden payment fields
*
* @since 3.1
*
*/
function cp_get_order_pack_vals( $order_vals ) {
	global $cp_options;
	// lookup the pack info
	$pack = get_pack( $order_vals['pack'] );

	// figure out the number of days this ad was listed for
	// not needed? keeping for safety
	$order_vals['prune_period'] = $cp_options->prun_period;

	//setup variables depending on the purchase type
	if ( isset( $pack->pack_name ) && stristr( $pack->pack_status, 'membership' ) ) {

		$order_vals['item_name'] = sprintf( __( 'Membership on %1$s for %2$s days', APP_TD ), get_bloginfo('name'), $pack->pack_duration );
		$order_vals['item_number'] = stripslashes($pack->pack_name);
		$order_vals['item_amount'] = $pack->pack_membership_price;
		$order_vals['notify_url'] = add_query_arg( array( 'invoice' => $order_vals['oid'], 'uid' => $order_vals['user_id'] ), home_url('/') );
		$order_vals['return_url'] = add_query_arg( array( 'oid' => $order_vals['oid'], 'uid' => $order_vals['user_id'] ), CP_MEMBERSHIP_PURCHASE_CONFIRM_URL );
		$order_vals['return_text'] = sprintf( __( 'Click here to complete your purchase on %s', APP_TD ), get_bloginfo('name') );

	} else {

		_e( "Sorry, but there's been an error.", APP_TD );
		die;

	}

	return $order_vals;
}



//function retreives the membership pack name given a membership pack ID
function get_pack($theID, $type = '', $return = '') {
	global $wpdb, $the_pack;

	if ( stristr($theID, 'pend') )
	    $theID = get_pack_id($theID);

	//if the type is dashboard or ad, then get the assume the ID sent is the postID and packID needs to be obtained
	if ( $type == 'ad' || $type == 'dashboard' )
		$theID = get_pack_id( $theID, $type );

	//make sure the value is a proper MySQL int value
	$theID = intval($theID);

	if ( $theID > 0 ) {
		$the_pack = $wpdb->get_row( "SELECT * FROM $wpdb->cp_ad_packs WHERE pack_id = '$theID'" );
		$the_pack = apply_filters( 'cp_get_package', $the_pack, $theID );
	}

	if ( !empty($return) && !empty($the_pack)) {
		$the_pack = (array)$the_pack;

		if ( $return == 'array' )
		    return $the_pack;
		else
		    return $the_pack[$return];
	}

	return $the_pack;
}

//function send a string and attempt to filter out and return only the actual packID
function get_pack_id($active_pack, $type = '') {
	if ( !empty($type) ) { /*TODO LOOKUP PACK ID FROM POST - Will be possible once pack is stored with posts*/	}
	preg_match('/^pend(?P<pack_id>\w+)-(?P<order_id>\w+)/', $active_pack, $matches);

	if ($matches)
	    return $matches['pack_id'];
	else
	    return $active_pack;
}

//function send a string and attempt to filter out and return only the private order ID
function get_order_id($active_pack) {
	//attempt to match based on "pend" prefix
	preg_match('/^pend(?P<membership_pack_id>\w+)-(?P<private_order_id>\w+)/', $active_pack, $matches);

	//if order id is not foundyet, attempt to match based on option_name prefix
	if ( !isset($matches['private_order_id']) )
		preg_match('/^cp_order_(?P<user_id>\w+)_(?P<private_order_id>\w+)/', $active_pack, $matches);

	return $matches['private_order_id'];
}

//function send a string and attempt to filter out and return only the user ID from the order
function get_order_userid($active_pack) {
	//attempt to match based on "pend" prefix
	preg_match('/^pend(?P<membership_pack_id>\w+)-(?P<private_order_id>\w+)/', $active_pack, $matches);

	//if order id is not foundyet, attempt to match based on option_name prefix
	if ( !isset($matches['private_order_id']) )
		preg_match('/^cp_order_(?P<user_id>\w+)_(?P<private_order_id>\w+)/', $active_pack, $matches);

	return $matches['user_id'];
}

//function that retreives a users pending orders
function get_user_orders($user_id = '', $oid = '') {
	global $wpdb;
	$lookup = 'cp_order';

	if (!empty($user_id))
	    $lookup = 'cp_order_'.$user_id;

	if (!empty($oid))
	    $lookup = $oid;

	$orders = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%".$lookup."%'");

	//currently only expecting 1 order to be available, but programmed to enable easy expansion
	if (isset($orders[0])) {
		//if the order ID is passed, we always return the option string related tothe order
		if (!empty($oid)) return $orders[0]->option_name;
		//if the order ID is not passed, send back an array of all the "orders" for the user
		else return array($orders[0]->option_name);
	}

	//if not returning yet, this value is most likely just "false"
	return $orders;
}

//function that takes a membership pack and returns the proper benefit explanation
function get_pack_benefit($membership, $returnTotal = false) {
	$benefitHTML = '';

	if ( ! current_theme_supports( 'app-price-format' ) )
		return false;

	switch ( $membership->pack_type ) {
		case 'percentage':
			if($returnTotal) return number_format(($returnTotal * ($membership->pack_price / 100)),2);
			$benefitHTML .= preg_replace('/.00$/', '', $membership->pack_price).'% '. __( 'of price', APP_TD ); //remove decimal when decimal is .00
			break;
		case 'discount':
			if($returnTotal) return number_format(($returnTotal - ($membership->pack_price*1)),2);
			$benefitHTML .= sprintf( __( '%s\'s less per ad', APP_TD ), appthemes_get_price( $membership->pack_price ) );
			break;
		case 'required_static':
			if($returnTotal) return number_format(($membership->pack_price*1),2);
			if((float)$membership->pack_price == 0) $benefitHTML .= __( 'Free Posting', APP_TD );
			else $benefitHTML .= sprintf( __( '%s per ad', APP_TD ), appthemes_get_price( $membership->pack_price ) );
			$benefitHTML .= ' ('. __( 'required to post', APP_TD ) .')';
			break;
		case 'required_discount':
			if($returnTotal) return number_format(($returnTotal - ($membership->pack_price*1)),2);
			if($membership->pack_price > 0) $benefitHTML .= sprintf( __( '%s\'s less per ad', APP_TD ), appthemes_get_price( $membership->pack_price ) );
			$benefitHTML .= ' ('. __( 'required to post', APP_TD ) .')';
			break;
		case 'required_percentage':
			if($returnTotal) return number_format(($returnTotal * ($membership->pack_price / 100)),2);
			if($membership->pack_price < 100) $benefitHTML .= preg_replace('/.00$/', '', $membership->pack_price).'% '. __( 'of price', APP_TD ); //remove decimal when decimal is .00
			$benefitHTML .= ' ('. __( 'required to post', APP_TD ) .')';
			break;
		default: //likely 'static'
			if($returnTotal) return number_format(($membership->pack_price*1), 2);
			if((float)$membership->pack_price == 0) $benefitHTML .= __( 'Free Posting', APP_TD );
			else $benefitHTML .= sprintf( __( '%s per ad', APP_TD ), appthemes_get_price( $membership->pack_price ) );
	}

	return $benefitHTML;
}

function get_membership_requirement( $cat_id ) {
	global $cp_options;

	if ( $cp_options->required_membership_type == 'all' ) {
		// if all posts require "required" memberships
		return 'all';
	} else if ( $cp_options->required_membership_type == 'category' ) {
		// if post requirements are based on category specific requirements
		$required_categories = $cp_options->required_categories;
		if ( ! empty( $required_categories[ $cat_id ] ) )
			return $cat_id;
	}

	// no requirements active
	return false;
}

//if not meet membership requirement, redirect to membership purchase page
function cp_redirect_membership() {
	global $current_user, $cp_options;

	$current_requirement = false;
	$redirect_user = false;
	$current_user = wp_get_current_user();

	if ( ! $cp_options->enable_membership_packs )
		return;

	if ( isset( $_POST['cat'] ) )
		$current_requirement = get_membership_requirement( $_POST['cat'] );

	if ( $cp_options->required_membership_type == 'all' )
		$current_requirement = 'all';

	if ( ! $current_requirement )
		return;

	$current_membership = ( ! empty( $current_user->active_membership_pack ) ) ? get_pack( $current_user->active_membership_pack ) : false;

	if ( ! $current_membership || empty( $current_user->membership_expires ) ) {
		$redirect_user = true;
	} else if ( ! stristr( $current_membership->pack_type, 'required' ) || ( appthemes_days_between_dates( $current_user->membership_expires ) < 0 ) ) {
		$redirect_user = true;
	}

	if ( $redirect_user ) {
		$redirect_url = add_query_arg( array( 'membership' => 'required', 'cat' => $current_requirement ), CP_MEMBERSHIP_PURCHASE_URL );
		wp_redirect( $redirect_url );
		exit;
	}

}


//setup function to stop function from failing if sm debug bar is not installed
//this allows for optional use of sm debug bar plugin
if (!function_exists('dbug')) { function dbug($args) {} }


// update geo location in db
function cp_update_geocode( $post_id, $cat, $lat, $lng ) {
	global $wpdb;

	if ( !$lat || !$lng || !$cat || !$post_id )
		return false;

	$post_id = absint( $post_id );

	if ( !cp_get_geocode( $post_id, $cat ) )
		return cp_add_geocode( $post_id, $cat, $lat, $lng );

	$lat = floatval( $lat );
	$lng = floatval( $lng );

	$wpdb->update(
		$wpdb->cp_ad_geocodes,
		array (
			'lat' => $lat,
			'lng' => $lng
		),
		array(
			'post_id' => $post_id,
			'category' => $cat
		)
	);
	return true;
}


// add geo location to db
function cp_add_geocode( $post_id, $cat, $lat, $lng ) {
	global $wpdb;

	$post_id = intval( $post_id );
	$lat = floatval( $lat );
	$lng = floatval( $lng );

	if ( cp_get_geocode( $post_id, $cat ) )
		return false;

	$wpdb->insert( $wpdb->cp_ad_geocodes, array(
		'post_id' => $post_id,
		'category' => $cat,
		'lat' => $lat,
		'lng' => $lng
	) );
	return true;
}


// delete geo location from db
function cp_delete_geocode( $post_id, $cat = '' ) {
	global $wpdb;

	if ( $cat )
		return $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->cp_ad_geocodes WHERE post_id = %d AND category = %s", $post_id, $cat ) );
	else
		return $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->cp_ad_geocodes WHERE post_id = %d", $post_id ) );

}


// get geo location from db
function cp_get_geocode( $post_id, $cat = '' ) {
	global $wpdb;

	if ( $cat )
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT lat, lng FROM $wpdb->cp_ad_geocodes WHERE post_id = %d AND category = %s LIMIT 1", $post_id, $cat ) );
	else
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT lat, lng FROM $wpdb->cp_ad_geocodes WHERE post_id = %d LIMIT 1", $post_id ) );

	if ( is_object( $row ) )
		return array( 'lat' => $row->lat, 'lng' => $row->lng );
	else
		return false;
}


// retrieve geo location from google and update db
function cp_do_update_geocode( $meta_id, $post_id, $meta_key, $meta_value ) {
	global $wpdb, $cp_options;
	if ( in_array( $meta_key, array( 'cp_city', 'cp_country', 'cp_state', 'cp_street', 'cp_zipcode' ) ) ) {
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key IN ('cp_street','cp_city','cp_state','cp_zipcode','cp_country')", $post_id ), OBJECT_K );
		$address = '';
		foreach( $result as $cur ) {
			if ( ! empty( $cur->meta_key ) )
				$address .= "{$cur->meta_value}, ";
		}
		$address = rtrim( $address, ', ' );
		if ( $address ) {
			$region = $cp_options->gmaps_region;
			$address = urlencode( $address );
			$geocode = json_decode( wp_remote_retrieve_body( wp_remote_get( "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false&region=$region" ) ) );
			if ( 'OK' == $geocode->status ) {
				$category = wp_get_post_terms($post_id, APP_TAX_CAT);
				$category_name = ( empty($category) ) ? '' : $category[0]->name;
				cp_update_geocode( $post_id, $category_name, $geocode->results[0]->geometry->location->lat, $geocode->results[0]->geometry->location->lng );
			}
		}
	}

}
add_action( 'added_post_meta', 'cp_do_update_geocode', 10, 4 );
add_action( 'updated_post_meta', 'cp_do_update_geocode', 10, 4 );


// collect and cache featured images for displayed posts
function cp_collect_featured_images() {
	global $wpdb, $posts, $pageposts, $wp_query, $images_data;

  if(isset($posts) && is_array($posts))
    foreach($posts as $post)
      $post_ids[] = $post->ID;

  if(isset($pageposts) && is_array($pageposts))
    foreach($pageposts as $post)
      $post_ids[] = $post->ID;

  if(isset($wp_query->posts) && is_array($wp_query->posts))
    foreach($wp_query->posts as $post)
      $post_ids[] = $post->ID;

  if(isset($post_ids) && is_array($post_ids)){
    $post_ids = array_unique($post_ids);
    $post_list = implode(",",$post_ids);
    $images = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_parent IN ($post_list) AND (post_mime_type LIKE 'image/%') AND post_type = 'attachment' AND (post_status = 'inherit') ORDER BY ID ASC" );
  }

  if(isset($images) && is_array($images)){
    foreach($images as $image)
      if(!isset($images_data[$image->post_parent]))
        $images_data[$image->post_parent] = $image->ID;
    // create cache for images
    update_post_caches($images, 'post', false, true);
  }

  if(isset($post_ids) && is_array($post_ids)){
    foreach($post_ids as $post_id){
      if(!isset($images_data[$post_id]))
        $images_data[$post_id] = 0;
    }
  }

}


// get the featured image id for a post
function cp_get_featured_image_id($post_id) {
	global $wpdb, $images_data;

  if(isset($images_data[$post_id])) {
    $image_id = $images_data[$post_id];
  } else {
		$images = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID') );
    if($images){
      $image = array_shift( $images );
      $image_id = $image->ID;
    }
  }

  if(!isset($image_id) || !is_numeric($image_id))
    $image_id = 0;

  return $image_id;
}


// creates edit ad link, use only in loop
function cp_edit_ad_link() {
  global $post, $current_user, $cp_options;
  if( is_user_logged_in() ) :
    if( current_user_can('manage_options') ) {
      edit_post_link( __( 'Edit Post', APP_TD ), '<p class="edit">', '</p>', $post->ID );
    } elseif( $cp_options->ad_edit && $post->post_author == $current_user->ID ) {
      $edit_link = add_query_arg('aid', $post->ID, CP_EDIT_URL);
      echo '<p class="edit"><a class="post-edit-link" href="'.$edit_link.'" title="'. __( 'Edit Ad', APP_TD ) .'">'. __( 'Edit Ad', APP_TD ) .'</a></p>';
    }
  endif;
}


// return content stripped from html, shortcodes and trimmed to desired length. use only in loop
function cp_get_content_preview( $charlength = 160 ) {
	global $post;

	$content = !empty($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
	$content = strip_tags( $content );
	$content = strip_shortcodes( $content );
	if ( mb_strlen( $content ) > $charlength )
		$content = mb_substr( $content, 0, $charlength ) . '...';

	return apply_filters( 'cp_get_content_preview', $content, $charlength );
}


// load all page templates, setup cache, limits db queries
function cp_load_all_page_templates() {
	$pages = get_posts( array(
		'post_type' => 'page',
		'meta_key' => '_wp_page_template',
		'posts_per_page' => -1,
	) );

}


// return localized status if available
function cp_get_status_i18n( $status ) {
	$statuses = array(
		'active' => __( 'Active', APP_TD ),
		'active_membership' => __( 'Active', APP_TD ),
		'chargeback' => __( 'Chargeback', APP_TD ),
		'completed' => __( 'Completed', APP_TD ),
		'denied' => __( 'Denied', APP_TD ),
		'draft' => __( 'Draft', APP_TD ),
		'expired' => __( 'Expired', APP_TD ),
		'failed' => __( 'Failed', APP_TD ),
		'future' => __( 'Scheduled', APP_TD ),
		'inactive' => __( 'Inactive', APP_TD ),
		'inactive_membership' => __( 'Inactive', APP_TD ),
		'pending' => __( 'Pending', APP_TD ),
		'private' => __( 'Private', APP_TD ),
		'publish' => __( 'Published', APP_TD ),
		'refunded' => __( 'Refunded', APP_TD ),
		'reversed' => __( 'Reversed', APP_TD ),
		'trash' => __( 'Trash', APP_TD ),
		'unverified' => __( 'Unverified', APP_TD ),
		'verified' => __( 'Verified', APP_TD ),
		'voided' => __( 'Voided', APP_TD )
	);

	$status = strtolower($status);

	if ( array_key_exists($status, $statuses) )
		return $statuses[$status];
	else
		return ucfirst($status);
}


// helper function to display element classes and styles
function cp_display_style( $tags ) {
	global $cp_options;

	$styles = array();

	foreach ( (array) $tags as $tag ) {
		switch ( $tag ) {
			case 'search_field_width':
				if ( ! empty( $cp_options->search_field_width ) )
					$styles[] = 'width:' . $cp_options->search_field_width . ';';
				break;
			case 'ad_single_images':
				if ( ! $cp_options->ad_images )
					$styles[] = 'no-images';
				break;
			case 'ad_images':
				$styles[] = ( $cp_options->ad_images ) ? 'post-right' : 'post-right-no-img';
				break;
			case 'ad_class':
				if ( ! empty( $cp_options->ad_right_class ) )
					$styles[] = $cp_options->ad_right_class;
				break;
			case 'dir_cols':
				$styles[] = ( $cp_options->cat_dir_cols == 2 ) ? 'twoCol' : 'Col';
				break;
			case 'featured':
				if ( is_sticky() )
					$styles[] = 'featured';
				break;
		}
	}

	$styles = apply_filters( 'cp_display_style', $styles, $tags );
	echo implode( ' ', $styles );
}


// helper function to display messages
function cp_display_message( $tag ) {
	global $cp_options;

	switch ( $tag ) {
		case 'terms_of_use':
			$message = $cp_options->ads_tou_msg;
			break;
		case 'welcome':
			$message = $cp_options->ads_welcome_msg;
			break;
		case 'ads_form_help':
			$message = $cp_options->ads_form_msg;
			break;
		case 'membership_form_help':
			$message = $cp_options->membership_form_msg;
			break;
		default:
			$message = '';
			break;
	}

	echo apply_filters( 'cp_display_message', $message, $tag );
}


/**
 * Displays website current time and timezone in footer
 * @since 3.3
 */
function cp_website_current_time() {
	global $cp_options;

	if ( ! $cp_options->display_website_time )
		return;

	$timezone = get_option('gmt_offset');
	$time = date_i18n( get_option('time_format') );
	$message = sprintf( __( 'All times are GMT %1$s. The time now is %2$s.', APP_TD ), $timezone, $time );
	$message = html( 'p', $message );
	echo html( 'div', array( 'class' => 'website-time' ), $message );
}


/**
 * Generates unique ID for ads and memberships
 * @since 3.3.1
 *
 * @param string $type
 *
 * @return string
 */
function cp_generate_id( $type = 'ad' ) {
	$id = uniqid( rand( 10, 1000 ), false );

	if ( $type == 'ad' ) {
		if ( cp_get_listing_by_ref( $id ) )
			return cp_generate_id();
	}

	return $id;
}


/**
 * Retrieves listing data by given reference ID.
 * @since 3.3.1
 *
 * @param string $reference_id An listing reference ID
 *
 * @return object|bool A listing object, boolean False otherwise
 */
function cp_get_listing_by_ref( $reference_id ) {

	if ( empty( $reference_id ) || ! is_string( $reference_id ) )
		return false;

	$reference_id = appthemes_numbers_letters_only( $reference_id );

	$listing_q = new WP_Query( array(
		'post_type' => APP_POST_TYPE,
		'post_status' => 'any',
		'meta_key' => 'cp_sys_ad_conf_id',
		'meta_value' => $reference_id,
		'posts_per_page' => 1,
		'suppress_filters' => true,
		'no_found_rows' => true,
	) );

	if ( empty( $listing_q->posts ) )
		return false;

	return $listing_q->posts[0];
}


// run the appthemes_init() action hook
appthemes_init();


