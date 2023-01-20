<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'searsbdc_wp864' );

/** Database username */
define( 'DB_USER', 'searsbdc_wp864' );

/** Database password */
define( 'DB_PASSWORD', 'pA71[4.S1B' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'qnweagxdrgebltqf1txkehgtzr8chqkxdxsi1u2kdwxeb26n3o99idlkgtoekr60' );
define( 'SECURE_AUTH_KEY',  'ugrcqmgfqflr7tmo9khpyzdcuaa4cnfktabqcvgsxyypmcqwieqkezk5skwvihwp' );
define( 'LOGGED_IN_KEY',    'pzc4ewsi6ahczm0qukzhxzkytvszoixomrdth1a4fie93txmrh47m6qqecd7rfbc' );
define( 'NONCE_KEY',        'y1zxexjs7qrdxffgrznacwyqlkq6c00h5t9speo1forn7f45z3yvp8bof59xnu6h' );
define( 'AUTH_SALT',        'zwmtzzpbpynb55dz9ep8mgmmg0l3gr9b14rqpwgg2iijmopskalbso4lfvlrrxq8' );
define( 'SECURE_AUTH_SALT', 'hlmcehvdccp9rehchtfdexmrkzvucbyjngr4occ6hc0pyva8foenckff0jmwv6il' );
define( 'LOGGED_IN_SALT',   'px2yagkgodt3cpiokzpzf0qhuhahzqfxfiagiquymzlrrelb61s4sz72mcirkhah' );
define( 'NONCE_SALT',       'nqdoyjdl7ejowpigrggwubegq6c54gruho818iuewgytx2xgod3wzn6myvr35lsd' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpbf_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_MEMORY_LIMIT', '256M' );
set_time_limit(300);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
