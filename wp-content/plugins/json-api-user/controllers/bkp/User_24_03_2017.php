<?php

/*
  Controller name: User
  Controller description: User Registration, Authentication, User Info, User Meta, FB Login, BuddyPress xProfile Fields methods
  Controller Author: Ali Qureshi
  Controller Author Twitter: @parorrey
  Controller Author Website: parorrey.com

*/
require_once(ABSPATH.'aws.phar');
require_once(ABSPATH.'aws/vendor/autoload.php');
require_once(ABSPATH.'sns.class.php');

use Aws\Sns\SnsClient;
class JSON_API_User_Controller {

  /**
     * Returns an Array with registered userid & valid cookie
     * @param String username: username to register
     * @param String email: email address for user registration
	 * @param String user_pass: user_pass to be set (optional)
     * @param String display_name: display_name for user
     */
public function __construct() {
		global $json_api;
		// allow only connection over https. because, well, you care about your passwords and sniffing.
		// turn this sanity-check off if you feel safe inside your localhost or intranet.
		// send an extra POST parameter: insecure=cool
		if (empty($_SERVER['HTTPS']) ||
		    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off')) {
			if (empty($_REQUEST['insecure']) || $_REQUEST['insecure'] != 'cool') {
				$json_api->error("SSL is not enabled. Either use _https_ or provide 'insecure' var as insecure=cool to confirm you want to use http protocol.");
			}
		}


	}

public function info(){

	  	global $json_api;

   		return array(
				"version" => JAU_VERSION
		   );

	  }

public function register(){
	error_reporting(E_ALL);
	global $json_api;
  	$notify = "";
   if (!$json_api->query->username) {

			echo json_encode(array('status' => 'error', 'message' => "You must include 'username' var in your request.", 'data' => array()));
    exit;
		}
	else $username = sanitize_user( $json_api->query->username );


  if (!$json_api->query->email) {

			echo json_encode(array('status' => 'error', 'message' => "You must include 'email' var in your request. ", 'data' => array()));
    exit;
		}
	else $email = sanitize_email( $json_api->query->email );

 if (!$json_api->query->nonce) {

			echo json_encode(array('status' => 'error', 'message' => "You must include 'nonce' var in your request. Use the 'get_nonce' Core API method. ", 'data' => array()));
    exit;
		}
 else $nonce =  sanitize_text_field( $json_api->query->nonce ) ;

 if (!$json_api->query->display_name) {

			echo json_encode(array('status' => 'error', 'message' => "You must include 'display_name' var in your request. ", 'data' => array()));
    exit;
		}
	else $display_name = sanitize_text_field( $json_api->query->display_name );

	if (!$json_api->query->device_type) {

			echo json_encode(array('status' => 'error', 'message' => "You must include 'device_type' var in your request.", 'data' => array()));
    exit;
		}
	else $device_type = sanitize_user( $json_api->query->device_type );

	if (!$json_api->query->device_token) {

			echo json_encode(array('status' => 'error', 'message' => 'You must include "device_token" var in your request. ', 'data' => array()));
    exit;
		}
	else $device_token = sanitize_user( $json_api->query->device_token );


$user_pass = sanitize_text_field( $_REQUEST['user_pass'] );

if ($json_api->query->seconds) 	$seconds = (int) $json_api->query->seconds;

		else $seconds = 1209600;//14 days

//Add usernames we don't want used

$invalid_usernames = array( 'admin' );

//Do username validation

$nonce_id = $json_api->get_nonce_id('user', 'register');

 if( !wp_verify_nonce($json_api->query->nonce, $nonce_id) ) {


	echo json_encode(array('status' => 'error', 'message' => 'Invalid access, unverifiable "nonce" value. Use the "get_nonce" Core API method.', 'data' => array()));
    exit;
        }

 else {

	if ( !validate_username( $username ) || in_array( $username, $invalid_usernames ) ) {


  	echo json_encode(array('status' => 'error', 'message' => 'Username is invalid.', 'data' => array()));
    exit;

        }

    elseif ( username_exists( $username ) ) {
	echo json_encode(array('status' => 'error', 'message' => 'Username already exists.', 'data' => array()));
    exit;

           }

	else{


	if ( !is_email( $email ) ) {
   	 	echo json_encode(array('status' => 'error', 'message' => "E-mail address is invalid.", 'data' => array()));
    	exit;
             }
    elseif (email_exists($email)) {

	 echo json_encode(array('status' => 'error', 'message' => "E-mail address is already in use.", 'data' => array()));
     exit;

          }

else {

	//Everything has been validated, proceed with creating the user

//Create the user

if( !isset($_REQUEST['user_pass']) ) {

	$user_pass = wp_generate_password();
	$_REQUEST['user_pass'] =  $user_pass;
}

 $_REQUEST['user_login'] = $username;
 $_REQUEST['user_email'] = $email;

$allowed_params = array('user_login', 'user_email', 'user_pass', 'display_name', 'user_nicename', 'user_url', 'nickname', 'first_name',
                         'last_name', 'description', 'rich_editing', 'user_registered', 'role', 'jabber', 'aim', 'yim',
						 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front'
                   );

foreach($_REQUEST as $field => $value){

	if( in_array($field, $allowed_params) ) $user[$field] = trim(sanitize_text_field($value));

    }
$user['role'] = get_option('default_role');
$user_id = wp_insert_user( $user );

 if(isset($_REQUEST['first_name']))
 {
 	$field_label = "First Name";
 	$values = $_REQUEST['first_name'];
  	$result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true );
 }
 if(isset($_REQUEST['last_name']))
 {
 	$field_label = "Last Name";
 	$values = $_REQUEST['last_name'];
  	$result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true );
 }
 if(isset($_REQUEST['email']))
 {
 	$field_label = "Email";
 	$values = $_REQUEST['email'];
  	$result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true );
 }

/*Send e-mail to admin and new user -
You could create your own e-mail instead of using this function*/

if( isset($_REQUEST['user_pass']) && isset($_REQUEST['notify']) &&  $_REQUEST['notify']=='no') {
	$notify = '';
  }elseif(isset($_REQUEST['notify']) && $_REQUEST['notify']!='no') $notify = $_REQUEST['notify'];


if($user_id) wp_new_user_notification( $user_id, '',$notify );


			}
		}
   }

	if (isset($device_type) && !empty($device_type)) {

                if ($device_type == 1) { // device is Android
                    $endpointArn = $this->createEndPointForSNS($device_token, ANDRIOD_ARN);
                    $postdata['device_type'] = $device_type;
                    $postdata['endpointArn'] = $endpointArn;
                } else if ($device_type == 2) {// device is ios / iphone
                    $endpointArn = $this->createEndPointForSNS($device_token, IOS_ARN);

					$postdata['device_type'] = $device_type;
                    $postdata['endpointArn'] = $endpointArn;
                } else {

                    echo json_encode(array('status' => '-61', 'message' => 'deviceType must be 1 and 2.', 'data' => array()));
                    exit;
                }


				$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				// Check connection
				if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);

				}

				$sql = "UPDATE wp_users SET device_token = '$endpointArn', device_type=$device_type WHERE ID = $user_id";
	    		$res2 = $conn->query($sql);

            } else {
                echo json_encode(array('status' => '-6', 'message' => 'device_type parameter is required.', 'data' => array()));
                exit;
    }
	$expiration = time() + apply_filters('auth_cookie_expiration', $seconds, $user_id, true);

	$cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');


    bp_update_user_meta($user_id,'notification_activity_new_mention','yes');
    bp_update_user_meta($user_id,'notification_activity_new_reply','yes');
    bp_update_user_meta($user_id,'notification_messages_new_message','yes');
    bp_update_user_meta($user_id,'notification_groups_invite','yes');
    bp_update_user_meta($user_id,'notification_groups_group_updated','yes');
    bp_update_user_meta($user_id,'notification_groups_admin_promotion','yes');
    bp_update_user_meta($user_id,'notification_groups_membership_request','yes');
    bp_update_user_meta($user_id,'ass_self_post_notification','yes');
    bp_update_user_meta($user_id,'notification_starts_following','yes');
    bp_update_user_meta($user_id,'notification_friends_friendship_request','yes');
    bp_update_user_meta($user_id,'notification_friends_friendship_accepted','yes');
    bp_update_user_meta($user_id,'notification_groups_email_send','yes');
    bp_update_user_meta($user_id,'notification_groups_email_send','yes');
    bp_update_user_meta($user_id,'notification_messages_new_notice','yes');

    $user = new BP_Core_User($user_id);

    $arg = array(
        'user_id'   => $user_id,
        'component' => 'activity',
        'type'      => 'new_member',
        'action'  => '<a href="'.$user->user_url.'" title="'.$_REQUEST['user_login'].'">'.$username.'</a> <span class=\"activity-bold\"> became a registered member.</span>',
        'content'   => ''
    );
    $activity_id = bp_activity_add($arg);

 return array(
          "cookie" => $cookie,
		  "user_id" => $user_id
		  );

  }

