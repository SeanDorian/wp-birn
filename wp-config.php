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
define('DB_NAME', 'wordpress');

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
define('AUTH_KEY',         'z29?:%0(r@D|=O|Ds2nrgqg;% Au. xD**{MPoDno:`Pc{v[kN`BN`R[<P|K&<*6');
define('SECURE_AUTH_KEY',  'wuTOm1C*l7hS-I~ovk+t;uUiwxhGf{GtfIZC:ll7g_0mCcOfzeB&%B,|I{~[7PTH');
define('LOGGED_IN_KEY',    ')6@x)P.wQI;ZgQ4|OHZK<7FI<x3K}A]@E[C-g(tiK#:6p[D J]z=V?9kdcI;Et<%');
define('NONCE_KEY',        'K`p(q9g2t1,ly!>p!|XXat-1]c|B$8f*yfs9?O2vkm-yS5ce-be%K1in:lLFsQ6@');
define('AUTH_SALT',        'ba<)|rL3>~>~79|OV]k)}(b#Kg7y1wP4q;aRbp%|ULTtS}}<vK7U? kO2$|UC<N#');
define('SECURE_AUTH_SALT', '2+H!N@+(;-HGUefI >lW;6-}5>N.mEi-vn64DH|j=}8aSlVK0$yQB|?qW7Zq|H1W');
define('LOGGED_IN_SALT',   'dS_]{t,34U+G,d_D4-csl3@t2XV)S)]M=PgrT2@+U><`LL-;KE1wD9j7G)}O!ydS');
define('NONCE_SALT',       'Uf9K{*_[@9b!9eBn8_< mlHdAQ|OA*z+wu7KSy1 R) $F]xD@DgQ}.%u%V^`#!rD');

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
