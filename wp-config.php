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
 * @package Wo//rdPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define('DB_NAME', 'mrunal');
define('DB_NAME', 'kinectemtestdb');
//define('DB_NAME', 'kinectem-live');
/** MySQL database username */
//define('DB_USER', 'developer');
define('DB_USER', 'bypt');

/** MySQL database password */
//define('DB_PASSWORD', '5V6CaW8AjrwB');
define('DB_PASSWORD', 'Bypt@2012');

/** MySQL hostname */
//define('DB_HOST', 'kinectemdb.cphzym3fwrwc.us-east-1.rds.amazonaws.com');
define('DB_HOST', 'BYPTSERVER');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

//For development server
define('AMAZON_KEY','AKIAJTTDBYLY2HY2T5JA');
define('AMAZON_SECRET','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');
define('REGION','us-west-2');
define('ANDRIOD_ARN', 'arn:aws:sns:us-west-2:901034789161:app/GCM/Kinectem-Android');
define('IOS_ARN', 'arn:aws:sns:us-west-2:901034789161:app/APNS_SANDBOX/Kinectem-dev');
//define('IOSPASSENGER_ARN', 'arn:aws:sns:us-west-2:520491347273:app/APNS_SANDBOX/PinCab_Passenger_Push_Development');
//define('IOSPASSENGER_ARN', 'arn:aws:sns:us-west-2:520491347273:app/APNS_SANDBOX/PinCab_Passenger_Push_Development_New');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Tr]WblJ|z{%m3BF>oXb M])r@Df9}Xf_(b@W`Wu6^~RldLA&M)OgIh<|w0m[mSmo');
define('SECURE_AUTH_KEY',  '|2;XsFK?ADZ~,D?)+/NKFd+oizSi{#AA_6=+EUH;>XE2s~L/bs0AY`MAIgJM{$|%');
define('LOGGED_IN_KEY',    'n9ZHXIt@c$-~Hr+^&FY2=M={AHIQ_kN`8a.i@~hA)1gv,DGkqNq(L*^+JJFKVsEt');
define('NONCE_KEY',        'Pr$Z#t%T8b@lT{v:9>Npm`v`p7W9?7 b9LZsNh9$AZMaZe:HR)bk%:+:O &|Ulm5');
define('AUTH_SALT',        '}yS)p8JL7pE,KA-+5-t3g[)r@w{$|WiUD!O1Kv@rOOL;=H6_o#G+C3~G:D%{]l&+');
define('SECURE_AUTH_SALT', ';EMAq6n<c98&Eu4Cn2nD?u|Jn#M+LkMQ7c)M2^J/zhk,|.+21ee Dh1li(4_[80x');
define('LOGGED_IN_SALT',   'dhFnEKr3vb]DMhRb.o9qiE{J}{1V)+6[%${|tB9aMrnwcM`QP4XB:zht)WDCuo-Z');
define('NONCE_SALT',       'UJj-cVEy>4SlxW> pfT)m1CC#-&70--|wnV[aj}+x=@,p0?~TwXX`;qU$1UxIq7=');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
