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
define('DB_NAME', 'gittraining');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '#8b:3PY|lm%:OA~THIct-*=M Z%uC^vl^C2#<;I_)x0TPW!0.hI$US9NQI<2-l:a');
define('SECURE_AUTH_KEY',  'hGah%5zJzK5b_*`+~SA  <~#p<^#rH6H~fY-9{F{AVhP&tw^<se63ygi^9oRtV<_');
define('LOGGED_IN_KEY',    'QWS/Z=_&oLXmv-*ZdQ,7_D4Pn>xTj+0m>!|`(<o9Mlt-$ouX0>|Nexz%(`)aBs=h');
define('NONCE_KEY',        'wN-LRz`4t?NZ`XNBbPhR+m;hIMI}MH@Q[7I <TCI/f_T.RZ0DE4#<,[!+|A4drY/');
define('AUTH_SALT',        'vWAPsXnjPs=GP1_,#D{MXH{7eKSO|y;9WOVJ2_YFeE|r?k7916^*-m)y>$@2 r|j');
define('SECURE_AUTH_SALT', 'd(w;GZ[~!gDPXEc;:,/UaH`U;XjoC+mjaO+nds2xI?$?mnXt`t/cHl{Rvg9@i8q ');
define('LOGGED_IN_SALT',   'vn?(n83wB, GY}E/,NeonW=@Cl}8+cupW3l4)9hTNivk-ML>-:~UNX-aAD-bGo,q');
define('NONCE_SALT',       'Vr4N^I7FK;Ec1an-^|`yE0X}6CLf|!*.h c!gr_zw<S7+=2(^g/3|*?aG^xJ5;L*');

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
