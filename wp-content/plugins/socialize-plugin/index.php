<?php
/*
Plugin Name: Socialize Plugin
Plugin URI: 
Description: A required plugin for Socialize theme you purchased from ThemeForest. It includes a number of features that you can still use if you switch to another theme.
Version: 1.3
Author: GhostPool
Author URI: http://themeforest.net/user/GhostPool/portfolio?ref=GhostPool
License: You should have purchased a license from ThemeForest.net
*/

if ( ! class_exists( 'Socialize_Plugin' ) ) {

	class Socialize_Plugin {

		public function __construct() {

			if ( ! class_exists( 'CustomSidebars' ) ) {
				require_once( sprintf( "%s/custom-sidebars/custom-sidebars.php", dirname( __FILE__ ) ) );
			}

			if ( ! post_type_exists( 'gp_portfolio' ) && ! class_exists( 'Socialize_Portfolio' ) ) {
				require_once( sprintf( "%s/post-types/portfolio-tax.php", dirname( __FILE__ ) ) );
				$Socialize_Portfolio = new Socialize_Portfolio();
			}

			if ( ! post_type_exists( 'gp_slide' ) && ! class_exists( 'Socialize_Slides' ) ) {
				require_once( sprintf( "%s/post-types/slide-tax.php", dirname( __FILE__ ) ) );
				$Socialize_Slides = new Socialize_Slides();
			}

			/*if ( function_exists( 'vc_set_as_theme' ) && ! class_exists( 'Socialize_Shortcodes' ) ) {
				require_once( sprintf( "%s/theme-shortcodes.php", dirname( __FILE__ ) ) );
				$Socialize_Shortcodes = new Socialize_Shortcodes();
			}*/
																								
		} 
		
		public static function socialize_activate() {} 		
		public static function socialize_deactivate() {}
		
	}
	
}

// User registration emails
if ( ! function_exists( 'wp_new_user_notification' ) ) {
	function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
		$user = new WP_User( $user_id );

		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );

		$message  = sprintf( esc_html__( 'New user registration on your blog %s:' ), get_option('blogname') ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( 'Username: %s' ), $user_login ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( 'Email: %s' ), $user_email ) . "\r\n";

		@wp_mail( get_option( 'admin_email' ), sprintf( esc_html__( '[%s] New User Registration' ), get_option( 'blogname' ) ), $message );

		if ( empty( $plaintext_pass ) )
			return;

		$message  = esc_html__( 'Hi there,' ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( "Welcome to %s! Please log in at:" ), get_option( 'blogname') ) . "\r\n\r\n";
		$message .= wp_login_url() . "\r\n";
		$message .= sprintf( esc_html__('Username: %s' ), $user_login ) . "\r\n";
		$message .= sprintf( esc_html__('Password: %s' ), $plaintext_pass ) . "\r\n\r\n";

		wp_mail( $user_email, sprintf( esc_html__( '[%s] Your username and password' ), get_option( 'blogname' ) ), $message );

	}
}

if ( class_exists( 'Socialize_Plugin' ) ) {

	register_activation_hook( __FILE__, array( 'Socialize_Plugin', 'socialize_activate' ) );
	register_deactivation_hook( __FILE__, array( 'Socialize_Plugin', 'socialize_deactivate' ) );

	$socialize_plugin = new Socialize_Plugin();

}

?>