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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'mysqldb');

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
define('AUTH_KEY',         'f_kZ8cr>[zfQcI~|q*U! EgZ~{XbmaTNTJ1I }c1NyuFt. Y.e8VK(6(Ja)*H&jD');
define('SECURE_AUTH_KEY',  '3c)Fk}Q,yju=yR99K,*mi99Ey=.=0;kP2y:n~fBQ*k1O<9o,eX9p[wq,;@ ~WJ0r');
define('LOGGED_IN_KEY',    'hg2zXa&dgrn*->[FKLBtn)A`B%%qj[qOy4q*x]c+4~16m#!xj~Dwq8T%,L)58P(#');
define('NONCE_KEY',        'IHTsvA2AK#N8_@J0F[Fc=?7apu3^j$*3`,UPSFN*ir1A8&T_ih E1,6a%RmF2#>!');
define('AUTH_SALT',        '/~t:}_Jv}gPHB/jEqN!G2vN&Lw+mX&mTZ=L,_/qbLTE~O8r`p`6#KhJ$~>(*Yv+O');
define('SECURE_AUTH_SALT', '4xYU#-!|K[O[.0b2U@b#8bfq>v00~,#c:ybWl{mU)cGn1V|t&X-(SmwxfO0np5Br');
define('LOGGED_IN_SALT',   '&h@.h*$L?k<B>EuXFc;?w@t)8,R^kdVd;RXO#QGsDm0]X6JX?:.]m^GfYR;xN=1X');
define('NONCE_SALT',       '.PEm*_c9-t9(H<HEDJCk,qv`{ km@Va<a!|@@y4*2C3VBG Kh%$89`#Fl#qYEhd>');

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
