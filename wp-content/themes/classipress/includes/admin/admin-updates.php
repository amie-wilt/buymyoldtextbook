<?php
/**
 * Functions to be called in install and upgrade scripts.
 *
 * @since 3.1.0
 */
if ( !function_exists('cp_upgrade_all') ) :
	function cp_upgrade_all() {
		global $app_abbr, $app_version, $app_db_version;

		$current_db_version = get_option($app_abbr.'_db_version');

		if ( $current_db_version < 1280 )
			cp_update_advanced_search_db();

		if ( $current_db_version < 1290 )
			cp_upgrade_317();

		if ( $current_db_version < 1320 )
			cp_upgrade_320();

		if ( $current_db_version < 1960 )
			cp_upgrade_330();

		update_option($app_abbr.'_db_version', $app_db_version);
		//update_option($app_abbr.'_version', $app_version);

	}
endif;
add_action('appthemes_first_run', 'cp_upgrade_all');


/**
 * These are functions to run for ClassiPress 3.0.5.
 * Display nag if instance needs to be upgraded
 *
 * @since 3.0.5
 */
function cp_upgrade_305_nag() {
	global $wpdb, $app_abbr, $the_cats;

	if ( !current_user_can('manage_options') )
		return;

	if ( get_option($app_abbr.'_upgrade_305') != 'done' ) {
		// quick test to see if it's a new install (hack job)
		$wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 10" );
		if ( $wpdb->num_rows < 10 ) {
			update_option($app_abbr.'_upgrade_305', 'done');
			return;
		}

		// see if ads have already been migrated over to new custom post type
		$wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = '".APP_POST_TYPE."' LIMIT 10" );
		// upgrade already done
		if ( $wpdb->num_rows > 1 ) {
			update_option($app_abbr.'_upgrade_305', 'done');
			return;
		}

		// get all the blog categories in a comma delimited string
		$incats = cp_get_blog_cat_ids();

		$sql = "SELECT count(ID)
			FROM $wpdb->posts wposts
			LEFT JOIN $wpdb->term_relationships ON (wposts.ID = $wpdb->term_relationships.object_id)
			LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			WHERE $wpdb->term_taxonomy.taxonomy = 'category'
			AND $wpdb->term_taxonomy.term_id NOT IN($incats)
			AND post_type = 'post'";

		$ads_to_migrate = $wpdb->get_var( $sql );

		// get the blog cats and nice names
		$blog_cats = get_categories("hide_empty=0&include=$incats");
		foreach ( $blog_cats as $blog_cat ) {
			$the_cats .= $blog_cat->name . ', ';
		}

		$the_cats = trim( $the_cats, ', ' );

		// get the total count of ad categories
		$ad_cats = get_categories("hide_empty=0&exclude=$incats");
		$cats_to_migrate = count( $ad_cats );

		// get the total count of tags
		$all_tags = get_tags();
		$tags_to_migrate = count( $all_tags );
	?>

		<div id="message2" class="updated">

			<h3><?php _e( 'ClassiPress upgrade required', APP_TD ); ?></h3>
			<p>Your database needs to be updated before using this version of ClassiPress. It's important to first back-up your database <strong>BEFORE</strong> running this upgrade tool. We are not responsible for any lost or corrupt data. We recommend using the <a href='http://wordpress.org/extend/plugins/wp-db-backup/' target='_blank'>WP-DB plugin</a> to easily back-up your database. To install it directly from within WordPress, just go to your '<a href='plugin-install.php'>Add New</a>' plugins page and search for 'WP-DB-Backup'. For more instructions, see <a href='http://codex.wordpress.org/Backing_Up_Your_Database#Installation' target='_blank'>this page</a>.</p>

			<h3>What will this upgrade do?</h3>
			<p>As of version 3.0.5, ClassiPress takes advantage of the new custom post types and taxonomies available in WordPress so we need to migrate your ads, ad categories, and copy your tags from 'posts' to 'ads'. See the new 'Ads' menu group in your left-hand sidebar? Yep, that's where we're going to move them to.</p>
			<p>This script will take any ads NOT assigned to your blog categories (and blog sub-categories) which in your case are: <strong style="color:#009900;"><?php echo $the_cats ?></strong> and move them over. If this does not look correct or you wish to move ads out of these categories, please do so before running this script. These blog categories are determined by your "Blog Category ID" option on your settings page.</p>

			<p>This script will attempt to move your <strong style="color:#009900;"><?php echo number_format($ads_to_migrate); ?> ads, <?php echo number_format($cats_to_migrate); ?> ad categories, and <?php echo number_format($tags_to_migrate); ?> tags</strong> under the new 'Ads' menu group. <strong>NOTE</strong>: Only tags assigned to an ad will be moved over so less than the total tags found (<strong><?php echo number_format($tags_to_migrate); ?> tags</strong>) will likely be moved.</p>
			<p><strong>IMPORTANT:</strong> Once you click the update button below, there's no going back. Chances of anything going wrong are slim, and since you've already backed up your database, there's nothing to be worried about. :-) This may take a while depending on how many ads you have. Please only click the button once.</p>

			<form action="admin.php?page=app-system-info&tab=cp_tools" id="msgForm" method="post">
				<p class="submit btop">
					<input class="button-secondary" type="submit" value="Migrate My Ads" name="cp_tools[rerun_migration]" onclick="return confirmUpdate();" />
				</p>
			</form>

			<p><small><?php _e( 'Note: This message will not disappear until you have upgraded your database.', APP_TD ); ?></small></p>

		</div>

		<script type="text/javascript">
		/* <![CDATA[ */
			function confirmUpdate() { return confirm("Are you sure you wish to run the ClassiPress upgrade script? Promise you already backed up your database? It's better to be safe than sorry! :-)"); }
		/* ]]> */
		</script>
<?php
	}
}
add_action('admin_notices', 'cp_upgrade_305_nag', 3);


