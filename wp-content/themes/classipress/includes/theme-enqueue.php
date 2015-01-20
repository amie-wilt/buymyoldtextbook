<?php
/**
 * These are scripts used within the theme
 * To increase speed and performance, we only want to
 * load them when needed
 *
 * @package ClassiPress
 *
 */


// correctly load all the jquery scripts so they don't conflict with plugins
if ( !function_exists('cp_load_scripts') ) :
function cp_load_scripts() {
	global $cp_options;

	// load google cdn hosted scripts if enabled
	if ( $cp_options->google_jquery ) {
		wp_deregister_script('jquery');
		$protocol = is_ssl() ? 'https' : 'http';
		wp_register_script( 'jquery', ($protocol . '://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'), false, '1.8.3' );
	}

	// needed for single ad sidebar email & comments on pages, edit ad & profile pages, ads, blog posts
	if ( is_singular() ) {
		wp_enqueue_script( 'validate', get_template_directory_uri() . '/includes/js/validate/jquery.validate.min.js', array( 'jquery' ), '1.11.0' );
		// enqueue the language validation file
		if ( $cp_options->form_val_lang ) {
			$lang_code = trim( $cp_options->form_val_lang );
			wp_enqueue_script( 'validate-lang', get_template_directory_uri() . "/includes/js/validate/localization/messages_$lang_code.js", array( 'jquery' ), '1.11.0' );
		}
	}

	// search autocomplete and slider on certain pages
	wp_enqueue_script( 'jquery-ui-autocomplete' );

	// advanced search sidebar and home page carousel
	wp_enqueue_script( 'jquery-ui-slider' );

	// used to convert header menu into select list on mobile devices
	wp_enqueue_script( 'tinynav', get_template_directory_uri() . '/includes/js/jquery.tinynav.js', array( 'jquery' ), '1.1' );
	// used to transform tables on mobile devices
	wp_enqueue_script( 'footable', get_template_directory_uri() . '/includes/js/jquery.footable.js', array( 'jquery' ), '0.1' );

	// adds touch events to jQuery UI on mobile devices
	if ( wp_is_mobile() )
		wp_enqueue_script( 'jquery-touch-punch' );

	if ( ! wp_is_mobile() && $cp_options->selectbox ) {
		// styles select elements
		wp_enqueue_script( 'selectbox', get_template_directory_uri() . '/includes/js/jquery.selectBox.min.js', array( 'jquery' ), '1.2.0' );
	}

	if ( $cp_options->enable_featured && is_page_template( 'tpl-ads-home.php' ) ) {
		wp_enqueue_script( 'jqueryeasing', get_template_directory_uri() . '/includes/js/easing.js', array( 'jquery' ), '1.3' );
		wp_enqueue_script( 'jcarousellite', get_template_directory_uri() . '/includes/js/jcarousellite.min.js', array( 'jquery', 'jquery-ui-slider' ), '1.8.3' );
	}

	wp_enqueue_script( 'theme-scripts', get_template_directory_uri() . '/includes/js/theme-scripts.js', array( 'jquery' ), '3.3' );

	// only load the general.js if available in child theme
	if ( file_exists( get_stylesheet_directory() . '/general.js' ) )
		wp_enqueue_script( 'general', get_stylesheet_directory_uri() . '/general.js', array( 'jquery' ), '1.0' );

	// only load cufon if it's been enabled
	if ( $cp_options->cufon_enable ) {
		wp_enqueue_script( 'cufon-yui', get_template_directory_uri() . '/includes/js/cufon-yui.js', array( 'jquery' ), '1.0.9i' );
		wp_enqueue_script( 'cufon-font-vegur', get_template_directory_uri() . '/includes/fonts/Vegur_400-Vegur_700.font.js', array( 'cufon-yui' ) );
		wp_enqueue_script( 'cufon-font-liberation', get_template_directory_uri() . '/includes/fonts/Liberation_Serif_400.font.js', array( 'cufon-yui' ) );
	}

	// only load colorbox & gmaps when we need it
	if ( is_singular( APP_POST_TYPE ) ) {
		$cp_gmaps_lang = esc_attr( $cp_options->gmaps_lang );
		$cp_gmaps_region = esc_attr( $cp_options->gmaps_region );
		$google_maps_url = ( is_ssl() ? 'https' : 'http' ) . '://maps.googleapis.com/maps/api/js';
		$google_maps_url = add_query_arg( array( 'sensor' => 'false', 'language' => $cp_gmaps_lang, 'region' => $cp_gmaps_region ), $google_maps_url );

		wp_enqueue_script( 'colorbox', get_template_directory_uri() . '/includes/js/colorbox/jquery.colorbox-min.js', array( 'jquery' ), '1.4.3' );
		wp_enqueue_script( 'google-maps', $google_maps_url, array( 'jquery' ), '3.0' );
	}

	// enqueue the user profile scripts
	if ( is_page_template( 'tpl-profile.php' ) )
		wp_enqueue_script( 'user-profile' );

	/* Script variables */
	$params = array(
		'appTaxTag' => APP_TAX_TAG,
		'ad_parent_posting' => $cp_options->ad_parent_posting,
		'ad_currency' => $cp_options->curr_symbol,
		'currency_position' => $cp_options->currency_position,
		'home_url' => home_url( '/' ),
		'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ),
		'text_before_delete_ad' => __( 'Are you sure you want to delete this ad?', APP_TD ),
		'text_mobile_navigation' => __( 'Navigation', APP_TD ),
	);
	wp_localize_script('theme-scripts', 'classipress_params', $params);

	$params = array(
		'empty' => __( 'Strength indicator', APP_TD ),
		'short' => __( 'Very weak', APP_TD ),
		'bad' => __( 'Weak', APP_TD ),
		'good' => __( 'Medium', APP_TD ),
		'strong' => __( 'Strong', APP_TD ),
		'mismatch' => __( 'Mismatch', APP_TD ),
	);
	wp_localize_script('password-strength-meter', 'pwsL10n', $params);

}
endif;
add_action( 'wp_enqueue_scripts', 'cp_load_scripts' );


