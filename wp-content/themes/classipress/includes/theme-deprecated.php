<?php
/**
 *
 * Holding Deprecated functions oldest at the bottom (delete and clean as needed)
 * @package ClassiPress
 * @author AppThemes
 *
 */


/**
 * Global path variables.
 *
 * @deprecated 3.3
 */
define( 'CP_ADD_NEW_CONFIRM_URL', home_url( '/' ) );
define( 'CP_MEMBERSHIP_PURCHASE_CONFIRM_URL', home_url( '/' ) );


/**
 * Assemble the blog path.
 *
 * @deprecated 3.0.5
 */
if ( ! function_exists('cp_detect_blog_path') ) {
	function cp_detect_blog_path() {
		_deprecated_function( __FUNCTION__, '3.0.5' );

		$blogcatid = get_option('cp_blog_cat');

		if ( ! empty( $blogcatid ) )
			$blogpath = get_category_link( get_option('cp_blog_cat') );
		else
			$blogpath = cp_cat_base() . '/blog/';

		return $blogpath;
	}
}


/**
 * Return category base. If not set, uses the default "category".
 *
 * @deprecated 3.0.5
 */
if ( ! function_exists('cp_cat_base') ) {
	function cp_cat_base() {
		_deprecated_function( __FUNCTION__, '3.0.5' );

		if ( appthemes_clean( get_option('category_base') ) == '' )
			$cat_base = home_url('/') . 'category';
		else
			$cat_base = home_url('/') . get_option('category_base');

		return $cat_base;
	}
}


/**
 * checks if blog post is in subcategory, used in CP 3.0.4 and earlier
 *
 * @deprecated 3.0.5
 */
function cp_post_in_desc_cat( $cats, $post = null ) {
	_deprecated_function( __FUNCTION__, '3.0.5' );

	foreach ( (array) $cats as $cat ) {
		$descendants = get_term_children( (int) $cat, 'category' );
		if ( $descendants && in_category( $descendants, $post ) )
			return true;
	}
	return false;
}


/**
 * returns blog category id, used in CP 3.0.4 and earlier
 *
 * @deprecated 3.0.5
 */
function cp_get_blog_catid() {
	_deprecated_function( __FUNCTION__, '3.0.5' );

	$blogcatid = get_option('cp_blog_cat');

	if ( empty( $blogcatid ) )
		$blogcatid = 1;

	return $blogcatid;
}


/**
 * returns comma separated list of blog category ids, used in CP 3.0.4 and earlier
 *
 * @deprecated 3.0.5
 */
function cp_get_blog_cat_ids() {
	_deprecated_function( __FUNCTION__, '3.0.5' );

	$catids = cp_get_blog_cat_ids_array();
	$allcats = trim( join( ',', $catids ) );

	return $allcats;
}


/**
 * returns array of blog category ids, used in CP 3.0.4 and earlier
 *
 * @deprecated 3.0.5
 */
function cp_get_blog_cat_ids_array() {
	_deprecated_function( __FUNCTION__, '3.0.5' );

	$catid = cp_get_blog_catid();
	$descendants = get_term_children( (int) $catid, 'category' );

	$output = array();
	$output[] = $catid;

	foreach ( $descendants as $key => $value )
		$output[] = $value;

	return $output;
}


/**
 * Categories list.
 *
 * @deprecated 3.1.9
 * @deprecated Use cp_create_categories_list()
 * @see cp_create_categories_list()
 */
if ( !function_exists('cp_cat_menu_drop_down') ) {
	function cp_cat_menu_drop_down( $cols = 3, $subs = 0 ) {
		_deprecated_function( __FUNCTION__, '3.1.9', 'cp_create_categories_list()' );

		return cp_create_categories_list( 'dir' );
	}
}


/**
 * Directory home page category display.
 *
 * @deprecated 3.0.5.2
 * @deprecated Use cp_create_categories_list()
 * @see cp_create_categories_list()
 */
if ( !function_exists('cp_directory_cat_columns') ) {
	function cp_directory_cat_columns($cols) {
		_deprecated_function( __FUNCTION__, '3.0.5.2', 'cp_create_categories_list()' );

		return cp_create_categories_list( 'dir' );
	}
}


/**
 * Create geocodes database table.
 *
 * @deprecated 3.2
 * @deprecated Use 'appthemes_first_run' hook
 * @see appthemes_first_run' hook
 */
if ( !function_exists('cp_create_geocode_table') ) {
	function cp_create_geocode_table() {
		_deprecated_function( __FUNCTION__, '3.2', 'appthemes_first_run' );

		return false;
	}
}


/**
 * Get the ad price and position the currency symbol.
 * Meta field 'price' used on CP 2.9.3 and earlier
 *
 * @deprecated 3.2
 * @deprecated Use cp_get_price()
 * @see cp_get_price()
 */
function cp_get_price_legacy($postid) {
	_deprecated_function( __FUNCTION__, '3.2', 'cp_get_price' );

	return cp_get_price($postid, 'price');
}


/**
 * Builds the edit ad form on the tpl-edit-item.php page template.
 *
 * @deprecated 3.2.1
 * @deprecated Use cp_formbuilder()
 * @see cp_formbuilder()
 */
