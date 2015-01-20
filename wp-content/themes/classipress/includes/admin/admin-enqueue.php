<?php
/**
 * These are scripts used within the AppThemes admin pages
 *
 * @package AppThemes
 *
 */



/**
 * Load admin scripts and styles.
 */
function cp_load_admin_scripts() {
	global $cp_options, $pagenow;

	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_style('thickbox'); // needed for image upload


	//TODO: For now we call these on all admin pages because of some javascript errors, however it should be registered per admin page (like wordpress does it)
	wp_enqueue_script('jquery-ui-sortable'); //this script has issues on the page edit.php?post_type=ad_listing


	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script( 'timepicker', get_template_directory_uri() . '/includes/js/timepicker.min.js', array( 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.0.0' );

	wp_enqueue_style( 'jquery-ui-style', get_template_directory_uri() . '/includes/js/jquery-ui/jquery-ui.css', false, '1.9.2' );

	if ( $pagenow == 'admin.php' ) { // only trigger this on CP edit pages otherwise it causes a conflict with edit ad and edit post meta field buttons
		wp_enqueue_script( 'validate', get_template_directory_uri() . '/includes/js/validate/jquery.validate.min.js', array( 'jquery-ui-core' ), '1.11.0' );
		// enqueue the language validation file
		if ( $cp_options->form_val_lang ) {
			$lang_code = trim( $cp_options->form_val_lang );
			wp_enqueue_script( 'validate-lang', get_template_directory_uri() . "/includes/js/validate/localization/messages_$lang_code.js", array( 'jquery' ), '1.11.0' );
		}
	}

	wp_enqueue_script( 'admin-scripts', get_template_directory_uri() . '/includes/admin/admin-scripts.js', array( 'jquery', 'media-upload', 'thickbox' ), '3.3.1' );

	wp_enqueue_script( 'excanvas', get_template_directory_uri() . '/includes/js/excanvas.min.js', array( 'jquery' ), '1.0' );
	wp_enqueue_script( 'flot', get_template_directory_uri() . '/includes/js/jquery.flot.min.js', array( 'excanvas' ), '0.6' );

	/* Script variables */
	$params = array(
		'text_check_all' => __( 'check all', APP_TD ),
		'text_uncheck_all' => __( 'uncheck all', APP_TD ),
		'text_before_delete_tables' => __( 'WARNING: You are about to completely delete all ClassiPress database tables. Are you sure you want to proceed? (This cannot be undone)', APP_TD ),
		'text_before_delete_options' => __( 'WARNING: You are about to completely delete all ClassiPress configuration options from the wp_options database table. Are you sure you want to proceed? (This cannot be undone)', APP_TD ),
	);
	wp_localize_script( 'admin-scripts', 'classipress_admin_params', $params );

}
add_action( 'admin_enqueue_scripts', 'cp_load_admin_scripts' );


