<?php

class ExtensionLoginForm {

    function __construct() {

        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );
 
        // Use this when creating a shortcode addon
        add_shortcode( 'vc_loginform', array( $this, 'renderLoginForm' ) );
 
        // Register CSS for Frontend
        add_action('wp_footer', array($this, 'printCSS'));
    }
 
    public function integrateWithVC() {

        //  Add the Element
        vc_map( array(
            "name" => __("Login Form", PLUGIN_VCA_TEXT_DOMAIN),
            "description" => __("A login form for you page", PLUGIN_VCA_TEXT_DOMAIN),
            "base" => "vc_loginform",
            "class" => "",
            "controls" => "full",
            "icon" => PLUGIN_VCA_URL . 'assets/images/element_icon.png',
            "category" => __('Content', 'js_composer'),
            "params" => array(
                array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Title", PLUGIN_VCA_TEXT_DOMAIN),
                  "param_name" => "form_title",
                  "value" => __("Login", PLUGIN_VCA_TEXT_DOMAIN),
                  "description" => __("Login Form Title", PLUGIN_VCA_TEXT_DOMAIN)
                ),
                array(
                    'type' => 'checkbox',
                    //'heading' => __( 'Display Registration Link?', PLUGIN_VCA_TEXT_DOMAIN ),
                    'param_name' => 'form_register_link',
                    'value' => array( __( 'Show Register Link (site registration must be enabled)', PLUGIN_VCA_TEXT_DOMAIN ) => 'yes' )
                ),
                array(
                    'type' => 'checkbox',
                    //'heading' => __( 'Display Forgotten Link?', PLUGIN_VCA_TEXT_DOMAIN ),
                    'param_name' => 'form_reset_link',
                    'value' => array( __( 'Show forgotten password link', PLUGIN_VCA_TEXT_DOMAIN ) => 'yes' )
                ),
                array(
                    'type' => 'checkbox',
                    //'heading' => __( 'Disable Plugin CSS?', PLUGIN_VCA_TEXT_DOMAIN ),
                    'param_name' => 'form_disable_css',
                    'value' => array( __( 'Disable css applied by the plugin for login form', PLUGIN_VCA_TEXT_DOMAIN ) => 'yes' )
                ),
                array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Extra Class", PLUGIN_VCA_TEXT_DOMAIN),
                  "param_name" => "form_class",
                  "value" => __("", PLUGIN_VCA_TEXT_DOMAIN),
                  "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", PLUGIN_VCA_TEXT_DOMAIN)
                ),
                array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Login Redirect Link", PLUGIN_VCA_TEXT_DOMAIN),
                  "param_name" => "form_redirect_login",
                  "value" => __("", PLUGIN_VCA_TEXT_DOMAIN),
                  "description" => __("Custom URL to redirect to after login is successful. Leave empty to redirect to active page.", PLUGIN_VCA_TEXT_DOMAIN)
                ),
                array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Logout Redirect Link", PLUGIN_VCA_TEXT_DOMAIN),
                  "param_name" => "form_redirect_logout",
                  "value" => __("", PLUGIN_VCA_TEXT_DOMAIN),
                  "description" => __("Custom URL to redirect to after logout is successful. Leave empty to redirect to active page.", PLUGIN_VCA_TEXT_DOMAIN)
                )
            )
        ) );

        //  Check for Submission
        if(isset($_GET['vlf_login']) && $_GET['vlf_login'] == '1') {

            //  Credentials
            $creds = array();
        	$creds['user_login'] = $_POST['log'];
        	$creds['user_password'] = $_POST['pwd'];
        	$creds['remember'] = true;

            //  Redirect To
            $redirect_to = (isset($_POST['redirect_to']) && !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : @$_GET['redirect_to']);

            //  Try Signin
        	$user = wp_signon( $creds, false );

            //  Check for Error
        	if ( is_wp_error($user) && sizeof($user->errors) > 0 ) {

                //  Global Message
                global $vlf_error;
                $vlf_error = $user->get_error_message();
        	} else {

                //  Check for Empty
                if(empty($creds['user_login']) || empty($creds['user_password'])) {

                    //  Global Message
                    global $vlf_error;
                    $vlf_error = 'Username and/or Password field is empty';
                } else {

                    //  Redirect
                    wp_redirect($redirect_to);
                }
        	}
        }
        else if(isset($_GET['vlf_logout']) && $_GET['vlf_logout'] == '1') {

            //  Redirect To
            $redirect_to = (isset($_POST['redirect_to']) && !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : @$_GET['redirect_to']);

            //  Logout
            wp_logout();

            //  Redirect
            wp_redirect($redirect_to);
        }
    }
 
    /*
    Shortcode logic how it should be rendered
    */
    public function renderLoginForm( $atts, $content = null ) {

        //  Extract Shortcode Attributes
        extract( shortcode_atts( array(
            'form_title' => 'Login',
            'form_register_link' => '',
            'form_reset_link' => '',
            'form_disable_css' => '',
            'form_class' => '',
            'form_redirect_login' => get_permalink(),
            'form_redirect_logout' => get_permalink()
        ), $atts ) );

        // fix unclosed/unwanted paragraph tags in $content
        $content = wpb_js_remove_wpautop($content, true);

        //  Fix Links
        if(substr($form_redirect_login, 0, 4) != 'http')    $form_redirect_login = 'http://' . $form_redirect_login;
        if(substr($form_redirect_logout, 0, 4) != 'http')    $form_redirect_logout = 'http://' . $form_redirect_logout;
 
        //  Flags
        $display_register_link = (($form_register_link == 'yes' && get_option('users_can_register')) ? true : false);
        $display_forgot_link = ($form_reset_link == 'yes');
        $disable_plugin_css = ($form_disable_css == 'yes');

        //  Get Global
        global $vlf_print_css;

        //  Check
        if(is_null($vlf_print_css) || $vlf_print_css == true) {

            //  Set
            $vlf_print_css = !$disable_plugin_css;
        }

        //  Check Already Logged In
        //if(is_user_logged_in()) return 'You are already logged in. <a href="' . wp_logout_url($form_redirect_logout) . '">Logout</a>';
        if(is_user_logged_in()) return 'You are already logged in. <a href="?vlf_logout=1&redirect_to=' . $form_redirect_logout . '">Logout</a>';

        //  Get Error
        global $vlf_error;

        //  Generate Form
        $output = '
        <div class="vcform ' . $form_class . '" id="vcform-' . time() . "-" . uniqid() . '">
            <form method="post" action="?vlf_login=1">
                <div class="vcform-header">
                    <h2>' . $form_title . '</h2>
                </div>
                <div class="vcform-body">' . 
                    ($vlf_error ? '<p class="vlf-error">' . $vlf_error . '</p>' : '') . 
        			'<div class="username_holder">
        				<input type="text" name="log" class="username_input" placeholder="Username" value="' . (isset($_POST['log']) ? $_POST['log'] : '') . '" />
        			</div>
        			<div class="password_holder">
        				<input type="password" name="pwd" class="password_input" placeholder="Password" />
        			</div>
    			</div>
    			<div class="vcform-footer">
        			<div class="login_submit">
        				<input type="submit" name="wp-submit" value="Sign In" class="login-btn" />
        			</div>
        			<div class="login_links"><ul>' .
            			($display_forgot_link ? '<li><a href="' . wp_lostpassword_url($form_redirect_login) . '">Forgot your Password?</a></li>' : '') . 
            			($display_register_link ? '<li><a href="' . wp_registration_url($form_redirect_login) . '">Register</a></li>' : '') . 
        		    '</ul></div>
        		    <div class="clear"></div>
    		    </div>
    		    <input type="hidden" name="redirect_to" value="' . $form_redirect_login . '" />
    		    <input type="hidden" name="base_url" value="' . get_permalink() . '" />
		    </form>
		</div>';

        return $output;
    }

    /*
    Get Form CSS
    */
    public function printCSS() {

        //  Get Global
        global $vlf_print_css;

        //  Check
        if(!is_null($vlf_print_css) && $vlf_print_css == true) {

            //  Enqueue Style
            wp_enqueue_style('vfl-form-css', PLUGIN_VCA_URL . 'assets/css/vlf-form.css');
        }
    }
}