public function get_avatar(){

	  	global $json_api;

if (function_exists('bp_is_active')) {

    if (!$json_api->query->user_id) {

			echo json_encode(array('status' => 'error', 'message' => "You must include 'user_id' var in your request. ", 'data' => array()));
    exit;
		}

	  if (!$json_api->query->type) {
		  echo json_encode(array('status' => 'error', 'message' => "You must include 'type' var in your request. possible values 'full' or 'thumb' ", 'data' => array()));
    exit;

		}


$avatar	= bp_core_fetch_avatar ( array( 'item_id' => $json_api->query->user_id, 'type' => $json_api->query->type, 'html'=>false ));

        return array('avatar'=>$avatar);
   } else {


	   echo json_encode(array('status' => 'error', 'message' => "You must install and activate BuddyPress plugin to use this method.", 'data' => array()));
    exit;

	  }

	 }

public function get_userinfo(){

	  	global $json_api;

    if (!$json_api->query->user_id) {

			 echo json_encode(array('status' => 'error', 'message' => "You must include 'user_id' var in your request. ", 'data' => array()));
    exit;
		}

		$user = get_userdata($json_api->query->user_id);

        preg_match('|src="(.+?)"|', get_avatar( $user->ID, 32 ), $avatar);

		$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' => $user->ID ) );

		return array(
				"id" => $user->ID,
				//"username" => $user->user_login,
				"nicename" => $user->user_nicename,
				//"email" => $user->user_email,
				"url" => $user->user_url,
				"displayname" => $user->display_name,
				"firstname" => $user->user_firstname,
				"lastname" => $user->last_name,
				"nickname" => $user->nickname,
				"avatar" => $avatar[1],
				"cover_image" => $cover_image_url
		   );

	  }

