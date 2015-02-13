<?php
/**
 * Function to prevent visitors without admin permissions
 * to access the wordpress backend. If you wish to permit
 * others besides admins acces, change the user_level
 * to a different number.
 *
 * http://codex.wordpress.org/Roles_and_Capabilities#level_8
 *
 * @global <type> $user_level
 *
 */

function cp_security_check() {
	global $cp_options;

	$cp_access_level = $cp_options->admin_security;
	// if there's no value then give everyone access
	if ( empty( $cp_access_level ) )
		$cp_access_level = 'read';

	// previous releases had incompatible capability with MU installs
	if ( 'install_themes' == $cp_access_level )
		$cp_access_level = 'manage_options';

	if ( $cp_access_level == 'disable' || defined( 'DOING_AJAX' ) )
		return;

	if ( current_user_can( $cp_access_level ) )
		return;
?>

	<!DOCTYPE html>
	<html>

		<head>
			<title><?php _e( 'Access Denied.', APP_TD ); ?></title>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<link rel="stylesheet" href="<?php echo admin_url('css/install.css'); ?>" type="text/css" />
		</head>

		<body id="error-page">
			<p><?php _e( 'Access Denied. Your site administrator has blocked your access to the WordPress back-office.', APP_TD ); ?></p>
		</body>

	</html>

<?php
	exit();

}
add_action( 'admin_init', 'cp_security_check', 1 );


