<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wrd_je2i3dgcef');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'zmwt6mmFV537ARoKRN9EWWo3BUs5QZeBfkTWklrV1HYv3oj3dwPZJkzZF59TpUjU');
define('SECURE_AUTH_KEY',  'WBmijwPJyh0ZpAvjEsJTm55Hd9B7GZpRls4balLPZbXII8PyqvYpvsa54bqx8XSt');
define('LOGGED_IN_KEY',    'cHy9ML1ohz4l4UH048gx2DJZU5YnX6AETrvo7l605qmBD6ohtrPoMBvoXe1ks5ss');
define('NONCE_KEY',        'Fj3tLcMuGiokeILbCDvWVoN9FszjEye0cBIa5qds18OOP4Zm7hZScvO0gdYpTqf9');
define('AUTH_SALT',        '93GLvHLg6IxxFu8HRJroKRrfQ0Xxn5XyuTeBzP5Qtydohrpqt1p6AEhMSsl9H39q');
define('SECURE_AUTH_SALT', 'r6zkSwF8qDpja5QzqVdPyWtWMUhsNAnsmYS7VO8SJjnEOjwCUMKDFBoPNfuzWJLx');
define('LOGGED_IN_SALT',   '3fzC5QHLeSm8aUWUKKsWdHgTuXkq22OQb6GgjhpvJst8OLe9YReVawwHaTyTgvs8');
define('NONCE_SALT',       'hhDjWpPkMAx34uZWRr8dHnHq6C6lssXbqeWgWdMI7ld9KEJqMLSaulfjrYmI1l4P');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
