<?php
/*
  Plugin Name: Admin Plug - BYPT
  Plugin URI: http://wp-bypt.in/
  Description: My first wordpress admin plugins.
  Version: 1.0
  Author: Vishal Panchal
  Author URI: http://twitter.com/hiddenpearls
  License: GPLv2+
  Text Domain: wp-admin-plug
*/

class WP_Admin_Plug{

  // Constructor
    function __construct() {
		
		add_action( 'admin_menu', array( $this, 'wpa_add_menu' ));
		//add_action( 'admin_enqueue_scripts', array( $this, 'wpa_styles') );
        register_activation_hook( __FILE__, array( $this, 'wpa_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'wpa_uninstall' ) );
    }

    /*
      * Actions perform at loading of admin menu
      */
    function wpa_add_menu() {

        add_menu_page( 'Bypt Users', 'BYPT', 'manage_options', 'bypt-dashboard','test_init');

        add_submenu_page( 'bypt-dashboard', 'BYPT Users' . ' Dashboard', ' Dashboard', 'manage_options', 'bypt-dashboard', array(
                              __CLASS__,
                             'wpa_page_file_path'
                            ));

        add_submenu_page( 'bypt-dashboard', 'Setting' . ' Settings', '<b style="color:#f9845b">Settings</b>', 'manage_options', 'analytify-settings', array(
                              __CLASS__,
                             'wpa_page_file_path'
                            ));
    }
	
	/**
	 * Styling: loading stylesheets for the plugin.
	 */
	public function wpa_styles( $page ) {

		wp_enqueue_style( 'wp-analytify-style', plugins_url('css/wp-analytify-style.css', __FILE__));
	}

    /*
     * Actions perform on loading of menu pages
     */
    function wpa_page_file_path() {



    }

    /*
     * Actions perform on activation of plugin
     */
    function wpa_install() {



    }

    /*
     * Actions perform on de-activation of plugin
     */
    function wpa_uninstall() {



    }
	
	
	
	

}

function test_init()
	{
		global $wpdb;
		$sql = "select ID,display_name,user_login from wp_users";
		$members = $wpdb->get_results($sql);
		echo "<h1>Hello World</h1>";
		echo "<p>klasdfj sakjdfjaskldfj;laskf lkjfdlk asjflkas dflkasjdflkajsfdlksadf</p>";
		
		echo "<table class='wp-list-table widefat fixed users'>";
		echo "<tr><th>ID</th><th>Username</th><th>Nickname</th><th>Email</th><th>Display</th><th>Date</th></tr>";
		$i=0;
		foreach($members as $row){
			if($i%2==0){
				echo "<tr>";
			} else {
				echo "<tr class='alternate'>";
			}
			
			echo "<td>".$row->ID."</td>";
			echo "<td>".$row->user_login."</td>";
			echo "<td>".$row->user_nickname."</td>";
			echo "<td>".$row->user_email."</td>";
			echo "<td>".$row->display_name."</td>";
			echo "<td>".$row->user_registered."</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
		//echo "<table class='wp-list-table widefat fixed users'><tr class='alternate'><td>Name</td><td>Vishal Panchal</td></tr><tr><td>Designation</td><td>Software Developer</td></tr></table>";
	}
// Client ID : 380197976152-8re2fbcd4b3framlgc9nns9ioc48365l.apps.googleusercontent.com
// Client Secret : Vba3DVaJYBj0Yg-T6CkaXGEL
// API KEY : AIzaSyAHkELOvoimzKquZcG-1GZpfI26z-J5cqw
//
new WP_Admin_Plug();
?>