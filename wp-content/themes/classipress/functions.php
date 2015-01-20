<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package ClassiPress
 * @author AppThemes
 */

global $cp_options;

// current version
$app_theme = 'ClassiPress';
$app_abbr = 'cp';
$app_version = '3.3.1';
$app_db_version = 1960;
$app_edition = 'Ultimate Edition';

// define rss feed urls
$app_rss_feed = 'http://feeds2.feedburner.com/appthemes';
$app_twitter_rss_feed = 'http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=appthemes';
$app_forum_rss_feed = 'http://forums.appthemes.com/external.php?type=RSS2';

// define the transients we use
$app_transients = array($app_abbr.'_cat_menu');

define( 'APP_TD', 'classipress' );

// Framework
require_once( dirname( __FILE__ ) . '/framework/load.php' );

// Payments
require_once( dirname( __FILE__ ) . '/includes/payments/load.php' );

// Options
require_once( dirname( __FILE__ ) . '/includes/theme-options.php' );

scb_register_table( 'app_pop_daily', $app_abbr . '_ad_pop_daily' );
scb_register_table( 'app_pop_total', $app_abbr . '_ad_pop_total' );
APP_Mail_From::init();

require_once( dirname( __FILE__ ) . '/framework/includes/stats.php' );

if ( is_admin() )
	require_once( dirname( __FILE__ ) . '/framework/admin/importer.php' );

// Theme-specific files
require_once( dirname( __FILE__ ) . '/includes/theme-functions.php' );

