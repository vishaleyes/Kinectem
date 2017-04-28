<?php
/*
Plugin Name: BP Followers Email
Version: 1.0.6
Plugin URI: 
Description: This plugin adds followers email functionality to BuddyPress Team followers.
Author: 
Author URI: 
Network: true
WDP ID: 110

*/

//------------------------------------------------------------------------//

//---Config---------------------------------------------------------------//

//------------------------------------------------------------------------//

$bp_follower_email_current_version = '1.0.6';

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_follower_email_init() {
	if (class_exists('BP_Group_Extension'))
		require_once( dirname( __FILE__ ) . '/includes/bp-follower-email.php' );
}
add_action( 'bp_init', 'bp_follower_email_init' );

function bp_follower_email_localization() {
  // Load up the localization file if we're using WordPress in a different language
	// Place it in this plugin's "languages" folder and name it "groupemail-[value in wp-config].mo"
	load_plugin_textdomain( 'followeremail', FALSE, '/bp-followers-email/languages' );
}
add_action( 'plugins_loaded', 'bp_follower_email_localization' );

///////////////////////////////////////////////////////////////////////////
/* -------------------- Update Notifications Notice -------------------- */
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'install_plugins' ) )
      echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
  }
}
/* --------------------------------------------------------------------- */
?>