public function retrieve_password(){

    global $wpdb, $json_api, $wp_hasher;

   if (!$json_api->query->user_login) {


			echo json_encode(array('status' => 'error', 'message' => "You must include 'user_login' var in your request. ", 'data' => array()));
    exit;

		}

    $user_login = $json_api->query->user_login;

  if ( strpos( $user_login, '@' ) ) {

        $user_data = get_user_by( 'email', trim( $user_login ) );

        if ( empty( $user_data ) )
    	{

    		echo json_encode(array('status' => 'error', 'message' => "Your email address not found! ", 'data' => array()));	exit;
    	}





    } else {

        $login = trim($user_login);

        $user_data = get_user_by('login', $login);

    }


    // redefining user_login ensures we return the right case in the email

    $user_login = $user_data->user_login;

    $user_email = $user_data->user_email;


    do_action('retrieve_password', $user_login);


    $allow = apply_filters('allow_password_reset', true, $user_data->ID);

    if ( ! $allow )  $json_api->error("password reset not allowed! ");

    elseif ( is_wp_error($allow) )  $json_api->error("An error occured! ");



    $key = wp_generate_password( 20, false );

	do_action( 'retrieve_password_key', $user_login, $key );



    if ( empty( $wp_hasher ) ) {

        require_once ABSPATH . 'wp-includes/class-phpass.php';

        $wp_hasher = new PasswordHash( 8, true );

    }


    $hashed = time() . ':' . $wp_hasher->HashPassword( $key );

    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

    $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";

    $message .= network_home_url( '/' ) . "\r\n\r\n";

    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";

    $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";

    $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";


	$message .= network_site_url() . "?action=reset_pwd&key=$hashed&login=" . rawurlencode( $user_login ) . "\r\n\r\n";

    //$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";



    if ( is_multisite() )

        $blogname = $GLOBALS['current_site']->site_name;

    else

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);



    $title = sprintf( __('[%s] Password Reset'), $blogname );



    $title = apply_filters('retrieve_password_title', $title);

    $message = apply_filters('retrieve_password_message', $message, $key);



    if ( $message && !wp_mail($user_email, $title, $message) )
	{

	   echo json_encode(array('status' => 'error', 'message' => "The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...", 'data' => array()));
    exit;

	}
	else
	{
   return array(

    "msg" => 'Link for password reset has been emailed to you. Please check your email.',

		  );
	}
     }

public function validate_auth_cookie() {

		global $json_api;

		if (!$json_api->query->cookie) {


			echo json_encode(array('status' => 'error', 'message' => "You must include a 'cookie' authentication cookie. Use the `create_auth_cookie` method.", 'data' => array()));
    exit;

		}

    	$valid = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');

		return array(

			"valid" => $valid

		);

	}

public function generate_auth_cookie() {
		error_reporting(E_ALL);
		global $json_api;

		foreach($_POST as $k=>$val) {
			if (isset($_POST[$k])) {
				$json_api->query->$k = $val;
			}
		}


		if (!$json_api->query->username && !$json_api->query->email) {


			echo json_encode(array('status' => 'error', 'message' => "Invalid username/email and/or password", 'data' => array()));
    exit;

		}


		if (!isset($_REQUEST['password'])) {

			echo json_encode(array('status' => 'error', 'message' => "You must include a 'password' var in your request.", 'data' => array()));
    exit;

		}

		if(!isset($_POST['device_type']))
		{

			echo json_encode(array('status' => 'error', 'message' => "You must include a 'device_type' var in your request.", 'data' => array()));
            exit;
		}
		if(!isset($_POST['device_token']))
		{

			echo json_encode(array('status' => 'error', 'message' => "You must include a 'device_token' var in your request.", 'data' => array()));
    exit;
		}

		if ($json_api->query->seconds) 	$seconds = (int) $json_api->query->seconds;

		else $seconds = 1209600;//14 days

       if ( $json_api->query->email ) {


		 if ( is_email(  $json_api->query->email ) ) {
		  if( !email_exists( $json_api->query->email))  {

			 echo json_encode(array('status' => 'error', 'message' => "email does not exist.", 'data' => array()));
    exit;
			  }
		 }else {
		 echo json_encode(array('status' => 'error', 'message' => "Invalid email address.", 'data' => array()));
    exit;
		 }

        $user_obj = get_user_by( 'email', $json_api->query->email );


		$user = wp_authenticate($user_obj->data->user_login, $json_api->query->password);
    }else {

		 $user = wp_authenticate($json_api->query->username, $json_api->query->password);
		}


    	if (is_wp_error($user)) {

    		echo json_encode(array('status' => 'error', 'message' => "Invalid username/email and/or password.", 'data' => array()));
    die;


    		remove_action('wp_login_failed', $json_api->query->username);

    	}


    	$expiration = time() + apply_filters('auth_cookie_expiration', $seconds, $user->ID, true);

    	$cookie = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');

		preg_match('|src="(.+?)"|', get_avatar( $user->ID, 512 ), $avatar);
		$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' => $user->ID ) );

		// Updated device_token and device_type in wp_user table
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection;
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		if (isset($_POST['device_type']) && !empty($_POST['device_type'])) {

				$device_type = $_POST['device_type'] ;
				$device_token = $_POST['device_token'] ;
                if ($device_type == 1) { // device is Android
                    $endpointArn = $this->createEndPointForSNS($device_token, ANDRIOD_ARN);
                    $postdata['device_type'] = $device_type;
                    $postdata['endpointArn'] = $endpointArn;
                } else if ($device_type == 2) {// device is ios / iphone

                    $endpointArn = $this->createEndPointForSNS($device_token, IOS_ARN);

					$postdata['device_type'] = $device_type;
                    $postdata['endpointArn'] = $endpointArn;
                } else {

                    $this->response(array('status' => '-61', 'message' => 'device_type must be 1 and 2.', 'data' => array()));
                    exit;
                }

				$sql = "UPDATE wp_users SET device_token = '".$endpointArn."', device_type=$device_type WHERE ID = ".$user->ID;
	    		$res2 = $conn->query($sql);


            } else {
                $this->response(array('status' => '-6', 'message' => 'device_type parameter is required.', 'data' => array()));
                exit;
    }

		//$sql = "select * from wp_users WHERE ID = ".$user->ID;
	    //$res3 = $conn->query($sql);
		//$userData = $res3->fetch_assoc();
		$userData = new BP_Core_User($user->ID);
		$lastname = "";
		if(isset($userData->profile_data['Last Name']['field_data']))
		{
			$lastname = $userData->profile_data['Last Name']['field_data'];
		}
		return array(
			"cookie" => $cookie,
			"cookie_name" => LOGGED_IN_COOKIE,
			"user" => array(
				"id" => $user->ID,
				"username" => $user->user_login,
				"nicename" => $user->user_nicename,
				"email" => $user->user_email,
				"url" => $user->user_url,
				"registered" => $user->user_registered,
				"displayname" => $user->display_name,
				"firstname" => $user->user_firstname,
				"lastname" =>  $lastname,
				"nickname" => $user->nickname,
				"description" => $user->user_description,
				"capabilities" => $user->wp_capabilities,
				"avatar" => $avatar[1],
				"cover_image" => $cover_image_url

			),
		);
	}

