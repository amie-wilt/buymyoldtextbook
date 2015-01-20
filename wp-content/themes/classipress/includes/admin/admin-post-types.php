<?php
/**
 * Custom post types and taxonomies
 *
 *
 * @version 3.0.5
 * @author AppThemes
 * @package ClassiPress
 *
 */



// register the custom post type and category taxonomy for ad listings
function cp_ad_listing_post_type() {
	global $cp_options;

	// get the slug value for the ad custom post type & taxonomies
	$post_type_base_url = $cp_options->post_type_permalink;
	$cat_tax_base_url = $cp_options->ad_cat_tax_permalink;
	$tag_tax_base_url = $cp_options->ad_tag_tax_permalink;

	// register the new post type
	register_post_type( APP_POST_TYPE, array(
		'labels' => array(
			'name' => __( 'Ads', APP_TD ),
			'singular_name' => __( 'Ad', APP_TD ),
			'add_new' => __( 'Add New', APP_TD ),
			'add_new_item' => __( 'Create New Ad', APP_TD ),
			'edit' => __( 'Edit', APP_TD ),
			'edit_item' => __( 'Edit Ad', APP_TD ),
			'new_item' => __( 'New Ad', APP_TD ),
			'view' => __( 'View Ads', APP_TD ),
			'view_item' => __( 'View Ad', APP_TD ),
			'search_items' => __( 'Search Ads', APP_TD ),
			'not_found' => __( 'No ads found', APP_TD ),
			'not_found_in_trash' => __( 'No ads found in trash', APP_TD ),
			'parent' => __( 'Parent Ad', APP_TD ),
		),
		'description' => __( 'This is where you can create new classified ads on your site.', APP_TD ),
		'public' => true,
		'show_ui' => true,
		'has_archive' => true,
		'capability_type' => 'post',
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'menu_position' => 8,
		'menu_icon' => FAVICON,
		'hierarchical' => false,
		'rewrite' => array( 'slug' => $post_type_base_url, 'with_front' => false, 'feeds' => true ),
		'query_var' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky' ),
	) );

	// register the new ad category taxonomy
	register_taxonomy( APP_TAX_CAT, array( APP_POST_TYPE ), array(
		'labels' => array(
			'name' => __( 'Ad Categories', APP_TD ),
			'singular_name' => __( 'Ad Category', APP_TD ),
			'search_items' =>  __( 'Search Ad Categories', APP_TD ),
			'all_items' => __( 'All Ad Categories', APP_TD ),
			'parent_item' => __( 'Parent Ad Category', APP_TD ),
			'parent_item_colon' => __( 'Parent Ad Category:', APP_TD ),
			'edit_item' => __( 'Edit Ad Category', APP_TD ),
			'update_item' => __( 'Update Ad Category', APP_TD ),
			'add_new_item' => __( 'Add New Ad Category', APP_TD ),
			'new_item_name' => __( 'New Ad Category Name', APP_TD ),
		),
		'hierarchical' => true,
		'show_ui' => true,
		'query_var' => true,
		'update_count_callback' => '_update_post_term_count',
		'rewrite' => array( 'slug' => $cat_tax_base_url, 'with_front' => false, 'hierarchical' => true ),
	) );

	// register the new ad tag taxonomy
	register_taxonomy( APP_TAX_TAG, array( APP_POST_TYPE ), array(
		'labels' => array(
			'name' => __( 'Ad Tags', APP_TD ),
			'singular_name' => __( 'Ad Tag', APP_TD ),
			'search_items' =>  __( 'Search Ad Tags', APP_TD ),
			'all_items' => __( 'All Ad Tags', APP_TD ),
			'parent_item' => __( 'Parent Ad Tag', APP_TD ),
			'parent_item_colon' => __( 'Parent Ad Tag:', APP_TD ),
			'edit_item' => __( 'Edit Ad Tag', APP_TD ),
			'update_item' => __( 'Update Ad Tag', APP_TD ),
			'add_new_item' => __( 'Add New Ad Tag', APP_TD ),
			'new_item_name' => __( 'New Ad Tag Name', APP_TD ),
		),
		'hierarchical' => false,
		'show_ui' => true,
		'query_var' => true,
		'update_count_callback' => '_update_post_term_count',
		'rewrite' => array( 'slug' => $tag_tax_base_url, 'with_front' => false ),
	) );


}
add_action( 'init', 'cp_ad_listing_post_type', 0 );


