<?php
/*
  Plugin Name: Analytify - Reshaping Google Analytics for WordPress
  Plugin URI: http://wp-analytify.com/
  Description: Analytify makes Google Analytics simple for everything in WordPress (posts,pages etc). It presents the statistics in a beautiful way under the WordPress Posts/Pages at front end, backend and in its own Dashboard. This provides Stats from Country, Referrers, Social media, General stats, New visitors, Returning visitors, Exit pages, Browser wise and Top keywords. This plugin provides the Real Time statistics in a new UI that is easy to understand and looks good.
  Version: 1.0
  Author: Vishal Panchal
  Author URI: http://twitter.com/hiddenpearls
  License: GPLv2+
  Text Domain: wp-analytify-simple
*/

class WP_Analytify_Simple{

  // Constructor
    function __construct() {
		
		 if ( !class_exists( 'Google_Client' ) ) {

       
		require_once  'lib/vendor/autoload.php';
	    //require_once dirname(__FILE__) . '/lib/google-api-php-client/src/Google/Client.php';
        //require_once dirname(__FILE__) . '/lib/google-api-php-client/src/Google/Service/Analytics.php';
    }
		// Client ID : 380197976152-8re2fbcd4b3framlgc9nns9ioc48365l.apps.googleusercontent.com
		// Client Secret : Vba3DVaJYBj0Yg-T6CkaXGEL
		// API KEY : AIzaSyAHkELOvoimzKquZcG-1GZpfI26z-J5cqw

		$this->client = new Google_Client();
		$this->client->setApprovalPrompt( 'force' );
		$this->client->setAccessType( 'offline' );
		$this->client->setClientId( '380197976152-8re2fbcd4b3framlgc9nns9ioc48365l.apps.googleusercontent.com' );
		$this->client->setClientSecret( 'Vba3DVaJYBj0Yg-T6CkaXGEL' );
		$this->client->setRedirectUri( 'urn:ietf:wg:oauth:2.0:oob' );
		$this->client->setScopes( 'https://www.googleapis.com/auth/analytics' ); 
		$this->client->setDeveloperKey( 'AIzaSyAHkELOvoimzKquZcG-1GZpfI26z-J5cqw' ); 

		try{

			$this->service = new Google_Service_Analytics( $this->client );
			$this->wpa_connect();

		}
		catch ( Google_Service_Exception $e ) {

		}
		

        add_action( 'admin_menu', array( $this, 'wpa_add_menu' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'wpa_styles') );
        register_activation_hook( __FILE__, array( $this, 'wpa_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'wpa_uninstall' ) );
    }

    /*
      * Actions perform at loading of admin menu
      */
    function wpa_add_menu() {

        add_menu_page( 'Analytify simple', 'Analytify', 'manage_options', 'analytify-dashboard', array(
                          __CLASS__,
                         'wpa_page_file_path'
                        ), plugins_url('images/menu_icon.png', __FILE__),'2.2.9');

        add_submenu_page( 'analytify-dashboard', 'Analytify simple' . ' Dashboard', ' Dashboard', 'manage_options', 'analytify-dashboard', array(
                              __CLASS__,
                             'wpa_page_file_path'
                            ));

        add_submenu_page( 'analytify-dashboard', 'Analytify simple' . ' Settings', '<b style="color:#f9845b">Settings</b>', 'manage_options', 'analytify-settings', array(
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
	
	public function wpa_connect() {

    $access_token = get_option('access_token');

    if (! empty( $access_token )) {

        $this->client->setAccessToken( $access_token );

    } 
    else{

        $authCode = get_option( 'access_code' );

        if ( empty( $authCode ) ) return false;

        try {

            $accessToken = $this->client->authenticate( $authCode );
        }
        catch ( Exception $e ) {
            return false;
        }

        if ( $accessToken ) {

            $this->client->setAccessToken( $accessToken );
            update_option( 'access_token', $accessToken );

            return true;
        }
        else {

            return false;
        }
    }

    $this->token = json_decode($this->client->getAccessToken());
    return true;

	}
	
	
	/**
	 * Get profiles from user Google Analytics account profiles.
	 */
	public function pt_get_analytics_accounts() {

			try {

				if( get_option( 'access_token' ) !='' ) {
					$profiles = $this->service->management_profiles->listManagementProfiles( "~all", "~all" );
					return $profiles;
				}

				else{
					echo '<br /><p class="description">' . __( 'You must authenticate to access your web profiles.', 'wp-analytify' ) . '</p>';
				}

			}

			catch (Exception $e) {
				die('An error occured: ' . $e->getMessage() . '\n');
			}
	}


	/*
	 * This function grabs the data from Google Analytics
	 * For dashboard.
	 */

	 public function pa_get_analytics_dashboard($metrics, $startDate, $endDate, $dimensions = false, $sort = false, $filter = false, $limit = false){

			try{

				$this->service = new Google_Service_Analytics($this->client);
				$params        = array();

				if ($dimensions){
					$params['dimensions'] = $dimensions;
				}
				if ($sort){
					$params['sort'] = $sort;
				} 
				if ($filter){
					$params['filters'] = $filter;
				}
				if ($limit){
					$params['max-results'] = $limit;
				} 

				$profile_id = get_option("pt_webprofile_dashboard");
				if (!$profile_id){
					return false;
				}

				return $this->service->data_ga->get('ga:' . $profile_id, $startDate, $endDate, $metrics, $params);

			}

			catch ( Google_Service_Exception $e ) {

				// Show error message only for logged in users.
				if ( is_user_logged_in() ) echo $e->getMessage();

			}
		}

}
// Client ID : 380197976152-8re2fbcd4b3framlgc9nns9ioc48365l.apps.googleusercontent.com
// Client Secret : Vba3DVaJYBj0Yg-T6CkaXGEL
// API KEY : AIzaSyAHkELOvoimzKquZcG-1GZpfI26z-J5cqw
//
new WP_Analytify_Simple();
?>