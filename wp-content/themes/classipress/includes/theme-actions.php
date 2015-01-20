<?php
/**
 * Adds all action hooks for the theme
 *
 * @since 3.1
 * @uses add_action() calls to trigger the hooks.
 *
 */
 

/**
 * add the ad price field in the loop before the ad title 
 * @since 3.1.3
 */
function cp_ad_loop_price() {
	global $post;
	if ( $post->post_type == 'page' || $post->post_type == 'post' )
		return;
?>
	<div class="price-wrap">
		<span class="tag-head">&nbsp;</span><p class="post-price"><?php cp_get_price( $post->ID, 'cp_price' ); ?></p>
	</div>

<?php
}
add_action( 'appthemes_before_post_title', 'cp_ad_loop_price' );


/**
 * add the ad meta in the loop after the ad title 
 * @since 3.1
 */
function cp_ad_loop_meta() {
	global $post, $cp_options;
	if ( is_singular( APP_POST_TYPE ) )
		return;
?>	
    <p class="post-meta">
        <span class="folder"><?php if ( $post->post_type == 'post' ) the_category(', '); else echo get_the_term_list( $post->ID, APP_TAX_CAT, '', ', ', '' ); ?></span> | <span class="owner"><?php if ( $cp_options->ad_gravatar_thumb ) appthemes_get_profile_pic( get_the_author_meta('ID'), get_the_author_meta('user_email'), 16 ) ?><?php the_author_posts_link(); ?></span> | <span class="clock"><span><?php echo appthemes_date_posted($post->post_date); ?></span></span>
    </p>
<?php
}
add_action( 'appthemes_after_post_title', 'cp_ad_loop_meta' );


/**
 * add the stats after the ad listing and blog post content 
 * @since 3.1
 */
function cp_do_loop_stats() {
	global $post, $cp_options;

	if ( is_singular( array( 'post', APP_POST_TYPE ) ) )
		return;

  if ( ! $cp_options->ad_stats_all || ! current_theme_supports( 'app-stats' ) )
		return;
?>
	<p class="stats"><?php appthemes_stats_counter( $post->ID ); ?></p>
<?php
}
add_action( 'appthemes_after_post_content', 'cp_do_loop_stats' );
add_action( 'appthemes_after_blog_post_content', 'cp_do_loop_stats' );


/**
 * add the ad reference ID after the ad listing content 
 * @since 3.1.3
 */
function cp_do_ad_ref_id() {
	global $post;
	if ( ! is_singular( APP_POST_TYPE ) )
		return;
?>		
	<div class='note'><strong><?php _e( 'Ad Reference ID:', APP_TD ); ?></strong> <?php if ( get_post_meta( $post->ID, 'cp_sys_ad_conf_id', true ) ) echo get_post_meta( $post->ID, 'cp_sys_ad_conf_id', true ); else _e( 'N/A', APP_TD ); ?></div>
    <div class="dotted"></div>
    <div class="pad5"></div>
<?php
}
add_action( 'appthemes_after_post_content', 'cp_do_ad_ref_id' );


/**
 * add the pagination after the ad listing and blog post content 
 * @since 3.1
 */
function cp_do_pagination() {
	// don't do on pages, the home page, or single blog post
	if ( is_page() || is_singular( 'post' ) )
		return;

	if ( function_exists('appthemes_pagination') )
		appthemes_pagination();

}
add_action( 'appthemes_after_endwhile', 'cp_do_pagination' );
add_action( 'appthemes_after_blog_endwhile', 'cp_do_pagination' );


/**
 * add the no ads found message 
 * @since 3.1
 */
function cp_ad_loop_else() {
?>		
    <div class="shadowblock_out">

		<div class="shadowblock">

			<div class="pad10"></div>

			<p><?php _e( 'Sorry, no listings were found.', APP_TD ); ?></p>

			<div class="pad50"></div>
        
		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->
<?php
}
add_action('appthemes_loop_else', 'cp_ad_loop_else');


/**
 * Blog section actions
 *
 */

/**
 * add the post meta after the blog post title 
 * @since 3.1
 */
