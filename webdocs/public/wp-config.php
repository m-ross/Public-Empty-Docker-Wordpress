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
define( 'WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);
define( 'WP_SITEURL', WP_HOME . '/core/');

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
define('AUTH_KEY',         'Kz{a,99EEDY{:^GB|#q[Ktw S([..Id[A^B]g>Iydn]K*UGQ:;/@&WAxPQAZ]~~Y');
define('SECURE_AUTH_KEY',  'Gt||#H:F_mQ)W`yC~thm%,{9N1*}?3A_s?&UW]-edcaDE3RY#1p53Z4@jVbmwu|=');
define('LOGGED_IN_KEY',    'rtYSqowI?DPF6r+-@i$YSa<vP<LzJqakQyw_7jpd?/%!kiw<<Co.0>ihJ_p>d[Tx');
define('NONCE_KEY',        '${>e!?oVG[lZ4Ru0 9$weS#@I43)Sx_76]RhdMKQuQI$,3b4O |d])q?DkK=E~&z');
define('AUTH_SALT',        '`HkS<9cG=%|}3Un&=t]:d5gy*DoBv;[GsnNR;b*|P]C|Mj]tD?Jm-wMDPv[L+}qk');
define('SECURE_AUTH_SALT', 's#M:G{>gk<cEM5 k27A>-`e?Oo/q((o 428>o?:&n#%#/Ft(Pa/m!gJqinG+rNHn');
define('LOGGED_IN_SALT',   'XS6dPr&r8V_;Wf6zZr= m&A`ehlxb*y$`}r^e(TV%52:WMkH4OK^Q6%58Z=|))P2');
define('NONCE_SALT',       'y^.cWVyF>.3}^?$b9yS$uK+IJD!ddH>*to@;&qKE:;PzvtTw3S:5mX*8LA7@GjkB');

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
