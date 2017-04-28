<?php
/*
 * Plugin Name: Mass Messaging in BuddyPress
 * Version: 2.1.3
 * Plugin URI: http://eliottrobson.co.uk/portfolio/mass-messaging-in-buddypress/
 * Description: Ever wanted to send a message to many people at once? Now you can, introducing - Mass Messaging.
 * Tags: buddypress
 * Author: Eliott Robson
 * Author URI: http://www.eliottrobson.co.uk/
 * Requires at least: 3.0.0
 * Tested up to: 4.4
 *
 * Text Domain: mass-messaging-in-buddypress
 * Domain Path: /lang/
 */

if(!defined('ABSPATH')) exit;

// Load plugin class files
require_once 'includes/instance.php';
require_once 'includes/settings.php';

// Load plugin libraries
require_once 'includes/lib/wordpress-api.php';
require_once 'includes/lib/buddypress-api.php';
require_once 'includes/lib/admin-api.php';

function Mass_Messaging_in_BuddyPress(){
	$instance = Mass_Messaging_in_BuddyPress::instance(__FILE__, '2.1.1');

	if(is_null($instance->settings)){
		$instance->settings = Mass_Messaging_in_BuddyPress_Settings::instance($instance);
	}
}
add_action('bp_include', 'Mass_Messaging_in_BuddyPress');