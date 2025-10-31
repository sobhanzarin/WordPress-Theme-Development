<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db_theme' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'Rdyh;MLz-b4zA3Naw^g:TZVPCPz|SjO7*;0775m2O{0HX 3z`mH>_{w@wu(Ecrpe' );
define( 'SECURE_AUTH_KEY',  'hzA{9V(&di<~f]`&jSJ|.z^Tmt:qkc|/3^7)|ns~6-1FfrYCD8K(?hW~/;(ibR<(' );
define( 'LOGGED_IN_KEY',    ',5xNPBOy9H?n/:MJ!7#nmwcYv4G_9kB(2CNM#/PJkQOJQHcB%ZP6sWM8XOQAF/vf' );
define( 'NONCE_KEY',        'PF2<Tgc/1[F]1y$T)|LkXRM~jfg7o:bCnpY?! f~JC6(Rvb0x61m3LG!TkWQAAcX' );
define( 'AUTH_SALT',        ':Sew]cW1mazpr`6j8EN*mcF-L*mD)o;?4V,oi_oY)vLPf7R6Sf--BMZAe%Y4i(Y,' );
define( 'SECURE_AUTH_SALT', '55EEu4HuQwX5LNGyfc`J p#@^w)GQ,_}nLYQ EGz-^F>&/*]0.Lij-hJR<BI-{B ' );
define( 'LOGGED_IN_SALT',   'M,c9!yQ>HR}|.Q7YNeD49{6~Ax[g$SJSOF}R!g(}JB/~~y:otAj[:hsQ1]9k3?Eo' );
define( 'NONCE_SALT',       'd3AGL9KMehCU9mWO6RfjUWnbU9yR2ZQ^mYHBFJ`4Up1TK03xvwFK(5/~!fx4]JVA' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'simagar_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