/**
 * ClassiPress 3.0.5 upgrade script
 * Convert post types to ad_listing
 *
 * @since 3.0.5
 */
function cp_convert_posts2Ads() {
	global $wpdb, $app_version;

	echo '<div id="message2" class="updated" style="padding:10px 20px;">';

	// setup post conversion and stop if there are no valid ad listings in the posts table to convert
	$blogCatIDs = array();
	$blogCatIDs = cp_get_blog_cat_ids_array();

	// get all posts not in blog cats for quick check
	$args = array('category__not_in' => $blogCatIDs, 'post_status' => 'any', 'numberposts' => 10);
	$theposts = get_posts($args);

	if ( count($theposts) < 1 )
		wp_die('<h3>Migration script error</h3><p>Process did not run. No ad listings were found. You only have blog posts or your blog parent category ID is incorrect.</p>');

	// convert all the NON-BLOG categories to be part of the new ad_cat taxonomy
	echo '<p>Converting ad categories.........</p>';


	// get all category ids
	$cat_ids = get_all_category_ids();

	$cat_count_total = count($cat_ids);

	echo '<ul>';

	$cat_count = 0;

	foreach ( $cat_ids as $cat_id ) {

		// only move categories not belonging to the blog cats or blog sub cats
		if ( !in_array($cat_id, $blogCatIDs) ) {
			$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => APP_TAX_CAT ), array( 'term_id' => $cat_id ) );
			$thisCat = get_category($cat_id);
			echo '<li style="color:#009900"><strong>' . $thisCat->name . '</strong> (ID:' . $cat_id . ')' . ' category has been moved</li>';
			$cat_count ++;
		} else {
			$thisCat = get_category($cat_id);
			echo '<li><strong>' . $thisCat->name . '</strong> (ID:' . $cat_id . ')' . ' category has been skipped</li>';
		}

	}

	echo '</ul>';


	//convert all the NON-BLOG posts to be part of the new "ad_listing" taxonomy
	echo '<br /><p><strong>Converting posts........</strong></p>';

	$newTagsSummary = array();
	$post_count = 0;
	$ad_count = 0;
	$tag_count = 0;

	echo '<ul>';


	// get all the posts
	$args = array('post_type' => 'post', 'post_status' => 'any', 'numberposts' => 250);
	$theposts = get_posts($args);

	foreach ( $theposts as $post ) {

		setup_postdata($post);

		// get the post terms
		$oldTags = wp_get_post_terms($post->ID);
		$newTags = array();

		// get the cat object array for the post
		$post_cats = get_the_category($post->ID);

		// grab the first cat id found
		$cat_id = $post_cats[0]->cat_ID;

		//check if the post is in a blog category
		if ( !in_array($cat_id, $blogCatIDs) ) {

			// if yes, then first see if it has any tags
			if ( !empty($oldTags) ) {
				foreach($oldTags as $thetag) :
					$newTags[] = $thetag->name;
					$newTagsSummary[] = '<li style="color:#009900"><strong>"' . $thetag->name . '"</strong> tag has been copied</li>';
					$tag_count++;
				endforeach;
			}

			// copy the tag array over if it's not empty
			if ( !empty($newTags) )
				wp_set_post_terms($post->ID, $newTags, APP_TAX_TAG);

			//now change the post to an ad
			set_post_type($post->ID, APP_POST_TYPE);
			echo '<li style="color:#009900"><strong>"' . $post->post_title . '"</strong> (ID:' . $post->ID . ') post was converted</li>';
			$ad_count++;

		// not an ad so must be a blog post
		} else {

			// see if it has tags since we still want to echo them not moved
			if ( !empty($oldTags) ) {
			foreach($oldTags as $thetag) {
				$newTags[] = $thetag->name;
				$newTagsSummary[] = '<li><strong>"' . $thetag->name . '"</strong> tag has been skipped</li>';
				//$tag_count++;
				}
			}

			echo '<li><strong>"<a href="post.php?post='.$post->ID.'&action=edit" target="_blank">' . $post->post_title . '</a>"</strong> (ID:' . $post->ID . ') post has been skipped (in blog or blog-sub category)</li>';
		}

		$post_count++;

	}


	echo '<br/><p><strong>Copying tags...........</strong></p>';

	// get the total count of tags
	$all_tags = get_tags();
	$tags_count_total = count($all_tags);


	// calculate the results
	$blog_cats_total = $cat_count_total - $cat_count;
	$blog_posts_total = $post_count - $ad_count;
	$blog_tags_total = $tags_count_total - $tag_count;

	// print out all the tags
	foreach($newTagsSummary as $key => $value)
		echo $value;

	echo '</ul><br/>';

	echo '<h3>Migration Summary</h3>';
	echo '<p>Total categories converted: <strong>' . $cat_count . '/'.$cat_count_total.'</strong>  <small>(excluded '.$blog_cats_total.' blog categories)</small><br/>';
	echo 'Total posts converted: <strong>' . $ad_count . '/'.$post_count.'</strong>  <small>(excluded '.$blog_posts_total.' blog posts)</small><br/>';
	echo 'Total tags copied: <strong>' . $tag_count . '/'.$tags_count_total.'</strong>  <small>(excluded '.$blog_tags_total.' tags not assigned to ads)</small><br/>';

	// get all posts not in blog cats for quick check
	$args = array('category__not_in' => $blogCatIDs, 'post_status' => 'any', 'numberposts' => 10);
	$theposts = get_posts($args);

	if ( count($theposts) < 1 )
		echo '<br/><p><strong style="color:#009900;">The ads conversion utility has completed!</strong></p>';
	else
		echo '<br/><p><strong style="color:#FF3300;">The ads conversion did not completed yet! Please run migration script again!</strong></p>';

	//reset the old version to current so this script doesn't appear again
	// update_option('cp_version_old', $app_version);
