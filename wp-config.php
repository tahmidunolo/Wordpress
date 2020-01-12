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
define( 'DB_NAME', 'tahmid' );

/** MySQL database username */
define( 'DB_USER', 'tahmid' );

/** MySQL database password */
define( 'DB_PASSWORD', 'UbrqdhfKhbRnpMag' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'z^lla}fG0+f?.I^N!/c-f*u)zh!G6<pTA_8.6#f<fBPLr&Qs?,bJ(|4(w#%Qt?1}' );
define( 'SECURE_AUTH_KEY',  'Ezg^jHn[XdXsaGL:(]TqMv&kvHL:u=ZU^Qm}GtTf0`<d|2wJiKUH/%ku.R$aI|<7' );
define( 'LOGGED_IN_KEY',    '6N0;G.FV4e3cLC_Yz@` GM99%R-&OSV64}Vk09brD+!N@lhxmi.OTrmp;~L#]2kq' );
define( 'NONCE_KEY',        's;HIKVl0.)6FD5*gA(fCr=?_0lb/QrS}ay5Sw35bM/w~O<fQDLh`VTV<|*%l7ZHX' );
define( 'AUTH_SALT',        'BI0Ix|#9Qy$KMJoX.ap~v}1T{*Ow#KKE3{t-t@j_+&_]Zlx#c7H+<YXElKx.,]Ka' );
define( 'SECURE_AUTH_SALT', '}sq_K##A!iIx:6(GG(i%b:=k9^9Y>cWVNetn_Pw-XPq!zU|.uNFM.#du}>lu(Auv' );
define( 'LOGGED_IN_SALT',   'n)iwUue2B@n32P41?EgW96O+rxP#V3@R&b`wVLw8gyRPp{^J$:49S-Gq(H TY[*-' );
define( 'NONCE_SALT',       'kFN,[BuMov&w%~f{|<VbvZ}8Judk%A,F)p{N%),dkO%*GTOR6SOcRjY*%}A)!,D5' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 't_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
