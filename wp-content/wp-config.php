<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache


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
define('DB_NAME', 'mrunal');


/** MySQL database username */
define('DB_USER', 'thisiskinectem');


/** MySQL database password */
define('DB_PASSWORD', 'AndrewKinectem2015');


/** MySQL hostname */
define('DB_HOST', 'kinectemdb.cphzym3fwrwc.us-east-1.rds.amazonaws.com');


/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');


/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

//For development server
/*define('AMAZON_KEY','AKIAJTTDBYLY2HY2T5JA');
define('AMAZON_SECRET','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');
define('REGION','us-west-2');
define('ANDRIOD_ARN', 'arn:aws:sns:us-west-2:901034789161:app/GCM/Kinectem-Android');
define('IOS_ARN', 'arn:aws:sns:us-west-2:901034789161:app/APNS_SANDBOX/Kinectem-dev');*/

// Production Server
define('AMAZON_KEY','AKIAJOWNVNJBM5N6LO7Q');
define('AMAZON_SECRET','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');
define('REGION','us-east-1');
define('ANDRIOD_ARN', 'arn:aws:sns:us-east-1:743445168844:app/GCM/Kinectem-Android');
//define('ANDRIOD_ARN', 'arn:aws:sns:us-east-1:743445168844:app/GCM/Kinectem-Android-Production');
//define('IOS_ARN', 'arn:aws:sns:us-east-1:743445168844:app/APNS_SANDBOX/Kinectem-development');
define('IOS_ARN', 'arn:aws:sns:us-east-1:743445168844:app/APNS/Kinectem-Production');


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'i5>ZtVo$W|J]M`olTQ*S-C|LF-<W3x@8Z^4hj:Zu*e8j@6$jQ~T?ebC8bf2>ZZ2(');

define('SECURE_AUTH_KEY',  'k)02;/0Y<x~BNa<heni=RXBN0.tUl?-7jD`AVQv&+-!3K/<NATK~D::Tf8W9xCxO');

define('LOGGED_IN_KEY',    'oV7&j(VWRplN<-,;^[{ljhENL02h~m+Ljmz:63a;$]-QFzHzDzlF/3.[_r1C*]7*');

define('NONCE_KEY',        '&{~x#n=*<1xb43x*;JEHsf <5`A)^77LHbh1qx#XK@_Y|E6)kPWW7FU)g^7tCp-G');

define('AUTH_SALT',        'sRQjigI9B-]:SSF~a-w|spkPAr~_|cK-BS2]isy^!k/[v.O*4.0G$H22f%.wMu:g');

define('SECURE_AUTH_SALT', '+mB$|s(< ?4L0*O<9_e+TV8Q-oVgJ5yQB.E&mNq|(Xz;4/shrw+EH.Xb+u<dQ+`v');

define('LOGGED_IN_SALT',   '/O0[DS$q+3v_e^/~u;QM%^t<#sZq}NeRzeO>q,p,Fi/yPk3rp@xRpGN<Z9Zddp0i');

define('NONCE_SALT',       'e3;onW^F:4n9-Y/g+>36B|G$Du2K-=sZ=*C_*qe.u4)Gi9^B3)k;cn@&m*BGPOg:');

define ( 'BP_AVATAR_FULL_WIDTH', 150 );
define ( 'BP_AVATAR_FULL_HEIGHT', 150 );

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

    




