<?php

/**
 * Add header elements via the wp_head hook
 *
 * Anything you add to this file will be dynamically
 * inserted in the header of your theme
 *
 * @since 3.0.0
 * @uses wp_head
 *
 */


// adds CP version number in the header for troubleshooting
function cp_version() {
	global $app_version;
	echo '<meta name="version" content="ClassiPress '.$app_version.'" />' . "\n";
}
add_action( 'wp_head', 'cp_version' );


// adds support for cufon font replacement
function cp_cufon_styles() {
	global $cp_options;

	if ( ! $cp_options->cufon_enable )
		return;
?>
	<!--[if gte IE 9]> <script type="text/javascript"> Cufon.set('engine', 'canvas'); </script> <![endif]-->

	<!-- cufon font replacements -->
	<script type="text/javascript">
		// <![CDATA[
		<?php echo stripslashes( $cp_options->cufon_code ) . "\n"; ?>
		// ]]>
	</script>
	<!-- end cufon font replacements -->

<?php
}
add_action('wp_head', 'cp_cufon_styles');


// remove the WordPress version meta tag
if ( $cp_options->remove_wp_generator )
	remove_action( 'wp_head', 'wp_generator' );


// remove the new 3.1 admin header toolbar visible on the website if logged in
if ( $cp_options->remove_admin_bar )
	add_filter( 'show_admin_bar', '__return_false' );