function cp_blog_post_meta() {
	if ( is_page() ) return; // don't do post-meta on pages
	global $post;
?>		
	<p class="meta dotted"><span class="user"><?php the_author_posts_link(); ?></span> | <span class="folderb"><?php the_category(', ') ?></span> | <span class="clock"><span><?php echo appthemes_date_posted( $post->post_date ); ?></span></span></p>
<?php
}
add_action('appthemes_after_blog_post_title', 'cp_blog_post_meta');


/**
 * add the blog post meta footer content 
 * @since 3.1.3
 */
function cp_blog_post_meta_footer() {
	global $post, $cp_options;
	if ( ! is_singular( array( 'post', APP_POST_TYPE ) ) )
		return;
?>		
	<div class="prdetails">
	    <?php if ( is_singular( 'post' ) ) { ?>
        <p class="tags"><?php if ( get_the_tags() ) echo the_tags( '', '&nbsp;', '' ); else _e( 'No Tags', APP_TD ); ?></p>
        <?php } else { ?>
        <p class="tags"><?php if ( get_the_term_list( $post->ID, APP_TAX_TAG ) ) echo get_the_term_list( $post->ID, APP_TAX_TAG, '', '&nbsp;', '' ); else _e( 'No Tags', APP_TD ); ?></p>
        <?php } ?>
        <?php if ( $cp_options->ad_stats_all && current_theme_supports( 'app-stats' ) ) { ?><p class="stats"><?php appthemes_stats_counter( $post->ID ); ?></p> <?php } ?>
        <p class="print"><?php if ( function_exists('wp_email') ) email_link(); ?>&nbsp;&nbsp;<?php if ( function_exists('wp_print') ) print_link(); ?></p>
        <?php cp_edit_ad_link(); ?>
    </div>
    
<?php
}
add_action('appthemes_after_blog_post_content', 'cp_blog_post_meta_footer');
add_action('appthemes_after_post_content', 'cp_blog_post_meta_footer');


/**
 * add the no blog posts found message 
 * @since 3.1
 */
function cp_blog_loop_else() {
?>
	<div class="shadowblock_out">

		<div class="shadowblock">

			<div class="pad10"></div>

			<p><?php _e( 'Sorry, no posts could be found.', APP_TD ); ?></p>

			<div class="pad50"></div>

		</div><!-- /shadowblock -->

	</div><!-- /shadowblock_out -->
<?php
}
add_action('appthemes_blog_loop_else', 'cp_blog_loop_else');


/**
 * add the comments bubble 
 * @since 3.1.3
 */
function cp_blog_comments_bubble() {
?>		
    <div class="comment-bubble"><?php comments_popup_link( '0', '1', '%' ); ?></div>
<?php
}
add_action( 'appthemes_before_blog_post_title', 'cp_blog_comments_bubble' );


/**
 * add the blog and ad listing single page banner ad 
 * @since 3.1.3
 */
function cp_single_ad_banner() {
	global $post;

	if ( ! is_singular( array( 'post', APP_POST_TYPE ) ) )
		return;

	appthemes_advertise_content();

}
add_action( 'appthemes_after_blog_loop', 'cp_single_ad_banner' );
add_action( 'appthemes_after_loop', 'cp_single_ad_banner' );


/**
 * collect stats if are enabled, limits db queries
 * @since 3.1.8
 */
function cp_cache_stats() {
	global $cp_options;
	if ( is_singular( array( APP_POST_TYPE, 'post' ) ) )
		return;

	if ( ! $cp_options->ad_stats_all || ! current_theme_supports( 'app-stats' ) )
		return;

	add_action( 'appthemes_before_loop', 'appthemes_collect_stats' );
	//add_action( 'appthemes_before_search_loop', 'appthemes_collect_stats' );
	add_action( 'appthemes_before_blog_loop', 'appthemes_collect_stats' );
}
add_action( 'wp', 'cp_cache_stats' );


/**
 * collect featured images if are enabled, limits db queries
 * @since 3.1.8
 */
function cp_cache_featured_images() {
	global $cp_options;
	if ( $cp_options->ad_images && ! is_singular( array( APP_POST_TYPE, 'post' ) ) ) {
		add_action( 'appthemes_before_loop', 'cp_collect_featured_images' );
		add_action( 'appthemes_before_featured_loop', 'cp_collect_featured_images' );
		//add_action( 'appthemes_before_search_loop', 'cp_collect_featured_images' );
		add_action( 'appthemes_before_blog_loop', 'cp_collect_featured_images' );
	}
}
add_action( 'wp', 'cp_cache_featured_images' );


