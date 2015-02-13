<?php
/**
 * ClassiPress Sidebars
 * This file defines sidebars for widgets.
 *
 */

// initialize all the sidebars so they are widgetized
function cp_sidebars_init() {
	if ( !function_exists( 'register_sidebars' ) )
		return;

	register_sidebar( array(
		'name' => __( 'Main Sidebar', APP_TD ),
		'id' => 'sidebar_main',
		'description' => __( 'This is your main ClassiPress sidebar.', APP_TD ),
		'before_widget' => '<div class="shadowblock_out %2$s" id="%1$s"><div class="shadowblock">',
		'after_widget' => '</div><!-- /shadowblock --></div><!-- /shadowblock_out -->',
		'before_title' => '<h2 class="dotted">',
		'after_title' => '</h2>',
	) );

	register_sidebar( array(
		'name' => __( 'Ad Sidebar', APP_TD ),
		'id' => 'sidebar_listing',
		'description' => __( 'This is your ClassiPress single ad listing sidebar.', APP_TD ),
		'before_widget' => '<div class="shadowblock_out %2$s" id="%1$s"><div class="shadowblock">',
		'after_widget' => '</div><!-- /shadowblock --></div><!-- /shadowblock_out -->',
		'before_title' => '<h2 class="dotted">',
		'after_title' => '</h2>',
	) );

	register_sidebar( array(
		'name' => __( 'Page Sidebar', APP_TD ),
		'id' => 'sidebar_page',
		'description' => __( 'This is your ClassiPress page sidebar.', APP_TD ),
		'before_widget' => '<div class="shadowblock_out %2$s" id="%1$s"><div class="shadowblock">',
		'after_widget' => '</div><!-- /shadowblock --></div><!-- /shadowblock_out -->',
		'before_title' => '<h2 class="dotted">',
		'after_title' => '</h2>',
	) );

	register_sidebar( array(
		'name' => __( 'Blog Sidebar', APP_TD ),
		'id' => 'sidebar_blog',
		'description' => __( 'This is your ClassiPress blog sidebar.', APP_TD ),
		'before_widget' => '<div class="shadowblock_out %2$s" id="%1$s"><div class="shadowblock">',
		'after_widget' => '</div><!-- /shadowblock --></div><!-- /shadowblock_out -->',
		'before_title' => '<h2 class="dotted">',
		'after_title' => '</h2>',
	) );

	register_sidebar( array(
		'name' => __( 'Dashboard Sidebar', APP_TD ),
		'id' => 'sidebar_user',
		'description' => __( 'This is your ClassiPress dashboard sidebar.', APP_TD ),
		'before_widget' => '<div class="shadowblock_out %2$s" id="%1$s"><div class="shadowblock">',
		'after_widget' => '</div><!-- /shadowblock --></div><!-- /shadowblock_out -->',
		'before_title' => '<h2 class="dotted">',
		'after_title' => '</h2>',
	) );

	register_sidebar( array(
		'name' => __( 'Author Sidebar', APP_TD ),
		'id' => 'sidebar_author',
		'description' => __( 'This is your ClassiPress author sidebar.', APP_TD ),
		'before_widget' => '<div class="shadowblock_out %2$s" id="%1$s"><div class="shadowblock">',
		'after_widget' => '</div><!-- /shadowblock --></div><!-- /shadowblock_out -->',
		'before_title' => '<h2 class="dotted">',
		'after_title' => '</h2>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer', APP_TD ),
		'id' => 'sidebar_footer',
		'description' => __( 'This is your ClassiPress footer. You can have up to four items which will display in the footer from left to right.', APP_TD ),
		'before_widget' => '<div class="column %2$s" id="%1$s">',
		'after_widget' => '</div><!-- /column -->',
		'before_title' => '<h2 class="dotted">',
		'after_title' => '</h2>',
	) );

}
add_action( 'init', 'cp_sidebars_init' );

?>