<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'alon2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'mysql');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'cw58nvy416siirp7mxirotoonijk0hfvwhzwa5d8bqz10n2gkddmcxs4dsepb7wb');
define('SECURE_AUTH_KEY',  'xk5bjobx1opyllgrmebkrel7wes2porif3pl1sbhpnzuznmokjztfaygk4xpvpe9');
define('LOGGED_IN_KEY',    '2kxrhjidvtm2mkz5zli2xesqfhk9g9xggefpmmtkygebkuf9ekgwres1gwb1dbkx');
define('NONCE_KEY',        'g7s5rz00admtrayegaoly5esvy0m3o8wz8chjlg8pfyslntfgsqva54fhl6gakbl');
define('AUTH_SALT',        'dmelmzlcrswnjfzm3hs3nzdmlyc8xbrrsdxrwnbwk3ryzwdbwfbgwghp4mrj8a2u');
define('SECURE_AUTH_SALT', 'hdoeqjx0wr306wvncvhp6z1g1vv4dusl2d83btconoiguduykjy1fmnqtpiz29cm');
define('LOGGED_IN_SALT',   'volmukv7pfl0zuwusmyf3xy3y3gztwtpyujwygd29azkwjyjk3twqy7jocnqickv');
define('NONCE_SALT',       'cbxtebdyhqct9spdxlokmuvaw1zs1sht7wuqhzv59mjgjpzqvgeev9b2feo3opkb');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
