<?php global $socialize; if ( ! is_user_logged_in() && ( isset( $socialize['popup_box'] ) && $socialize['popup_box'] == 'enabled' ) ) {

	// Login
	
	if ( isset( $_POST['action'] ) && $_POST['action'] == 'gp_login' ) {

		$gp_username = esc_sql( $_REQUEST['log'] );
		$gp_password = esc_sql( $_REQUEST['pwd'] );
		$gp_remember = esc_sql( $_REQUEST['rememberme'] );

		if ( $gp_remember ) { $gp_remember = 'true'; } else { $gp_remember = 'false'; }
		$gp_login_data = array();
		
		if ( ( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() === true ) OR ( function_exists( 'cptchpr_check_custom_form' ) && cptchpr_check_custom_form() === true ) OR ( ! function_exists( 'cptch_check_custom_form' ) && ! function_exists( 'cptchpr_check_custom_form' ) ) ) {
			$gp_login_data['user_login'] = $gp_username;
			$gp_login_data['user_password'] = $gp_password;
			$gp_login_data['remember'] = $gp_remember;
			$gp_user_verify = wp_signon( $gp_login_data, false ); 
		}					
	
		if ( empty( $_POST['log'] ) && empty( $_POST['pwd'] ) ) {
			echo "<span class='error'>" . esc_html__( 'Please enter your username and password', 'socialize' ) . "</span>";
			exit();
		} elseif ( empty( $_POST['log'] ) ) {
			echo "<span class='error'>" . esc_html__( 'Please enter your username', 'socialize' ) . "</span>";
			exit();
		} elseif ( empty( $_POST['pwd'] ) ) {
			echo "<span class='error'>" . esc_html__( 'Please enter your password', 'socialize' ) . "</span>";
			exit();
		}
			
		$gp_user_data = get_user_by( 'login', $gp_username );
		if ( ( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) OR ( function_exists( 'cptchpr_check_custom_form' ) && cptchpr_check_custom_form() !== true ) ) {
			echo "<span class='error'>" . esc_html__( 'Please complete the CAPTCHA', 'socialize' ) . "</span>";	
			exit();
		} elseif ( empty( $gp_user_data ) && is_wp_error( $gp_user_verify ) ) {
			echo "<span class='error'>" . esc_html__( 'Invalid username and password', 'socialize' ) . "</span>";
			exit();
		} elseif ( ! empty( $gp_user_data ) && is_wp_error( $gp_user_verify ) ) {
			echo "<span class='error'>" . esc_html__( 'Invalid password', 'socialize' ) . "</span>";
			exit();   
		} else {
			echo "<script type='text/javascript'>window.location.reload();</script>";
			exit();
		}

	}
	

	// Register
	
	if ( isset( $_POST['action'] ) && $_POST['action'] == 'gp_register' ) {

		$gp_info = array();
		$gp_info['user_nicename'] = $gp_info['nickname'] = $gp_info['display_name'] = $gp_info['first_name'] = $gp_info['user_login'] = sanitize_user( $_POST['user_login'] );
		$gp_info['user_pass'] = sanitize_text_field( $_POST['user_pass'] );
		$gp_info['user_email'] = sanitize_email( $_POST['user_email'] );

		if ( empty( $_POST['user_login'] ) OR empty( $_POST['user_email'] ) OR empty( $_POST['user_pass'] ) ) {
			$gp_user_register = '';
			echo "<span class='error'>" . esc_html__( 'Please complete all fields', 'socialize' ) . "</span>";
			exit();
		} elseif ( ( function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true ) OR ( function_exists( 'cptchpr_check_custom_form' ) && cptchpr_check_custom_form() !== true ) ) {
			$gp_user_register = '';
			echo "<span class='error'>" . esc_html__( 'Please complete the CAPTCHA', 'socialize' ) . "</span>";
			exit();
		} else {
			$gp_user_register = wp_insert_user( $info );
		}
	
 		if ( is_wp_error( $gp_user_register ) ) {	
			$gp_error = $gp_user_register->get_error_codes();
			if ( in_array( 'empty_user_login', $gp_error ) ) {
				echo "<span class='error'>" . $gp_user_register->get_error_message( 'empty_user_login' ) . "</span>";	
				exit();
			} elseif ( in_array( 'existing_user_login', $gp_error ) ) {
				echo "<span class='error'>" . esc_html__( 'This username is already registered.', 'socialize' ) . "</span>";	
				exit();
			} elseif ( in_array( 'existing_user_email', $gp_error ) ) {
				echo "<span class='error'>" . esc_html__( 'This email address is already registered.', 'socialize' ) . "</span>";	
				exit(); 
			}
		} else {
			wp_new_user_notification( $gp_user_register, sanitize_text_field( $_POST['user_pass'] ) );
			echo "<span class='gp-success'>" . esc_html__( 'An email has been sent with your details.', 'socialize' ) . "</span>";	
			exit(); 
		}

	}
	
		
	// Lost Password
	
	function socialize_validate_url() {
		global $post;
		$gp_page_url = esc_url( home_url() );
		$gp_urlget = strpos( $gp_page_url, '?' );
		if ( $gp_urlget === false ) {
			$gp_concate = "?";
		} else {
			$gp_concate = "&";
		}
		return $gp_page_url . $gp_concate;
	}
	
	if ( isset( $_GET['key'] ) && $_GET['action'] == 'reset_pwd' ) {
	
		$gp_reset_key = $_GET['key'];
		$gp_user_login = $_GET['login'];
		$gp_user_data = $wpdb->get_row( $wpdb->prepare( "SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $gp_reset_key, $gp_user_login ) );
	
		$gp_user_login = $gp_user_data->user_login;
		$gp_user_email = $gp_user_data->user_email;
	
		if ( ! empty( $gp_reset_key ) && ! empty( $gp_user_data ) ) {
		
			$gp_new_password = wp_generate_password( 7, false );
			wp_set_password( $gp_new_password, $gp_user_data->ID );
			$gp_message = esc_html__( 'Your new password for the account at:', 'socialize' ) . "\r\n\r\n";
			$gp_message .= get_option( 'siteurl' ) . "\r\n\r\n";
			$gp_message .= sprintf( esc_html__( 'Username: %s', 'socialize' ), $gp_user_login ) . "\r\n\r\n";
			$gp_message .= sprintf( esc_html__( 'Password: %s', 'socialize' ), $gp_new_password ) . "\r\n\r\n";
			$gp_message .= esc_html__( 'You can now login with your new password at: ', 'socialize' ) . get_option( 'siteurl' )."/register/#login" . "\r\n\r\n";
		
			if ( $gp_message && ! wp_mail( $gp_user_email, esc_html__( 'Password Reset Request', 'socialize' ), $gp_message ) ) {
				echo "<span class='error'>" . esc_html__( 'Email failed to send for some unknown reason', 'socialize' ) . "</span>";
				exit();
			} else {
				//$gp_redirect_to = home_url() . '#login?action=reset_success';
				$gp_redirect_to = home_url().'/register/';
				wp_safe_redirect( $gp_redirect_to );
				exit();
			}
			
		} else {
		
			exit( 'Not a valid key.' );
			
		}
		
	}

	if ( isset( $_POST['action'] ) && $_POST['action'] == 'gp_lost_password' ) {

		if ( ! wp_verify_nonce( $_POST['gp_pwd_nonce'], 'gp_pwd_nonce' ) ) {
			exit( 'No trick please' );
		}  

		if ( empty( $_POST['user_login'] ) ) {
			echo "<span class='error'>" . esc_html__( 'Please enter your username or email address', 'socialize' ) . "</span>";
			exit();
		}
	
		$gp_user_input = esc_sql( trim( $_POST['user_login'] ) );
	
				if ( strpos( $gp_user_input, '@' ) ) {
			$gp_user_data = get_user_by( 'email', $gp_user_input );
			if ( empty( $gp_user_data ) || ( isset( $gp_user_data->caps['administrator'] ) && $gp_user_data->caps['administrator'] == 1 ) ) {
				echo "<span class='error'>" . esc_html__( 'Invalid email address', 'socialize' ) . "</span>";
				exit();
			}
		} else {
			$gp_user_data = get_user_by( 'login', $gp_user_input );
			if ( empty( $gp_user_data ) || ( isset( $gp_user_data->caps['administrator'] ) && $gp_user_data->caps['administrator'] == 1 ) ) {
				echo "<span class='error'>" . esc_html__( 'Invalid username.', 'socialize' )."</span>";
				exit();
			}
		}
	
		$gp_user_login = $gp_user_data->user_login;
		$gp_user_email = $gp_user_data->user_email;
	
		$gp_key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $gp_user_login ) );
		if ( empty( $gp_key ) ) { // generate reset key
			$gp_key = wp_generate_password( 20, false );
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $gp_key ), array( 'user_login' => $gp_user_login ) );	
		}
	
		// Mailing reset details to the user
		$gp_message = esc_html__( 'Someone requested that the password be reset for the following account:', 'socialize' ) . "\r\n\r\n";
		$gp_message .= get_option( 'siteurl' ) . "\r\n\r\n";
		$gp_message .= sprintf( esc_html__( 'Username: %s', 'socialize' ), $gp_user_login ) . "\r\n\r\n";
		$gp_message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.', 'socialize' ) . "\r\n\r\n";
		$gp_message .= esc_html__( 'To reset your password, visit the following address:', 'socialize' ) . "\r\n\r\n";
		$gp_message .= socialize_validate_url() . "action=reset_pwd&key=$gp_key&login=" . rawurlencode( $gp_user_login ) . "\r\n\r\n";
		$message .= esc_html__( 'You will receive another email with your new password.', 'socialize' ) . "\r\n"; 
	
		//mail($gp_user_email,"Password Reset Request",$gp_message );
		if ( $gp_message && ! wp_mail( $gp_user_email, esc_html__( 'Password Reset Request', 'socialize' ), $gp_message ) ) {
			echo "<span class='error'>" . esc_html__( 'Email failed to send for some unknown reason.', 'socialize' ) . "</span>";
			exit();
		} else {
			echo "<span class='gp-success'>" . esc_html__( 'We have just sent you an email with instructions to reset your password.', 'socialize' ) . "</span>";
			exit();
		}
	
	}

}

?>