?>

	<form action="admin.php?page=app-system-info&tab=cp_tools" id="msgForm" method="post">
		<p class="submit btop">
			<input class="button-secondary" type="submit" value="Run Migration Script Again?" name="cp_tools[rerun_migration]" />
		</p>
	</form>

	<p><strong>IMPORTANT: </strong>If you navigate away from this page, you will no longer be able to access this script. If you wish to run it again, open another browser tab and make your changes there first. Then come back and push the above button and the script will re-run.</p>

	<?php

	echo '</div>';
}


/**
 * Execute changes made in ClassiPress 3.1.0.
 * Geocoding migration script.
 *
 * @since 3.1.0
 */
function cp_update_advanced_search_db() {
	global $wpdb, $app_abbr;

	$output = '';
	$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s ORDER BY ID ASC", APP_POST_TYPE ) );
	if ( $post_ids ) {
		echo scb_admin_notice( __( 'Geocoding ad listing addresses to make the advanced search radius feature work. This process queries Google Maps to get longitude and latitude coordinates based on each ad listings address. Please be patient as this may take a few minutes to complete.', APP_TD ) );

		foreach ( $post_ids as $post_id ) {
			if ( !cp_get_geocode( $post_id ) ) {
				$result = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key IN ('cp_street','cp_city','cp_state','cp_zipcode','cp_country')", $post_id ), OBJECT_K );
				$address = '';
				foreach( $result as $cur ) {
					if ( !empty( $cur->meta_key ) )
						$address .= "{$cur->meta_value}, ";
				}
				$address = rtrim( $address, ', ' );
				if ( $address ) {
					$output .= sprintf( '<p>' . __( "Ad #%d - %s ", APP_TD ), $post_id, $address );
					$geocode = json_decode( wp_remote_retrieve_body( wp_remote_get( 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . '&sensor=false' ) ) );
					if ( 'OK' == $geocode->status ) {
						$output .= esc_html( "({$geocode->results[0]->geometry->location->lat}, {$geocode->results[0]->geometry->location->lng})" );
						$category = wp_get_post_terms($post_id, APP_TAX_CAT);
						$category_name = ( empty($category) ) ? '' : $category[0]->name;
						cp_update_geocode( $post_id, $category_name, $geocode->results[0]->geometry->location->lat, $geocode->results[0]->geometry->location->lng );
						$output .= ' &raquo; <font color="green">' . __( 'Geocoding complete.', APP_TD ) . '</font>';
					} else {
						$output .= ' &raquo; <font color="red">' . __( 'Geocoding failed - address not found.', APP_TD ) . '</font>';
					}
					$output .= '</p>';
				}
			}
		}

		$output .= '<br /><strong>' . __(' Geocoding table updated.', APP_TD ) . '</strong><br />';
		$output .= '<small>' . __( 'Please note: Ads that failed during this process will not show up during a radius search since the address was invalid.', APP_TD ) . '</small>';

		update_option($app_abbr.'_db_version', '1280');
		echo scb_admin_notice($output);
	} // end if $post_ids

	update_option($app_abbr.'_db_version', '1280');

}


