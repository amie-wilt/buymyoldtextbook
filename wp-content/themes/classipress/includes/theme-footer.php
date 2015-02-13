<?php

/**
 * Add footer elements via the wp_footer hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the footer of your theme
 *
 * @since 3.0.0
 * @uses cp_footer_actions
 *
 */


// insert the google analytics tracking code in the footer
function cp_google_analytics_code() {
	global $cp_options;

	if ( ! empty( $cp_options->google_analytics ) )
		echo stripslashes( $cp_options->google_analytics );

}
add_action( 'wp_footer', 'cp_google_analytics_code' );


// enable the gravatar hovercards in footer
function cp_gravatar_hovercards() {
	global $cp_options;

	if ( $cp_options->use_hovercards )
		wp_enqueue_script( 'gprofiles', 'http://s.gravatar.com/js/gprofiles.js', array( 'jquery' ), '1.0', true );

}
add_action( 'wp_enqueue_scripts', 'cp_gravatar_hovercards' );