// add the custom edit ads page columns
function cp_edit_ad_columns( $columns ) {
	$columns = array(
		'cb' => "<input type=\"checkbox\" />",
		'title' => __( 'Title', APP_TD ),
		'author' => __( 'Author', APP_TD ),
		APP_TAX_CAT => __( 'Category', APP_TD ),
		APP_TAX_TAG => __( 'Tags', APP_TD ),
		'cp_price' => __( 'Price', APP_TD ),
		'cp_daily_count' => __( 'Views Today', APP_TD ),
		'cp_total_count' => __( 'Views Total', APP_TD ),
		'cp_sys_expire_date' => __( 'Expires', APP_TD ),
		'comments' => '<div class="vers"><img src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>',
		'date' => __( 'Date', APP_TD ),
	);

	return $columns;
}
add_filter( 'manage_' . APP_POST_TYPE . '_posts_columns', 'cp_edit_ad_columns' );


// register the columns as sortable
function cp_ad_column_sortable( $columns ) {
	$columns['cp_price'] = 'cp_price';
	$columns['cp_daily_count'] = 'cp_daily_count';
	$columns['cp_total_count'] = 'cp_total_count';
	$columns['cp_sys_expire_date'] = 'cp_sys_expire_date';

	return $columns;
}
add_filter( 'manage_edit-' . APP_POST_TYPE . '_sortable_columns', 'cp_ad_column_sortable' );


// how the custom columns should sort
function cp_ad_column_orderby( $vars ) {

	if ( isset( $vars['orderby'] ) && 'cp_price' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'cp_price',
			'orderby' => 'meta_value_num',
		) );
	}

	if ( isset( $vars['orderby'] ) && 'cp_daily_count' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'cp_daily_count',
			'orderby' => 'meta_value_num',
		) );
	}

	if ( isset( $vars['orderby'] ) && 'cp_total_count' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'cp_total_count',
			'orderby' => 'meta_value_num',
		) );
	}

	return $vars;
}
add_filter( 'request', 'cp_ad_column_orderby' );


// add the custom edit ads page column values
function cp_custom_columns($column){
	global $post;
	$custom = get_post_custom();

	switch ($column) :

		case 'cp_sys_expire_date':
			if ( isset( $custom['cp_sys_expire_date'][0] ) && ! empty( $custom['cp_sys_expire_date'][0] ) )
				echo appthemes_display_date( $custom['cp_sys_expire_date'][0] );
		break;

		case 'cp_price':
			cp_get_price( $post->ID, 'cp_price' );
		break;
		
		case 'cp_daily_count':
			if ( isset($custom['cp_daily_count'][0]) && !empty($custom['cp_daily_count'][0]) )
				echo $custom['cp_daily_count'][0];
		break;
		
		case 'cp_total_count':
			if ( isset($custom['cp_total_count'][0]) && !empty($custom['cp_total_count'][0]) )
				echo $custom['cp_total_count'][0];
		break;

		case APP_TAX_TAG :
			echo get_the_term_list($post->ID, APP_TAX_TAG, '', ', ','');
		break;

		case APP_TAX_CAT :
			echo get_the_term_list($post->ID, APP_TAX_CAT, '', ', ','');
		break;

	endswitch;
}
add_action('manage_posts_custom_column',  'cp_custom_columns');


// add the custom edit ad categories page columns
function cp_edit_ad_cats_columns( $columns ) {
	$columns = array(
		'cb' => "<input type=\"checkbox\" />",
		'name' => __( 'Name', APP_TD ),
		'description' => __( 'Description', APP_TD ),
		'slug' => __( 'Slug', APP_TD ),
		'num' => __( 'Ads', APP_TD ),
	);

	return $columns;
}
// don't enable this yet. see wp-admin function _tag_row for main code
//add_filter( 'manage_' . APP_TAX_CAT . '_posts_columns', 'cp_edit_ad_cats_columns' );