/**
 * Execute changes made in ClassiPress 3.1.7.
 * Convert checkbox fields
 *
 * @since 3.1.7
 */
function cp_upgrade_317() {
	global $wpdb, $app_abbr, $app_version;

	$sql = "SELECT field_name FROM $wpdb->cp_ad_fields WHERE field_type = 'checkbox' ";
	$results = $wpdb->get_results( $sql );

	if ( $results ) {
		foreach ( $results as $result ) {
			$sql_meta = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s GROUP BY post_id", $result->field_name );
			$results_meta = $wpdb->get_results( $sql_meta );

			if ( $results_meta ) {
				foreach( $results_meta as $meta ) {
					$post_meta = get_post_meta($meta->post_id, $result->field_name, true);
					if ( !empty($post_meta) ) {
						delete_post_meta($meta->post_id, $result->field_name);
						delete_post_meta($meta->post_id, $result->field_name.'_list');
						$post_meta_vals = explode(",", $post_meta);
						if ( is_array($post_meta_vals) )
							foreach( $post_meta_vals as $checkbox_value )
								add_post_meta($meta->post_id, $result->field_name, $checkbox_value );
					}
				}
			}
		}
	}

	update_option($app_abbr.'_db_version', '1290');

}


/**
 * Execute changes made in ClassiPress 3.2.
 *
 * @since 3.2
 */
function cp_upgrade_320() {
	global $wpdb, $app_abbr;

	if ( get_option($app_abbr.'_admin_security') == 'install_themes' )
		update_option($app_abbr.'_admin_security', 'manage_options');

	// remove old table indexes
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	drop_index($wpdb->cp_ad_pop_daily, 'id');
	drop_index($wpdb->cp_ad_pop_total, 'id');

	update_option($app_abbr.'_db_version', '1320');

}


/**
 * Convert and remove legacy meta used in ClassiPress 2.9.3 and earlier.
 *
 * @since 3.3
 */
