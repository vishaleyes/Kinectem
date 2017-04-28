<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file and WordPress will use these functions instead of the original functions.
*/

/////////////////////////////////////// Load parent style.css ///////////////////////////////////////

if ( ! function_exists( 'socialize_enqueue_child_styles' ) ) {
	function socialize_enqueue_child_styles() { 
		wp_enqueue_style( 'gp-parent-style', get_template_directory_uri() . '/style.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'socialize_enqueue_child_styles' );


//Removes Groups and Friends pane from profile activity (doesn't remove mention functionality)
function ray_remove_mention_nav() {
global $bp;
bp_core_remove_subnav_item( $bp->activity->slug, 'groups' );
bp_core_remove_subnav_item( $bp->activity->slug, 'friends' );
}
add_action( 'bp_setup_nav', 'ray_remove_mention_nav', 15 );

/*
function change_translate_text( $translated_text ) {
if ( $translated_text == 'Forums' ) {
	$translated_text = 'Groups';}
	
return $translated_text;
}
add_filter( 'gettext', 'change_translate_text', 20 );
*/

// Remove groups tab from profile
// adjust accordingly to remove other items! Simple huh?
// Enjoy!
/*
function bphelp_remove_groups_from_profile(){
bp_core_remove_nav_item('groups');
}
add_action('bp_groups_setup_nav','bphelp_remove_groups_from_profile');
*/


/*function bp_help_redirect_to_profile(){
if( is_user_logged_in() && bp_is_front_page() ) {
  bp_core_redirect( get_option('home') . '/members/' . bp_core_get_username( bp_loggedin_user_id() ) );
}
}*/

function bp_help_redirect_to_profile(){
    global $bp;
if( is_user_logged_in() && bp_is_front_page() ) {
  bp_core_redirect( get_option('home') . '/members/' . bp_core_get_username( bp_loggedin_user_id() ) );
  echo $component = $bp->current_component;
}else {
  if ( !is_user_logged_in() && !bp_is_register_page() && !bp_is_activation_page())
    if($_REQUEST['action']!='reset_pwd'){
        if(is_page(array(171))){   
        }
        else{
        bp_core_redirect( get_option('home') . '/register/');   
            }
        }
    }
}
add_action( 'get_header', 'bp_help_redirect_to_profile',1);

add_action('wp_logout','go_home');
function go_home(){
  wp_redirect( home_url().'/register/' );
  exit();
}

// Removing public message option

function remove_public_message_button() {
remove_filter( 'bp_member_header_actions','bp_send_public_message_button', 20);

}
add_action( 'bp_member_header_actions', 'remove_public_message_button' );


/* remove @username from the profile header */

function bpfr_remove_mention_from_profile() {	

	echo '<style> h2.user-nicename { display:none; } </style>'; // hide the h2 containing the @

	if( bp_is_user() && ! bp_get_member_user_id() ) {  // be sure we get the right user_id
        $user_id = 'displayed: '. bp_displayed_user_id();
    } else {
        $user_id = 'get_member_user: '. bp_get_member_user_id();
    }

	remove_filter( 'bp_get_displayed_user_mentionname', bp_activity_get_user_mentionname( bp_displayed_user_id() ) );
	
}


//add_filter( 'bp_get_displayed_user_mentionname', 'bpfr_remove_mention_from_profile' );


    
/*
// Filter wp_nav_menu() to add profile link
add_filter( 'wp_nav_menu_items', 'my_nav_menu_profile_link' );
function my_nav_menu_profile_link($menu) { 	
	if (!is_user_logged_in())
		return $menu;
	else
		$profilelink = '<li><a href="' . bp_loggedin_user_domain( '/' ) . '">' . __('Visit your Awesome Profile') . '</a></li>';
		$menu = $menu . $profilelink;
		return $menu;
}

*/
    
/*
function user_name_firstname($fullname,$user_id=false)
    {
        
        global $bp;
        
        global $site_members_template;
        
        $first_name_field_id=40;
        
        $name_field_id=39;
        
        if ( !$user_id ) $user_id=bp_get_the_site_member_user_id();
        
        if ( function_exists(‘xprofile_install’) ) {
            
            $first_name= xprofile_get_field_data($first_name_field_id, $user_id );
            
            $name = xprofile_get_field_data($name_field_id, $user_id );
            
            if ($name) {
                
                $fullname = $name;
                
                if ($first_name) $fullname.=‘ ‘.$first_name;
                
            }
            
            wp_cache_set( ‘bp_user_fullname_’ . $user_id, $fullname, ‘bp’ );
            
        }
        
        return apply_filters( ‘user_name_firstname’, $fullname );
        
    }
    
    add_filter( ‘bp_get_the_site_member_name’, ‘user_name_firstname’,9);
 
 */
function your_theme_xprofile_cover_image( $settings = array() ) {
    $settings['width']  = 2000;
    $settings['height'] = 250;
        
    return $settings;
    }
    add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'your_theme_xprofile_cover_image', 10, 1 );



function follower_save(){
    global $wpdb;
    $table_check=$wpdb->prefix."bp_team_follow";
    $authorid=$_REQUEST['authorid'];
    $groupid=$_REQUEST['groupid'];
    $data=$wpdb->insert( 
            $table_check, 
            array('author_id' => $authorid,'group_id' => $groupid), 
            array('%d','%d')
          );
    die();
}
add_action('wp_ajax_follower_save', 'follower_save');
add_action('wp_ajax_nopriv_follower_save', 'follower_save');

function follower_delete(){
    global $wpdb;
    $table_check=$wpdb->prefix."bp_team_follow";
    $authorid=$_REQUEST['authorid'];
    $groupid=$_REQUEST['groupid'];
    $wpdb->query($wpdb->prepare(" DELETE FROM $table_check WHERE author_id = %d AND group_id = %d", $authorid, $groupid));
    die();
}
add_action('wp_ajax_follower_delete', 'follower_delete');
add_action('wp_ajax_nopriv_follower_delete', 'follower_delete');

function bpfr_hide_profile_edit( $retval ) {    
    // remove field from registration page    
    if ( bp_is_register_page() ) {
        $retval['exclude_fields'] = '6,18,26,29,32,33,34,35,36,37'; // ID's separated by comma
        }  
    return $retval; 
}
add_filter( 'bp_after_has_profile_parse_args', 'bpfr_hide_profile_edit' );
