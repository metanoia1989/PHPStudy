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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp-blog' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         'rCsN)^7|E&To`^vs6s(d$10MYB,_hs.tjZ<Z3)o,xulvHvErV5-h[[;d?9gkv=^J' );
define( 'SECURE_AUTH_KEY',  'at&V -g&+,qqogg4k]el{I0JQ7Z8Cgry2xNsScD:VgS`!pwJ7},(E)};gABCzR-U' );
define( 'LOGGED_IN_KEY',    ']Ta4L+^4z~A,MczHxSDaa<`.,#*3<P}[#<zlTt4yW+_q8kD5Y<D?fnLfeW$/fxkQ' );
define( 'NONCE_KEY',        '#uZiHM2r_V { ;3<|M|n$w?Moz_*KhJZN&=%@J18v_I,Cx9iE_`,@UMa-S}Tq]dR' );
define( 'AUTH_SALT',        'y!=%}l9&YNL~Vfa{U6EbX9qg@~CLQM(6.)Chh>C0-}!x,wZ~>JtukskL@D2p7y7E' );
define( 'SECURE_AUTH_SALT', 'j38] |kAqDpAKv@mqW@q1&nKUaInKkz)pLV(tZ=^:|{(daOjATM)r;B8TbY/erO9' );
define( 'LOGGED_IN_SALT',   ']!dkfo2h%I+D0~uVwN9z65.;AY-$03bk}jRW|#Bo<pRk?F|T7I[w*GJ4v[RWPPJ?' );
define( 'NONCE_SALT',       ':rU)^#e0}FW)n2;T=Ho,%=8Nnm08Sj.]PFjI:S7b_?%/Tiur^e Z_~>t kDp/dWR' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