function cp_upgrade_legacy_meta_293() {
	global $wpdb, $app_abbr;

	// legacy meta
	$meta_keys = array(
		'expires' => 'cp_sys_expire_date',
		'price' => 'cp_price',
		'phone' => 'cp_phone',
		'location' => 'cp_city',
		'cp_totalcost' => 'cp_sys_total_ad_cost',
		'cp_adURL' => 'cp_url',
		'email' => '',
		'name' => '',
	);
	$meta_query = "SELECT * FROM $wpdb->postmeta WHERE meta_key IN ('" . implode( "', '", array_keys( $meta_keys ) ) . "') ";
	$legacy_meta = $wpdb->get_results( $meta_query );

	if ( $legacy_meta ) {
		foreach ( $legacy_meta as $postmeta ) {

			// convert anonymous posters to users
			if ( $postmeta->meta_key == 'email' ) {
				$user = get_user_by( 'email', $postmeta->meta_value );
				$post = get_post( $postmeta->post_id );
				if ( $post ) {
					if ( ! $user ) {
						$user_login = ( get_post_meta( $postmeta->post_id, 'name', true ) ) ? sanitize_user( get_post_meta( $postmeta->post_id, 'name', true ) . '-' . rand( 10, 1000 ), true ) : sanitize_title( $postmeta->meta_value );
						if ( ! username_exists( $user_login ) ) {
							$user_id = wp_create_user( $user_login, wp_generate_password(), $postmeta->meta_value );
							if ( $user_id && is_integer( $user_id ) )
								$user = get_user_by( 'id', $user_id );
						}
					}
					if ( $user && $user->ID != $post->post_author )
						wp_update_post( array( 'ID' => $post->ID, 'post_author' => $user->ID ) );
				}
			}

			// convert and remove legacy meta
			foreach ( $meta_keys as $old_meta_key => $new_meta_key ) {
				if ( $postmeta->meta_key != $old_meta_key )
					continue;

				// remove legacy meta if no replacement
				if ( empty( $new_meta_key ) )
					delete_post_meta( $postmeta->post_id, $old_meta_key );

				$new_meta_value = get_post_meta( $postmeta->post_id, $new_meta_key, true );
				if ( ! empty( $new_meta_value ) ) {
					delete_post_meta( $postmeta->post_id, $old_meta_key );
				} else {
					$old_meta_value = get_post_meta( $postmeta->post_id, $old_meta_key, true );
					update_post_meta( $postmeta->post_id, $new_meta_key, $old_meta_value );
					delete_post_meta( $postmeta->post_id, $old_meta_key );
				}

			}
		}
	}

}


/**
 * Convert coupons to format of AppThemes Coupons plugin.
 *
 * @since 3.3
 */
function cp_upgrade_coupons_330() {
	global $wpdb;

	// legacy coupons
	$legacy_coupons = $wpdb->get_results( "SELECT * FROM $wpdb->cp_coupons " );

	if ( ! $legacy_coupons )
		return;

	foreach ( $legacy_coupons as $coupon ) {
		// create new post for coupon
		$new_coupon_post = array(
			'post_title' => $coupon->coupon_code,
			'post_status' => ( ( $coupon->coupon_status == 'active' ) ? 'publish' : 'draft' ),
			'post_type' => 'discount_coupon',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
		);

		$new_coupon_id = wp_insert_post( $new_coupon_post );

		if ( ! $new_coupon_id )
			continue;

		// add meta fields for coupon
		$new_coupon_postmeta = array(
			'code' => $coupon->coupon_code,
			'amount' => $coupon->coupon_discount,
			'type' => ( ( $coupon->coupon_discount_type == '%' ) ? 'percent' : 'flat' ),
			'start_date' => date( 'm/d/Y', strtotime( $coupon->coupon_start_date ) ),
			'end_date' => date( 'm/d/Y', strtotime( $coupon->coupon_expire_date ) ),
			'use_limit' => $coupon->coupon_max_use_count,
			'user_use_limit' => $coupon->coupon_max_use_count,
			'use_count' => $coupon->coupon_use_count,
		);

		foreach ( $new_coupon_postmeta as $meta_key => $meta_value ) {
			add_post_meta( $new_coupon_id, $meta_key, $meta_value, true );
		}

		// remove legacy entry
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->cp_coupons WHERE coupon_id = %d", $coupon->coupon_id ) );
	}

}


/**
 * Convert transactions to format of new AppThemes Payments.
 *
 * @since 3.3
 */
