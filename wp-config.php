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
define( 'DB_NAME', 'econ' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'zeV~~4n|nmgMyCY:c3glPL{c{w;K6%^lK%Lpy&C@:6rBD~vnzr|6(L-xr%J:f{1=' );
define( 'SECURE_AUTH_KEY',  'vtALq$Kpza`$m`_sE8)ckM~ql7+io_KEwQ|B`o<]poc|(i,`WR8w~Kc~/=;m&qq)' );
define( 'LOGGED_IN_KEY',    ',Qc(FKsE1|QKBv~yKqY^XVbPrAZIalLpvwI4w@(| YrqwN?P^#&5>3/Pkc/IihUs' );
define( 'NONCE_KEY',        '8F;=Fcd9D~zl>R-2c-Al!HyxWS:$f`ZXVY6gBG{JBn# E`^;w=+U?7nT=uo:w)_,' );
define( 'AUTH_SALT',        'T^(AV{);3~SMEgAJn8|:p6 !dD_kYakc]Xy7&yJ8P%1`}2VC)J E<P<L~oLY3, A' );
define( 'SECURE_AUTH_SALT', 'Eq&-l2A(ipBQ2D[K.j^+``*DGuU1![=oz;]$hdE+P(cyvZ{oMoYT@)L6{8YI0ij_' );
define( 'LOGGED_IN_SALT',   ';k{,vJ-qF,`6_pQZQb+;%t4e/*d,`mN}S|}t5a^V b5X^j#|NtR*#z3~17eF6>nE' );
define( 'NONCE_SALT',       'A}2Y6Q]ZV(@^}k`dv4-5%Jk=;P|/jU-av3BP2-}9;.&{]M{|0:W1snf5u41cliTT' );

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