// this function is called when submitting a new ad listing in tpl-add-new.php
if ( !function_exists('cp_load_form_scripts') ) :
function cp_load_form_scripts() {
	global $cp_options;

	// only load the tinymce editor when html is allowed
	if ( $cp_options->allow_html ) {
		wp_enqueue_script( 'tiny_mce', includes_url('js/tinymce/tiny_mce.js'), array( 'jquery' ), '3.0' );
		wp_enqueue_script( 'wp-langs-en', includes_url('js/tinymce/langs/wp-langs-en.js'), array( 'jquery' ), '3241-1141' );
	}
	wp_enqueue_script( 'validate', get_template_directory_uri() . '/includes/js/validate/jquery.validate.min.js', array( 'jquery' ), '1.11.0' );
	wp_enqueue_script( 'easytooltip', get_template_directory_uri() . '/includes/js/easyTooltip.js', array( 'jquery' ), '1.0' );

	// enqueue the language validation file
	if ( $cp_options->form_val_lang ) {
		$lang_code = trim( $cp_options->form_val_lang );
		wp_enqueue_script( 'validate-lang', get_template_directory_uri() . "/includes/js/validate/localization/messages_$lang_code.js", array( 'jquery' ), '1.11.0' );
	}
}
endif;



// changes the css file based on what is selected on the options page
if ( !function_exists('cp_style_changer') ) :
function cp_style_changer() {
	global $cp_options;

	wp_enqueue_style( 'at-main', get_stylesheet_uri(), false );

	// turn off stylesheets if customers want to use child themes
	if ( ! $cp_options->disable_stylesheet )
		wp_enqueue_style( 'at-color', get_template_directory_uri() . '/styles/' . $cp_options->stylesheet, false );

	if ( file_exists( get_template_directory() . '/styles/custom.css' ) )
		wp_enqueue_style( 'at-custom', get_template_directory_uri() . '/styles/custom.css', false );

}
endif;
add_action( 'wp_enqueue_scripts', 'cp_style_changer' );


// load the css files correctly
if ( !function_exists('cp_load_styles') ) :
function cp_load_styles() {

	// load colorbox only on single ad listing page
	if ( is_singular( APP_POST_TYPE ) )
		wp_enqueue_style( 'colorbox', get_template_directory_uri() . '/includes/js/colorbox/colorbox.css', false, '1.4.3' );

	wp_enqueue_style( 'jquery-ui-style', get_template_directory_uri() . '/includes/js/jquery-ui/jquery-ui.css', false, '1.9.2' );

}
endif;
add_action( 'wp_enqueue_scripts', 'cp_load_styles' );