function cp_upgrade_transactions_330() {
	global $wpdb;

	if ( ! current_theme_supports( 'app-payments' ) )
		return;

	// legacy orders
	$legacy_orders = $wpdb->get_results( "SELECT * FROM $wpdb->cp_order_info " );

	if ( ! $legacy_orders )
		return;

	foreach ( $legacy_orders as $legacy_order ) {
		// create new post for order
		$new_order_post = array(
			'post_title' => __( 'Transaction', APP_TD ),
			'post_content' => __( 'Transaction Data', APP_TD ),
			'post_status' => ( ( $legacy_order->payment_status == 'Completed' ) ? APPTHEMES_ORDER_ACTIVATED : APPTHEMES_ORDER_PENDING ),
			'post_type' => APPTHEMES_ORDER_PTYPE,
			'post_date' => date( 'Y-m-d H:i:s', strtotime( $legacy_order->payment_date ) ),
			'post_author' => ( ( $legacy_order->user_id ) ? $legacy_order->user_id : 1 ),
		);

		$new_order_id = wp_insert_post( $new_order_post );

		if ( ! $new_order_id )
			continue;

		// set correct slug
		wp_update_post( array( 'ID' => $new_order_id, 'post_name' => $new_order_id ) );

		$price = ( empty( $legacy_order->mc_gross ) || ! is_numeric( $legacy_order->mc_gross ) ) ? 0 : $legacy_order->mc_gross;
		// add meta fields for order
		$new_order_postmeta = array(
			'currency' => $legacy_order->mc_currency,
			'total_price' => $price,
			'gateway' => ( ( $legacy_order->payment_type == 'banktransfer' ) ? 'bank-transfer' : 'paypal' ),
			'transaction_id' => $legacy_order->txn_id,
			'bt-sentemail' => '1',
			'ip_address' => appthemes_get_ip(),
			'first_name' => $legacy_order->first_name,
			'last_name' => $legacy_order->last_name,
			'street' => $legacy_order->street,
			'city' => $legacy_order->city,
			'state' => $legacy_order->state,
			'postcode' => $legacy_order->zipcode,
			'country' => $legacy_order->residence_country,
		);

		foreach ( $new_order_postmeta as $meta_key => $meta_value ) {
			add_post_meta( $new_order_id, $meta_key, $meta_value, true );
		}

		$order = appthemes_get_order( $new_order_id );
		if ( ! $order )
			continue;

		if ( ! empty( $legacy_order->ad_id ) && $legacy_order->ad_id > 0 ) {
			$order->add_item( CP_ITEM_LISTING, $price, $legacy_order->ad_id );
		} else {
			$order->add_item( CP_ITEM_MEMBERSHIP, $price );
		}

		// remove legacy entry
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->cp_order_info WHERE id = %d", $legacy_order->id ) );
	}

}


/**
 * Convert old settings to scbOptions format.
 *
 * @since 3.3
 */
