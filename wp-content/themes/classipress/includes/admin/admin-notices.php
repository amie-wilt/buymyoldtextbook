<?php
/**
 * These are the admin alert messages displayed
 * on the WordPress admin pages
 * 
 * 
 */


function cp_admin_info_box() {

	// reserved for future use

}


// enable to see the current screen name
function dev_check_current_screen() {
	global $current_screen;

	print_r( $current_screen );
}
// add_action( 'admin_notices', 'dev_check_current_screen' );