/**
 * modify Social Connect redirect to url
 * @since 3.1.9
 */
function cp_social_connect_redirect_to( $redirect_to ) {
	if ( preg_match('#/wp-(admin|login)?(.*?)$#i', $redirect_to) )
		$redirect_to = home_url();

	if ( current_theme_supports( 'app-login' ) ) {
		if ( APP_Login::get_url('redirect') == $redirect_to || appthemes_get_registration_url('redirect') == $redirect_to )
			$redirect_to = home_url();
	}

	return $redirect_to;
}
add_filter( 'social_connect_redirect_to', 'cp_social_connect_redirect_to', 10, 1 );


/**
 * query ads on author page in main wp query, fixes pagination
 * @since 3.2
 */
function cp_query_ads_on_author_page( $query ) {
  if ( $query->is_main_query() && $query->is_author() )
    $query->set( 'post_type', array( 'post', APP_POST_TYPE ) );
}
if ( version_compare($wp_version, '3.3', '>=') && !is_admin() )
  add_action( 'pre_get_posts', 'cp_query_ads_on_author_page' );


/**
 * process Social Connect request if App Login enabled
 * @since 3.2
 */
function cp_social_connect_login() {
	if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'social_connect' ) {
		if ( current_theme_supports( 'app-login' ) && function_exists('sc_social_connect_process_login') )
			sc_social_connect_process_login( false );
	}
}
add_action( 'init', 'cp_social_connect_login' );


/**
 * adds reCaptcha support
 * @since 3.2
 */
function cp_recaptcha_support() {
	global $cp_options;

	if ( ! $cp_options->captcha_enable )
		return;

	add_theme_support( 'app-recaptcha', array(
		'file' => get_template_directory() . '/includes/lib/recaptchalib.php',
		'theme' => $cp_options->captcha_theme,
		'public_key' => $cp_options->captcha_public_key,
		'private_key' => $cp_options->captcha_private_key,
	) );

}
add_action( 'appthemes_init', 'cp_recaptcha_support' );
add_action( 'register_form', 'appthemes_recaptcha' );


/**
 * controls password fields visibility
 * @since 3.2
 */
function cp_password_fields_support( $bool ) {
	global $cp_options;

	return (bool) $cp_options->allow_registration_password;
}
add_filter( 'show_password_fields_on_registration', 'cp_password_fields_support', 10, 1 );


/**
 * replaces default registration email
 * @since 3.2
 */
function cp_custom_registration_email() {
	remove_action( 'appthemes_after_registration', 'wp_new_user_notification', 10, 2 );
	add_action( 'appthemes_after_registration', 'cp_new_user_notification', 10, 2 );
}
add_action( 'after_setup_theme', 'cp_custom_registration_email', 1000 );


/**
 * redirects logged in users to homepage
 * @since 3.2
 */
function cp_redirect_to_home_page() {
	if ( !isset($_REQUEST['redirect_to']) ) {
		wp_redirect( home_url() );
		exit();
	}
}
add_action( 'wp_login', 'cp_redirect_to_home_page' );
add_action( 'app_login', 'cp_redirect_to_home_page' );


/**
 * 336 x 280 ad box on single page
 * @since 3.3
 */
function cp_adbox_336x280() {
	global $cp_options;

	if ( ! $cp_options->adcode_336x280_enable )
		return;
?>
	<div class="shadowblock_out">
		<div class="shadowblock">
			<h2 class="dotted"><?php _e( 'Sponsored Links', APP_TD ); ?></h2>
<?php
			if ( ! empty( $cp_options->adcode_336x280 ) ) {
				echo stripslashes( $cp_options->adcode_336x280 );
			} elseif ( $cp_options->adcode_336x280_url ) {
				$img = html( 'img', array( 'src' => $cp_options->adcode_336x280_url, 'border' => '0', 'alt' => '' ) );
				echo html( 'a', array( 'href' => $cp_options->adcode_336x280_dest, 'target' => '_blank' ), $img );
			}
?>
		</div><!-- /shadowblock -->
	</div><!-- /shadowblock_out -->
<?php
}
add_action( 'appthemes_advertise_content', 'cp_adbox_336x280' );