public function get_currentuserinfo() {

		global $json_api;

		if (!$json_api->query->cookie) {

			echo json_encode(array('status' => 'error', 'message' => "You must include a 'cookie' var in your request. Use the `generate_auth_cookie` Auth API method.", 'data' => array()));
    		die;

		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');


		if (!$user_id) {
			echo json_encode(array('status' => 'error', 'message' => "Invalid authentication cookie. Use the `generate_auth_cookie` method.", 'data' => array()));
    		die;
		}

		$user = get_userdata($user_id);

        preg_match('|src="(.+?)"|', get_avatar( $user->ID, 32 ), $avatar);

		$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' => $user->ID ) );

		return array(

			"user" => array(

				"id" => $user->ID,

				"username" => $user->user_login,

				"nicename" => $user->user_nicename,

				"email" => $user->user_email,

				"url" => $user->user_url,

				"registered" => $user->user_registered,

				"displayname" => $user->display_name,

				"firstname" => $user->user_firstname,

				"lastname" => $user->last_name,

				"nickname" => $user->nickname,

				"description" => $user->user_description,

				"capabilities" => $user->wp_capabilities,

				"avatar" => $avatar[1],

				"cover_image" => $cover_image_url

			)

		);

	}

public function get_user_meta() {

	  global $json_api;

	  if (!$json_api->query->cookie) {
			echo json_encode(array('status' => 'error', 'message' => "You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.", 'data' => array()));
    		die;

		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');

		if (!$user_id){

				echo json_encode(array('status' => 'error', 'message' => "Invalid cookie. Use the `generate_auth_cookie` method.", 'data' => array()));
				die;
		}
 		$meta_key = sanitize_text_field($json_api->query->meta_key);


		if($meta_key) $data[$meta_key] = get_user_meta(  $user_id, $meta_key);
		else {
		// Get all user meta data for $user_id
			$meta = get_user_meta( $user_id );

			// Filter out empty meta data
			$data = array_filter( array_map( function( $a ) {
					return $a[0];
					}, $meta ) );

     	 }
//d($data);
	   return $data;


	  }

public function update_user_meta() {

	  global $json_api;

	   if (!$json_api->query->cookie) {

			echo json_encode(array('status' => 'error', 'message' => "You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.", 'data' => array()));
				die;
		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');

	if (!$user_id) {
			echo json_encode(array('status' => 'error', 'message' => "Invalid cookie. Use the `generate_auth_cookie` method.", 'data' => array()));
			die;

	}

   if (!$json_api->query->meta_key){

		echo json_encode(array('status' => 'error', 'message' => "You must include a 'meta_key' var in your request.", 'data' => array()));
		die;
   }
		else $meta_key = $json_api->query->meta_key;

   if (!$json_api->query->meta_value) {
			echo json_encode(array('status' => 'error', 'message' => "You must include a 'meta_value' var in your request. You may provide multiple values separated by comma for 'meta_value' var.", 'data' => array()));
			die;
		}
		else $meta_value = sanitize_text_field($json_api->query->meta_value);

  if( strpos($meta_value,',') !== false ) {
		$meta_values = explode(",", $meta_value);
	   $meta_values = array_map('trim',$meta_values);

	   $data['updated'] = update_user_meta(  $user_id, $meta_key, $meta_values);
	   }
 else $data['updated'] = update_user_meta(  $user_id, $meta_key, $meta_value);

	   return $data;

	  }

public function delete_user_meta() {

	  global $json_api;

	   if (!$json_api->query->cookie) {
			echo json_encode(array('status' => 'error', 'message' => "You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.", 'data' => array()));
			die;
		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');

	if (!$user_id) {
			echo json_encode(array('status' => 'error', 'message' => "Invalid cookie. Use the `generate_auth_cookie` method.", 'data' => array()));
			die;
	}


   if (!$json_api->query->meta_key) {
   			echo json_encode(array('status' => 'error', 'message' => "You must include a 'meta_key' var in your request.", 'data' => array()));
			die;
   }

		else $meta_key = $json_api->query->meta_key;

   if (!$json_api->query->meta_value) {
			echo json_encode(array('status' => 'error', 'message' => "You must include a 'meta_value' var in your request.", 'data' => array()));
			die;
		}
		else $meta_value = sanitize_text_field($json_api->query->meta_value);


		$data['deleted'] = delete_user_meta(  $user_id, $meta_key, $meta_value);

	   return $data;

	  }

public function update_user_meta_vars() {

	  global $json_api;

	  if (!$json_api->query->cookie) {

			echo json_encode(array('status' => 'error', 'message' => "You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.", 'data' => array()));
			die;
		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
//	echo '$user_id: '.$user_id;

		if (!$user_id) {
			$json_api->error("Invalid cookie. Use the `generate_auth_cookie` method.");
		}

	if( sizeof($_REQUEST) <=1) $json_api->error("You must include one or more vars in your request to add or update as user_meta. e.g. 'name', 'website', 'skills'. You must provide multiple meta_key vars in this format: &name=Ali&website=parorrey.com&skills=php,css,js,web design. If any field has the possibility to hold more than one value for any multi-select fields or check boxes, you must provide ending comma even when it has only one value so that it could be added in correct array format to distinguish it from simple string var. e.g. &skills=php,");

//d($_REQUEST);
foreach($_REQUEST as $field => $value){

	if($field=='cookie') continue;

	$field_label = str_replace('_',' ',$field);

	if( strpos($value,',') !== false ) {
		$values = explode(",", $value);
	   $values = array_map('trim',$values);
	   }
	else $values = trim($value);
	//echo 'field-values: '.$field.'=>'.$value;
	//d($values);

   $result[$field_label]['updated'] =  update_user_meta(  $user_id, $field, $values);

}

	 return $result;


  }

public function xprofile() {

	  global $json_api;

if (function_exists('bp_is_active')) {

	  if (!$json_api->query->user_id) {
			$json_api->error("You must include a 'user_id' var in your request.");
		}
		else $user_id = $json_api->query->user_id;


   if (!$json_api->query->field) {

			echo json_encode(array('status' => 'error', 'message' => "You must include a 'field' var in your request. Use 'field=default' for all default fields.", 'data' => array()));
			die;
		}
	  elseif ($json_api->query->field=='default') {
			$field_label='First Name,Last Name,Bio,Sports,GPA,Email,School,Class rank,User role,Athlete,Coach,Fan,Organization,Gender,Birthday,Height,Your State,Your City,Graduation Year,SAT Score';/*you should add your own field labels here for quick viewing*/
		}
		else $field_label = sanitize_text_field($json_api->query->field);


  $fields = explode(",", $field_label);

  if(is_array($fields)){

	  foreach($fields as $k){

		 $fields_data[$k] = xprofile_get_field_data( $k, $user_id );
		  }
		 $current_visibility_levels = bp_get_user_meta( $user_id, 'bp_xprofile_visibility_levels', true );

		$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' => $user_id ) );
		$fields_data['cover_image'] = $cover_image_url;
		$fields_data['visibility_level'] = $current_visibility_levels;
    if(!is_array($fields_data['Sports'])){
      $fields_data['Sports'] =	array();
		}
    if(isset($fields_data['Sports'][0]) && $fields_data['Sports'][0] == '')
    {
      $fields_data['Sports'] =	array();
    }
    if(!empty($fields_data)){
	    	return $fields_data;
		} else {
			return array();
		}

	  }
	  else
	  {
		   $json_api->error("Invalid user id passed.");
	  }

   }

  else {

	  $json_api->error("You must install and activate BuddyPress plugin to use this method.");

	  }

  }




public function xprofile_update() {

	  global $json_api;

if (function_exists('bp_is_active')) {

	  if (!$json_api->query->cookie) {
			$json_api->error("You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.");
		}

		$user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
//	echo '$user_id: '.$user_id;

		if (!$user_id) {
			$json_api->error("Invalid cookie. Use the `generate_auth_cookie` method.");
		}


foreach($_REQUEST as $field => $value){

	if($field=='cookie') continue;

	$field_label = str_replace('_',' ',$field);

	if( strpos($value,',') !== false ) {
		$values = explode(",", $value);
	   $values = array_map('trim',$values);
	   }
	else $values = trim($value);
	//echo 'field-values: '.$field.'=>'.$value;
	//d($values);
if($field_label == "Sports")
{
     if(!is_array($values))
     {
       $values = array($values);
     }
}


  $result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true );
  //var_dump($result[$field_label]['updated']);
  if(isset($field_label) && $field_label == "First Name"){
    update_user_meta($user_id,'first_name',$value);
	$sql = "UPDATE wp_users SET display_name = '".$value."' WHERE ID = ".$user_id;
	$res2 = $conn->query($sql);
  }
  if(isset($field_label) && $field_label == "Last Name"){
    update_user_meta($user_id,'last_name',$value);
  }

  // Updated device_token and device_type in wp_user table
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

}

	 return $result;
   }

  else {

	  $json_api->error("You must install and activate BuddyPress plugin to use this method.");

	  }

  }

public function fb_connect(){

	    global $json_api;

		if ($json_api->query->fields) {

			$fields = $json_api->query->fields;

		}else $fields = 'id,name,first_name,last_name,email';

		if ($json_api->query->ssl) {
			 $enable_ssl = $json_api->query->ssl;
		}else $enable_ssl = true;

	if (!$json_api->query->access_token) {
			$json_api->error("You must include a 'access_token' variable. Get the valid access_token for this app from Facebook API.");
		}else{

$url='https://graph.facebook.com/me/?fields='.$fields.'&access_token='.$json_api->query->access_token;

	//  Initiate curl
$ch = curl_init();
// Enable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

	$result = json_decode($result, true);

   if(isset($result["email"])){

            $user_email = $result["email"];
           	$email_exists = email_exists($user_email);

			if($email_exists) {
				$user = get_user_by( 'email', $user_email );
			  $user_id = $user->ID;
			  $user_name = $user->user_login;
			 }



		    if ( !$user_id && $email_exists == false ) {

			  $user_name = strtolower($result['first_name'].'.'.$result['last_name']);

				while(username_exists($user_name)){
				$i++;
				$user_name = strtolower($result['first_name'].'.'.$result['last_name']).'.'.$i;

					}

			 $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
      		   $userdata = array(
                           'user_login'    => $user_name,
						   'user_email'    => $user_email,
                           'user_pass'  => $random_password,
						   'display_name'  => $result["name"],
						   'first_name'  => $result['first_name'],
						   'last_name'  => $result['last_name'],
                           'device_type'  => isset($_REQUEST['device_type'])?$_REQUEST['device_type']:'',
                           'device_token'  => isset($_REQUEST['device_token'])?$_REQUEST['device_token']:''
                                     );

                   $user_id = wp_insert_user( $userdata ) ;

                if(isset($result['first_name']))
                {
                    $field_label = "First Name";
                    $values = $result['first_name'];
                    $result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true );
                }
                if(isset($result['last_name']))
                {
                    $field_label = "Last Name";
                    $values = $result['last_name'];
                    $result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true );
                }
                if(isset($user_email))
                {
                    $field_label = "Email";
                    $values = $user_email;
                    $result[$field_label]['updated'] = xprofile_set_field_data( $field_label,  $user_id, $values, $is_required = true );
                }


                if($user_id) $user_account = 'user registered.';

            } else {

				 if($user_id) $user_account = 'user logged in.';
				}

			 $expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user_id, true);
    	     $cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');

		$response['msg'] = $user_account;
		$response['wp_user_id'] = $user_id;
		$response['cookie'] = $cookie;
		$response['user_login'] = $user_name;
		$response['user_email'] =  $user_email;
		$response['display_name'] = $result["name"];
		$response['first_name'] =  $result['first_name'];
		$response['last_name'] = $result['last_name'];

		}
		else {
			$response['msg'] = "Your 'access_token' did not return email of the user. Without 'email' user can't be logged in or registered. Get user email extended permission while joining the Facebook app.";

			}

	}

return $response;

	  }

public function post_comment(){
   global $json_api;

  if (!$json_api->query->cookie) {
			$json_api->error("You must include a 'cookie' var in your request. Use the `generate_auth_cookie` method.");
		}

  $user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');

		if (!$user_id) {
			$json_api->error("Invalid cookie. Use the `generate_auth_cookie` method.");
		}

 if ( !$json_api->query->post_id ) {
  $json_api->error("No post specified. Include 'post_id' var in your request.");
  } elseif (!$json_api->query->content ) {
  $json_api->error("Please include 'content' var in your request.");
  }

  if (!$json_api->query->comment_status ) {
  $json_api->error("Please include 'comment_status' var in your request. Possible values are '1' (approved) or '0' (not-approved)");
  }else $comment_approved = $json_api->query->comment_status;

$user_info = get_userdata(  $user_id );

 $time = current_time('mysql');
 $agent = $_SERVER['HTTP_USER_AGENT'];
 $ip=$_SERVER['REMOTE_ADDR'];

    $data = array(
  'comment_post_ID' => $json_api->query->post_id,
  'comment_author' => $user_info->user_login,
  'comment_author_email' => $user_info->user_email,
  'comment_author_url' => $user_info->user_url,
  'comment_content' => $json_api->query->content,
  'comment_type' => '',
  'comment_parent' => 0,
  'user_id' => $user_info->ID,
  'comment_author_IP' =>  $ip,
  'comment_agent' => $agent,
  'comment_date' => $time,
  'comment_approved' => $comment_approved,
   );

//print_r($data);

 $comment_id = wp_insert_comment($data);

 return array(
             "comment_id" => $comment_id
             );
   }



public function createEndPointForSNS($token=NULL, $platform_AppArn=NULL) {

        if (!empty($token) && !empty($platform_AppArn)) {
            try {
                //$sns = new EndpointSNS(AMAZON_KEY,AMAZON_SECRET);
                $sns = new EndpointSNS(AMAZON_KEY, AMAZON_SECRET, REGION);
				//print "<pre>"; print_r($sns); die;

                //echo "SNS " + $sns;
                try {
                    $endpointArn = $sns->generateEndpoint($token, $platform_AppArn);
                } catch (Exception $e) {
                    return json_encode(array("status" => '-6', "message" => $e->getMessage(), 'data' => array()));
                    exit;
                }
                // Get the application's endpoints
                $endpointAtt = $sns->getEndpointAttributes($endpointArn, $token);
                if ($endpointAtt == true) {
                    // echo "if condition ";
                    // Endpoint is either have invalid token or it is marked as disabled.
                    $sns->setEndpointAttributes($token, "true", $endpointArn);
                }
                if ($endpointAtt == -1) {
                    $endpointArn = $sns->generateEndpoint($token, $platform_AppArn);
                } else {
                    $sns->setEndpointAttributes($token, "true", $endpointArn);
                }
                /// echo $endpointArn;die;
                return $endpointArn;
            } catch (Exception $e) {
                return json_encode(array("status" => '-3', "message" => "Error while generating endpoint: " . $e->getMessage(), 'data' => array()));
                exit;
            }
        } else {
            return json_encode(array("status" => '-4', "message" => "deviceToken is not found. ", 'data' => array()));
            exit;
        }
    }

 function sendRidePushNotification($push_message, $endpointArn, $senderdata, $reciever_user_type, $recieverdata) {


        //error_reporting(E_ALL);
        /* print "<pre>";
          echo $push_message;
          print_r($endpointArn);
          print_r($senderdata);
          echo $reciever_user_type;
          print_r($recieverdata);
          die; */


        //e//cho "deviceType" . "  " . $recieverdata['deviceType'];
//echo $recieverdata['driverID'];
        if (!empty($push_message) && !empty($endpointArn)) {
            // Create a new Amazon SNS client
            try {
                $sns = SnsClient::factory(array(
                            'key' => AMAZON_KEY,
                            'secret' => AMAZON_SECRET,
                            'region' => REGION
                ));

                $sound = 'doorbell.caf';
                if ($reciever_user_type == 1) { // User is Driver
                    if ($recieverdata['deviceType'] == 1) {  // device is andriod

                        $msgpayload = json_encode(array('data' => array('message' => $push_message, 'sound' => $sound, 'passengerID' => $senderdata['passengerID'], 'firstname' => $senderdata['firstname'], 'lastname' => $senderdata['lastname'], 'photoURL' => $senderdata['photoURL'], 'rideID' => $senderdata['rideID'], 'rideRequestID' => $senderdata['rideRequestID'], 'notificationTypeId' => $senderdata['tripId'],)));

                        //$type= 'GCM';
                        $response = $sns->publish(array(
                            'TargetArn' => $endpointArn,
                            'MessageStructure' => 'json',
                            'Message' => json_encode(array(
                                'default' => $push_message,
                                'GCM' => $msgpayload
                            ))
                        ));
                    } else if ($recieverdata['deviceType'] == 2) { // device is iphone

                        $msgpayload = json_encode(array('aps' => array('alert' => $push_message, 'sound' => $sound, 'passengerID' => $senderdata['passengerID'], 'firstname' => $senderdata['firstname'], 'lastname' => $senderdata['lastname'], 'photoURL' => $senderdata['photoURL'], 'rideID' => $senderdata['rideID'], 'rideRequestID' => $senderdata['rideRequestID'], 'notificationTypeId' => $senderdata['tripId'],)));

                        $response = $sns->publish(array(
                            'TargetArn' => $endpointArn,
                            'MessageStructure' => 'json',
                            'Message' => json_encode(array(
                                'default' => $push_message,
                                'APNS_SANDBOX' => $msgpayload
                            ))
                        ));
                    }
                }

                /*$pushData = array();
                $pushData['rideID'] = $senderdata['rideID'];
                $pushData['payload'] = $msgpayload;
                $pushData['response'] = $response;
                $pushData['notificationType'] = $senderdata['tripId'];
                $pushData['status'] = 1;
                $pushData['created'] = date("Y-m-d H:i:s");
                $pushObj = new Tblpushnotificationhistory();
                $pushObj->setData($pushData);
                $pushObj->insertData();
                //error_log(print_r($msgpayload,true),3,"payload.txt");
                //error_log(print_r($response,true),3,"push.txt");*/
                return $status = 1;
            } catch (Exception $e) {
                /* if($e->getMessage() == '')
                  {
                  if($reciever_user_type == 1) // User is Driver
                  {
                  $TbldriverObj = new Tbldriver();
                  $driverData = $TbldriverObj->getdetailsbyId($recieverdata['driverID']);
                  $token = $driverData[''];
                  }
                  $this->createEndPointForSNS($token,$platform_AppArn);
                  } */

                //$this->response(array("status"=>'-1',"message"=>"Error while sending notification: " .$e->getMessage() ,'data'=>array())); exit;

                /*$pushData = array();
                $pushData['rideID'] = $senderdata['rideID'];
                $pushData['payload'] = $msgpayload;
                $pushData['response'] = $e->getMessage();
                $pushData['notificationType'] = $senderdata['tripId'];
                $pushData['status'] = 0;
                $pushData['created'] = date("Y-m-d H:i:s");

                $pushObj = new Tblpushnotificationhistory();
                $pushObj->setData($pushData);
                $pushObj->insertData();*/
                return $status = 0;
            }
        } else {
            return $status = -1;
        }
    }

 function logout()
 {
	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$arr = array();
	$sql = "UPDATE wp_users SET device_token = '' WHERE ID = ".$_REQUEST['user_id'];
	$res = $conn->query($sql);
	wp_destroy_current_session();
	wp_clear_auth_cookie();
	$response = array();
	$response['status'] = "ok";
	$response['success'] = "success";
	$response['message'] = "success";
	return $response;


 }


 public function search2()
 {
   error_reporting(E_ALL);
   if(!$_REQUEST['search_term']){$oReturn->error = __('search_term is required.','aheadzen'); return $oReturn;}
   $keyword = $_REQUEST['search_term'];
   $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
   // Check connection
   if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
   }
   $sql = "(SELECT DISTINCT
               id,
               'posts' AS TYPE,
               post_title LIKE '%".$_REQUEST['search_term']."%' AS relevance,
               post_date AS entry_date
             FROM
               wp_posts
             WHERE 1 = 1
               AND (
                 (
                   (post_title LIKE '%".$_REQUEST['search_term']."%')
                   OR (post_content LIKE '%".$_REQUEST['search_term']."%')
                 )
               )
               AND post_type IN ('post', 'page')
               AND post_status = 'publish')
             UNION
             (SELECT DISTINCT
               u.id,
               'members' AS TYPE,
               u.display_name LIKE '%".$_REQUEST['search_term']."%' AS relevance,
               a.date_recorded AS entry_date
             FROM
               wp_users u
               JOIN wp_bp_activity a
                 ON a.user_id = u.id
             WHERE 1 = 1
               AND u.id IN
               (SELECT
                 ID
               FROM
                 wp_users
               WHERE (
                   user_login LIKE '%".$_REQUEST['search_term']."%'
                   OR display_name LIKE '%".$_REQUEST['search_term']."%'
                   OR user_email LIKE '%".$_REQUEST['search_term']."%'
                 ))
               OR u.id IN
               (SELECT
                 user_id
               FROM
                 wp_bp_xprofile_data
               WHERE VALUE LIKE '%".$_REQUEST['search_term']."%'
                 AND field_id IN (1, 25))
               AND a.component = 'members'
               AND a.type = 'last_activity'
             GROUP BY u.id)
             UNION
             (SELECT DISTINCT
               id,
               'forums' AS TYPE,
               post_title LIKE '%".$_REQUEST['search_term']."%' AS relevance,
               post_date AS entry_date
             FROM
               wp_posts
             WHERE 1 = 1
               AND (
                 (
                   (post_title LIKE '%".$_REQUEST['search_term']."%')
                   OR (post_content LIKE '%".$_REQUEST['search_term']."%')
                 )
               )
               AND post_type IN ('forum', 'topic', 'reply')
               AND post_status = 'publish')
             UNION
             (SELECT DISTINCT
               g.id,
               'groups' AS TYPE,
               g.name LIKE '%".$_REQUEST['search_term']."%' AS relevance,
               gm2.meta_value AS entry_date
             FROM
               wp_bp_groups_groupmeta gm1,
               wp_bp_groups_groupmeta gm2,
               wp_bp_groups g
             WHERE 1 = 1
               AND g.id = gm1.group_id
               AND g.id = gm2.group_id
               AND gm2.meta_key = 'last_activity'
               AND gm1.meta_key = 'total_member_count'
               AND (
                 g.name LIKE '%".$_REQUEST['search_term']."%'
                 OR g.description LIKE '%".$_REQUEST['search_term']."%'
               )
               AND g.status != 'hidden')
             UNION
             (SELECT DISTINCT
               a.id,
               'activity' AS TYPE,
               a.content LIKE '%".$_REQUEST['search_term']."%' AS relevance,
               a.date_recorded AS entry_date
             FROM
               wp_bp_activity a
             WHERE 1 = 1
               AND is_spam = 0
               AND a.content LIKE '%".$_REQUEST['search_term']."%'
               AND a.hide_sitewide = 0
               AND a.type = 'activity_update')
             UNION
             (SELECT DISTINCT
               id,
               'cpt-gp_portfolio_item' AS TYPE,
               post_title LIKE '%".$_REQUEST['search_term']."%' AS relevance,
               post_date AS entry_date
             FROM
               wp_posts
             WHERE 1 = 1
               AND (
                 (
                   (post_title LIKE '%".$_REQUEST['search_term']."%')
                   OR (post_content LIKE '%".$_REQUEST['search_term']."%')
                 )
               )
               AND post_type = 'gp_portfolio_item'
               AND post_status = 'publish')
             UNION
             (SELECT DISTINCT
               id,
               'cpt-gp_slide' AS TYPE,
               post_title LIKE '%".$_REQUEST['search_term']."%' AS relevance,
               post_date AS entry_date
             FROM
               wp_posts
             WHERE 1 = 1
               AND (
                 (
                   (post_title LIKE '%".$_REQUEST['search_term']."%')
                   OR (post_content LIKE '%".$_REQUEST['search_term']."%')
                 )
               )
               AND post_type = 'gp_slide'
               AND post_status = 'publish')
             ORDER BY relevance DESC,
             entry_date DESC";
     $response = array();
     $res = $conn->query($sql);
     if ($res->num_rows > 0) {
         while($row2 = $res->fetch_assoc())
         {
             //echo "<br/>";
             //print_r($row2);
             if($row2['TYPE'] == "groups")
             {
                $response[] = $this->getGroupByID($row2['id']);
             }
             if($row2['TYPE'] == "activity")
             {
                $activity_id = $row2['id'];
                global $table_prefix,$wpdb;
                if($activity_id){
                  $res2 = $wpdb->get_results("select * from ".$table_prefix."bp_activity where id=".$row2['id']."");
                  $oActivity = $res2[0];
                  $data = get_formatted_activity_data((array)$oActivity);
                  $response[] = $data ;
                }
             }
             if($row2['TYPE'] == "members")
             {
               $response[] = get_userdata($row2['id']);
               /*$user = new BP_Core_User( $row2['id'] );
         			 if($user->avatar){
         				$user_avatar = $user->avatar;
         				$avatar_thumb = $user->avatar_thumb;
         				$avatar_mini = $user->avatar_mini;
         				preg_match_all('/(src)=("[^"]*")/i',$user_avatar, $user_avatar_result);
         				$user_avatar_src = str_replace('"','',$user_avatar_result[2][0]);
         				if($user_avatar_src && !strstr($user_avatar_src,'http:')){ $user_avatar_src = 'http:'.$user_avatar_src;}
         				preg_match_all('/(src)=("[^"]*")/i',$avatar_mini, $avatar_mini_result);
         				$avatar_mini_src = str_replace('"','',$avatar_mini_result[2][0]);
         				if($avatar_mini_src && !strstr($avatar_mini_src,'http:')){ $avatar_mini_src = 'http:'.$avatar_mini_src;}
         				preg_match_all('/(src)=("[^"]*")/i',$avatar_thumb, $avatar_thumb_result);
         				$avatar_thumb_src = str_replace('"','',$avatar_thumb_result[2][0]);
         				if($avatar_thumb_src && !strstr($avatar_thumb_src,'http:')){ $avatar_thumb_src = 'http:'.$avatar_thumb_src;}

         				$bbp_cover_pic = get_user_meta( $row2['id'], 'bbp_cover_pic',true);
                $res->cover_pic = $bbp_cover_pic;

         				if(!$bbp_cover_pic){$bbp_cover_pic=$user_avatar_src;}

         				$res->avatar = $user_avatar_src;
         				$res->avatar_big = $user_avatar_src;
         				$res->avatar_thumb = $avatar_thumb_src;
         				$res->avatar_mini = $avatar_mini;*/
                //$response[] = $res;
              //}
               ///$res2 = bp_get_user_meta($row['id']);
             }


         }
         print "<pre>";
         print_r($response);
         die;
     }


 }


 function getGroupByID($groupID=NULL)
 {
   $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
   // Check connection
   if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
   }
   $sql = "SELECT * FROM wp_bp_groups WHERE id = '".$groupID."'";
   $res = $conn->query($sql);
   $groupData = $res->fetch_assoc();

   if(empty($groupData))
   {
       $oReturn = new stdClass();
       $oReturn->status = "false";
       $oReturn->error = "error";
       $oReturn->data = array();
       return $oReturn;
   }

   $sql = "SELECT * FROM `wp_users` WHERE `ID` = ".$groupData['creator_id'];
   $res = $conn->query($sql);
   $arr = array();

   if ($res->num_rows > 0) {
     $i=0;
     while($row = $res->fetch_assoc()) {
       $result = array_merge($groupData,$row);
       $groupData = array();
       $groupData = $result;
       $useravatar_url = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row['ID'], 'html'=>false, 'type'=>'full'));
       $groupData['avatar'] = $useravatar_url;
     }
   }

   $sql = "SELECT * FROM `wp_bp_groups_groupmeta` WHERE group_id = ".$groupData['id'];
   $res = $conn->query($sql);

   while($row = $res->fetch_assoc())
   {
     if($row['meta_key'] == "total_member_count")
     {
       $groupData['total_member_count'] = $row['meta_value'];
     }
     if($row['meta_key'] == "last_activity")
     {
       $groupData['last_activity'] = $row['meta_value'];
     }
   }
     return $groupData;
 }

 function test()
 {
	 echo "<br/><br/>";
	  echo $endpointArn = $this->createEndPointForSNS("fcf1ceb386ac316f784b3177a625a2cc259b0a0d394c24437c545be321ac130b", IOS_ARN);
                    die;
 }
 }
