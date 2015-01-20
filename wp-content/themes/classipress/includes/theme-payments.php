<?php

define( 'CP_ITEM_LISTING', 'ad-listing' );
define( 'CP_ITEM_MEMBERSHIP', 'membership-pack' );

add_action( 'pending_to_publish', 'cp_payments_handle_moderated_transaction' );

add_action( 'appthemes_transaction_completed', 'cp_payments_handle_ad_listing_completed' );
add_action( 'appthemes_transaction_activated', 'cp_payments_handle_ad_listing_activated' );

add_action( 'appthemes_transaction_completed', 'cp_payments_handle_membership_completed' );
add_action( 'appthemes_transaction_activated', 'cp_payments_handle_membership_activated' );


/**
 * Activates ad listing order and redirects user to ad if moderation is disabled,
 * otherwise redirects user to order summary.
 *
 * @param object $order
 */
function cp_payments_handle_ad_listing_completed( $order ) {
	global $cp_options;

	$ad_id = _cp_get_order_ad_id( $order );
	if ( ! $ad_id )
		return;

	$ad_url = get_permalink( $ad_id );
	$order_url = get_permalink( $order->get_id() );

	if ( $cp_options->post_status == 'publish' ) {
		$order->activate();
		if ( ! is_admin() ) {
			if ( did_action( 'wp_head' ) )
				cp_js_redirect( $ad_url );
			else
				wp_redirect( $ad_url );
		}
		return;
	} else {
		wp_update_post( array( 'ID' => $ad_id, 'post_status' => 'pending' ) );
		if ( ! is_admin() ) {
			if ( did_action( 'wp_head' ) )
				cp_js_redirect( $order_url );
			else
				wp_redirect( $order_url );
		}
		return;
	}
}

/**
 * Activates membership order if has been completed.
 *
 * @param object $order
 */
function cp_payments_handle_membership_completed( $order ) {

	if ( $order->get_items( CP_ITEM_MEMBERSHIP ) ) {
		$order->activate();
		if ( ! is_admin() ) {
			$order_url = $order->get_url( $order->get_id() );
			if ( did_action( 'wp_head' ) )
				cp_js_redirect( $order_url );
			else
				wp_redirect( $order_url );
		}
	}

}

/**
 * Handles moderated transaction.
 *
 * @param object $post
 */
function cp_payments_handle_moderated_transaction( $post ) {

	if ( $post->post_type != APP_POST_TYPE )
		return;

	$order = appthemes_get_order_connected_to( $post->ID );
	if ( ! $order || $order->get_status() !== APPTHEMES_ORDER_COMPLETED )
		return;

	add_action( 'save_post', 'cp_payments_activate_moderated_transaction', 11 );
}

/**
 * Activates moderated transaction.
 *
 * @param int $post_id
 */
function cp_payments_activate_moderated_transaction( $post_id ) {

	if ( get_post_type( $post_id ) != APP_POST_TYPE )
		return;

	$order = appthemes_get_order_connected_to( $post_id );
	if ( ! $order || $order->get_status() !== APPTHEMES_ORDER_COMPLETED )
		return;

	$order->activate();

}

/**
 * Processes ad listing activation on order activation.
 *
 * @param object $order
 */
function cp_payments_handle_ad_listing_activated( $order ) {
	global $cp_options;

	foreach ( $order->get_items( CP_ITEM_LISTING ) as $item ) {
		$ad_id = wp_update_post( array( 'ID' => $item['post_id'], 'post_status' => 'publish' ) );

		$ad_length = get_post_meta( $ad_id, 'cp_sys_ad_duration', true );
		if ( empty( $ad_length ) )
			$ad_length = $cp_options->prun_period;

		$ad_expire_date = appthemes_mysql_date( current_time( 'mysql' ), $ad_length );
		update_post_meta( $ad_id, 'cp_sys_expire_date', $ad_expire_date );
	}

}

/**
 * Processes membership activation on order activation.
 *
 * @param object $order
 */
function cp_payments_handle_membership_activated( $order ) {

	// include all the functions needed for this action
	require_once( get_template_directory() . '/includes/forms/step-functions.php' );

	foreach ( $order->get_items( CP_ITEM_MEMBERSHIP ) as $item ) {
		$user = get_user_by( 'id', $order->get_author() );
		$membership_orders = get_user_orders( $user->ID, false );
		if ( empty( $membership_orders ) )
			continue;

		$order_id = get_order_id( $membership_orders[0] );
		$stored_order = get_option( $membership_orders[0] );
		$order_processed = appthemes_process_membership_order( $user, $stored_order );
		if ( $order_processed )
			cp_owner_activated_membership_email( $user, $order_processed );
	}

}

/**
 * Returns associated listing ID for given order, false if not found.
 *
 * @param object $order
 * @return int|bool
 */
function _cp_get_order_ad_id( $order ) {
	foreach ( $order->get_items( CP_ITEM_LISTING ) as $item ) {
		return $item['post_id'];
	}

	return false;
}

/**
 * Prints an redirect button and javascript.
 *
 * @param string $url
 * @param string $message
 */
function cp_js_redirect( $url, $message = '' ) {
	if ( empty( $message ) )
		$message = __( 'Continue', APP_TD );

	echo html( 'a', array( 'href' => $url ), $message );
	echo html( 'script', 'location.href="' . $url . '"' );
}

/**
 * Checks if payments are enabled on site.
 *
 * @return bool
 */
function cp_payments_is_enabled() {
	global $cp_options;

	if ( ! current_theme_supports( 'app-payments' ) || ! current_theme_supports( 'app-price-format' ) )
		return false;

	if ( ! $cp_options->charge_ads )
		return false;

	if ( $cp_options->price_scheme == 'featured' && ! is_numeric( $cp_options->sys_feat_price ) )
		return false;

	return true;
}

/**
 * Checks if post have some pending payment orders.
 *
 * @param int $post_id
 * @return bool
 */
function cp_have_pending_payment( $post_id ) {

	if ( ! cp_payments_is_enabled() )
		return false;

	$order = appthemes_get_order_connected_to( $post_id );
	if ( ! $order || ! in_array( $order->get_status(), array( APPTHEMES_ORDER_PENDING, APPTHEMES_ORDER_FAILED ) ) )
		return false;

	return true;
}

/**
 * Returns url of order connected to given Post ID.
 *
 * @param int $post_id
 * @return string
 */
function cp_get_order_permalink( $post_id ) {

	if ( ! cp_payments_is_enabled() )
		return;

	$order = appthemes_get_order_connected_to( $post_id );
	if ( ! $order )
		return;

	return appthemes_get_order_url( $order->get_id() );
}