// add a sticky option to the edit ad submit box
function cp_sticky_option_submit_box() {
	global $post;

	if ( $post->post_type != APP_POST_TYPE )
		return;

	if ( current_user_can( 'edit_others_posts' ) ) {
?>
		<div class="misc-pub-section misc-pub-section-last sticky-ad">
			<span id="sticky"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post->ID ) ); ?> tabindex="4" />
			<label for="sticky" class="selectit"><?php _e( 'Featured Ad (sticky)', APP_TD ); ?></label><br /></span>
		</div>
<?php
	} elseif ( is_sticky( $post->ID ) ) {
		echo html( 'input', array( 'name' => 'sticky', 'type' => 'hidden', 'value' => 'sticky' ) );
	}
}
add_action( 'post_submitbox_misc_actions', 'cp_sticky_option_submit_box' );


// hack until WP supports custom post type sticky feature
// add the sticky option to the quick edit area
function cp_sticky_option_quick_edit() {
	global $post;

	//if post is a custom post type and only during the first execution of the action quick_edit_custom_box
	if ( $post->post_type != APP_POST_TYPE || did_action( 'quick_edit_custom_box' ) !== 1 )
		return;
?>
	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<label class="alignleft">
				<input type="checkbox" name="sticky" value="sticky" />
				<span class="checkbox-title"><?php _e( 'Featured Ad (sticky)', APP_TD ); ?></span>
			</label>
		</div>
	</fieldset>
<?php
}
add_action( 'quick_edit_custom_box', 'cp_sticky_option_quick_edit' );


// custom user page columns
function cp_manage_users_columns( $columns ) {
	$newcol = array_slice( $columns, 0, 1 );
	$newcol = array_merge( $newcol, array( 'id' => __( 'Id', APP_TD ) ) );
	$columns = array_merge( $newcol, array_slice( $columns, 1 ) );

	$columns['cp_ads_count'] = __( 'Ads', APP_TD );
	$columns['last_login'] = __( 'Last Login', APP_TD );
	$columns['registered'] = __( 'Registered', APP_TD );

	return $columns;
}
add_action('manage_users_columns', 'cp_manage_users_columns');


// register the columns as sortable
function cp_users_column_sortable( $columns ) {
	$columns['id'] = 'id';
	return $columns;
}
add_filter('manage_users_sortable_columns', 'cp_users_column_sortable');


// display the coumn values for each user
function cp_manage_users_custom_column( $r, $column_name, $user_id ) {
	switch ( $column_name ) {
		case 'cp_ads_count' :
			global $cp_counts;

			if ( !isset( $cp_counts ) )
				$cp_counts = cp_count_ads();

			if ( !array_key_exists( $user_id, $cp_counts ) )
				$cp_counts = cp_count_ads();

			if ( $cp_counts[$user_id] > 0 ) {
				$r .= "<a href='edit.php?post_type=" . APP_POST_TYPE . "&author=$user_id' title='" . esc_attr__( 'View ads by this author', APP_TD ) . "' class='edit'>";
				$r .= $cp_counts[$user_id];
				$r .= '</a>';
			} else {
				$r .= 0;
			}
		break;
	
		case 'last_login' :
			$r = get_user_meta($user_id, 'last_login', true);
			if ( ! empty( $r ) )
				$r = appthemes_display_date( $r );
		break;

		case 'registered' :
			$user_info = get_userdata($user_id);
			$r = $user_info->user_registered;
			if ( ! empty( $r ) )
				$r = appthemes_display_date( $r, 'datetime', true );
		break;

		case 'id' :
			$r = $user_id;
	}

	return $r;
}
//Display the ad counts for each user
add_action( 'manage_users_custom_column', 'cp_manage_users_custom_column', 10, 3 );


// count the number of ad listings for the user
function cp_count_ads() {
	global $wpdb, $wp_list_table;

	$users = array_keys( $wp_list_table->items );
	$userlist = implode( ',', $users );
	$result = $wpdb->get_results( "SELECT post_author, COUNT(*) FROM $wpdb->posts WHERE post_type = '" . APP_POST_TYPE . "' AND post_author IN ($userlist) GROUP BY post_author", ARRAY_N );
	foreach ( $result as $row ) {
		$count[ $row[0] ] = $row[1];
	}

	foreach ( $users as $id ) {
		if ( ! isset( $count[ $id ] ) )
			$count[ $id ] = 0;
	}

	return $count;
}

?>