function cp_upgrade_settings_330() {
	global $wpdb, $cp_options;

	$new_options = array();
	$options_to_delete = array();

	// fields to convert from select 'yes/no' to checkbox
	$select_fields = array(
		'allow_registration_password',
		'use_logo',
		'search_ex_pages',
		'search_ex_blog',
		'search_custom_fields',
		'ad_edit',
		'allow_relist',
		'ad_inquiry_form',
		'allow_html',
		'ad_stats_all',
		'ad_gravatar_thumb',
		'post_prune',
		'ad_images',
		'ad_image_preview',
		'captcha_enable',
		'adcode_468x60_enable',
		'adcode_336x280_enable',
		'disable_stylesheet',
		'debug_mode',
		'google_jquery',
		'disable_wp_login',
		'remove_wp_generator',
		'remove_admin_bar',
		'display_website_time',
		'cufon_enable',
		'new_ad_email',
		'prune_ads_email',
		'new_ad_email_owner',
		'expired_ad_email_owner',
		'nu_admin_email',
		'membership_activated_email_owner',
		'membership_ending_reminder_email',
		'nu_custom_email',
		'charge_ads',
		'enable_featured',
		'clean_price_field',
		'force_zeroprice',
		'hide_decimals',
		'enable_membership_packs',
	);

	// fields to translate
	$fields_to_translate = array(
		'cp_curr_pay_type' => 'currency_code',
		'cp_curr_symbol_pos' => 'currency_position',
	);

	// legacy settings
	$legacy_options = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'cp_%'" );

	if ( ! $legacy_options )
		return;

	foreach ( $legacy_options as $option ) {
		$new_option_name = substr( $option->option_name, 3 );

		// grab price per category options into an array
		$is_cat_price = appthemes_str_starts_with( $new_option_name, 'cat_price_' );
		if ( $is_cat_price ) {
			$cat_id = substr( $new_option_name, 10 );
			$new_options['price_per_cat'][ $cat_id ] = $option->option_value;
			$options_to_delete[] = $option->option_name;
			continue;
		}

		// translate old payment settings to new one
		if ( array_key_exists( $option->option_name, $fields_to_translate ) ) {
			$new_options[ $fields_to_translate[ $option->option_name ] ] = $option->option_value;
			$options_to_delete[] = $option->option_name;
			continue;
		}

		// skip not used options and membership entries
		if ( is_null( $cp_options->$new_option_name ) || $new_option_name == 'options' )
			continue;

		// convert select 'yes/no' to checkbox
		if ( in_array( $new_option_name, $select_fields ) )
			$option->option_value = ( $option->option_value == 'yes' ) ? 1 : 0;

		$new_options[ $new_option_name ] = maybe_unserialize( $option->option_value );
		$options_to_delete[] = $option->option_name;
	}

	// migrate payment gateways settings
	$gateways = array(
		'enabled' => array(
			'paypal' => ( get_option('cp_enable_paypal') == 'yes' ) ? 1 : 0,
			'bank-transfer' => ( get_option('cp_enable_bank') == 'yes' ) ? 1 : 0,
		),
		'paypal' => array(
			'email_address' => get_option('cp_paypal_email'),
			'ipn_enabled' => ( get_option('cp_enable_paypal_ipn') == 'yes' ) ? 1 : 0,
			'sandbox_enabled' => ( get_option('cp_paypal_sandbox') ) ? 1 : 0,
		),
		'bank-transfer' => array(
			'message' => get_option('cp_bank_instructions'),
		),
	);
	$new_options['gateways'] = $gateways;
	$options_to_delete = array_merge( $options_to_delete, array( 'cp_enable_paypal', 'cp_enable_bank', 'cp_paypal_email', 'cp_enable_paypal_ipn', 'cp_paypal_sandbox', 'cp_bank_instructions' ) );

	// enable selectbox js for those, which updating
	$new_options['selectbox'] = 1;
	// save new options
	$new_options = array_merge( get_option( 'cp_options', array() ), $new_options );
	update_option( 'cp_options', $new_options );

	// delete old options
	foreach ( $options_to_delete as $option_name ) {
		delete_option( $option_name );
	}
}


/**
 * Execute changes made in ClassiPress 3.3.
 *
 * @since 3.3
 */
function cp_upgrade_330() {
	global $wpdb, $app_abbr;

	// convert and remove legacy meta
	cp_upgrade_legacy_meta_293();

	// convert all expire dates to format 'Y-m-d H:i:s'
	$args = array(
		'post_type' => APP_POST_TYPE,
		'post_status' => 'any',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'meta_query' => array(
			array(
				'key' => 'cp_sys_expire_date',
				'value' => '',
				'compare' => '!=',
			),
		),
	);
	$legacy = new WP_Query( $args );
	if ( isset( $legacy->posts ) && is_array( $legacy->posts ) ) {
		foreach ( $legacy->posts as $post_id ) {
			$expire_time = strtotime( get_post_meta( $post_id, 'cp_sys_expire_date', true ) );
			$expire_date = date( 'Y-m-d H:i:s', $expire_time );
			update_post_meta( $post_id, 'cp_sys_expire_date', $expire_date );
		}
	}

	// change default for search field width option
	if ( get_option( 'cp_search_field_width' ) == '450px' )
		update_option( 'cp_search_field_width', '' );

	// convert coupons to format of AppThemes Coupons plugin
	cp_upgrade_coupons_330();

	// convert transactions to format of new AppThemes Payments
	cp_upgrade_transactions_330();

	// convert old settings to scbOptions format
	cp_upgrade_settings_330();

	// set blog and ads pages
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', CP_Ads_Home::get_id() );
	update_option( 'page_for_posts', CP_Blog_Archive::get_id() );

	// remove old blog page
	$args = array(
		'post_type' => 'page',
		'meta_key' => '_wp_page_template',
		'meta_value' => 'tpl-blog.php',
		'posts_per_page' => 1,
		'suppress_filters' => true,
	);
	$blog_page = new WP_Query( $args );

	if ( ! empty( $blog_page->posts ) )
		wp_delete_post( $blog_page->posts[0]->ID, true );

	update_option($app_abbr.'_db_version', '1960');
}