/**
 * 468 x 60 ad box in header
 * @since 3.3
 */
function cp_adbox_468x60() {
	global $cp_options;

	if ( ! $cp_options->adcode_468x60_enable )
		return;

	if ( ! empty( $cp_options->adcode_468x60 ) ) {
		echo stripslashes( $cp_options->adcode_468x60 );
	} else {
		if ( ! $cp_options->adcode_468x60_url ) {
			$img = html( 'img', array( 'src' => get_template_directory_uri() . '/images/468x60-banner.jpg', 'width' => '468', 'height' => '60', 'border' => '0', 'alt' => 'Premium WordPress Themes - AppThemes' ) );
			echo html( 'a', array( 'href' => 'http://www.appthemes.com', 'target' => '_blank' ), $img );
		} else {
			$img = html( 'img', array( 'src' => $cp_options->adcode_468x60_url, 'border' => '0', 'alt' => '' ) );
			echo html( 'a', array( 'href' => $cp_options->adcode_468x60_dest, 'target' => '_blank' ), $img );
		}
	}

}
add_action( 'appthemes_advertise_header', 'cp_adbox_468x60' );


/**
 * Disables WordPress 'auto-embeds' option.
 * @since 3.3
 */
function cp_disable_auto_embeds() {
	global $cp_options;

	if ( ! $cp_options->disable_embeds )
		return;

	remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
}
add_action( 'init', 'cp_disable_auto_embeds' );


/**
 * Inserts link for admin to reset stats of an ad or post.
 * @since 3.3
 */
function cp_add_reset_stats_link() {
	global $cp_options;

	if ( ! is_singular( array( APP_POST_TYPE, 'post' ) ) || ! $cp_options->ad_stats_all )
		return;

	appthemes_reset_stats_link();
}
add_action( 'appthemes_after_post_content', 'cp_add_reset_stats_link' );
add_action( 'appthemes_after_blog_post_content', 'cp_add_reset_stats_link' );


/**
 * Inserts links to example CSV files into Importer page.
 * @since 3.3
 */
function cp_add_links_to_example_csv_files() {
	$link1 = html( 'a', array( 'href' => get_template_directory_uri() . '/examples/ads.csv', 'title' => __( 'Download CSV file', APP_TD ) ), __( 'Ads', APP_TD ) );
	$link2 = html( 'a', array( 'href' => get_template_directory_uri() . '/examples/ads-with-attachments.csv', 'title' => __( 'Download CSV file', APP_TD ) ), __( 'Ads with attachments', APP_TD ) );

	echo html( 'p', sprintf( __( 'Download example CSV files: %1$s, %2$s', APP_TD ), $link1, $link2 ) );
}
add_action( 'appthemes_after_import_upload_form', 'cp_add_links_to_example_csv_files' );


/**
 * Pings 'update services' while publish ad listing.
 * @since 3.3
 */
add_action( 'publish_' . APP_POST_TYPE, '_publish_post_hook', 5, 1 );


/**
 * Closes comments for old ads.
 * see WordPress->Settings->Discussion
 * @since 3.3
 */
function cp_close_comments_for_old_ads( $post_types ) {
	$post_types[] = APP_POST_TYPE;
	return $post_types;
}
add_filter( 'close_comments_for_post_types', 'cp_close_comments_for_old_ads' );


/**
 * Changes drop down indentation on mobile devices.
 * @since 3.3.1
 */
function cp_change_dropdown_indentation_on_mobile( $dropdown ) {
	if ( wp_is_mobile() )
		$dropdown = preg_replace( '/&nbsp;&nbsp;&nbsp;/', ' - ', $dropdown );

	return $dropdown;
}
add_filter( 'wp_dropdown_cats', 'cp_change_dropdown_indentation_on_mobile' );


/**
 * Limits characters in the price field.
 * @since 3.3.1
 */
function cp_limit_characters_in_price_field( $args ) {
	$args['maxlength'] = 15;

	return $args;
}
add_filter( 'cp_formbuilder_cp_price', 'cp_limit_characters_in_price_field' );