if ( ! function_exists('cp_edit_ad_formbuilder') ) {
	function cp_edit_ad_formbuilder( $results, $post ) {
		_deprecated_function( __FUNCTION__, '3.2.1', 'cp_formbuilder' );

		require_once( get_template_directory() . '/includes/forms/step-functions.php' );

		return cp_formbuilder( $results, $post );
	}
}


/**
 * called before ad update to hook into the confirmation page
 *
 * @deprecated 3.3
 */
function cp_add_new_confirm_before_update() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * called after ad update to hook into the confirmation page
 *
 * @deprecated 3.3
 */
function cp_add_new_confirm_after_update() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * called to process the payment
 *
 * @deprecated 3.3
 */
function cp_action_gateway( $order_vals ) {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * called to hook into the payment list
 *
 * @deprecated 3.3
 */
function cp_action_payment_button( $post_id ) {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * called to hook into the payment dropdown
 *
 * @deprecated 3.3
 */
function cp_action_payment_method() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * called to hook into the admin gateway options
 *
 * @deprecated 3.3
 */
function cp_action_gateway_values() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * called to hook into db transaction process
 *
 * @deprecated 3.3
 */
function cp_process_transaction_entry() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was sending new membership notification email to buyer when purchased by bank transfer
 *
 * @deprecated 3.3
 */
function cp_bank_owner_new_membership_email( $oid ) {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was sending new ad notification email to buyer when purchased by bank transfer
 *
 * @deprecated 3.3
 */
function cp_bank_owner_new_ad_email( $post_id ) {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was sending new membership notification email to admin
 *
 * @deprecated 3.3
 */
function cp_new_membership_email( $oid ) {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was calculating total membership cost
 *
 * @deprecated 3.3
 */
function cp_calc_membership_cost( $pack_id, $coupon ) {
	_deprecated_function( __FUNCTION__, '3.3' );

	$membership = get_pack( $pack_id );
	if ( $membership )
		return $membership->pack_membership_price;

	return 0;
}


/**
 * was checking coupon code and returning coupon object, bool false if not found
 *
 * @deprecated 3.3
 */
function cp_check_coupon_discount( $coupon_code ) {
	_deprecated_function( __FUNCTION__, '3.3' );

	return false;
}


/**
 * was returning coupons list that match criteria, bool false if nothing found
 *
 * @deprecated 3.3
 */
function cp_get_coupons( $coupon_code = '' ) {
	_deprecated_function( __FUNCTION__, '3.3' );

	return false;
}


/**
 * was incrementing coupon used times value
 *
 * @deprecated 3.3
 */
function cp_use_coupon( $coupon_code ) {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * Prints price with positioned currency
 *
 * @deprecated 3.3
 * @deprecated Use cp_pos_currency()
 * @see cp_pos_currency()
 */
function cp_pos_price( $price, $price_type = '' ) {
	_deprecated_function( __FUNCTION__, '3.3', 'cp_pos_currency' );
	$price = cp_pos_currency( $price, $price_type );
	echo $price;
}


/**
 * was localizing admin scripts
 *
 * @deprecated 3.3
 */
function cp_theme_scripts_admin() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was creating admin dashboard page
 *
 * @deprecated 3.3
 * @see CP_Theme_Dashboard 
 */
function cp_dashboard() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was creating admin general settings page
 *
 * @deprecated 3.3
 * @see CP_Theme_Settings_General 
 */
function cp_settings() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was creating admin emails settings page
 *
 * @deprecated 3.3
 * @see CP_Theme_Settings_Emails 
 */
function cp_emails() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was creating admin pricing settings page
 *
 * @deprecated 3.3
 * @see CP_Theme_Settings_Pricing 
 */
function cp_pricing() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was updating admin settings
 *
 * @deprecated 3.3
 */
function cp_update_options() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was printing scripts for category selection on add-new page
 *
 * @deprecated 3.3
 */
function cp_ajax_addnew_js_header() {
	_deprecated_function( __FUNCTION__, '3.3' );
}


/**
 * was creating admin system info page
 *
 * @deprecated 3.3.1
 * @see CP_Theme_System_Info
 */
function cp_system_info() {
	_deprecated_function( __FUNCTION__, '3.3.1' );
}


/**
 * was printing dropdown menu with categories in Add New page
 *
 * @deprecated 3.3.1
 * @deprecated Use cp_addnew_dropdown_child_categories()
 * @see cp_addnew_dropdown_child_categories()
 */
if ( ! function_exists( 'cp_getChildrenCategories' ) ) {
	function cp_getChildrenCategories() {
		_deprecated_function( __FUNCTION__, '3.3.1', 'cp_addnew_dropdown_child_categories' );

		cp_addnew_dropdown_child_categories();
	}
}


/**
 * Sends custom new user notification
 *
 * @deprecated 3.3.1
 * @deprecated Use cp_new_user_notification()
 * @see cp_new_user_notification()
 */
function app_new_user_notification( $user_id, $plaintext_pass = '' ) {
	_deprecated_function( __FUNCTION__, '3.3.1', 'cp_new_user_notification' );
	cp_new_user_notification( $user_id, $plaintext_pass );
}

