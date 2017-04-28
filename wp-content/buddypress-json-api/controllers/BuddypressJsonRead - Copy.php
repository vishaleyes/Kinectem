<?php
/*
  Controller name: Buddypress Read
  Controller description: Buddypress controller for reading actions
 */

require_once BUDDYPRESS_JSON_API_HOME . '/library/functions.class.php';
require_once('aws.phar');
require_once('aws/vendor/autoload.php');
require_once('sns.class.php');
use Aws\Sns\SnsClient;
$upload_dir = wp_upload_dir();

class JSON_API_BuddypressRead_Controller {

	public $activityData = array();
	function __construct() {
	   header("Access-Control-Allow-Origin: *");
	   header('Content-Type: application/json');
		$userid = 0;
		if($_REQUEST['userid']){
			$userid = $_REQUEST['userid'];
		}else if($_REQUEST['user_id']){
			$userid = $_REQUEST['user_id'];
		}else if($this->userid){
			$userid = $this->userid;
		}
		if($userid>0){
			bp_update_user_last_activity($userid);
		}
	}

public function profile_get_settings() {
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_GET['user_id']){$oReturn->error = __('Wrong User.','aheadzen'); return $oReturn;}
	$user_id = $_GET['user_id'];
	//print "<pre>";print_r(bp_get_user_meta($user_id));die;
	//$oReturn->settings->profile_set_private = false;
	$oReturn->settings->emlActivityMentions = false;
	$oReturn->settings->emlActivityReply = false;
	$oReturn->settings->emlMemberSend = false;
	$oReturn->settings->emlGroupInvite = false;
	$oReturn->settings->emlGroupUpdate = false;
	$oReturn->settings->emlGroupPromoted = false;
	$oReturn->settings->emlJoinYourGroup = false;
	$oReturn->settings->emlForumPostNotification = false;
	$oReturn->settings->emlFollowActivity = false;
	$oReturn->settings->send_requests = false;
	$oReturn->settings->accept_requests = false;
	$oReturn->settings->notification_groups_email_send = false;

	//if(bp_get_user_meta($user_id,'profile_set_private',true)=='yes'){$oReturn->settings->profile_set_private = true;}
	if(bp_get_user_meta($user_id,'notification_activity_new_mention',true)=='yes'){$oReturn->settings->emlActivityMentions = true;}
	if(bp_get_user_meta($user_id,'notification_activity_new_reply',true)=='yes'){$oReturn->settings->emlActivityReply = true;}
	if(bp_get_user_meta($user_id,'notification_messages_new_message',true)=='yes'){$oReturn->settings->emlMemberSend = true;}

	if(bp_get_user_meta($user_id,'notification_friends_friendship_request',true)=='yes'){$oReturn->settings->send_requests = true;}

	if(bp_get_user_meta($user_id,'notification_friends_friendship_accepted',true)=='yes'){$oReturn->settings->accept_requests = true;}

	if(bp_get_user_meta($user_id,'notification_groups_invite',true)=='yes'){$oReturn->settings->emlGroupInvite = true;}
	if(bp_get_user_meta($user_id,'notification_groups_group_updated',true)=='yes'){$oReturn->settings->emlGroupUpdate = true;}
	if(bp_get_user_meta($user_id,'notification_groups_admin_promotion',true)=='yes'){$oReturn->settings->emlGroupPromoted = true;}
	if(bp_get_user_meta($user_id,'notification_groups_membership_request',true)=='yes'){$oReturn->settings->emlJoinYourGroup = true;}
	if(bp_get_user_meta($user_id,'ass_self_post_notification',true)=='yes'){$oReturn->settings->emlForumPostNotification = true;}
	if(bp_get_user_meta($user_id,'notification_starts_following',true)=='yes'){$oReturn->settings->emlFollowActivity = true;}


	if(bp_get_user_meta($user_id,'notification_messages_new_message',true)=='yes'){$oReturn->settings->new_message = true;}
	if(bp_get_user_meta($user_id,'notification_messages_new_notice',true)=='yes'){$oReturn->settings->new_notice = true;}



	$oReturn->settings->profileInactive = true;
	$oReturn->settings->inMemberDirectory = true;
	$oReturn->settings->inMemberSearch = true;
	if(get_user_meta($user_id,'_is_account_inactive',true)){$oReturn->settings->profileInactive = false;}
	if(get_user_meta($user_id,'bp_exclude_in_dir',true)=='yes'){$oReturn->settings->inMemberDirectory = false;}
	if(get_user_meta($user_id,'bp_exclude_in_search',true)=='yes'){$oReturn->settings->inMemberSearch = false;}

	if ( !get_user_meta( $user_id, 'notification_follower_email_send', true) || 'yes' == get_user_meta( $user_id, 'notification_follower_email_send', true) ) {
		$oReturn->settings->notification_groups_email_send = true;
	}
	return $oReturn;
}

public function profile_save_settings() {
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
	if(!$_POST['user_id']){$oReturn->error = __('Wrong User.','aheadzen'); return $oReturn;}
	if(!$_POST['cookie']){$oReturn->error = __('Cookie parameter is required.','aheadzen'); return $oReturn;}
	$user_id = $_POST['user_id'];
	$pw = $_POST['settingstype'];
	$settingstype = $_POST['settingstype'];

	//if(!aheadzen_check_valid_user($_POST['user_id'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}
	$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');
	if($valid != $_POST['user_id']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}


	if($settingstype=='privacy'){
		update_user_meta($user_id,'_is_account_inactive',1);
		update_user_meta($user_id,'bp_exclude_in_dir','yes');
		update_user_meta($user_id,'bp_exclude_in_search','yes');
		if($_POST['profileInactive']=='true'){delete_user_meta($user_id,'_is_account_inactive');}
		if($_POST['inMemberDirectory']=='true'){update_user_meta($user_id,'bp_exclude_in_dir','no');}
		if($_POST['inMemberSearch']=='true'){update_user_meta($user_id,'bp_exclude_in_search','no');}
		$oReturn->success->msg = __('Privacy Settings Saved Successfully.','aheadzen');
	}elseif($settingstype=='email'){
		/*bp_update_user_meta($user_id,'notification_activity_new_mention','no');
		bp_update_user_meta($user_id,'notification_activity_new_reply','no');
		bp_update_user_meta($user_id,'notification_messages_new_message','no');
		bp_update_user_meta($user_id,'notification_groups_invite','no');
		bp_update_user_meta($user_id,'notification_groups_group_updated','no');
		bp_update_user_meta($user_id,'notification_groups_admin_promotion','no');
		bp_update_user_meta($user_id,'notification_groups_membership_request','no');
		bp_update_user_meta($user_id,'ass_self_post_notification','no');
		bp_update_user_meta($user_id,'notification_starts_following','no');
		bp_update_user_meta($user_id,'notification_friends_friendship_request','no');
		bp_update_user_meta($user_id,'notification_friends_friendship_accepted','no');
		bp_update_user_meta($user_id,'notification_groups_email_send','no');*/
		if($_POST['emlActivityMentions']=='true'){bp_update_user_meta($user_id,'notification_activity_new_mention','yes');}
		if($_POST['emlActivityMentions']=='false'){bp_update_user_meta($user_id,'notification_activity_new_mention','no');}
		if($_POST['emlActivityReply']=='true'){bp_update_user_meta($user_id,'notification_activity_new_reply','yes');}
		if($_POST['emlActivityReply']=='false'){bp_update_user_meta($user_id,'notification_activity_new_reply','no');}
		if($_POST['emlMemberSend']=='true'){bp_update_user_meta($user_id,'notification_messages_new_message','yes');}
		if($_POST['emlMemberSend']=='false'){bp_update_user_meta($user_id,'notification_messages_new_message','no');}
		if($_POST['emlGroupInvite']=='true'){bp_update_user_meta($user_id,'notification_groups_invite','yes');}
		if($_POST['emlGroupInvite']=='false'){bp_update_user_meta($user_id,'notification_groups_invite','no');}
		if($_POST['emlGroupUpdate']=='true'){bp_update_user_meta($user_id,'notification_groups_group_updated','yes');}
		if($_POST['emlGroupUpdate']=='false'){bp_update_user_meta($user_id,'notification_groups_group_updated','no');}
		if($_POST['emlGroupPromoted']=='true'){bp_update_user_meta($user_id,'notification_groups_admin_promotion','yes');}
		if($_POST['emlGroupPromoted']=='false'){bp_update_user_meta($user_id,'notification_groups_admin_promotion','no');}
		if($_POST['emlJoinYourGroup']=='true'){bp_update_user_meta($user_id,'notification_groups_membership_request','yes');}
		if($_POST['emlJoinYourGroup']=='false'){bp_update_user_meta($user_id,'notification_groups_membership_request','no');}
		if($_POST['emlForumPostNotification']=='true'){bp_update_user_meta($user_id,'ass_self_post_notification','yes');}
		if($_POST['emlForumPostNotification']=='false'){bp_update_user_meta($user_id,'ass_self_post_notification','no');}
		if($_POST['emlFollowActivity']=='true'){bp_update_user_meta($user_id,'notification_starts_following','yes');}
		if($_POST['emlFollowActivity']=='false'){bp_update_user_meta($user_id,'notification_starts_following','no');}
		if($_POST['send_requests']=='true'){bp_update_user_meta($user_id,'notification_friends_friendship_request','yes');}
		if($_POST['send_requests']=='false'){bp_update_user_meta($user_id,'notification_friends_friendship_request','no');}
		if($_POST['accept_requests']=='true'){bp_update_user_meta($user_id,'notification_friends_friendship_accepted','yes');}
		if($_POST['accept_requests']=='false'){bp_update_user_meta($user_id,'notification_friends_friendship_accepted','no');}
		if ($_POST['notification_groups_email_send']=='true') {bp_update_user_meta($user_id,'notification_follower_email_send','yes');}
		if ($_POST['notification_groups_email_send']=='false') {bp_update_user_meta($user_id,'notification_follower_email_send','no');}


		$oReturn->success->msg = __('User Email Settings Saved Successfully.','aheadzen');
	}
	return $oReturn;
}

public function profile_delete_profile() {
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
	if(!$_POST['userid']){$oReturn->error = __('Wrong User.','aheadzen'); return $oReturn;}
	if(!$_POST['cookie']){$oReturn->error = __('Cookie parameter is required.','aheadzen'); return $oReturn;}
	//if(!$_POST['pw']){$oReturn->error = __('Wrong Security Check.','aheadzen'); return $oReturn;}
	$userid = $_POST['userid'];
	//$pw = $_POST['pw'];
	$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');


	if($valid == $_POST['userid']){
		wp_set_current_user($userid);
		$res = $this->delete_core_user($userid);
		if($res){
			$oReturn->success->msg = __('User Deleted Successfully.','aheadzen');
		}else{
			$oReturn->error = __('User Delete Error.','aheadzen');
		}
	}else{
	   $oReturn->error = __('Wrong Security Check.','aheadzen');
	}
   return $oReturn;
}

public function delete_core_user($user_id)
{


	// Site admins cannot be deleted.
	if ( is_super_admin( $user_id ) ) {
		return false;
	}

	// Extra checks if user is not deleting themselves.
	if ( bp_loggedin_user_id() !== absint( $user_id ) ) {

		// Bail if current user cannot delete any users.
		/*if ( ! bp_current_user_can( 'delete_users' ) ) {
			return false;
		}*/

		// Bail if current user cannot delete this user.
		//if ( ! current_user_can_for_blog( bp_get_root_blog_id(), 'delete_user', $user_id ) ) {
		//	return false;
		//}
	}



	// Specifically handle multi-site environment.
	if ( is_multisite() ) {
		require_once( ABSPATH . '/wp-admin/includes/ms.php'   );
		require_once( ABSPATH . '/wp-admin/includes/user.php' );

		$retval = wpmu_delete_user( $user_id );

	// Single site user deletion.
	} else {
		require_once( ABSPATH . '/wp-admin/includes/user.php' );
		$retval = wp_delete_user( $user_id );
	}


	return $retval;

}

public function profile_remove_photo() {
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
	if(!$_POST['user_id']){$oReturn->error = __('Wrong User.','aheadzen'); return $oReturn;}
	if(!$_POST['clicked_pic']){$oReturn->error = __('Wrong User.','aheadzen'); return $oReturn;}
	$user_id = $_POST['user_id'];

	if(!aheadzen_check_valid_user($_POST['user_id'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}

	$deleted = 0;
	if($_POST['clicked_pic']=='cover_pic'){
		delete_user_meta( $user_id, 'bbp_cover_pic');
		$deleted = 1;
	}elseif($_POST['clicked_pic']=='profile_pic'){
		if (bp_core_delete_existing_avatar(array('item_id'=>$user_id,'object'=>'user'))){
			$deleted = 1;
		}
	}

	if($deleted){
		$avatar = bp_core_fetch_avatar( array(
				'object'  => 'user',
				'item_id' => $user_id,
				'html'    => false,
				'type'    => 'full',
			) );
		if($avatar && !strstr($avatar,'http:')){ $avatar = 'http:'.$avatar;}
		$oReturn->success->avatar = $avatar;
		$oReturn->success->msg = __('Profile Picture Deleted Successfully.','aheadzen');
	}else{
		$oReturn->error = __('Profile Picture Delete Error.','aheadzen');
	}
	return $oReturn;
}

function users_by_dob_zodiac(){
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	//if(!$_GET['zodiac']){$oReturn->message = __('Wrong Zodiac.','aheadzen'); return $oReturn;}
	$zodiac = $_GET['zodiac'];
	global $wpdb,$table_prefix;
	if($zodiac){
		$zodiacDateArr = array();
		$zodiacDateArr['aries']=array('03-21','04-20');
		$zodiacDateArr['taurus']=array('04-21','05-21');
		$zodiacDateArr['gemini']=array('05-22','06-21');
		$zodiacDateArr['cancer']=array('06-22','07-22');
		$zodiacDateArr['leo']=array('07-23','08-22');
		$zodiacDateArr['virgo']=array('08-23','09-22');
		$zodiacDateArr['libra']=array('09-23','10-22');
		$zodiacDateArr['scorpio']=array('10-23','11-21');
		$zodiacDateArr['sagittarius']=array('11-22','12-21');
		$zodiacDateArr['capricorn']=array('12-22','01-20');
		$zodiacDateArr['aquarius']=array('01-21','02-19');
		$zodiacDateArr['pisces']=array('02-20','03-20');

		$zodiac_date = $zodiacDateArr[$zodiac];
		if($zodiac_date){
			$sql = "SELECT user_id FROM ".$table_prefix."usermeta WHERE meta_key = 'birthday' AND STR_TO_DATE(meta_value, '%e-%c') BETWEEN '0000-".$zodiac_date[0]."' AND '0000-".$zodiac_date[1]."' ORDER  BY user_id DESC LIMIT 50";
			$users = $wpdb->get_col($sql);
			if($users){
				$zodiacMatchCounter = 0;
				for($u=0;$u<count($users);$u++){
					if(bp_get_user_has_avatar($users[$u])){
						$user = new BP_Core_User($users[$u]);
						if($user->avatar_thumb){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
							$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						}
						if($user->user_url){
							$username = str_replace('/','',str_replace(site_url('/members/'),'',$user->user_url));
						}
						$oReturn->zodiacmatch[$u]->id = $user->id;
						$oReturn->zodiacmatch[$u]->username = $username;
						$oReturn->zodiacmatch[$u]->fullname = bpaz_user_name_from_email($user->fullname);
						$oReturn->zodiacmatch[$u]->last_active = $user->last_active;
						$oReturn->zodiacmatch[$u]->avatar_thumb = $avatar_thumb;
						$oReturn->zodiacmatch[$u]->dob = get_user_meta($user->id,'birthday',true);
						$zodiacMatchCounter++;
						if($zodiacMatchCounter==4){break;}
					}
				}
			}
		}
	}
	if($_GET['dobOn']){
		$sql = "SELECT user_id FROM ".$table_prefix."usermeta WHERE meta_key = 'birthday' AND DATE_FORMAT(STR_TO_DATE(meta_value, '%e-%c'),'%m-%d') = DATE_FORMAT('".$_GET['dobOn']."','%m-%d') ORDER  BY user_id DESC LIMIT 50";
		$dobusers = $wpdb->get_col($sql);
		if($dobusers){
			$dobMatchCounter = 0;
			for($du=0;$du<count($dobusers);$du++){
				if(bp_get_user_has_avatar($dobusers[$du])){
					$user = new BP_Core_User($dobusers[$du]);
					if($user->avatar_thumb){
						preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
						$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
						if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
					}
					if($user->user_url){
						$username = str_replace('/','',str_replace(site_url('/members/'),'',$user->user_url));
					}
					$oReturn->dobmatch[$du]->id = $user->id;
					$oReturn->dobmatch[$du]->username = $username;
					$oReturn->dobmatch[$du]->fullname = bpaz_user_name_from_email($user->fullname);
					$oReturn->dobmatch[$du]->last_active = $user->last_active;
					$oReturn->dobmatch[$du]->avatar_thumb = $avatar_thumb;
					$oReturn->dobmatch[$du]->dob = get_user_meta($user->id,'birthday',true);
					$dobMatchCounter++;
					if($dobMatchCounter==4){break;}
			}
			}
		}
	}

	return $oReturn;
}
function messages_new_message(){
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
	if(!$_POST['subject']){$oReturn->message = __('Empty Subject.','aheadzen'); return $oReturn;}
	if(!$_POST['content']){$oReturn->message = __('Empty Content.','aheadzen'); return $oReturn;}
	if(!$_POST['sender_id']){$oReturn->message = __('Wrong sender id try.','aheadzen'); return $oReturn;}
	if(!$_POST['recipients']){$oReturn->message = __('Wrong Recipients.','aheadzen'); return $oReturn;}

	/*if(!aheadzen_check_valid_user($_POST['sender_id'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}*/
	$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');

	if($valid != $_POST['sender_id']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}

	wp_set_current_user($_POST['sender_id']);

	$recipients = $_POST['recipients'];
	$recipientsArr = explode(',',$recipients);
	$recipients1 = array();
	$username = array();
	if($recipientsArr){
		foreach($recipientsArr as $key => $val){
			$recipients1[] = trim(str_replace('@','',$val));
		}
	}
	$recipients = $recipients1;

	$result = messages_new_message( array('subject'=>$_POST['subject'], 'content' => $_POST['content'], 'sender_id' => $_POST['sender_id'], 'recipients' => $recipients ) );

	foreach($recipientsArr as $row){
		// Notification data making
			$userDataObj= get_userdata($_POST['sender_id']);
			$friendDataObj = get_userdata($row);
			$userData = $userDataObj->data;
			$friendData = $friendDataObj->data;
			$friendData->notification_type_id = 8;

			$message = $userData->display_name. " sent you a message.";
			$this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);
	}


	if(!empty( $result )){
		$oReturn->success->msg = __('Message added successfully.','aheadzen');
		$oReturn->success->id = $result;
	}else{
		$oReturn->error = __('Message add error.','aheadzen');
	}
	//echo '<pre>';print_r($oReturn);
	return $oReturn;
}
public function users_spam_user() {
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
	if(!$_POST['user_login']){$oReturn->error = __('Empty User Login ID.','aheadzen'); return $oReturn;}

	$user_login = $_POST['user_login'];
	$user_email = $_POST['user_email'];
	$registered = date('Y-m-d h:i:s');
	$activated = date('Y-m-d h:i:s');
	$active = 0;
	$activation_key = time();
	global $wpdb,$table_prefix;
	//$sql = "update ".$table_prefix."signups set active='0' where user_login=\"$user_login\"";
	$sql = "INSERT INTO ".$table_prefix."signups (user_login,user_email,registered,activated,active,activation_key) VALUES(\"$user_login\",\"$user_email\",\"$registered\",\"$activated\",\"$active\",\"$activation_key\") ON DUPLICATE KEY UPDATE user_login=\"$user_login\", user_email=\"$user_email\"";
	$result = $wpdb->query($sql);
	if($result){
		$oReturn->success->msg = __('User Spam Successfully.','aheadzen');
	}else{
		$oReturn->error = __('User Spam Error.','aheadzen');
	}
	return $oReturn;
}

public function comments_spam_comment() {
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
	if(!$_POST['comment_id']){$oReturn->error = __('Empty Comment ID.','aheadzen'); return $oReturn;}
	$comment_id = $_POST['comment_id'];
	$result = wp_set_comment_status( $comment_id, 'hold', true );
	if($result){
		$oReturn->success->msg = __('Comment Spam Successfully.','aheadzen');
	}else{
		$oReturn->error = __('Comment Spam Error.','aheadzen');
	}
	return $oReturn;
}

public function get_dashboard_members($user_id) {
	$returnArr = null;
	if(!$user_id){$user_id = $_GET['user_id'];}
	if($user_id){
		$following_ids = bp_get_following_ids(array('user_id'=>$user_id));
		$args = array(
					'type'     => 'active',
					'per_page' => 50,
				);
		if($following_ids){
			$args['exclude'] = $following_ids.','.$user_id;
		}else{
			$args['exclude'] = $user_id;
		}
		$counter = 0;
		$users = bp_core_get_users($args);
		if($users){
			foreach($users['users'] as $usersObj){
				if(bp_get_user_has_avatar($usersObj->ID)){
					$user = new BP_Core_User($usersObj->ID);
					if($user){
						$username = $avatar_thumb = '';
						if($user->avatar_thumb){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
							$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						}
						if($user->user_url){
							$username = str_replace('/','',str_replace(site_url('/members/'),'',$user->user_url));
						}

						$is_following = 0;
						if(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$user->id,'follower_id'=>$user_id))){
							$is_following = 1;
						}
						$returnArr[$counter]->id 		= $user->id;
						$returnArr[$counter]->username 	= $username;
						$returnArr[$counter]->fullname 	= bpaz_user_name_from_email($user->fullname);
						$returnArr[$counter]->email 	= $user->email;
						$returnArr[$counter]->last_active= $user->last_active;
						$returnArr[$counter]->avatar_thumb = $avatar_thumb;
						$returnArr[$counter]->is_following = $is_following;
						if($counter==10){break;}
						$counter++;
					}
				}
			}
		}
	}
	return $returnArr;
}

public function get_dashboard_groups($user_id) {
	$returnArr = null;
	if($user_id){
		$per_page = 10;
		global $table_prefix, $wpdb;
		$memberGroupSql = "select group_id from ".$table_prefix."bp_groups_members where user_id='".$user_id."'";
		$memberGroups = $wpdb->get_col($memberGroupSql);

		$aParams ['type'] = 'popular';
        $aParams ['per_page'] = $per_page;
		$aParams ['order'] = 'ASC';
		$aParams ['orderby'] = 'last_activity';
		$aParams ['exclude'] = $memberGroups;
		$aGroups = groups_get_groups($aParams);
		$counter = 0;
		foreach ($aGroups['groups'] as $aGroup) {
			if ($aGroup->status == "private" && !is_user_logged_in() && !$aGroup->is_member === true)
                continue;
			$returnArr[$counter]->id = $aGroup->id;
			$returnArr[$counter]->name = $aGroup->name;
            $returnArr[$counter]->slug = $aGroup->slug;
            $returnArr[$counter]->count_member = $aGroup->total_member_count;
			$avatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$aGroup->id, 'html'=>false, 'type'=>'full'));
			if($avatar_url && !strstr($avatar_url,'http:')){ $avatar_url = 'http:'.$avatar_url;}
			$returnArr[$counter]->avatar = $avatar_url;
			$counter++;
        }
	}
	return $returnArr;
}

public function get_unread_notification_count() {

	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_GET['user_id']){$oReturn->error = __('Empty User ID.','aheadzen'); return $oReturn;}
	$oReturn->notification_count = bp_notifications_get_unread_notification_count($_GET['user_id']);
	$oReturn->message_count = BP_Messages_Thread::get_inbox_count($_GET['user_id']);
	return $oReturn;
}

public function mark_notification_read() {

	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_GET['user_id']){$oReturn->error = __('Empty User ID.','aheadzen'); return $oReturn;}
	global $wpdb,$table_prefix;
	$user_id = $_GET['user_id'];
	$is_new = $_GET['is_new'];
	if($is_new){ $is_new=1; }else{ $is_new=0; }
	$wpdb->query("update ".$table_prefix."bp_notifications set is_new=\"$is_new\" where user_id=\"$user_id\"");
	$oReturn->success->msg = __('User Notifications marked Successfully.','aheadzen');
	return $oReturn;
}

public function read_unread_notification()
{
	//if(!$_GET['user_id']){$oReturn->error = __('Empty User ID.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['notification_id']){$oReturn->error = __('Empty Notification ID.','aheadzen'); return $oReturn;}
	// Get the action.
	$action = !empty( $_REQUEST['action']          ) ? $_REQUEST['action']          : 'unread';
	$id     = !empty( $_REQUEST['notification_id'] ) ? $_REQUEST['notification_id'] : '';


	// Bail if no action or no ID.
	/*if ( ( 'unread' !== $action ) || empty( $id )) {
		return false;
	}*/

	$notIds = explode(",",$_REQUEST['notification_id']);

	$bool = false;
	if($_REQUEST['action']  == "unread")
	{
		$bool = true;
	}

	foreach($notIds as $ID)
	{

		$res =  BP_Notifications_Notification::update(
			array( 'is_new' => $bool ),
			array( 'id'     => $ID )
		);

	}
	// Check the nonce and mark the notification.
	if ( $res ) {


		$oReturn = new stdClass();
		$oReturn->success = 'Notification successfully marked '.$_REQUEST['action'].'.';
		$oReturn->error = '';

	} else {

		$oReturn = new stdClass();
		$oReturn->success = 'There was a problem marking that notification.';
		$oReturn->error = '';

	}

	return $oReturn;
}

public function forum_topic_spam() {

	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Wrong Post Method.','aheadzen'); return $oReturn;}
	if(!$_POST['topic_id']){$oReturn->error = __('Empty Topic ID.','aheadzen'); return $oReturn;}

	$topic_id = $_POST['topic_id'];
	$topic = bbp_get_topic( $topic_id );
	if(empty($topic)){$oReturn->error = __('Topic Does Not Exists.','aheadzen');return  $oReturn;}
	$result = bbp_spam_topic( $topic_id );
	if($result){
		$oReturn->success->id = $topic_id;
		$oReturn->success->msg = __('Topic Spam Successfully.','aheadzen');
	}else{
		$oReturn->error = __('Topic Spam Error.','aheadzen');
	}
	return $oReturn;
}

public function forum_reply_spam() {

	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Wrong Post Method.','aheadzen'); return $oReturn;}
	if(!$_POST['reply_id']){$oReturn->error = __('Empty Reply ID.','aheadzen'); return $oReturn;}

	$reply_id = $_POST['reply_id'];
	$reply = bbp_get_reply( $reply_id );
	if(empty($reply)){$oReturn->error = __('Reply Does Not Exists.','aheadzen');return  $oReturn;}
	$result = bbp_spam_reply( $reply_id );
	if($result){
		$oReturn->success->id = $reply_id;
		$oReturn->success->msg = __('Reply Spam Successfully.','aheadzen');
	}else{
		$oReturn->error = __('Reply Spam Error.','aheadzen');
	}
	return $oReturn;
}

public function bbp_api_new_reply_handler() {

	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Wrong Post Method.','aheadzen'); return $oReturn;}
	if(!$_POST['bbp_reply_content']){$oReturn->error = __('Reply content should not empty.','aheadzen'); return $oReturn;}

	if($_POST['post_id']){
		$post_id = $_POST['post_id'];
		$reply_data = array(
			'ID'    => $post_id,
			'post_content'		=> $_POST['bbp_reply_content'],
		);
		$reply_id = wp_update_post( $reply_data );
		$oReturn->success->id = $reply_id;
		$oReturn->success->msg = __('Reply Added Successfully.','aheadzen');
		return  $oReturn;
	}

	// Define local variable(s)
	$topic_id = $forum_id = $reply_author = $anonymous_data = $reply_to = 0;
	$reply_title = $reply_content = $terms = '';
	/** Reply Author **********************************************************/
$reply_author = $_POST['user_id'];

	/** Topic ID **************************************************************/
	// Topic id was not passed
	if ( empty( $_POST['bbp_topic_id'] ) ) {
		bbp_add_error( 'bbp_reply_topic_id', __( '<strong>ERROR</strong>: Topic ID is missing.', 'bbpress' ) );
	// Topic id is not a number
	} elseif ( ! is_numeric( $_POST['bbp_topic_id'] ) ) {
		bbp_add_error( 'bbp_reply_topic_id', __( '<strong>ERROR</strong>: Topic ID must be a number.', 'bbpress' ) );
	// Topic id might be valid
	} else {
		// Get the topic id
		$posted_topic_id = intval( $_POST['bbp_topic_id'] );
		// Topic id is a negative number
		if ( 0 > $posted_topic_id ) {
			bbp_add_error( 'bbp_reply_topic_id', __( '<strong>ERROR</strong>: Topic ID cannot be a negative number.', 'bbpress' ) );
		// Topic does not exist
		} elseif ( ! bbp_get_topic( $posted_topic_id ) ) {
			bbp_add_error( 'bbp_reply_topic_id', __( '<strong>ERROR</strong>: Topic does not exist.', 'bbpress' ) );
		// Use the POST'ed topic id
		} else {
			$topic_id = $posted_topic_id;
		}
	}
	/** Forum ID **************************************************************/
	// Try to use the forum id of the topic
	if ( ! isset( $_POST['bbp_forum_id'] ) && ! empty( $topic_id ) ) {
		$forum_id = bbp_get_topic_forum_id( $topic_id );
	// Error check the POST'ed forum id
	} elseif ( isset( $_POST['bbp_forum_id'] ) ) {
		// Empty Forum id was passed
		if ( empty( $_POST['bbp_forum_id'] ) ) {
			bbp_add_error( 'bbp_reply_forum_id', __( '<strong>ERROR</strong>: Forum ID is missing.', 'bbpress' ) );
		// Forum id is not a number
		} elseif ( ! is_numeric( $_POST['bbp_forum_id'] ) ) {
			bbp_add_error( 'bbp_reply_forum_id', __( '<strong>ERROR</strong>: Forum ID must be a number.', 'bbpress' ) );
		// Forum id might be valid
		} else {
			// Get the forum id
			$posted_forum_id = intval( $_POST['bbp_forum_id'] );
			// Forum id is empty
			if ( 0 === $posted_forum_id ) {
				bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum ID is missing.', 'bbpress' ) );
			// Forum id is a negative number
			} elseif ( 0 > $posted_forum_id ) {
				bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum ID cannot be a negative number.', 'bbpress' ) );
			// Forum does not exist
			} elseif ( ! bbp_get_forum( $posted_forum_id ) ) {
				bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum does not exist.', 'bbpress' ) );
			// Use the POST'ed forum id
			} else {
				$forum_id = $posted_forum_id;
			}
		}
	}
	// Forum exists
	if ( ! empty( $forum_id ) ) {
		// Forum is a category
		if ( bbp_is_forum_category( $forum_id ) ) {
			bbp_add_error( 'bbp_new_reply_forum_category', __( '<strong>ERROR</strong>: This forum is a category. No replies can be created in this forum.', 'bbpress' ) );
		// Forum is not a category
		} else {
			// Forum is closed and user cannot access
			if ( bbp_is_forum_closed( $forum_id ) && !current_user_can( 'edit_forum', $forum_id ) ) {
				bbp_add_error( 'bbp_new_reply_forum_closed', __( '<strong>ERROR</strong>: This forum has been closed to new replies.', 'bbpress' ) );
			}
			// Forum is private and user cannot access
			if ( bbp_is_forum_private( $forum_id ) ) {
				if ( !current_user_can( 'read_private_forums' ) ) {
					bbp_add_error( 'bbp_new_reply_forum_private', __('<strong>ERROR</strong>: This forum is private and you do not have the capability to read or create new replies in it.', 'bbpress' ) );
				}
			// Forum is hidden and user cannot access
			} elseif ( bbp_is_forum_hidden( $forum_id ) ) {
				if ( !current_user_can( 'read_hidden_forums' ) ) {
					bbp_add_error( 'bbp_new_reply_forum_hidden', __( '<strong>ERROR</strong>: This forum is hidden and you do not have the capability to read or create new replies in it.', 'bbpress' ) );
				}
			}
		}
	}


	/** Reply Title ***********************************************************/
	if ( ! empty( $_POST['bbp_reply_title'] ) ) {
		$reply_title = sanitize_text_field( $_POST['bbp_reply_title'] );
	}
	// Filter and sanitize
	$reply_title = apply_filters( 'bbp_new_reply_pre_title', $reply_title );
	/** Reply Content *********************************************************/
	if ( ! empty( $_POST['bbp_reply_content'] ) ) {
		$reply_content = $_POST['bbp_reply_content'];
	}
	// Filter and sanitize
	$reply_content = apply_filters( 'bbp_new_reply_pre_content', $reply_content );
	// No reply content
	if ( empty( $reply_content ) ) {
		bbp_add_error( 'bbp_reply_content', __( '<strong>ERROR</strong>: Your reply cannot be empty.', 'bbpress' ) );
	}
	/** Reply Flooding ********************************************************/
	if ( ! bbp_check_for_flood( $anonymous_data, $reply_author ) ) {
		bbp_add_error( 'bbp_reply_flood', __( '<strong>ERROR</strong>: Slow down; you move too fast.', 'bbpress' ) );
	}
	/** Reply Duplicate *******************************************************/
	if ( ! bbp_check_for_duplicate( array( 'post_type' => bbp_get_reply_post_type(), 'post_author' => $reply_author, 'post_content' => $reply_content, 'post_parent' => $topic_id, 'anonymous_data' => $anonymous_data ) ) ) {
		bbp_add_error( 'bbp_reply_duplicate', __( '<strong>ERROR</strong>: Duplicate reply detected; it looks as though you&#8217;ve already said that!', 'bbpress' ) );
	}
	/** Reply Blacklist *******************************************************/
	if ( ! bbp_check_for_blacklist( $anonymous_data, $reply_author, $reply_title, $reply_content ) ) {
		bbp_add_error( 'bbp_reply_blacklist', __( '<strong>ERROR</strong>: Your reply cannot be created at this time.', 'bbpress' ) );
	}
	/** Reply Status **********************************************************/
	// Maybe put into moderation
	if ( ! bbp_check_for_moderation( $anonymous_data, $reply_author, $reply_title, $reply_content ) ) {
		$reply_status = bbp_get_pending_status_id();
	// Default
	} else {
		$reply_status = bbp_get_public_status_id();
	}
	/** Reply To **************************************************************/
	// Handle Reply To of the reply; $_REQUEST for non-JS submissions
	if ( isset( $_REQUEST['bbp_reply_to'] ) ) {
		$reply_to = bbp_validate_reply_to( $_REQUEST['bbp_reply_to'] );
	}
	/** Topic Closed **********************************************************/
	// If topic is closed, moderators can still reply
	if ( bbp_is_topic_closed( $topic_id ) && ! current_user_can( 'moderate' ) ) {
		bbp_add_error( 'bbp_reply_topic_closed', __( '<strong>ERROR</strong>: Topic is closed.', 'bbpress' ) );
	}
	/** Topic Tags ************************************************************/
	// Either replace terms
	if ( bbp_allow_topic_tags() && current_user_can( 'assign_topic_tags' ) && ! empty( $_POST['bbp_topic_tags'] ) ) {
		$terms = sanitize_text_field( $_POST['bbp_topic_tags'] );
	// ...or remove them.
	} elseif ( isset( $_POST['bbp_topic_tags'] ) ) {
		$terms = '';
	// Existing terms
	} else {
		$terms = bbp_get_topic_tag_names( $topic_id );
	}
	/** Additional Actions (Before Save) **************************************/
	do_action( 'bbp_new_reply_pre_extras', $topic_id, $forum_id );
	// Bail if errors
	if ( bbp_has_errors() ) {
		$errors = bbp_has_errors();
		$oReturn->error = __('Something Wrong While Insert Reply.','aheadzen'); return $oReturn;
	}
	/** No Errors *************************************************************/
	// Add the content of the form to $reply_data as an array
	// Just in time manipulation of reply data before being created
	$reply_data = apply_filters( 'bbp_new_reply_pre_insert', array(
		'post_author'    => $reply_author,
		'post_title'     => $reply_title,
		'post_content'   => $reply_content,
		'post_status'    => $reply_status,
		'post_parent'    => $topic_id,
		'post_type'      => bbp_get_reply_post_type(),
		'comment_status' => 'closed',
		'menu_order'     => bbp_get_topic_reply_count( $topic_id, true ) + 1
	) );
	// Insert reply
	$reply_id = wp_insert_post( $reply_data );
	/** No Errors *************************************************************/
	// Check for missing reply_id or error
	if ( ! empty( $reply_id ) && !is_wp_error( $reply_id ) ) {
		/** Topic Tags ********************************************************/
		// Just in time manipulation of reply terms before being edited
		$terms = apply_filters( 'bbp_new_reply_pre_set_terms', $terms, $topic_id, $reply_id );
		// Insert terms
		$terms = wp_set_post_terms( $topic_id, $terms, bbp_get_topic_tag_tax_id(), false );
		// Term error
		if ( is_wp_error( $terms ) ) {
			bbp_add_error( 'bbp_reply_tags', __( '<strong>ERROR</strong>: There was a problem adding the tags to the topic.', 'bbpress' ) );
		}
		/** Trash Check *******************************************************/
		// If this reply starts as trash, add it to pre_trashed_replies
		// for the topic, so it is properly restored.
		if ( bbp_is_topic_trash( $topic_id ) || ( $reply_data['post_status'] === bbp_get_trash_status_id() ) ) {
			// Trash the reply
			wp_trash_post( $reply_id );
			// Only add to pre-trashed array if topic is trashed
			if ( bbp_is_topic_trash( $topic_id ) ) {
				// Get pre_trashed_replies for topic
				$pre_trashed_replies = get_post_meta( $topic_id, '_bbp_pre_trashed_replies', true );
				// Add this reply to the end of the existing replies
				$pre_trashed_replies[] = $reply_id;
				// Update the pre_trashed_reply post meta
				update_post_meta( $topic_id, '_bbp_pre_trashed_replies', $pre_trashed_replies );
			}
		/** Spam Check ********************************************************/
		// If reply or topic are spam, officially spam this reply
		} elseif ( bbp_is_topic_spam( $topic_id ) || ( $reply_data['post_status'] === bbp_get_spam_status_id() ) ) {
			add_post_meta( $reply_id, '_bbp_spam_meta_status', bbp_get_public_status_id() );
			// Only add to pre-spammed array if topic is spam
			if ( bbp_is_topic_spam( $topic_id ) ) {
				// Get pre_spammed_replies for topic
				$pre_spammed_replies = get_post_meta( $topic_id, '_bbp_pre_spammed_replies', true );
				// Add this reply to the end of the existing replies
				$pre_spammed_replies[] = $reply_id;
				// Update the pre_spammed_replies post meta
				update_post_meta( $topic_id, '_bbp_pre_spammed_replies', $pre_spammed_replies );
			}
		}
		/** Update counts, etc... *********************************************/
		do_action( 'bbp_new_reply', $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, false, $reply_to );
		/** Additional Actions (After Save) ***********************************/
		do_action( 'bbp_new_reply_post_extras', $reply_id );

		$success = $reply_id;
		$oReturn->success->ID = $reply_id;
		$oReturn->success->msg = __('Reply Post Success.','aheadzen');

	/** Errors ****************************************************************/
	} else {
		$append_error = ( is_wp_error( $reply_id ) && $reply_id->get_error_message() ) ? $reply_id->get_error_message() . ' ' : '';
		bbp_add_error( 'bbp_reply_error', __( '<strong>ERROR</strong>: The following problem(s) have been found with your reply:' . $append_error . 'Please try again.', 'bbpress' ) );
		$errors = bbp_has_errors();
		$oReturn->error = __('Something Wrong While Insert Reply.','aheadzen'); return $oReturn;
  }
  return $oReturn;
}


public function bbp_api_new_topic_handler() {
	header("Access-Control-Allow-Origin: *");
	$oReturn = new stdClass();
	$oReturn->success = '';
	$oReturn->error = '';
	if(!$_POST){$oReturn->error = __('Wrong Post Method.','aheadzen'); return $oReturn;}
	if(!$_POST['bbp_topic_title']){$oReturn->error = __('Title should not empty.','aheadzen'); return $oReturn;}
	if(!$_POST['bbp_topic_content']){$oReturn->error = __('Content should not empty.','aheadzen'); return $oReturn;}

	if($_POST['topic_id']){
		$topic_id  = $_POST['topic_id'];
		if(function_exists( 'bbp_get_version' )){ //New  Version
			$topic_data = array(
					'post_title'	=> $_POST['bbp_topic_title'],
					'post_content'	=> $_POST['bbp_topic_content'],
					'ID'			=> $topic_id,
				);
			$topic_id = wp_update_post($topic_data);
			$oReturn->success->msg = __('Topic Edited Successfully.','aheadzen');
		}else{
				$topic_data = array(
					'topic_title' 	=> $_POST['bbp_topic_title'],
					'topic_text'  	=> $_POST['bbp_topic_content'],
					'topic_id'  	=> $topic_id,
				);
				$topic_id = bp_forums_update_topic($topic_data); //Update Topic
				$oReturn->success->msg = __('Topic Edited Successfully.','aheadzen');
		}
		return $oReturn;
	}

	// Define local variable(s)
	$view_all = false;
	$forum_id = $topic_author = $anonymous_data = 0;
	$topic_title = $topic_content = '';
	$terms = array( bbp_get_topic_tag_tax_id() => array() );
	/** Topic Author **********************************************************/
	$topic_author = $_POST['user_id'];

	/** Topic Title ***********************************************************/
	if ( ! empty( $_POST['bbp_topic_title'] ) ) {
		$topic_title = sanitize_text_field( $_POST['bbp_topic_title'] );
	}
	// Filter and sanitize
	$topic_title = apply_filters( 'bbp_new_topic_pre_title', $topic_title );
	// No topic title
	if ( empty( $topic_title ) ) {
		bbp_add_error( 'bbp_topic_title', __( '<strong>ERROR</strong>: Your topic needs a title.', 'bbpress' ) );
	}
	/** Topic Content *********************************************************/
	if ( ! empty( $_POST['bbp_topic_content'] ) ) {
		$topic_content = $_POST['bbp_topic_content'];
	}
	// Filter and sanitize
	$topic_content = apply_filters( 'bbp_new_topic_pre_content', $topic_content );
	// No topic content
	if ( empty( $topic_content ) ) {
		bbp_add_error( 'bbp_topic_content', __( '<strong>ERROR</strong>: Your topic cannot be empty.', 'bbpress' ) );
	}
	/** Topic Forum ***********************************************************/
	// Error check the POST'ed topic id
	if ( isset( $_POST['bbp_forum_id'] ) ) {
		// Empty Forum id was passed
		if ( empty( $_POST['bbp_forum_id'] ) ) {
			bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum ID is missing.', 'bbpress' ) );
		// Forum id is not a number
		} elseif ( ! is_numeric( $_POST['bbp_forum_id'] ) ) {
			bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum ID must be a number.', 'bbpress' ) );
		// Forum id might be valid
		} else {
			// Get the forum id
			$posted_forum_id = intval( $_POST['bbp_forum_id'] );
			// Forum id is empty
			if ( 0 === $posted_forum_id ) {
				bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum ID is missing.', 'bbpress' ) );
			// Forum id is a negative number
			} elseif ( 0 > $posted_forum_id ) {
				bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum ID cannot be a negative number.', 'bbpress' ) );
			// Forum does not exist
			} elseif ( ! bbp_get_forum( $posted_forum_id ) ) {
				bbp_add_error( 'bbp_topic_forum_id', __( '<strong>ERROR</strong>: Forum does not exist.', 'bbpress' ) );
			// Use the POST'ed forum id
			} else {
				$forum_id = $posted_forum_id;
			}
		}
	}
	// Forum exists
	if ( ! empty( $forum_id ) ) {
		// Forum is a category
		if ( bbp_is_forum_category( $forum_id ) ) {
			bbp_add_error( 'bbp_new_topic_forum_category', __( '<strong>ERROR</strong>: This forum is a category. No topics can be created in this forum.', 'bbpress' ) );
		// Forum is not a category
		} else {
			// Forum is closed and user cannot access
			if ( bbp_is_forum_closed( $forum_id ) && ! current_user_can( 'edit_forum', $forum_id ) ) {
				bbp_add_error( 'bbp_new_topic_forum_closed', __( '<strong>ERROR</strong>: This forum has been closed to new topics.', 'bbpress' ) );
			}
			// Forum is private and user cannot access
			if ( bbp_is_forum_private( $forum_id ) ) {
				if ( ! current_user_can( 'read_private_forums' ) ) {
					bbp_add_error( 'bbp_new_topic_forum_private', __( '<strong>ERROR</strong>: This forum is private and you do not have the capability to read or create new topics in it.', 'bbpress' ) );
				}
			// Forum is hidden and user cannot access
			} elseif ( bbp_is_forum_hidden( $forum_id ) ) {
				if ( ! current_user_can( 'read_hidden_forums' ) ) {
					bbp_add_error( 'bbp_new_topic_forum_hidden', __( '<strong>ERROR</strong>: This forum is hidden and you do not have the capability to read or create new topics in it.', 'bbpress' ) );
				}
			}
		}
	}
	/** Topic Flooding ********************************************************/
	if ( ! bbp_check_for_flood( $anonymous_data, $topic_author ) ) {
		bbp_add_error( 'bbp_topic_flood', __( '<strong>ERROR</strong>: Slow down; you move too fast.', 'bbpress' ) );
	}
	/** Topic Duplicate *******************************************************/
	if ( ! bbp_check_for_duplicate( array( 'post_type' => bbp_get_topic_post_type(), 'post_author' => $topic_author, 'post_content' => $topic_content, 'anonymous_data' => $anonymous_data ) ) ) {
		bbp_add_error( 'bbp_topic_duplicate', __( '<strong>ERROR</strong>: Duplicate topic detected; it looks as though you&#8217;ve already said that!', 'bbpress' ) );
	}
	/** Topic Blacklist *******************************************************/
	if ( ! bbp_check_for_blacklist( $anonymous_data, $topic_author, $topic_title, $topic_content ) ) {
		bbp_add_error( 'bbp_topic_blacklist', __( '<strong>ERROR</strong>: Your topic cannot be created at this time.', 'bbpress' ) );
	}
	/** Topic Status **********************************************************/
	// Maybe put into moderation
	if ( ! bbp_check_for_moderation( $anonymous_data, $topic_author, $topic_title, $topic_content ) ) {
		$topic_status = bbp_get_pending_status_id();
	// Check a whitelist of possible topic status ID's
	} elseif ( ! empty( $_POST['bbp_topic_status'] ) && in_array( $_POST['bbp_topic_status'], array_keys( bbp_get_topic_statuses() ) ) ) {
		$topic_status = sanitize_key( $_POST['bbp_topic_status'] );
	// Default to published if nothing else
	} else {
		$topic_status = bbp_get_public_status_id();
	}
	/** Topic Tags ************************************************************/
	if ( bbp_allow_topic_tags() && ! empty( $_POST['bbp_topic_tags'] ) ) {
		// Escape tag input
		$terms = sanitize_text_field( $_POST['bbp_topic_tags'] );
		// Explode by comma
		if ( strstr( $terms, ',' ) ) {
			$terms = explode( ',', $terms );
		}
		// Add topic tag ID as main key
		$terms = array( bbp_get_topic_tag_tax_id() => $terms );
	}
	/** Additional Actions (Before Save) **************************************/
	do_action( 'bbp_new_topic_pre_extras', $forum_id );
	// Bail if errors
	if ( bbp_has_errors() ) {
		$errors = bbp_has_errors();
		if(!$_POST){$oReturn->error = __('Wrong Data Insertion Error..','aheadzen'); return $oReturn;}
	}
	/** No Errors *************************************************************/
	// Add the content of the form to $topic_data as an array.
	// Just in time manipulation of topic data before being created
	$topic_data = apply_filters( 'bbp_new_topic_pre_insert', array(
		'post_author'    => $topic_author,
		'post_title'     => $topic_title,
		'post_content'   => $topic_content,
		'post_status'    => $topic_status,
		'post_parent'    => $forum_id,
		'post_type'      => bbp_get_topic_post_type(),
		'tax_input'      => $terms,
		'comment_status' => 'closed'
	) );
	// Insert topic
	$topic_id = wp_insert_post( $topic_data );
	/** No Errors *************************************************************/
	if ( ! empty( $topic_id ) && ! is_wp_error( $topic_id ) ) {
		/** Close Check *******************************************************/
		// If the topic is closed, close it properly
		if ( ( get_post_field( 'post_status', $topic_id ) === bbp_get_closed_status_id() ) || ( $topic_data['post_status'] === bbp_get_closed_status_id() ) ) {
			// Close the topic
			bbp_close_topic( $topic_id );
		}
		/** Trash Check *******************************************************/
		// If the forum is trash, or the topic_status is switched to
		// trash, trash the topic properly
		if ( ( get_post_field( 'post_status', $forum_id ) === bbp_get_trash_status_id() ) || ( $topic_data['post_status'] === bbp_get_trash_status_id() ) ) {
			// Trash the topic
			wp_trash_post( $topic_id );
			// Force view=all
			$view_all = true;
		}
		/** Spam Check ********************************************************/
		// If the topic is spam, officially spam this topic
		if ( $topic_data['post_status'] === bbp_get_spam_status_id() ) {
			add_post_meta( $topic_id, '_bbp_spam_meta_status', bbp_get_public_status_id() );
			// Force view=all
			$view_all = true;
		}
		/** Update counts, etc... *********************************************/
		do_action( 'bbp_new_topic', $topic_id, $forum_id, $anonymous_data, $topic_author );
		/** Stickies **********************************************************/
		// Sticky check after 'bbp_new_topic' action so forum ID meta is set
		if ( ! empty( $_POST['bbp_stick_topic'] ) && in_array( $_POST['bbp_stick_topic'], array( 'stick', 'super', 'unstick' ) ) ) {
			// What's the caps?
			if ( current_user_can( 'moderate' ) ) {
				// What's the haps?
				switch ( $_POST['bbp_stick_topic'] ) {
					// Sticky in this forum
					case 'stick'   :
						bbp_stick_topic( $topic_id );
						break;
					// Super sticky in all forums
					case 'super'   :
						bbp_stick_topic( $topic_id, true );
						break;
					// We can avoid this as it is a new topic
					case 'unstick' :
					default        :
						break;
				}
			}
		}
		/** Additional Actions (After Save) ***********************************/
		do_action( 'bbp_new_topic_post_extras', $topic_id );

		$sucess = $topic_id;
		$oReturn->success->ID = $topic_id;
		$oReturn->success->msg = 'Topic Added Successfully';
	// Errors
	} else {
		$append_error = ( is_wp_error( $topic_id ) && $topic_id->get_error_message() ) ? $topic_id->get_error_message() . ' ' : '';
		bbp_add_error( 'bbp_topic_error', __( '<strong>ERROR</strong>: The following problem(s) have been found with your topic:' . $append_error, 'bbpress' ) );
		$errors = bbp_has_errors();
		if(!$_POST){$oReturn->error = __('Wrong Data Insertion Error..','aheadzen'); return $oReturn;}
  }

  return $oReturn;
}

	/************************************************
	Get Post Forum Detail
	************************************************/
	 public function get_forum_topic_detail() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_GET['t_id']){$oReturn->error = __('Wrong Topic ID.','aheadzen'); return $oReturn;}
		$topic_id = $_GET['t_id'];
		if(function_exists('bp_forums_get_topic_details')){
			$response = bp_forums_get_topic_details( $topic_id );
			$oReturn->topic->id = $response->topic_id;
			$oReturn->topic->title = $response->topic_title;
			$oReturn->topic->content = $response->topic_content;
			$oReturn->topic->slug = $response->topic_slug;
			$oReturn->topic->poster->id = $response->topic_poster;
			$oReturn->topic->poster->name = $response->topic_poster_name;
			$oReturn->topic->lastposter->id = $response->topic_last_poster;
			$oReturn->topic->lastposter->name = $response->topic_last_poster_name;
			$oReturn->topic->start_time = $response->topic_start_time;
			$oReturn->topic->time = $response->topic_time;
			$oReturn->topic->last_post_id = $response->topic_last_post_id;
			$oReturn->topic->forum_name = $response->object_name;
			$oReturn->topic->forum_slug = $response->object_slug;
		}elseif(function_exists('bbp_get_topic')){
			$response = bbp_get_topic($topic_id);
			$oReturn->topic->id = $response->ID;
			$oReturn->topic->title = $response->post_title;
			$oReturn->topic->content = $response->post_content;
			$oReturn->topic->slug = $response->post_name;

			$user = new BP_Core_User($response->post_author);
			$oReturn->topic->poster->id = $user->id;
			$oReturn->topic->poster->name = $user->fullname;

			$oReturn->topic->start_time = $response->post_date;
			$oReturn->topic->time = $response->post_date;

			$last_reply_id = bbp_get_topic_last_reply_id($topic_id);
			if($last_reply_id){
				$reply = bbp_get_reply($last_reply_id);
				$user = new BP_Core_User($reply->post_author);

				$oReturn->topic->last_post_id = $last_reply_id;
				$oReturn->topic->lastposter->id = $user->id;
				$oReturn->topic->lastposter->name = $user->fullname;
			}

			$oForum = bbp_get_forum((int)$response->post_parent);
			$oReturn->topic->forum_name = $oForum->post_title;
			$oReturn->topic->forum_slug = $oForum->post_name;

		}
		/*if (function_exists( 'bbp_get_version' )){ //New  Version
			$response = bp_forums_get_topic_details( $topic_id );
		}else{ //OLD Version
			//$response = bp_forums_delete_topic(array('post_id' => $post_id));
		}*/

		return  $oReturn;
	 }

	/************************************************
	Get Post Forum Detail
	************************************************/
	 public function get_forum_post_topic_detail() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_GET['p_id']){$oReturn->error = __('Wrong Post ID.','aheadzen'); return $oReturn;}
		$post_id = $_GET['p_id'];

		if(function_exists('bp_forums_get_post')){
			$response = bp_forums_get_post( $post_id );
			$oReturn->post->id = $response->post_id;
			$oReturn->post->forum_id = $response->forum_id;
			$oReturn->post->topic_id = $response->topic_id;
			$oReturn->post->poster_id = $response->poster_id;
			$oReturn->post->post_title = '';
			$oReturn->post->post_text = $response->post_text;
			$oReturn->post->post_time = $response->post_time;
			$oReturn->post->post_status = $response->post_status;
		}elseif(function_exists('bbp_get_reply')){
			$response = bbp_get_reply($post_id);
			$oReturn->post->id = $response->ID;
			$oReturn->post->topic_id = $response->post_parent;
			$oReturn->post->poster_id = $response->post_author;
			$oReturn->post->post_title = $response->post_title;
			$oReturn->post->post_text = $response->post_content;
			$oReturn->post->post_time = $response->post_date;
			$oReturn->post->post_status = $response->post_status;
			$topicResponse = bbp_get_topic($response->post_parent);
			$oReturn->post->topic_title = $topicResponse->post_title;
			$oReturn->post->topic_slug = $topicResponse->post_name;
			$oReturn->post->forum_id = $topicResponse->post_parent;
		}

		return  $oReturn;
	 }


	/************************************************
	Post Forum Topic Delete
	************************************************/
	 public function forum_post_topic_delete() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['t_id']){$oReturn->error = __('Wrong Topic ID.','aheadzen'); return $oReturn;}
		if(!$_POST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}

		$topic_id = $_POST['t_id'];
		$action = 'bbp_toggle_topic_trash';

		wp_set_current_user($_POST['user_id']);
		if('bbp_toggle_topic_trash' === $action && !current_user_can( 'delete_topic', $topic_id)){
			$oReturn->success->error = __('Current User cannot delete topic reply.','aheadzen');return  $oReturn;
		}
		if(!aheadzen_check_valid_user($_POST['user_id'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}

		$topic = bbp_get_topic( $topic_id );
		if(empty($topic)){
			$oReturn->success->error = __('Topic Does Not Exists.','aheadzen');return  $oReturn;
		}

		// Do additional topic toggle actions
		$response = wp_trash_post( $topic_id );
		$post_data = array( 'ID' => $topic_id ); // Prelim array
		do_action( 'bbp_toggle_topic_handler', $success, $post_data, $action );
		bp_activity_delete( array('item_id' => $topic_id,'type' => 'bbp_topic_create'));
		bp_activity_delete( array('secondary_item_id' => $topic_id,'type' => 'bbp_reply_create'));
		//$response = bbp_delete_topic($topic_id);
		if($response){
			$oReturn->success->id = $topic_id;
			$oReturn->success->message = __('Topic Deleted Successfully.','aheadzen');
		}else{
			$oReturn->success->error = __('Topic Delete Error.','aheadzen');
		}
		return  $oReturn;
	 }

	/************************************************
	Forum Topic Post Delete
	************************************************/
	 public function forum_post_topicpost_delete() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['p_id']){$oReturn->error = __('Wrong Post ID.','aheadzen'); return $oReturn;}
		if(!$_POST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		$post_id = $_POST['p_id'];
		$action = 'bbp_toggle_reply_trash';

		if(!aheadzen_check_valid_user($_POST['user_id'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}

		wp_set_current_user($_POST['user_id']);
		if('bbp_toggle_reply_trash' === $action && !current_user_can( 'delete_reply', $post_id)){
			$oReturn->error = __('Current User cannot delete topic reply.','aheadzen');return  $oReturn;
		}

		$reply = bbp_get_reply( $post_id );
		if(empty($reply)){
			$oReturn->error = __('Topic Post Does Not Exists.','aheadzen');return  $oReturn;
		}

		// Do additional reply toggle actions
		$response = wp_trash_post( $post_id );
		$post_data = array( 'ID' => $post_id ); // Prelim array
		do_action( 'bbp_toggle_reply_handler', $response, $post_data, $action );
		bp_activity_delete( array('item_id' => $post_id,'type' => 'bbp_reply_create'));
		//$response = bbp_delete_reply($post_id);
		if($response){
			$oReturn->success->id = $post_id;
			$oReturn->success->message = __('Topic Post Deleted Successfully.','aheadzen');
		}else{
			$oReturn->success->error = __('Topic Post Delete Error.','aheadzen');
		}
		return  $oReturn;
	 }

	/************************************************
	Post Forum Topic
	************************************************/
	 public function forum_post_topic() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!$_POST['title']){$oReturn->error = __('Title is empty.','aheadzen'); return $oReturn;}
		if(!$_POST['content']){$oReturn->error = __('Content is empty.','aheadzen'); return $oReturn;}
		if(!$_POST['f_id']){$oReturn->error = __('Wrong Forum ID.','aheadzen'); return $oReturn;}

		if(!aheadzen_check_valid_user($_POST['user_id'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}

		$user_id = $_POST['user_id'];
		$title = trim($_POST['title']);
		$content = trim($_POST['content']);
		$forum_id = $_POST['f_id'];
		$topic_id = $_POST['t_id'];
		$terms = array();

		// Insert topic
		if (function_exists( 'bbp_get_version' )){ //New  Version
			// Create the initial topic
			$arg1 = array(
					'post_parent'  => $forum_id,
					'post_title'   => $title,
					'post_content' => $content,
					'post_author'    => $user_id,
				);
			if($topic_id){
				$arg1['ID'] = $topic_id;
				$topic_id = wp_update_post( $arg1 );
			}else{
				$topic_id = bbp_insert_topic($arg1,array( 'forum_id'  => $forum_id ));
			}
		}else{ //OLD Version
			$topic_data = array(
				'topic_title' => $title,
				'topic_text'  => $content,
			);
			 if($topic_id){
				$topic_data['topic_id'] = $topic_id;
				$topic_id = bp_forums_update_topic($topic_data); //Update Topic
				$successmsg = __('Topic Edited Error.','aheadzen');
			 }else{
				 $topic_data['topic_poster'] = $user_id;
				 $topic_data['forum_id'] = $forum_id;
				$topic_id =  bp_forums_new_topic($topic_data);  //Insert Topic
				$successmsg = __('Topic Add Error.','aheadzen');
			 }
		}

		if($topic_id){
			$oReturn->success->id = $topic_id;
			$oReturn->success->message = $successmsg;
		}else{
			$oReturn->success->error = __('Topic Add/Edit Error.','aheadzen');
		}
		return  $oReturn;
	 }

	 /************************************************
	Post Forum Topic
	************************************************/
	 public function forum_post_topicpost() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!$_POST['content']){$oReturn->error = __('Content is empty.','aheadzen'); return $oReturn;}
		if(!$_POST['t_id']){$oReturn->error = __('Wrong Topic ID.','aheadzen'); return $oReturn;}

		if(!aheadzen_check_valid_user($_POST['user_id'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}

		$userid = trim($_POST['user_id']);
		$title = '';
		$content = trim($_POST['content']);
		$topic_id = trim($_POST['t_id']);
		$terms = array();
		$post_id = $_POST['p_id']; //To Edit Post
		$successmessage = __('Topic Reply Added Successfully.','aheadzen');
		if($topic_id){ $successmessage = __('Topic Reply Edited Successfully.','aheadzen'); }

		// Insert reply
		if (function_exists( 'bbp_get_version' )){ //New  Version
			$reply_data = array(
				'post_author'    => $userid,
				'post_title'     => $title,
				'post_content'   => $content,
				'post_parent'    => $topic_id,
				'post_type'      => bbp_get_reply_post_type(),
			);
			if($post_id){ $reply_data['ID']=$post_id; }
			$reply_id = bbp_insert_reply($reply_data);

		}else{ //OLD Version
			 $reply_data = array(
			  'post_id'       => $post_id,
			  'topic_id'      => $topic_id,
			  'post_text'     => $content,
			  'poster_id'     => $userid, // accepts ids or names
			 );
			$reply_id = bp_forums_insert_post($reply_data);
		}

		if($reply_id){
			$oReturn->success->id = $reply_id;
			$oReturn->success->message = $successmessage;
		}else{
			$oReturn->success->error = __('Topic Reply Add/Edit Error.','aheadzen');
		}
		return  $oReturn;
	 }


	/************************************************
	Change Password
	************************************************/
	 public function profile_change_pw() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['userid']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!$_POST['email']){$oReturn->error = __('Email address is a required field.','aheadzen'); return $oReturn;}
		if(!$_POST['cookie']){$oReturn->error = __('Cookie is a required field.','aheadzen'); return $oReturn;}
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){$oReturn->error = __('Invalid Email.','aheadzen'); return $oReturn;}
		//if(!$_POST['pw']){$oReturn->error = __('Current password is wrong.','aheadzen'); return $oReturn;}
		if(!$_POST['npw']){$oReturn->error = __('New password is wrong.','aheadzen'); return $oReturn;}
		if(!$_POST['confirmpw']){$oReturn->error = __('New confirm password is wrong.','aheadzen'); return $oReturn;}
		if($_POST['confirmpw']!=$_POST['npw']){$oReturn->error = __('Password should be same.','aheadzen'); return $oReturn;}

		$userid = $_POST['userid'];
		//$pw = trim($_POST['npw']);
		$user_email = trim($_POST['email']);

		$user_id = wp_update_user(array('ID' =>$userid,'user_email'=> $user_email));
		$userData = get_user_by( 'email', $user_email );
		$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');
		if($valid != $_POST['userid']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}
		$user = get_userdata($_POST['userid']);
		//$user = wp_authenticate($userData->user_login, $_POST['pw']);

		if ( !empty($user) && isset($user->ID) ) {
			wp_set_password($pw,$userid );
			wp_password_change_notification($user);

		}else{
			$oReturn = new stdClass();
			$oReturn->msg = "Not authenticated user or provide wrong old password.";
			$oReturn->error = 'error';
			$oReturn->status = 'ok';
			return $oReturn;
		}
		$oReturn->status = 'ok';
		$oReturn->success->id = $userid;
		$oReturn->success->pw = $pw;
		$oReturn->success->email = $user_email;
		$oReturn->success->message = __('Password Updated Successfully.','aheadzen');
		return  $oReturn;
	}

	/************************************************
	Change Password
	************************************************/
	 public function user_profile_gallery() {

		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_GET['userid']){$oReturn->error = __('Wrong User.','aheadzen'); return $oReturn;}
		$perpage = $_GET['perpage'];
		$thepage = $_GET['thepage'];
		if(!$perpage){$perpage=20;}
		if(!$thepage){$thepage=1;}
		$starter = $perpage*($thepage-1);
		$laster = $perpage*($thepage);

		$files = array();
		$bp_upload = xprofile_avatar_upload_dir('',$_GET['userid']);
		$basedir = $bp_upload['path'];
		$baseurl = $bp_upload['url'];
		$dh  = opendir($basedir);

		$imageCounter = 0;
		$counter=0;
		while (false !== ($filename = readdir($dh))) {
			if($filename=='.' || $filename=='..'){
			}else{
				if(file_exists($basedir.'/'.$filename)){
					$imageCounter++;
					if($imageCounter>=$starter && $imageCounter<$laster){
						if(strstr($filename,'-bpfbt.') || strstr($filename,'-bpthumb.')){

						}else{
							$oReturn->images[$counter]->src = $baseurl.'/'.$filename;
							$oReturn->images[$counter]->sub = '';
							$counter++;
						}
					}
					if($imageCounter>$laster){break;}

				}
			}
		}
		return  $oReturn;
	}

	function upload_image_to_user()
	{
		header("Access-Control-Allow-Origin: *");
		$post_data = array();
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if($_GET['image']==''){$oReturn->error = __('Wrong image URL','aheadzen'); return $oReturn;}
		if($_GET['userid']==''){$oReturn->error = __('Wrong User ID','aheadzen'); return $oReturn;}
		$user_id = $_GET['userid'];
		$image = $_GET['image'];
		$ext = pathinfo($image, PATHINFO_EXTENSION);
		$imageFilename = basename($image,'.'.$ext);

		$bp_upload = xprofile_avatar_upload_dir('',$user_id);
		$basedir = $bp_upload['path'];
		$baseurl = $bp_upload['url'];
		if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
		$filename = 'avatar_'.$user_id.'.jpg';
		$outputFile = $basedir.'/'.$filename;
		$imageurl = $baseurl.'/'.$filename;
		$cp = copy($image, $outputFile);

		$imgdata = @getimagesize( $outputFile );
		$img_width = $imgdata[0];
		$img_height = $imgdata[1];
		$upload_dir = wp_upload_dir();
		$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );
		$args = array(
			'item_id'       => $user_id,
			'original_file' => $existing_avatar_path,
			'crop_x'        => 0,
			'crop_y'        => 0,
			'crop_w'        => $img_width,
			'crop_h'        => $img_height
		);

		// Add the activity
		if($outputFile && function_exists('bp_activity_add')){
			bp_activity_add( array(
				'user_id'   => $user_id,
				'component' => 'profile',
				'type'      => 'new_avatar'
			));
		}
		if (bp_core_avatar_handle_crop( $args ) ) {
			$imageurl = bp_core_fetch_avatar( array( 'item_id' => $user_id,'html'=>false,'type' => 'thumb'));
			$oReturn->success->msg = 'Image uploaded successfully.';
			$oReturn->success->url = $imageurl;
		}else{
			$oReturn->error = 'Upload error';
		}
		if(file_exists($outputFile)){@unlink($outputFile);}
		return $oReturn;

	}
	/*
	Share to Users -- http://localhost/api/buddypressread/share_activity_data/?id=19&ptype=post&userid=1&shareto=user&sharetouser=@buyer1,@chynna,@testuser5
	Share to Activity -- http://localhost/api/buddypressread/share_activity_data/?id=19&ptype=post&userid=1
	Share to Group -- http://localhost/api/buddypressread/share_activity_data/?id=19&ptype=post&userid=1&shareto=group&sharetogroup=1
	id = post id, page id, forum topic id.....
	ptype = post type like post, page,forum topic
	userid = poster user id/current logged user id
	shareto =
		keep blank -- for activity share
		group -- for share in group activity
		user -- for share in users mention list
	sharetouser = user mention id like 	:: @buyer1,@chynna,@testuser5
	sharetogroup = group id to which group user want to share
	*/
	function share_activity_data()
	{
		header("Access-Control-Allow-Origin: *");
		$post_data = array();
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if($_GET['id']==''){$oReturn->error = __('Wrong activity ID','aheadzen'); return $oReturn;}
		if($_GET['userid']==''){$oReturn->error = __('Wrong User ID','aheadzen'); return $oReturn;}

		$post_data['aid'] = $_GET['id'];
		$post_data['userid'] = $_GET['userid'];
		$post_data['sharetogroup']=$post_data['mentions']='';
		$post_data['shareto'] = $_GET['shareto'];
		if($_GET['shareto']=='user'){
			if($_GET['sharetouser']==''){$oReturn->error = __('Wrong User ID','aheadzen'); return $oReturn;}
			$post_data['mentions'] = $_GET['sharetouser'];
		}elseif($_GET['shareto']=='group'){
			if($_GET['sharetogroup']==''){$oReturn->error = __('Wrong Group ID','aheadzen'); return $oReturn;}
			$post_data['sharetogroup'] = $_GET['sharetogroup'];
		}

		$activitys = bp_activity_get(array('in'	=> $post_data['aid']));
		if(!$activitys){$oReturn->error = __('Wrong activity ID','aheadzen'); return $oReturn;}
		$activitie = $activitys['activities'][0];
		$post_data['activity_user_id'] = $activitie->user_id;
		$activity_content = $post_data['mentions'];
		$activity_action = '';
		$display_name = bp_core_get_user_displaynames($post_data['userid']);
		$add_primary_link     = bp_core_get_userlink($post_data['userid'], false, true );
		$author_display_name = bp_core_get_user_displaynames($post_data['activity_user_id']);
		$author_display_name = $author_display_name[$post_data['activity_user_id']];
		$author_primary_link     = bp_core_get_userlink($post_data['activity_user_id'], false, true );
		$activity_action = '<a href="'.$add_primary_link.'">'.$display_name[$post_data['userid']].'</a> shared <a href="'.$author_primary_link.'">'.$author_display_name.'</a>\'s activity';

		if($post_data['sharetogroup']){
			$bp = buddypress();
			$bp->groups->current_group = groups_get_group(array('group_id' =>$post_data['sharetogroup']));
			if(groups_is_user_member($post_data['userid'],$post_data['sharetogroup'])){
				//$activity_action  = bp_core_get_userlink($post_data['userid']).' shared <a href="'.$author_primary_link.'">'.$author_display_name.'</a>\'s activity in the group <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>';
				$content_filtered = apply_filters( 'groups_activity_new_update_content', $activity_content );
				$activity_id = groups_record_activity(array(
					'user_id' => $post_data['userid'],
					'action'  => $activity_action,
					'content' => $content_filtered,
					'type'    => 'activityshare',
					'item_id' => $post_data['sharetogroup'],
					'secondary_item_id' => $post_data['aid']
				) );

				groups_update_groupmeta($post_data['sharetogroup'], 'last_activity', bp_core_current_time());
				$oReturn->success->id = $activity_id;
				$oReturn->success->msg = __('Activity shared in group successfully.','aheadzen');
			}else{
				$oReturn->error = __('User is not member of group.','aheadzen'); return $oReturn;
			}
		}else{
			//$activity_action = '<a href="'.$add_primary_link.'">'.$display_name[$post_data['userid']].'</a> shared <a href="'.$author_primary_link.'">'.$author_display_name.'</a>\'s activity';
			$add_content = apply_filters( 'bp_activity_new_update_content', $activity_content );
			$activity_id = bp_activity_add( array(
						'user_id'      => $post_data['userid'],
						'content'      => $add_content,
						'primary_link' => $add_primary_link,
						'component'    => buddypress()->activity->id,
						'type'         => 'activityshare',
						'action'       => $activity_action,
						'item_id'	   => $post_data['aid']
					) );
			if($activity_id){
				bp_update_user_meta($post_data['userid'], 'bp_latest_update', array(
					'id'      => $activity_id,
					'content' => $activity_content
				));
				$oReturn->success->id = $activity_id;
				if($post_data['mentions']){
					$oReturn->success->msg = __('Activity shared with users successfully.','aheadzen');
				}else{
					$oReturn->success->msg = __('Activity shared successfully.','aheadzen');
				}
			}else{
				$oReturn->error = __('Activity added error.','aheadzen');
			}
		}
		return $oReturn;
	}
	/*
	http://localhost/api/buddypressread/share_the_link/?id=19&ptype=post&userid=1&shareto=user&sharetouser=@buyer1,@chynna,@testuser5
	http://localhost/api/buddypressread/share_the_link/?id=19&ptype=post&userid=1
	http://localhost/api/buddypressread/share_the_link/?id=19&ptype=post&userid=1&shareto=group&sharetogroup=1
	id = post id, page id, forum topic id.....
	ptype = post type like post, page,forum topic
	userid = poster user id/current logged user id
	sharteto =
		keep blank -- for activity share
		group -- for share in group activity
		user -- for share in users mention list
	sharetouser = user mention id like 	:: @buyer1,@chynna,@testuser5
	sharetogroup = group id to which group user want to share

	*/
	function share_the_link(){
		$pid = $_GET['id'];
		$hide_site_wide = false;
		$post_data = array();
		$activity_action = '';
		$post_data['sharetogroup']=$post_data['mentions']='';
		if($_GET['shareto']=='user' && $_GET['sharetouser']){
			$post_data['mentions'] = $_GET['sharetouser'];
		}elseif($_GET['shareto']=='group' && $_GET['sharetogroup']){
			$post_data['sharetogroup'] = $_GET['sharetogroup'];
		}

		$post_data['userid'] = $_GET['userid'];
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if($pid==''){$oReturn->error = __('Wrong ID','aheadzen'); return $oReturn;}
		if($_GET['userid']==''){$oReturn->error = __('Wrong User ID','aheadzen'); return $oReturn;}

		$post = array();
		$arg = array('p'=>$pid);
		if($_GET['ptype']){ $arg['post_type']=$_GET['ptype']; }
		query_posts($arg);
		if($_GET['ptype']=='comment'){
			$commentData = get_comment($pid);
			if($commentData->user_id){
				$postId = $commentData->comment_post_ID;
				$post_data['title'] = get_the_title($postId);
				$post_data['author_id'] = $commentData->user_id;
				$post_data['the_content'] = $commentData->comment_content;
				$activity_action = 'comment on <a href="'.get_permalink($postId).'">'.get_the_title($postId).'</a>';
			}
		}elseif($_GET['ptype']=='reply' && function_exists('bp_forums_get_post')){
			$response = bp_forums_get_post($pid);
			$topic_id = $response->topic_id;
			$oForum = bbp_get_forum((int)$response->forum_id);
			$topicData = bp_forums_get_topic_details($topic_id);
			$topic_title = $topicData->topic_title;
			$post_data['title']=$topicData->topic_title;
			$post_data['author_id']=$response->poster_id;
			$post_data['the_content'] = $response->post_text;
			$forumURL = site_url('/groups/'.$oForum->post_name.'/forum/');
			$topicURL = site_url('/groups/'.$oForum->post_name.'/forum/topic/'.$topicData->topic_slug);
			$activity_content = $response->post_text;
			$activity_action = 'reply on <a href="'.$topicURL.'">'.$topic_title.'</a>';
		}elseif($_GET['ptype']=='topic' && function_exists('bp_forums_get_topic_details')){
			$response = bp_forums_get_topic_details($pid);
			$topicURL = site_url('/groups/'.$response->object_slug.'/forum/topic/'.$response->topic_slug);
			$groupURL = site_url('/groups/'.$response->object_slug);
			$post_data['title']=$response->topic_title;
			$post_data['author_id']=$response->topic_poster;
			$post_data['the_content'] = $response->topic_content;
			$activity_content = $response->topic_title;
			$activity_action = 'topic <a href="'.$topicURL.'">'.$response->topic_title.'</a> of group <a href="'.$groupURL.'">'.$response->object_name.'</a>';
		}elseif(have_posts()){
			while ( have_posts() ) : the_post();
				$post_data['title'] = get_the_title();
				$post_data['text'] = get_the_excerpt();
				$post_data['bpfb_url'] = get_permalink();
				$post_data['author_id'] = get_the_author_meta('ID');
				$post_data['the_content'] = get_the_content();
				$post_data['image'] = '';
				$activity_action = 'post';
			endwhile;
			wp_reset_query();
		}

		if($post_data['title'] && $post_data['author_id']){
				$image_src = '';
				if(has_post_thumbnail($pid)){
					$image = wp_get_attachment_image_src(get_post_thumbnail_id($pid),'single-post-thumbnail');
					$post_data['image'] = $image[0];
				}else{
					$images = get_children( array( 'post_parent' => $pid, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID' ) );
					if ( $images ) {
						$image = array_shift( $images );
						$image_id = $image->ID;
					}
					if($image_id>0){
						$adthumbarray = wp_get_attachment_image_src( $image_id, 'medium' );
						if ( $adthumbarray ) {
							$post_data['image'] = $adthumbarray[0];
						} else {
							$post_data['image'] = wp_get_attachment_image_src($image_id, 'thumbnail');
						}
					}
				}

				if($post_data['image']==''){
					preg_match('/<img.+src=[\'"](?P<src>.+)[\'"].*>/i', $post_data['the_content'], $image);
					if($image['src']){
						$imgarr = explode('"',$image['src']);
						$post_data['image'] = $imgarr[0];
					}
				}

				if($post_data['bpfb_url']){
					$BpfbCodec = new BpfbCodec();
					$activity_content = $BpfbCodec->create_link_tag($post_data['bpfb_url'],$post_data['title'],$post_data['text'],$post_data['image']);
				}
				if($post_data['mentions']){$post_data['mentions'] = $post_data['mentions'].' ';};
				if($_GET['isinvite'] && $activity_content==''){
					$the_content = $post_data['the_content'];
					if(strlen($the_content)>150){
						$the_content = substr($the_content,0,150).'...';
					}
					$activity_content = $the_content;
				}
				$activity_content = $post_data['mentions'].$activity_content;
				$display_name = bp_core_get_user_displaynames($post_data['userid']);
				$primary_link     = bp_core_get_userlink($post_data['userid'], false, true );
				$add_primary_link = apply_filters( 'bp_activity_new_update_primary_link', $primary_link );
				$author_display_name = bp_core_get_user_displaynames($post_data['author_id']);
				$author_primary_link     = bp_core_get_userlink($post_data['author_id'], false, true );
				$author_display_name = $author_display_name[$post_data['author_id']];
				if($post_data['sharetogroup']){ /*share to group*/
					$bp = buddypress();
					$bp->groups->current_group = groups_get_group(array('group_id' =>$post_data['sharetogroup']));
					if(groups_is_user_member($post_data['userid'],$post_data['sharetogroup'])){
						$activity_action  = bp_core_get_userlink($post_data['userid']).' shared <a href="'.$author_primary_link.'">'.$author_display_name.'</a>\'s post in the group <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>';
						$content_filtered = apply_filters( 'groups_activity_new_update_content', $activity_content );

						$activity_id = groups_record_activity(array(
							'user_id' => $post_data['userid'],
							'action'  => $activity_action,
							'content' => $content_filtered,
							'type'    => 'activityshare',
							'item_id' => $post_data['sharetogroup']
						) );

						groups_update_groupmeta($post_data['sharetogroup'], 'last_activity', bp_core_current_time());
						$oReturn->success->id = $activity_id;
						$oReturn->success->msg = __('Activity added in group successfully.','aheadzen');
					}else{
						$oReturn->error = __('User is not member of group.','aheadzen'); return $oReturn;
					}
					$oReturn->success->msg = __('Shared in group successfully.','aheadzen');
				}else{ /*share to activity*/
					// Record this on the user's profile
					if($_GET['isinvite']){
						$activity_action = '<a href="'.$add_primary_link.'">'.$display_name[$post_data['userid']].'</a> invited '.$post_data['mentions'].' to <a href="'.$author_primary_link.'">'.$author_display_name.'</a>\'s '.$activity_action;
						$hide_site_wide = true;
					}else{
						$activity_action = '<a href="'.$add_primary_link.'">'.$display_name[$post_data['userid']].'</a> shared <a href="'.$author_primary_link.'">'.$author_display_name.'</a>\'s '.$activity_action;
					}
					$add_content = apply_filters( 'bp_activity_new_update_content', $activity_content );
					// Now write the values

					$activityArgs = array(
						'user_id'      => $post_data['userid'],
						'content'      => $add_content,
						'primary_link' => $add_primary_link,
						'component'    => buddypress()->activity->id,
						'type'         => 'activityshare',
						'action'       => $activity_action,
						'hide_sitewide' => $hide_site_wide,
					);

					$activity_id = bp_activity_add($activityArgs);
					$activity_content = apply_filters( 'bp_activity_latest_update_content', $post_data['text'], $activity_content );
					bp_update_user_meta($post_data['userid'], 'bp_latest_update', array(
						'id'      => $activity_id,
						'content' => $activity_content
					));
					$successMsg = __('Shared to users successfully.','aheadzen');
					$errorMsg = __('Shared in activity successfully.','aheadzen');
					if($_GET['isinvite']){
						$successMsg = __('Invitation to users successfully.','aheadzen');
						$errorMsg = __('Invitation in activity successfully.','aheadzen');
					}
					if($post_data['mentions']){
						$oReturn->success->msg = $successMsg;
					}else{
						$oReturn->success->msg = $errorMsg;
					}
				}
				$oReturn->success->id = $activity_id;
		}else{
			$oReturn->error = __('No data available.','aheadzen');
		}
		return $oReturn;
	}

	function share_activity(){
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['userid']){$oReturn->message = __('Wrong User try.','aheadzen'); return $oReturn;}
		print_r($_POST);
		return $oReturn;
	}

	function follow_unfollow_set()
	{
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['userid']){$oReturn->message = __('Wrong User try.','aheadzen'); return $oReturn;}
		if(!$_POST['leader_id']){$oReturn->message = __('Wrong Leader id.','aheadzen'); return $oReturn;}

		if(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$_POST['leader_id'],'follower_id'=>$_POST['userid'])))
		{
			if(function_exists('bp_follow_stop_following')){
				if(bp_follow_stop_following(array('leader_id' => $_POST['leader_id'], 'follower_id' => $_POST['userid']))){
					$oReturn->success = "Unhallowed added successfully";
					$oReturn->is_following = 0;
					// Notification data making
					$userDataObj= get_userdata($_POST['leader_id']);
					$friendDataObj = get_userdata($_POST['userid']);
					$userData = $userDataObj->data;
					$friendData = $friendDataObj->data;
					$friendData->notification_type_id = 6;

					$message = $userData->display_name. " follows you.";
					$this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);
					if(function_exists('bp_follow_total_follow_counts')){
						$oReturn->follow_counts  = bp_follow_total_follow_counts( array( 'user_id' =>$_POST['leader_id'] ) );
					}
				}else{
					$oReturn->error = 'Error while unhallowed.';
					$oReturn->is_following = 1;
				}
			}
		}else{
			if(function_exists('bp_follow_start_following')){
				if(bp_follow_start_following(array('leader_id' => $_POST['leader_id'], 'follower_id' => $_POST['userid']))){
					$oReturn->success = "Follower added successfully.";
					$oReturn->is_following = 1;

					// Notification data making
					$userDataObj= get_userdata($_POST['leader_id']);
					$friendDataObj = get_userdata($_POST['userid']);
					$userData = $userDataObj->data;
					$friendData = $friendDataObj->data;
					$friendData->notification_type_id = 6;

					$message = $userData->display_name. " follows you.";
					$this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);


					if(function_exists('bp_follow_total_follow_counts')){
						$oReturn->follow_counts  = bp_follow_total_follow_counts( array( 'user_id' =>$_POST['leader_id'] ) );
					}
				}else{
					$oReturn->error = __('Error while adding follower.','aheadzen');
					$oReturn->is_following = 0;
				}
			}
		}



		return $oReturn;
	}

	function set_push_notification_device_token () {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_GET['token']){$oReturn->error = __('Wrong token.','aheadzen'); return $oReturn;}
		if(!$_GET['userid']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		$user_id = $_GET['userid'];
		$token = $_GET['token'];
		if($user_id && $token){
			update_user_meta( $user_id, 'ionic_push_device_token', $token);
		}
		$oReturn->success = __('Ionic Push Token added successfully.','aheadzen');
		return $oReturn;
	}
	/**
	 * Handles link preview requests.
	 */
	function activity_preview_link () {
		if($_GET['data']){
			$info = new SplFileInfo($_GET['data']);
			if($info){
				$fileExt = strtolower($info->getExtension());
				$imageExtArr = array('jpg','jpeg','png','gif');
				if(in_array($fileExt,$imageExtArr)){
					echo json_encode(array(
						"status" => "ok",
						"success" => "success",
						"url" => $_GET['data'],
						"images" => array($_GET['data']),
						"title" => '',
						"text" => '',
					));exit;
				}else{
					$_POST['data']=urldecode($_GET['data']);
					$BpfbBinder = new BpfbBinder();
					$res =  $BpfbBinder->ajax_preview_link();
					echo json_encode($res);
					die;
				}
			}
		}
	}

	public function activity_set_bpfb_url()
	{
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';

		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['title']){$oReturn->message = __('No title added.','aheadzen'); return $oReturn;}
		if(!$_POST['cookie']){$oReturn->message = __('Cookie parameter is required.','aheadzen'); return $oReturn;}

		//if(!aheadzen_check_valid_user($_POST['userid'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}
		$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');
		if($valid != $_POST['userid']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}

		$user_id = $_POST['userid'];
		$title = $_POST['title'];
		$text = $_POST['text'];
		$url = $_POST['url'];
		$image = $_POST['image'];

		$images = explode(',',$imagesfile);
		$BpfbCodec = new BpfbCodec();


		$bpfb_code = $BpfbCodec->create_images_tag($images);
		$bpfb_code = apply_filters('bpfb_code_before_save', $bpfb_code);
		if(function_exists('bp_activity_post_update')){
			//$activity_id = bp_activity_post_update(array('content' => $bpfb_code,'user_id' => $user_id));
			$primary_link = '';

			//$activity_id = bp_activity_post_update(array('content' => $bpfb_code,'user_id' => $user_id));

				if(function_exists('bp_core_get_userlink')){
					$primary_link     = bp_core_get_userlink($user_id, false, true );
				}
				$activity_id = bp_activity_add( array(
					'user_id'      => $user_id,
					'content'      => $bpfb_code,
					'primary_link' => $primary_link,
					'component'    => buddypress()->activity->id,
					'type'         => 'activity_photo',
				) );
				bp_update_user_meta($user_id, 'bp_latest_update', array(
					'id'      => $activity_id,
					'content' => $bpfb_code
				));
				if($activity_id){
					global $blog_id;
					bp_activity_update_meta($activity_id, 'bpfb_blog_id', $blog_id);
				}

			if($activity_id){
				$oReturn->success->id = $activity_id;
				$oReturn->success->msg = __('Activity added successfully.','aheadzen');
			}

		}else{
			$oReturn->error = __('Add activity error. Something wrong.','aheadzen');
		}
		return $oReturn;
	}

	public function activity_set_bpfb()
	{
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['cookie']){$oReturn->message = __('Cookie parameter is required.','aheadzen'); return $oReturn;}
		//if(!aheadzen_check_valid_user($_POST['userid'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}
		$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');
		if($valid != $_POST['userid']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}
		$user_id = $_POST['userid'];
		$BpfbCodec = new BpfbCodec();
		if (!empty($_POST['bpfb_video_url'])) {
			$bpfb_code = $BpfbCodec->create_video_tag($_POST['bpfb_video_url']);
			$activity_type = 'activity_update';
		}

		if (!empty($_POST['bpfb_url'])) {
			$bpfb_code = $BpfbCodec->create_link_tag($_POST['bpfb_url'],$_POST['title'],$_POST['text'],$_POST['image']);
			$activity_type = 'activity_update';
		}
		if (!empty($_POST['imagesfile'])) {
			$imagesfile = $_POST['imagesfile'];
			$images = explode(',',$imagesfile);
			$bpfb_code = $BpfbCodec->create_images_tag($images);
		}

		$bpfb_code = apply_filters('bpfb_code_before_save', $bpfb_code);
		if(!$bpfb_code){
			$oReturn->error = __('bpfb code - activity error. Something wrong.','aheadzen');return $oReturn;
		}
		if(trim($_POST['content'])){
			$bpfb_code = nl2br($_POST['content'] ."\r\n". $bpfb_code);
			//$bpfb_code = nl2br("This\r\nis\n\ra\nstring\r");
		}
		$groupid = 0;
		if(!$activity_type){$activity_type = 'activity_update';}
		if($_POST['bpfb_type']=='groups'){
			$groupid = $_POST['groupid'];  //groups;
			$bp = buddypress();
			$bp->groups->current_group = groups_get_group( array( 'group_id' => $groupid ) );
			$action  = sprintf( __( '%1$s posted an update in the group %2$s', 'buddypress'), bp_core_get_userlink( $user_id ), '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>' );
			$arg = array(
				'user_id' => $user_id,
				'action'  => $action,
				'content' => $bpfb_code,
				'type'    => $activity_type, //'activity_photo',
				'item_id' => $groupid
			);
			$activity_id = groups_record_activity($arg);
			groups_update_groupmeta( $groupid, 'last_activity', bp_core_current_time() );
			if($activity_id){
				$oReturn->success->id = $activity_id;
				$oReturn->success->msg = __('Activity added successfully.','aheadzen');
			}
		}elseif(function_exists('bp_activity_post_update')){
			//$activity_id = bp_activity_post_update(array('content' => $bpfb_code,'user_id' => $user_id));
			$primary_link = '';
			if(function_exists('bp_core_get_userlink')){
				$primary_link     = bp_core_get_userlink($user_id, false, true );
			}
			$args = array(
				'user_id'      => $user_id,
				'content'      => $bpfb_code,
				'primary_link' => $primary_link,
				'component'    => buddypress()->activity->id,
				'type'			=> $activity_type, //'activity_photo',
			);
			if($groupid){$args['item_id']=$groupid;}
			$activity_id = bp_activity_add($args);
			bp_update_user_meta($user_id, 'bp_latest_update', array(
				'id'      => $activity_id,
				'content' => $bpfb_code
			));
			if($activity_id){
				global $blog_id;
				bp_activity_update_meta($activity_id, 'bpfb_blog_id', $blog_id);
				$oReturn->success->id = $activity_id;
				$oReturn->success->msg = __('Activity added successfully.','aheadzen');
			}else{
				$oReturn->error = __('Add activity error. Something wrong.','aheadzen');
			}
		}else{
			$oReturn->error = __('Add activity Buddypress function error. Something wrong.','aheadzen');
		}

		return $oReturn;
	}

	public function activity_upload_image()
	{
		set_time_limit(0);
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';

		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_FILES){$oReturn->message = __('Wrong picture.','aheadzen'); return $oReturn;}
		$oReturn = $this->upload_image_activity();

		return $oReturn;
	}

	function upload_image_activity(){
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';
		global $bp;


		if($_FILES && $_FILES['file'] && $_FILES['file']['name'] && $_FILES['file']['size']>0 && $_FILES['file']['error']==0)
		{
			$tmp_name = $_FILES['file']['tmp_name'];
			$filename = $_FILES['file']['name'];
			$type = $_FILES['file']['type'];
			$size = $_FILES['file']['size'];

			$basedir = BPFB_BASE_IMAGE_DIR;
			$user_id = $_GET['user_id'];
			if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
			if(!file_exists($basedir.$user_id.'/')){@wp_mkdir_p($basedir.$user_id.'/');}
			$srch = array(' '," ",'"',"'",'-','`','~','!','@','#','$','%','^','&','*','(',')','+','=','|','\\','[',']','{','}',',','/','<','>');
			$repl = array('_','_','','','_','','','','','','','','','','','','','','','','','','','','','','','','');
			$filename = preg_replace('/[^0-9]/', '-', microtime()).'-'.rand(1,1000).'-'.str_replace($srch,$repl,$filename);

			$filename = $user_id.'/'.$filename;
			$targetFile = $basedir.$filename;
			$targetFileURL = BPFB_BASE_IMAGE_URL.$filename;
			$uploadOk = 1;
			$imageFileType = pathinfo($targetFile,PATHINFO_EXTENSION);
			// Check if image file is a actual image or fake image
			$check = getimagesize($tmp_name);
			if($check == false) {
				$oReturn->error = __('File is not an image.','aheadzen');
			}/*elseif ($size > 500000) { // Check file size
				$oReturn->error = __('Sorry, your file is too large.','aheadzen');
			}*/
			else // Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
				$oReturn->error = __('Sorry, only JPG, JPEG, PNG & GIF files are allowed.','aheadzen');
			}else{
				if (move_uploaded_file($tmp_name, $targetFile)) {
					if($targetFile){
						if (function_exists('wp_get_image_editor')) { // New way of resizing the image
							$image = wp_get_image_editor($targetFile);
							if (!is_wp_error($image)) {
								list($thumb_w,$thumb_h) = Bpfb_Data::get_thumbnail_size();
								$thumb_filename  = $image->generate_filename('bpfbt');
								$image->resize($thumb_w, $thumb_h, false);

								// Alright, now let's rotate if we can
								if (function_exists('exif_read_data')) {
									$exif = exif_read_data($targetFile); // Okay, we now have the data
									if (!empty($exif['Orientation']) && 3 === (int)$exif['Orientation']) $image->rotate(180);
									else if (!empty($exif['Orientation']) && 6 === (int)$exif['Orientation']) $image->rotate(-90);
									else if (!empty($exif['Orientation']) && 8 === (int)$exif['Orientation']) $image->rotate(90);
								}
								$image->save($thumb_filename);
							}
						} else {
							image_resize($targetFile, $thumb_w, $thumb_h, false, 'bpfbt');
						}
					}

					$oReturn->success->filenurl = $targetFileURL;
					$oReturn->success->filename = $filename;
					//$oReturn = $filename;
					$oReturn->success->msg = __('The file has been uploaded.','aheadzen');

				} else {
					$oReturn->success->outputFile = $outputFile;
					$oReturn->success->filename = $filename;
					$oReturn->error = __('Sorry, there was an error uploading file.','aheadzen');
				}
			}
		}

		return $oReturn;
	}

	public function members_get_short()
	 {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';
		global $wpdb,$table_prefix;

		$limit = 10;
		$keyword = trim($_GET['keyword']);
		if($_GET['limit']){$limit = trim($_GET['limit']);}
		if($keyword){
			$sql = "select ID,display_name,user_login from ".$table_prefix."users where user_login like \"$keyword%\" OR display_name like \"$keyword%\" order by display_name limit $limit";
			$members = $wpdb->get_results($sql);
			$counter = 0;
			if($members){
				foreach($members as $membersobj){
					$oReturn->members[$counter]->id = $membersobj->ID;
					$oReturn->members[$counter]->user_login = $membersobj->user_login;
					$oReturn->members[$counter]->display_name = bpaz_user_name_from_email($membersobj->display_name);
					$counter++;
				}
			}
		}else{
			$oReturn->members = array();
		}
		//echo '<pre>';print_r($oReturn);exit;
		return $oReturn;
	 }

	public function members_get_nameonly()
	 {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';
		global $wpdb,$table_prefix;

		$keyword = trim($_GET['keyword']);
		$memlist = trim($_GET['memlist']);
		$per_page = trim($_GET['per_page']);
		$currentUser = trim($_GET['currentUser']);
		$theid = trim($_GET['id']);
		$ptype = trim($_GET['ptype']);
		$groupid = trim($_GET['groupid']);
		if(!$per_page){$per_page=3;}
		$counter=0;
		if($keyword || $memlist){
			$suborderby = "user_login";
			$userStr = '';
			if($ptype){
				if($ptype=='topicpost' || $ptype=='topicpost'){
					$arg = array();
					$gper_page = $per_page*3;
					$arg['group_id'] = $groupid;
					$arg['per_page'] = $per_page*3;
					$aMembers = groups_get_group_members($arg);
					$userArr = array();
					if($aMembers){
						$counter=0;
						foreach($aMembers['members'] as $aMembersObj){
							$userArr[] = $aMembersObj->ID;
							$counter++;
							if($gper_page==$counter)break;
						}
						$userStr = implode(',',$userArr);
					}
					if($userStr){
						$subsql = " and ID in ($userStr)";
					}
				}elseif($ptype=='comments'){
					$response = get_comment($theid);
					$postId = $response->comment_post_ID;
					$pper_page = $per_page*3;
					if($postId){
						$userArr = $wpdb->get_col("select user_id from $wpdb->comments where comment_post_ID=\"$postId\" user_id>0 limit $pper_page");
						if($userArr){
							$userStr = implode(',',$userArr);
							$subsql = " and ID in ($userStr)";
						}
					}
				}
			}elseif($keyword){
				$subsql = "and (user_login like \"$keyword%\" || display_name like \"$keyword%\")";
			}
			$suborderby = "rand()";
			if($currentUser){
				$subsql = " and ID not in ($currentUser)";
			}
			$sql = "select ID,user_login,display_name from ".$table_prefix."users where user_login not like \"%@%\" $subsql order by $suborderby limit $per_page";
			$res = $wpdb->get_results($sql);
			if($res){
				$counter=0;
				foreach($res as $resobj){
					if($resobj->display_name){
						$user = new BP_Core_User($resobj->ID);
						if($user){
							$avatar_thumb = '';
							if($user->avatar_thumb){
								preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
								$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
								if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
							}
						}
						$oReturn->members[$counter]->id = $resobj->ID;
						$oReturn->members[$counter]->login = $resobj->user_login;
						$oReturn->members[$counter]->name = bpaz_user_name_from_email($resobj->display_name);
						$oReturn->members[$counter]->thumb = $avatar_thumb;
						$counter++;
					}
				}
			}else{
				$oReturn->msg = __('No Result','aheadzen');
			}
		}
		return $oReturn;
	 }

	public function members_get_members()
	 {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';
		$oReturn->total = 0;
		$bp_members = array();
		$member_data = array();
		$subsql = '';
		global $bp,$wpdb,$table_prefix;
		$keyword = trim($_GET['keyword']);
		$thepage = $_GET['thepage'];
		$limit = $_GET['limit'];
		$withphoto = $_GET['withphoto'];
		$userid = $_GET['userid'];
		if(!$thepage){$thepage=0;}
		if(!$limit){$limit=20;}
		if($keyword){$thepage=1;}
		$start = $thepage*$limit;
		$members = array();
		if($keyword){
			$sql = "select DISTINCT(user_id),MATCH (value) AGAINST('".$keyword."' IN BOOLEAN MODE) as score from ".$table_prefix."bp_xprofile_data HAVING score > 0 ORDER BY score DESC limit $start, $limit";
			$members = $wpdb->get_col($sql);
			if(!$members){
				$sql = "select DISTINCT(user_id) from ".$table_prefix."bp_xprofile_data where value like \"%$keyword%\" ORDER BY user_id DESC limit $start, $limit";
				$members = $wpdb->get_col($sql);
			}
			//if(!$members){$members=array('9999999999');}
		}

		if(!$thepage){$thepage=1;}
		if($members){$members = implode(',',$members);}
		$members_args = array(
			'user_id'         => 0,
			'type'		      => 'active',
			'page'		      => $thepage,
			'per_page'        => $limit,
			'populate_extras' => true,
			'search_terms'    => false,
			'include'		  => $members,
		);
		//if($userid){$members_args['exclude'] = array($userid);}
		global $members_template;
		$counter = 0;
		if(bp_has_members($members_args)){
			while(bp_members()){
				bp_the_member();
				$uid = $members_template->member->ID;
				if((bp_get_user_has_avatar($uid) && $withphoto) || !$withphoto){
					$user = new BP_Core_User($uid);
					$avatar_thumb = $user->avatar_thumb;
					if($avatar_thumb){
						preg_match_all('/(src)=("[^"]*")/i',$avatar_thumb, $user_avatar_result);
						$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
						if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
					}
					$oReturn->members[$counter]->id 		= $user->id;
					$oReturn->members[$counter]->username 	= $members_template->member->user_login;
					$oReturn->members[$counter]->fullname 	= bpaz_user_name_from_email($user->fullname);
					$oReturn->members[$counter]->email 		= $user->email;
					$oReturn->members[$counter]->last_active= $user->last_active;
					$oReturn->members[$counter]->avatar_thumb = $avatar_thumb;
					$profile_data = $user->profile_data;
					if($profile_data){
						foreach($profile_data as $sFieldName => $val){
							if(is_array($val)){
								$oReturn->members[$counter]->$sFieldName = $val['field_data'];
							}
						}
					}
					if(function_exists('bp_follow_total_follow_counts')){
						$oReturn->members[$counter]->follow_counts  = bp_follow_total_follow_counts( array( 'user_id' => $user->id ) );
					}
					$oReturn->members[$counter]->is_following = 0;
					if(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$user->id,'follower_id'=>$_GET['userid']))){
						$oReturn->members[$counter]->is_following = 1;
					}
					$counter++;
				}
			}
		}else{$oReturn->error = __('No Members Available To Display.','aheadzen');}
		//echo '<pre>';print_r($oReturn);exit;
		return $oReturn;
	 }

   /**
     * Returns an Array with all mentions
     * @param int pages: number of pages to display (default 1)
     * @param int maxlimit: number of maximum results (default 20)
	 * @param String sort: sort ASC or DESC (default DESC)
     * @param String username: username to filter on, comma-separated for more than one ID (default unset)
     * @return array mentions: an array containing the mentions
     */
    public function activity_get_mentions() { error_reporting(0);
        header("Access-Control-Allow-Origin: *");
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';

		if(!$_GET['username']){$oReturn->error = __('Missing parameter username.','aheadzen'); return $oReturn;}

		$username = $_GET['username'];
		$maxlimit = $_GET['maxlimit'];
		$page = $_GET['pages'];
		$orderby = $_GET['sort'];

		if(!$page){$page=1;}
		if(!$maxlimit){$maxlimit=20;}
		if(!$orderby){$orderby='DESC';}
		if(!$username){$oReturn->error = __('Wrong User Name.','aheadzen'); return $oReturn;}
		if(!username_exists($username)){return $this->error('xprofile', 1);}

		global $wpdb,$table_prefix;
		$sql = "SELECT ID FROM wp_users WHERE user_login = '".$username."'";
		$user_id = $wpdb->get_var($sql);
		$session_user = $user_id;

		$start = $maxlimit*($page-1);
		$end = $maxlimit;
		global $wpdb,$table_prefix;
		$total_count = $wpdb->get_var("select count(id) from ".$table_prefix."bp_activity where content like \"%@".$username."%\"");
		$sql = "select * from ".$table_prefix."bp_activity where content like \"%@".$username."%\" order by date_recorded $orderby limit $start,$end";
		$res = $wpdb->get_results($sql);
		 $oReturn->total_count = $total_count;
		 $oReturn->total_pages = ceil($total_count/$maxlimit);
		if($res){
			$counter=0;

			foreach($res as $oMentions){
				$user = new BP_Core_User($oMentions->user_id);
				if($user && $user->avatar){
					$oMentions->fullname = $user->fullname;
					$oMentions->email = $user->email;
					$oMentions->user_url = $user->user_url;

					if($user->user_url){
						$oMentions->username = str_replace('/','',str_replace(site_url('/members/'),'',$user->user_url));
					}
					if($user->avatar){
						preg_match_all('/(src)=("[^"]*")/i',$user->avatar, $user_avatar_result);
						$avatar_big = str_replace('"','',$user_avatar_result[2][0]);
						if($avatar_big && !strstr($avatar_big,'http:')){ $avatar_big = 'http:'.$avatar_big;}
						$oMentions->avatar_big = $avatar_big;
					}
					if($user->avatar_thumb){
						preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
						$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
						if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						$oMentions->avatar_thumb = $avatar_thumb;
					}
				}

				$sql = "SELECT COUNT(1) AS total_comments FROM wp_bp_activity WHERE item_id = ".$oMentions->id." AND TYPE = 'activity_comment'";
				$res = $conn->query($sql);

				$total_comment_count = 0;
				if ($res->num_rows > 0) {
					$total_comment_count = $res->fetch_assoc();
				}



				$oReturn->mentions[$counter]->id = $oMentions->id;
				$oReturn->mentions[$counter]->component = $oMentions->component;
				$oReturn->mentions[$counter]->type = $oMentions->type;
				$oReturn->mentions[$counter]->content = base64_encode(do_shortcode($oMentions->content));
				$oReturn->mentions[$counter]->action = $oMentions->action;
				$oReturn->mentions[$counter]->primary_link = $oMentions->primary_link;
				$oReturn->mentions[$counter]->item_id = $oMentions->item_id;
				$oReturn->mentions[$counter]->secondary_item_id = $oMentions->secondary_item_id;
				$oReturn->mentions[$counter]->total_comments = $total_comment_count['total_comments'] ;
				$oReturn->mentions[$counter]->date_recorded = $oMentions->date_recorded;
				$oReturn->mentions[$counter]->is_shared = $oMentions->is_shared;
				$oReturn->mentions[$counter]->is_checkedin = $oMentions->is_checkedin;
				$oReturn->mentions[$counter]->latitude = $oMentions->latitude;
				$oReturn->mentions[$counter]->longitude = $oMentions->longitude;
				$oReturn->mentions[$counter]->location = $oMentions->location;
				$oReturn->mentions[$counter]->user->id = $oMentions->user_id;
				$oReturn->mentions[$counter]->user->fullname = bpaz_user_name_from_email($oMentions->fullname);
				$oReturn->mentions[$counter]->user->email = $oMentions->email;
				$user = get_userdata($oMentions->user_id);
				$oReturn->mentions[$counter]->user->display_name = $user->display_name;
				$name = explode(" ",$oMentions->fullname);
				$oReturn->mentions[$counter]->user->firstname = $name[0];
				$oReturn->mentions[$counter]->user->lastname = $name[1];
				$oReturn->mentions[$counter]->user->username = $oMentions->username;
				$oReturn->mentions[$counter]->user->user_url = $oMentions->user_url;
				$oReturn->mentions[$counter]->user->avatar_thumb = $oMentions->avatar_thumb;
				$oReturn->mentions[$counter]->user->avatar_big = $oMentions->avatar_big;

				$comments = $this->get_activity_comments($oMentions->id);
				$oReturn->mentions[$counter]->comments = $comments ;
				$sql = "SELECT meta_value FROM `wp_bp_activity_meta` WHERE meta_key = 'favorite_count' AND meta_value > 0 AND activity_id = ".$oMentions->id;
				$res = $conn->query($sql);
				$favoriteCount = $res->fetch_assoc();
				$total_comment_count = 0;

				$favData = bp_activity_get_user_favorites($session_user);
				//echo "USER ID : ".$row['session_user'];print_r($favData);
				//echo "<br/>";
				$total_comment_count = 0;
				if (in_array($oMentions->id,$favData)) {

					//$arr['favorite'] = true;
					//$arr['favorite_count'] = $favoriteCount['meta_value'];
					$oReturn->mentions[$counter]->favorite = true;
					$oReturn->mentions[$counter]->favorite_count = $favoriteCount['meta_value'];

				}
				else
				{
					$oReturn->mentions[$counter]->favorite = false;
					if(isset($favoriteCount['meta_value']) && $favoriteCount['meta_value'] != '')
					{
						$oReturn->mentions[$counter]->favorite_count = $favoriteCount['meta_value'];
					}
					else
					{
						$oReturn->mentions[$counter]->favorite_count = "0";
					}

				}



				$counter++;
			}
		}else{
			$oReturn->msg = __('No Mentions Available To Display.','aheadzen');
		}

		return $oReturn;
    }


	public function activity_comments_delete()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['commentid']){$oReturn->error = __('Wrong Comment Id.','aheadzen'); return $oReturn;}
		if(!$_POST['activityid']){$oReturn->error = __('Wrong Activity Id.','aheadzen'); return $oReturn;}

		$comment_id = (int)$_POST['commentid'];
		$activity_id = (int)$_POST['activityid'];

		if(bp_activity_delete_comment( $activity_id, $comment_id ))
		{
			$oReturn->success->message = __('Activity comment deleted successfully.','aheadzen');
		}else{
			$error = __('Something wrong to delete activity comment.','aheadzen');
		}

		$oReturn->error = $error;
		return  $oReturn;
	}

	/**
     * Supply post data
     * @param int userid: User ID
     * @param String content: Activity comment content
	 * @param int activityid: Activity Id for which you want to add comments
     * @return array message: success or error message & added activity comment ID
     */
	public function activity_comments_add_edit()
	{
		//header("Access-Control-Allow-Origin: *");
		error_reporting(0);
		/*//The data only for testing purpose.
		$_POST['content'] = '123 HELLO THIS IS TEST ACTIVITY Comments FOR ME';
		$_POST['userid'] = 1;
		$_POST['activityid'] = 47;
		*/
		$error = '';
		$oReturn = new stdClass();
		$oReturn->success = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['content']){$oReturn->error = __('Please do not leave the content area blank.','aheadzen'); return $oReturn;}
		if(!$_POST['userid']){$oReturn->error = __('Wrong User Id.','aheadzen'); return $oReturn;}
		if(!$_POST['activityid']){$oReturn->error = __('Wrong Activity Id.','aheadzen'); return $oReturn;}
		if(!$_POST['cookie']){$oReturn->error = __('Cookie parameter is required.','aheadzen'); return $oReturn;}

		//if(!aheadzen_check_valid_user($_POST['userid'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}
		$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');
		if($valid != $_POST['userid']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}

		$content = $_POST['content'];

		$user_id = (int)$_POST['userid'];
		$activity_id = (int)$_POST['activityid'];
		$commentid = (int)$_POST['commentid'];
		$parent_id = false;
		if(isset($_POST['parent_id']))
		{
			$parent_id = (int)$_POST['parent_id'];
		}
		$arg = array(
			'content'    	=> $content,
			'activity_id' 	=> $activity_id,
			'user_id' 		=> $user_id,
			'parent_id'   => $parent_id
		);

		if($commentid){$arg['id'] = $commentid;} //update activity comment
		if($comment_id = bp_activity_new_comment($arg))
		{
			$oReturn->success->id = $comment_id;
			if($activityid){
				$oReturn->success->message = __('Activity comments updated successfully.','aheadzen');
			}else{
				$oReturn->success->message = __('Activity comments added successfully.','aheadzen');
			}
		}else{
			$error = __('Something wrong to updated activity comments.','aheadzen');
		}
		$oReturn->error = $error;
		return  $oReturn;
	}

	/**
     * Supply post data
     * @param int userid: User ID
     * @param String content: Activity content
	 * @param int activityid: Activity Id for update
     * @return array message: success or error message
     */
	public function activity_add_edit()
	{
		/*
		//The data only for testing purpose.
		$_POST['content'] = '123 HELLO THIS IS TEST ACTIVITY FOR ME 456';
		$_POST['userid'] = 1;
		$_POST['activityid'] = 48;
		*/
		$error = '';
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['content']){$oReturn->error = __('Empty content.','aheadzen'); return $oReturn;}
		if(!$_POST['userid']){$oReturn->error = __('Wrong User Id.','aheadzen'); return $oReturn;}
		if(!$_POST['cookie']){$oReturn->error = __('Cookie parameter is required.','aheadzen'); return $oReturn;}
		$content = $_POST['content'];
		$user_id = $_POST['userid'];
		$activityid = (int)$_POST['activityid'];
		//if(!aheadzen_check_valid_user($_POST['userid'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}
		$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');
		if($valid != $_POST['userid']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}
		$groupid = 0;
		if($_POST['bpfb_type']=='groups'){
			$groupid = $_POST['groupid'];  //groups;
			$bp = buddypress();
			$bp->groups->current_group = groups_get_group( array( 'group_id' => $groupid ) );
			$action  = sprintf( __( '%1$s posted an update in the group %2$s', 'buddypress'), bp_core_get_userlink( $user_id ), '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>' );

			$arg = array(
				'user_id' => $user_id,
				'action'  => $action,
				'content' => $content,
				'type'    => 'activity_update',
				'item_id' => $groupid
			);
			if($activityid){$arg['id'] = $activityid;} //update activity
			$activity_id = groups_record_activity($arg);
			groups_update_groupmeta( $groupid, 'last_activity', bp_core_current_time() );

		}else{
			$arg = array(
					'user_id'   => $user_id,
					'component' => 'activity',
					'type'      => 'activity_update',
					'content'   => $content
				);
			if($activityid){$arg['id'] = $activityid;} //update activity
			$activity_id = bp_activity_add($arg);
		}

		if($activity_id){
			$oReturn->success->id = $activity_id;
			if($activityid){
				$oReturn->success->message = __('Activity updated successfully.','aheadzen');
			}else{
				$oReturn->success->message = __('Activity added successfully.','aheadzen');
			}
		}else{
			if($activityid){
				$error = __('Something wrong to add activity.','aheadzen');
			}else{
				$error = __('Something wrong to updated activity.','aheadzen');
			}
		}
		$oReturn->error = $error;
		return  $oReturn;
	}

	/**
     * Supply post data
     * @param int userid: User ID
     * @param int activityid: Activity Id for update
     * @return array message: success or error message
     */
	public function activity_delete()
	{
		/*
		//The data only for testing purpose.
		$_POST['userid'] = 1;
		$_POST['activityid'] = 47;
		*/

		$error = '';
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		if(!$_POST){$oReturn->error = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['activityid']){$oReturn->error = __('Wrong activity Id.','aheadzen'); return $oReturn;}
		if(!$_POST['userid']){$oReturn->error = __('Wrong user Id.','aheadzen'); return $oReturn;}
		$user_id = $_POST['userid'];
		$activityid = (int)$_POST['activityid'];
		if(!aheadzen_check_valid_user($_POST['userid'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}

		$arg = array(
					'id'  		 => $activityid,
					'user_id' 	=> $user_id
				);
		if ( bp_activity_delete($arg)){
			$oReturn->success->message = __( 'Activity deleted successfully', 'aheadzen');
		}else{
			$error =  __( 'There was an error when deleting that activity', 'aheadzen' );
		}
		$oReturn->error = $error;
		return  $oReturn;
	}

	public function profile_ionic_upload_photo()
	{
		/*
		//below details are only for testing purpose.
		$_POST['clicked_pic'] = 'profile_pic'; //'profile_pic'; //'cover_pic';
		$_POST['user_id'] = 1;
		$imageDataEncoded = base64_encode(file_get_contents('http://localhost/profile_pic_192063.jpg'));
		$_POST['picture_code']=$imageDataEncoded;
		*/
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if($_FILES && $_FILES['file'] && $_FILES['file']['name']){ }else{$oReturn->message = __('Wrong picture.','aheadzen'); return $oReturn;}

		$clicked_pic = $_GET['clicked_pic'];
		$user_id = $_GET['user_id'];
		$bp_upload = xprofile_avatar_upload_dir('',$user_id);
		$basedir = $bp_upload['path'];
		$baseurl = $bp_upload['url'];

		if($_FILES && $_FILES['file'] && $_FILES['file']['name'] && $_FILES['file']['size']>0 && $_FILES['file']['error']==0)
		{
			$tmp_name = $_FILES['file']['tmp_name'];
			$filename = $_FILES['file']['name'];
			$type = $_FILES['file']['type'];
			$size = $_FILES['file']['size'];

			$targetFile = $basedir.$filename;
			$targetFileURL = $baseurl.$filename;
			$uploadOk = 1;
			$imageFileType = pathinfo($targetFile,PATHINFO_EXTENSION);

			if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
			$filename = $clicked_pic.'_'.$user_id.'.'.$imageFileType;
			$outputFile = $basedir.'/'.$filename;
			$imageurl = $outputFileURL = $baseurl.'/'.$filename;

			// Check if image file is a actual image or fake image
			$check = getimagesize($tmp_name);
			if($check == false) {
				$oReturn->error = __('File is not an image.','aheadzen');
			}/*elseif ($size > 500000) { // Check file size
				$oReturn->error = __('Sorry, your file is too large.','aheadzen');
			}*/
			else // Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
				$oReturn->error = __('Sorry, only JPG, JPEG, PNG & GIF files are allowed.','aheadzen');
			}else{
				if (move_uploaded_file($tmp_name, $outputFile)) {
					if($outputFile){
						if($outputFile && $clicked_pic=='cover_pic'){
							update_user_meta( $user_id, 'bbp_cover_pic', $imageurl);
							$imageurl1 = $imageurl;
						}elseif($outputFile && $clicked_pic=='profile_pic'){
							$imgdata = @getimagesize( $outputFile );
							$img_width = $imgdata[0];
							$img_height = $imgdata[1];
							$upload_dir = wp_upload_dir();
							$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );
							$args = array(
								'item_id'       => $user_id,
								'original_file' => $existing_avatar_path,
								'crop_x'        => 0,
								'crop_y'        => 0,
								'crop_w'        => $img_width,
								'crop_h'        => $img_height
							);
							if (bp_core_avatar_handle_crop( $args ) ) {
								$imageurl1 = bp_core_fetch_avatar( array( 'item_id' => $user_id,'html'=>false,'type' => 'full'));
								// Add the activity
								if(function_exists('bp_activity_add')){
									bp_activity_add( array(
										'user_id'   => $user_id,
										'component' => 'profile',
										'type'      => 'new_avatar'
									));
								}
								$oReturn->success->image = $imageurl1;
								$oReturn->success->msg = 'Image uploaded successfully.';
							}else{
								$oReturn->error = 'Upload error';
							}
						}
					}
					return $oReturn = $imageurl1;
				}
			}
		}
		$oReturn->imageurl = $outputFileURL;
		$oReturn->error = $error;
		return  $oReturn;

	}

	public function profile_upload_photo()
	{

		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = "";
		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['picture_code']){$oReturn->message = __('Wrong picture.','aheadzen'); return $oReturn;}
		if(!$_POST['user_id']){$oReturn->message = __('Wrong User.','aheadzen'); return $oReturn;}

		$clicked_pic = $_POST['clicked_pic'];
		$user_id = $_POST['user_id'];
		$picture_code = $_POST['picture_code'];
		$bp_upload = xprofile_avatar_upload_dir('',$user_id);

		$basedir = $bp_upload['path'];
		$baseurl = $bp_upload['url'];
		if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
		$filename = $clicked_pic.'_'.$user_id.'.jpg';
		$outputFile = $basedir.'/'.$filename;
		$imageurl = $outputFileURL = $baseurl.'/'.$filename;

		if(strstr($picture_code,'data:image/')){
			 $picture_code_arr = explode(',', $picture_code);
			$picture_code = $picture_code_arr[1];
		}

		$quality = 70;
		if(file_exists($outputFile)){@unlink($outputFile);}
		$data = base64_decode($picture_code);
		$image = imagecreatefromstring($data);
		$imageSave = imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
		if(!$imageSave){$oReturn->error = 'Image Save Error'; return  $oReturn;}
		if($outputFile && $clicked_pic=='cover_pic'){

			$data = $this->upload_member_cover($picture_code,$_POST['user_id'],$clicked_pic);
			$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' =>$_POST['user_id'] ) );
			echo json_encode(array("status"=>"ok","success"=>"Image uploaded successfully.","imageurl"=>$cover_image_url));
			die;
			update_user_meta( $user_id, 'bbp_cover_pic', $imageurl);
		}elseif($outputFile && $clicked_pic=='profile_pic'){
			$imgdata = @getimagesize( $outputFile );
			$img_width = $imgdata[0];
			$img_height = $imgdata[1];
			$upload_dir = wp_upload_dir();
			$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );

			$args = array(
				'item_id'       => $user_id,
				'original_file' => $existing_avatar_path,
				'crop_x'        => 0,
				'crop_y'        => 0,
				'crop_w'        => $img_width,
				'crop_h'        => $img_height
			);

			if (bp_core_avatar_handle_crop( $args ) ) {
				$imageurl = bp_core_fetch_avatar( array( 'item_id' => $user_id,'html'=>false,'type' => 'full'));

                /* To generate action link*/
                $oUser = get_user_by('id',$user_id);
                $activity_link = '<a href="'.get_site_url().'/members/'.$oUser->data->user_nicename.'/" title="'.$oUser->data->display_name.'">'.$oUser->data->display_name.'</a> <span class="activity-bold">changed their profile picture.<span></span></span>';
                $member_link = ''.get_site_url().'/members/'.$oUser->data->user_nicename.'/';

                /* To generate action link*/

				// Add the activity
				bp_activity_add( array(
					'user_id'   => $user_id,
					'component' => 'profile',
					'type'      => 'new_avatar',
                    'action'    => $activity_link,
                    'primary_link' => $member_link

				) );
				$oReturn->success = 'Image uploaded successfully.';
			}else{
				$error = 'Upload error';
			}
		}
		$oReturn->success = 'Image uploaded successfully.';
		if($clicked_pic=='cover_pic'){
			$oReturn->imageurl = bp_attachments_get_attachment( 'url', array( 'item_id' => $user_id ) );
		} else {
			$oReturn->imageurl = $imageurl;
		}
		//$oReturn->error = $error;
		return  $oReturn;

	}
	/************************************************
	EDIT PROFILE API
	The filed name should be like thefieldid_1, thefieldid_2,thefieldid_3,thefieldid_4.........
	where "thefieldid_" == is prefix variable and 1,2,3.... are the field id to store in buddypress db.
	api url : http://siteurl.com/api/buddypressread/profile_set_profile/
	************************************************/
	 public function profile_set_profile() {

		//The data only for testing purpose.
		//$_POST['data']='{"1":"Test UserName","5":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#039;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\n","2":"Male","3":"Native American","4":"Average","21":"Fit","32":"Kosher","39":"Sometimes","43":"Sometimes","47":"English","6":"Afghanistan","7":"Surat"}';
		//$_POST['userid'] = 1;

		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['data']){$oReturn->message = __('Wrong post data.','aheadzen'); return $oReturn;}
		$userid = $_POST['userid'];

		if(!aheadzen_check_valid_user($_POST['userid'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}

		if(!$userid){$oReturn->message = 'Wrong user ID.'; return $oReturn;}
		if (!bp_has_profile(array('user_id' => $userid))) {
			return $this->error('xprofile', 0);
		}
		$data = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $_POST['data'] );
		$data = json_decode( stripslashes($data) );

		foreach($data as $fieldid=>$val)
		{
			if($fieldid && $fieldid >0){
				$field_updated = xprofile_set_field_data( $fieldid, $userid, $val);
			}
		}

		// Add the activity
		bp_activity_add( array(
			'user_id'   => $userid,
			'component' => 'xprofile',
			'type'      => 'updated_profile'
		) );
		$oReturn->success->id = $userid;
		$oReturn->success->message = __('User Profile Updated Successfully.','aheadzen');
		return  $oReturn;
	 }

	 public function activity_get_activity() {error_reporting(0);
		header("Access-Control-Allow-Origin: *");
        $oReturn = new stdClass();

        if(!$_REQUEST['activity_id']){$oReturn->message = __('Wrong Activity ID passed.','aheadzen'); return $oReturn;}

		$activity_id = $_GET['activity_id'];
		global $table_prefix,$wpdb;
		if($activity_id){
			$res = $wpdb->get_results("select * from ".$table_prefix."bp_activity where id=\"$activity_id\"");

			if($res){
				$oActivity = $res[0];
				$oReturn->success = 'success';
				$data = $this->get_formatted_activity_data((array)$oActivity);
				$oReturn->data = $data;
				/*$oReturn->activitiy->id = $oActivity->id;
				$oReturn->activitiy->action = $oActivity->action;
				$oReturn->activitiy->content = $oActivity->content;
				$oReturn->activitiy->user_id = $oActivity->user_id;
				$oReturn->activitiy->item_id = $oActivity->item_id;
				$oReturn->activitiy->secondary_item_id = $oActivity->secondary_item_id;
				$oReturn->activitiy->date_recorded = $oActivity->date_recorded;*/

			}else{
				$oReturn->error = __('No data found.','aheadzen');
			}
		}
		return  $oReturn;
	 }

	 /**
     * Returns an Array with all activities
     * @param int pages: number of pages to display (default unset)
     * @param int offset: number of entries per page (default 10 if pages is set, otherwise unset)
     * @param int limit: number of maximum results (default 0 for unlimited)
     * @param String sort: sort ASC or DESC (default DESC)
     * @param String comments: 'stream' for within stream display, 'threaded' for below each activity item (default unset)
     * @param Int userid: userID to filter on, comma-separated for more than one ID (default unset)
     * @param String component: object to filter on e.g. groups, profile, status, friends (default unset)
     * @param String type: action to filter on e.g. activity_update, profile_updated (default unset)
     * @param int itemid: object ID to filter on e.g. a group_id or forum_id or blog_id etc. (default unset)
     * @param int secondaryitemid: secondary object ID to filter on e.g. a post_id (default unset)
     * @return array activities: an array containing the activities
     */
	 public function activity_get_activities_grouped() {
		add_filter('bp_insert_activity_meta','bp_insert_activity_meta_fun',999,2);
		add_filter('bp_activity_truncate_entry','bp_activity_truncate_entry_fun',999,3);
		header("Access-Control-Allow-Origin: *");
        $oReturn = new stdClass();
		$oReturn->success = '';
        $this->init('activity', 'see_activity');

		global $table_prefix,$wpdb;
		if(!$this->userid && $_GET['username']){
			$oUser = get_user_by('login', $_GET['username']);
			if($oUser){$this->userid = $oUser->data->ID;}
		}

		$aParams ['user_id'] = $this->userid;
		$aParams ['object'] = $this->component;
		$aParams ['type'] = $this->type;
		$aParams ['primary_id'] = $this->itemid;
		$aParams ['secondary_id'] = $this->secondaryitemid;
		$aParams ['display_comments'] = $this->comments;
		$aParams ['sort'] = $this->sort;
		$aParams ['filter'] ['user_id'] = $this->userid;
		$aParams ['filter'] ['object'] = $this->component;
		$aParams ['filter'] ['type'] = $this->type;
		$aParams ['filter'] ['primary_id'] = $this->itemid;
		$aParams ['filter'] ['secondary_id'] = $this->secondaryitemid;
		$iLimit = $this->limit;

		$page = $_GET['thepage'];
		if(!$page){$page=1;}
		$per_page = $_GET['per_page'];
		if(!$per_page){$per_page=50;}
		$count_total = $_GET['count_total'];
		if(!$count_total){$count_total=100;}

		$aParams['page']=$page;
		$aParams['per_page']=$per_page;
		$aParams['count_total']=$count_total;

		$activities = trim($_GET['activities']);
		if($activities){
			$aParams['in']=$activities;
		}

		if ($this->pages !== 1) {
			$aParams ['max'] = true;
			$aParams ['per_page'] = $this->offset;
			$iPages = $this->pages;
		}

		$bp_has_activities_Obj = bp_has_activities($aParams);
		if (!$bp_has_activities_Obj)
			return $this->error('activity');

		$theActivityGroup = array();
		global $activities_template;
		if ($bp_has_activities_Obj){
			$acounter=0;
			while ( bp_activities() ){
				bp_the_activity();
				$oActivity =  $activities_template->activity;
				remove_filter( 'bp_get_activity_content_body', 'bp_activity_truncate_entry', 5 );
				$oActivity->content = bp_get_activity_content_body();
				if($oActivity->component=='votes' || $oActivity->type=='joined_group'){ }else{
					if($oActivity->type=='updated_profile' || $oActivity->type=='new_avatar'){
						$theActivityGroup[$oActivity->component][$oActivity->type][$oActivity->user_id][0] = $oActivity;
					}else{
						if($oActivity->type=='save_chart' || $oActivity->type=='new_member' || $theAct->type=='joined_group'){
							$theActivityGroup[$oActivity->component][$oActivity->type][$oActivity->item_id][] = $oActivity;
						}else{
							$randVar = time().rand(1,10000);
							$theActivityGroup[$oActivity->component][$oActivity->type][$randVar][] = $oActivity;
						}
					}
				}
			}

			$activityFinalArr = array();
			if($theActivityGroup){
				foreach($theActivityGroup as $activityCompArr){
					foreach($activityCompArr as $activityTypeArr){
						foreach($activityTypeArr as $activityUerArr){
							$theStrArr = array();
							$varGrpName = '';
							$spliterStr = '';
							$multiActivity = 0;
							$newMembersArr = array();
							$spliterStr2 = '';
							if(count($activityUerArr)>1){
								$i=0;
								foreach($activityUerArr as $theAct){
									if($theAct->component=='groups' && $theAct->type=='joined_group'){
										$spliterStr = 'joined the group';
									}else if($theAct->component=='birth_chart' && $theAct->type=='save_chart'){
										$spliterStr = 'just received';
									}else if($theAct->component=='members' && $theAct->type=='new_member'){
										$spliterStr = 'became a registered member';
										$spliterStr2 = 'just registered.';
										$newMembersArr[] = $theAct->user_id;
									}
									if($spliterStr){
										$expActionArr = explode($spliterStr,$theAct->action);
										$theStrArr[] = trim($expActionArr[0]);
										$varGrpName = trim($expActionArr[1]);
										$multiActivity=1;
									}
									if($i==2){
										$others = (count($activityUerArr)-3);
										if($spliterStr2){$spliterStr = $spliterStr2;}
										if($others>=1){
											if($others>1){
												$spliterStr = 'and '.$others.' others ' . $spliterStr;
											}elseif($others==1){
												$spliterStr = 'and '.$others.' other '. $spliterStr;
											}
										}
										break;
									}
									$i++;
								}
								$theActivityVar = $activityUerArr[0];
								if(count($theStrArr)==2){$theSep = ' & ';}else{$theSep = ', ';}
								if($spliterStr){$spliterStr = ' '.$spliterStr.' ';}
								$theActivityVar->action = implode($theSep,$theStrArr).$spliterStr.$varGrpName;
								$theActivityVar->multiActivity = $multiActivity;
							}else{
								$activityUerArr[0]->multiActivity = 0;
								$theActivityVar=$activityUerArr[0];
							}
							if($theActivityVar->type=='bbp_reply_create' || $theActivityVar->type=='bbp_topic_create'){
								$theActivityVar->content1 = $theActivityVar->content;
								//$theActivityVar->component='groups';
							}else{
								$theActivityVar->content1 = '';
							}
							if($theActivityVar->component=='groups' && $this->component==''){
								$aGroup = groups_get_group( array( 'group_id' => $theActivityVar->item_id ) );
								if($aGroup){
									$Gname = $aGroup->name;
									$Gdescription = $aGroup->description;
									$Gslug = $aGroup->slug;
									$Gpermalink = site_url('/') . 'groups/' . $Gslug . '/';
								}
								$avatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$theActivityVar->item_id, 'html'=>false, 'type'=>'full'));
								if($avatar_url && !strstr($avatar_url,'http:')){ $avatar_url = 'http:'.$avatar_url;}
								$theActivityVar->content = '<a href="'.$Gpermalink.'"><img src="'.$avatar_url.'" alt="'.$Gname.'" class="full-image" style="max-width:250px;height:auto;"></a>';
							}else if($theActivityVar->component=='birth_chart' && $theActivityVar->type=='save_chart'){
								$post_thumbnail = get_the_post_thumbnail(4089,'medium',array( 'class' => 'full-image', 'style' => 'max-width:250px;height:auto;'));
								$birthChartLink = get_permalink(4089);
								if($post_thumbnail){
									$theActivityVar->content = '<a href="'.$birthChartLink.'">'.$post_thumbnail.'</a>';
								}
							}else if($theActivityVar->component=='members' && $theActivityVar->type=='new_member'){
								$contentStr =  '<div class="row activityJoinUsers">';
								if($newMembersArr){
									for($m=0;$m<count($newMembersArr);$m++){
										$user = new BP_Core_User($newMembersArr[$m]);

										if($user && $user->avatar){
											$avatar_thumb = $user->avatar_thumb;
											preg_match_all('/(src)=("[^"]*")/i',$avatar_thumb, $avatar_thumb_result);
											$avatar_thumb_src = str_replace('"','',$avatar_thumb_result[2][0]);
											if($avatar_thumb_src && !strstr($avatar_thumb_src,'http:')){ $avatar_thumb_src = 'http:'.$avatar_thumb_src;}
											$contentStr .= '<div class="col col-30"><a href="'.$user->user_url.'"><img src="'.$avatar_thumb_src.'" alt=""></a></div>';
										}
									}
								}
								$contentStr .= '</div>';
								$theActivityVar->content = $contentStr;
							}
							$activityFinalArr[]=$theActivityVar;
						}
					}
				}
			}
			if(!$activityFinalArr){return $oReturn;}
			for($a=0;$a<count($activityFinalArr);$a++){
				$oActivity = $activityFinalArr[$a];
				if($oActivity->type=='activity_comment'){

				}else{
					$user = new BP_Core_User($oActivity->user_id);
					if($user && $user->avatar){
						if($user->avatar){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar, $user_avatar_result);
							$thumb = str_replace('"','',$user_avatar_result[2][0]);
							if($thumb && !strstr($thumb,'http:')){ $thumb = 'http:'.$thumb;}
							$oActivity->avatar_thumb = $thumb;
						}
					}

					$oReturn->activities[$acounter]->id = $oActivity->id;
					$oReturn->activities[$acounter]->component = $oActivity->component;
					$oReturn->activities[$acounter]->type = $oActivity->type;
					$oReturn->activities[$acounter]->user->id = $oActivity->user_id;
					$oReturn->activities[$acounter]->user->username = $oActivity->user_login;
					$oReturn->activities[$acounter]->user->mail = $oActivity->user_email;
					$oReturn->activities[$acounter]->user->display_name = bpaz_user_name_from_email($oActivity->user_fullname);
					$oReturn->activities[$acounter]->user->avatar_thumb = $oActivity->avatar_thumb;
					$oReturn->activities[$acounter]->item_id = $oActivity->item_id;
					$oReturn->activities[$acounter]->secondary_item_id = $oActivity->secondary_item_id;
					$oReturn->activities[$acounter]->time = $oActivity->date_recorded;
					$oReturn->activities[$acounter]->multiActivity = $oActivity->multiActivity;

					$oReturn->activities[$acounter]->user->is_following = 0;
					if($_GET['currentUserId'] &&  $oActivity->user_id==$_GET['currentUserId']){
						$oReturn->activities[$acounter]->user->is_following = 1;
					}elseif(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$oActivity->user_id,'follower_id'=>$_GET['currentUserId']))){
						$oReturn->activities[$acounter]->user->is_following = 1;
					}

					if($oActivity->type=='new_avatar'){
						$oActivity->action = 'Changed their profile picture. <br /><img class="full-image" src="'.$oActivity->avatar_thumb.'" alt="" />';
					}else if($oActivity->type=='updated_profile'){
						if($oActivity->action=='' && $oActivity->content==''){
							$oActivity->action = 'Changed their profile';
						}
					}
					$oReturn->activities[$acounter]->action = $oActivity->action;
					if(strlen($oActivity->content)>10){
						$oActivity->content = do_shortcode($oActivity->content);
					}
					$srch = array('&rdquo;','&rdquo; ');
					$repl = array('"','"');
					if($oActivity->type=='new_blog_comment'){
						$oActivity->content = str_replace($srch,$repl,nl2br(wp_specialchars_decode($oActivity->content)));
					}
					$oReturn->activities[$acounter]->content = stripcslashes($oActivity->content);
					$oReturn->activities[$acounter]->content1 = stripcslashes($oActivity->content1);
					$oReturn->activities[$acounter]->is_hidden = $oActivity->hide_sitewide === "0" ? false : true;
					$oReturn->activities[$acounter]->is_spam = $oActivity->is_spam === "0" ? false : true;
					if($oActivity->children){
						$oReturn->activities[$acounter]->childCount = count($oActivity->children);
					}else{
						$oReturn->activities[$acounter]->childCount = 0;
					}
					$total_votes = $total_up = $total_down = 0;
					$uplink = $downlink = '#';
					$voteed_action = 'up';
					if(class_exists('VoterPluginClass'))
					{
						$arg = array(
							'item_id'=>$oActivity->id,
							'type'=>'activity',
							);
						$votes_str = VoterPluginClass::aheadzen_get_post_all_vote_details($arg);
						if($votes_str){
						$votes = json_decode($votes_str);
						$total_votes = $votes->total_votes;
						$total_up = $votes->total_up;
						$total_down = $votes->total_down;
						$uplink = $votes->post_voter_links->up;
						$downlink = $votes->post_voter_links->down;
						}
						if($_GET['currentUserId']){
							$user_id = $_GET['currentUserId'];
							$secondary_item_id = $oActivity->id;
							$type = 'activity';
							$item_id = 0;
							$component = 'buddypress';
							$voteed_action = $wpdb->get_var("SELECT action FROM `".$table_prefix."ask_votes` WHERE user_id=\"$user_id\" AND item_id=\"$item_id\" AND component=\"$component\" AND type=\"$type\" AND secondary_item_id=\"$secondary_item_id\"");

							$users = $wpdb->get_results("select user_id,date_recorded from `".$table_prefix."ask_votes` where component=\"$component\" and type=\"$type\" and item_id=\"$item_id\" and secondary_item_id=\"$secondary_item_id\" order by date_recorded desc limit 10");
							$voted_users = NULL;
							if($users){
								$vcount = 0;
								foreach($users as $usersobj)
								{
									$uid = $usersobj->user_id;
									$vuser = new BP_Core_User($uid);
									$voted_users[$vcount]->user_id=$uid;
									$voted_users[$vcount]->date=$usersobj->date_recorded;
									$voted_users[$vcount]->name=$vuser->fullname;
									if($vuser->avatar_thumb){
										preg_match_all('/(src)=("[^"]*")/i',$vuser->avatar_thumb, $user_avatar_result);
										$thumb = str_replace('"','',$user_avatar_result[2][0]);
										if($thumb && !strstr($thumb,'http:')){ $thumb = 'http:'.$thumb;}
										$voted_users[$vcount]->thumb=$thumb;
									}
									$voted_users[$vcount]->username=$vuser->profile_data['user_login'];
									$vcount++;
								}
								$oReturn->activities[$acounter]->vote->voted_users = $voted_users;
							}
						}
					}

					$oReturn->activities[$acounter]->vote->total_votes = $total_votes;
					$oReturn->activities[$acounter]->vote->total_up = $total_up;
					$oReturn->activities[$acounter]->vote->total_down = $total_down;
					$oReturn->activities[$acounter]->vote->action = $voteed_action;
					$oReturn->activities[$acounter]->suggetionGroups = null;
					$acounter++;
				}
			}

			if($page==1 && !$this->userid){
				$suggetionGroups = $this->get_dashboard_groups($_GET['currentUserId']);
				if($suggetionGroups){
					$oReturn->activities[$acounter]->id = 0;
					$oReturn->activities[$acounter]->component = 'list_suggestion';
					$oReturn->activities[$acounter]->type = 'group_suggestion';
					$oReturn->activities[$acounter]->user->id = 0;
					$oReturn->activities[$acounter]->multiActivity = 1;
					$oReturn->activities[$acounter]->suggetionGroups = $suggetionGroups;
					$acounter++;
				}
			}

			if($page==2 && !$this->userid){
				$suggetionMembers = $this->get_dashboard_members($_GET['currentUserId']);
				if($suggetionMembers){
					$oReturn->activities[$acounter]->id = 0;
					$oReturn->activities[$acounter]->component = 'list_suggestion';
					$oReturn->activities[$acounter]->type = 'member_suggestion';
					$oReturn->activities[$acounter]->user->id = 0;
					$oReturn->activities[$acounter]->multiActivity = 1;
					$oReturn->activities[$acounter]->suggetionMembers = $suggetionMembers;
					$acounter++;
				}
			}

			$oReturn->total_pages = ceil($aTempActivities['total']/$per_page);
			$oReturn->total_count = $aTempActivities['total'];
			$oReturn->currentuser_avatar = '';
			$oReturn->is_currentuser_avatar = 0;
			$oReturn->profile_complete = 10;
			if($_GET['currentUserId']){
				if(bp_get_user_has_avatar($_GET['currentUserId'])){
					$oReturn->is_currentuser_avatar = 1;
				}

				$user = new BP_Core_User($_GET['currentUserId']);
				if($user && $user->avatar){
					if($user->avatar){
						preg_match_all('/(src)=("[^"]*")/i',$user->avatar, $user_avatar_result);
						$thumb = str_replace('"','',$user_avatar_result[2][0]);
						if($thumb && !strstr($thumb,'http:')){ $thumb = 'http:'.$thumb;}
						$oReturn->currentuser_avatar = $thumb;
					}
				}

				$user = new BP_Core_User($_GET['currentUserId']);
				if($user){
					$profile_complete = 0;
					if($user->avatar){ $profile_complete = $profile_complete+5;}
					if($user->profile_data['Name']['field_data']){$profile_complete = $profile_complete+5;}
					if($user->profile_data['I am']['field_data']){$profile_complete = $profile_complete+5;}
					if($user->profile_data['Country']['field_data']){$profile_complete = $profile_complete+5;}
					if($user->profile_data['City']['field_data']){$profile_complete = $profile_complete+5;}
					if($user->profile_data['About Me']['field_data']){ $profile_complete = $profile_complete+5;} //30
					if($user->profile_data['Ethnicity']['field_data']){$profile_complete = $profile_complete+5;}
					if($user->profile_data['Height']['field_data']){$profile_complete = $profile_complete+5;}
					if($user->profile_data['Body Type']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Diet']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Smokes']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Drinks']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Speaks']['field_data']){$profile_complete = $profile_complete+4;} //30
					if($user->profile_data["I\'m really good at"]['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data["I\'m improving and seeking help for"]['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['My favorites']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Special five I could never do without']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['I spend a lot of time thinking about']['field_data']){$profile_complete = $profile_complete+4;} //20
					if($user->profile_data['Educational Qualification']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Primary Work/Job/Occupation']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Annual Income']['field_data']){$profile_complete = $profile_complete+4;} //12
					if($user->profile_data['Religion']['field_data']){$profile_complete = $profile_complete+4;}
					if($user->profile_data['Website']['field_data']){$profile_complete = $profile_complete+4;} //8
					$oReturn->profile_complete = $profile_complete;
				}
			}
		} else {
			return $this->error('activity');
		}

		//echo '<pre>';print_r($oReturn);echo '</pre>';
		return $oReturn;
	}
	/**
     * Returns an Array with all activities
     * @param int pages: number of pages to display (default unset)
     * @param int offset: number of entries per page (default 10 if pages is set, otherwise unset)
     * @param int limit: number of maximum results (default 0 for unlimited)
     * @param String sort: sort ASC or DESC (default DESC)
     * @param String comments: 'stream' for within stream display, 'threaded' for below each activity item (default unset)
     * @param Int userid: userID to filter on, comma-separated for more than one ID (default unset)
     * @param String component: object to filter on e.g. groups, profile, status, friends (default unset)
     * @param String type: action to filter on e.g. activity_update, profile_updated (default unset)
     * @param int itemid: object ID to filter on e.g. a group_id or forum_id or blog_id etc. (default unset)
     * @param int secondaryitemid: secondary object ID to filter on e.g. a post_id (default unset)
     * @return array activities: an array containing the activities
     */
	 public function activity_get_activities() {
		add_filter('bp_insert_activity_meta','bp_insert_activity_meta_fun',999,2);
		add_filter('bp_activity_truncate_entry','bp_activity_truncate_entry_fun',999,3);
		header("Access-Control-Allow-Origin: *");
        $oReturn = new stdClass();
		$oReturn->success = '';
        $this->init('activity', 'see_activity');

		global $table_prefix,$wpdb;
		if(!$this->userid && $_GET['username']){
			$oUser = get_user_by('login', $_GET['username']);
			if($oUser){$this->userid = $oUser->data->ID;}
		}
		$mentionid = $_GET['mentionid'];

		if($mentionid){
			global $wpdb,$table_prefix;
			$parent_activity = $wpdb->get_var("select item_id from ".$table_prefix."bp_activity where id=\"$mentionid\"");
			if($parent_activity==0){
				$parent_activity = $mentionid;
			}
			$aParams = array();
			$aParams ['display_comments'] = true;
			$aParams['in'] = array($parent_activity);
		}else{
			if (!bp_has_activities())
				return $this->error('activity');
			if ($this->pages !== 1) {
				$aParams ['max'] = true;
				$aParams ['per_page'] = $this->offset;
				$iPages = $this->pages;
			}

			$aParams ['display_comments'] = $this->comments;
			$aParams ['sort'] = $this->sort;

			if($this->userid){
				$aParams ['filter'] ['user_id'] = $this->userid;
				$aParams ['filter'] ['object'] = $this->component;
				$aParams ['filter'] ['type'] = $this->type;
				$aParams ['filter'] ['primary_id'] = $this->itemid;
				$aParams ['filter'] ['secondary_id'] = $this->secondaryitemid;
			}
			$iLimit = $this->limit;

			$page = $_GET['thepage'];
			if(!$page){$page=1;}
			$per_page = $_GET['per_page'];
			if(!$per_page){$per_page=50;}
			$count_total = $_GET['count_total'];
			if(!$count_total){$count_total=100;}

			$aParams['page']=$page;
			$aParams['per_page']=$per_page;
			$aParams['count_total']=$count_total;

			$activities = trim($_GET['activities']);
			if($activities){
				$aParams['in']=$activities;
			}
		}

		global $activities_template;
		if (bp_has_activities($aParams)){
				$acounter=0;
        		 while ( bp_activities() ){
					bp_the_activity();
					$oActivity =  $activities_template->activity;
					remove_filter( 'bp_get_activity_content_body', 'bp_activity_truncate_entry', 5 );
					$oActivity->content = bp_get_activity_content_body();
					if($oActivity->type=='activity_comment'){

					}else{
						$user = new BP_Core_User($oActivity->user_id);
						if($user && $user->avatar){
							if($user->avatar_thumb){
								preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
								$thumb = str_replace('"','',$user_avatar_result[2][0]);
								if($thumb && !strstr($thumb,'http:')){ $thumb = 'http:'.$thumb;}
								$oActivity->avatar_thumb = $thumb;

								$user_avatar = $user->avatar;
								preg_match_all('/(src)=("[^"]*")/i',$user_avatar, $user_avatar_result);
								$user_avatar_src = str_replace('"','',$user_avatar_result[2][0]);
								if($user_avatar_src && !strstr($user_avatar_src,'http:')){ $user_avatar_src = 'http:'.$user_avatar_src;}
								$oActivity->avatar_full = $user_avatar_src;
							}
						}

						$oReturn->activities[$acounter]->id = $oActivity->id;
						$oReturn->activities[$acounter]->component = $oActivity->component;
						$oReturn->activities[$acounter]->user->id = $oActivity->user_id;
						$oReturn->activities[$acounter]->user->username = $oActivity->user_login;
						$oReturn->activities[$acounter]->user->mail = $oActivity->user_email;
						//$oReturn->activities[$acounter]->user->display_name = bpaz_user_name_from_email($oActivity->user_fullname);
						$oReturn->activities[$acounter]->user->display_name = $user->fullname;
						//$oReturn->activities[$acounter]->user->avatar_big = $oActivity->avatar_big;
						$oReturn->activities[$acounter]->user->avatar_thumb = $oActivity->avatar_thumb;
						$oReturn->activities[$acounter]->item_id = $oActivity->item_id;
						$oReturn->activities[$acounter]->secondary_item_id = $oActivity->secondary_item_id;
						$oReturn->activities[$acounter]->type = $oActivity->type;
						$oReturn->activities[$acounter]->time = $oActivity->date_recorded;

						$oReturn->activities[$acounter]->user->is_following = 0;
						if($_GET['currentUserId']  && $oActivity->user_id==$_GET['currentUserId']){
							$oReturn->activities[$acounter]->user->is_following = 1;
						}elseif(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$oActivity->user_id,'follower_id'=>$_GET['currentUserId']))){
							$oReturn->activities[$acounter]->user->is_following = 1;
						}

						if($oActivity->type=='new_avatar'){
							//$oActivity->action = '<a href="'.$oActivity->primary_link.'">'.$oActivity->user_fullname.'</a> changed their profile picture. <br /><img src="'.$oActivity->avatar_thumb.'" alt="" />';
							$oActivity->action = 'Changed their profile picture. <br /><img class="full-image" src="'.$oActivity->avatar_full.'" alt="" />';
						}else if($oActivity->type=='updated_profile'){
							if($oActivity->action=='' && $oActivity->content==''){
								$oActivity->action = 'Changed their profile';
							}
						}
						$oReturn->activities[$acounter]->action = $oActivity->action;
						if(strlen($oActivity->content)>10){
							$oActivity->content = do_shortcode($oActivity->content);
						}
						$oReturn->activities[$acounter]->content = stripcslashes($oActivity->content);
						$oReturn->activities[$acounter]->is_hidden = $oActivity->hide_sitewide === "0" ? false : true;
						$oReturn->activities[$acounter]->is_spam = $oActivity->is_spam === "0" ? false : true;

						$total_votes = $total_up = $total_down = 0;
						$uplink = $downlink = '#';
						$voteed_action = 'up';
						if(class_exists('VoterPluginClass'))
						{
							$arg = array(
								'item_id'=>$oActivity->id,
								'user_id'=>$oActivity->user_id,
								'type'=>'activity',
								);

							$votes_str = VoterPluginClass::aheadzen_get_post_all_vote_details($arg);
							if($votes_str){
							$votes = json_decode($votes_str);
							$total_votes = $votes->total_votes;
							$total_up = $votes->total_up;
							$total_down = $votes->total_down;
							//$uplink = $votes->post_voter_links->up;
							//$downlink = $votes->post_voter_links->down;
							}
							if($_GET['currentUserId']){
								$user_id = $_GET['currentUserId'];
								$secondary_item_id = $oActivity->id;
								$type = 'activity';
								$item_id = 0;
								$component = 'buddypress';
								$voteed_action = $wpdb->get_var("SELECT action FROM `".$table_prefix."ask_votes` WHERE user_id=\"$user_id\" AND item_id=\"$item_id\" AND component=\"$component\" AND type=\"$type\" AND secondary_item_id=\"$secondary_item_id\"");
							}

							$users = $wpdb->get_results("select user_id,date_recorded from `".$table_prefix."ask_votes` where component=\"$component\" and type=\"$type\" and item_id=\"$item_id\" and secondary_item_id=\"$secondary_item_id\" order by date_recorded desc limit 20");
							$voted_users = NULL;
							if($users){
								$vcount = 0;
								foreach($users as $usersobj)
								{
									$uid = $usersobj->user_id;
									$vuser = new BP_Core_User($uid);
									$voted_users[$vcount]->user_id=$uid;
									$voted_users[$vcount]->date=$usersobj->date_recorded;
									$voted_users[$vcount]->name=$vuser->fullname;
									if($vuser->avatar_thumb){
										preg_match_all('/(src)=("[^"]*")/i',$vuser->avatar_thumb, $user_avatar_result);
										$thumb = str_replace('"','',$user_avatar_result[2][0]);
										if($thumb && !strstr($thumb,'http:')){ $thumb = 'http:'.$thumb;}
										$voted_users[$vcount]->thumb=$thumb;
									}
									$voted_users[$vcount]->username=$vuser->profile_data['user_login'];
									$vcount++;
								}
								$oReturn->activities[$acounter]->vote->voted_users = $voted_users;
							}
						}

						$oReturn->activities[$acounter]->vote->total_votes = $total_votes;
						$oReturn->activities[$acounter]->vote->total_up = $total_up;
						$oReturn->activities[$acounter]->vote->total_down = $total_down;
						//$oReturn->activities[$acounter]->vote->uplink = $uplink;
						//$oReturn->activities[$acounter]->vote->downlink = $downlink;
						$oReturn->activities[$acounter]->vote->action = $voteed_action;
						$oReturn->activities[$acounter]->multiActivity = 0;

						if($oActivity->children){
							/*children*/
							$counter=0;
							foreach($oActivity->children as $childoActivity){
							$childuser = new BP_Core_User($childoActivity->user_id);
							if($childuser && $childuser->avatar){
								if($childuser->avatar_thumb){
									preg_match_all('/(src)=("[^"]*")/i',$childuser->avatar_thumb, $user_avatar_result);
									$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
									if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
									$childoActivity->avatar_thumb = $avatar_thumb;
								}
							}
							$oReturn->activities[$acounter]->children->$counter->id = $childoActivity->id;
							$oReturn->activities[$acounter]->children->$counter->item_id = $childoActivity->item_id;
							$oReturn->activities[$acounter]->children->$counter->component = $childoActivity->component;
							$oReturn->activities[$acounter]->children->$counter->user->id = (int)$childoActivity->user_id;
							$oReturn->activities[$acounter]->children->$counter->user->username = $childoActivity->user_login;
							$oReturn->activities[$acounter]->children->$counter->user->mail = $childoActivity->user_email;
							$oReturn->activities[$acounter]->children->$counter->user->display_name = $childuser->fullname;;
							//$oReturn->activities[$acounter]->children->$counter->user->avatar_big = $childoActivity->avatar_big;
							$oReturn->activities[$acounter]->children->$counter->user->avatar_thumb = $childoActivity->avatar_thumb;
							$oReturn->activities[$acounter]->children->$counter->type = $childoActivity->type;
							$oReturn->activities[$acounter]->children->$counter->time = $childoActivity->date_recorded;
							$oReturn->activities[$acounter]->children->$counter->action = $childoActivity->action;
							$oReturn->activities[$acounter]->children->$counter->content = stripcslashes($childoActivity->content);
							$oReturn->activities[$acounter]->children->$counter->is_hidden = $childoActivity->hide_sitewide === "0" ? false : true;
							$oReturn->activities[$acounter]->children->$counter->is_spam = $childoActivity->is_spam === "0" ? false : true;
							$user = new BP_Core_User($childoActivity->user_id);

							$total_votes = $total_up = $total_down = 0;
							$uplink = $downlink = '#';
							$voteed_action = '';
							if(class_exists('VoterPluginClass'))
							{
								$arg = array(
									'item_id'=>$childoActivity->id,
									'user_id'=>$childoActivity->user_id,
									'type'=>'activity',
									//'component'=>'buddypress',
									);
								$votes_str = VoterPluginClass::aheadzen_get_post_all_vote_details($arg);
								$votes = json_decode($votes_str);

								$total_votes = $votes->total_votes;
								$total_up = $votes->total_up;
								$total_down = $votes->total_down;
								$uplink = $votes->post_voter_links->up;
								$downlink = $votes->post_voter_links->down;

								if($_GET['currentUserId']){
									$user_id = $_GET['currentUserId'];
									$secondary_item_id = $childoActivity->id;
									$type = 'activity';
									$item_id = 0;
									$component = 'buddypress';
									$voteed_action = $wpdb->get_var("SELECT action FROM `".$table_prefix."ask_votes` WHERE user_id=\"$user_id\" AND component=\"$component\" AND type=\"$type\" AND secondary_item_id=\"$secondary_item_id\"");
								}
							}

							$oReturn->activities[$acounter]->children->$counter->vote->total_votes = $total_votes;
							$oReturn->activities[$acounter]->children->$counter->vote->total_up = $total_up;
							$oReturn->activities[$acounter]->children->$counter->vote->total_down = $total_down;
							//$oReturn->activities[$acounter]->children->$counter->vote->uplink = $uplink;
							//$oReturn->activities[$acounter]->children->$counter->vote->downlink = $downlink;
							$oReturn->activities[$acounter]->children->$counter->vote->action = $voteed_action;

							$counter++;
							}

						}
						$acounter++;
					}

				}

				//echo '<pre>';print_r($oReturn); exit;
				$oReturn->total_pages = ceil($aTempActivities['total']/$per_page);
				$oReturn->total_count = $aTempActivities['total'];
            } else {
                return $this->error('activity');
            }

			//echo '<pre>';print_r($oReturn);echo '</pre>';
            return $oReturn;
	}

	public function activity_mark_spam()
	{
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';

		$activity_id = $_GET['activityid'];
		if(!$activity_id){$oReturn->error = __('No Activity Id.','aheadzen'); return $oReturn;}

		/*$activity_data = bp_activity_get(array('in'=>$activity_id));
		if(!$activity_data['activities']){$oReturn->error = __('Wrong Activity.','aheadzen'); return $oReturn;}

		$activity = $activity_data['activities'][0];
		bp_activity_mark_as_spam($activity);
		*/

		global $wpdb,$table_prefix;
		$res = $wpdb->query("update ".$table_prefix."bp_activity set is_spam=1 where id=\"$activity_id\"");
		if($res){
			$oReturn->success->msg = __('Activity marked as spam successfully.','aheadzen');
			$oReturn->success->id = $activity_id;
		}else{
			$oReturn->error = __('May be wrong activity Id or already spammed.','aheadzen');
		}
		return $oReturn;
	}

	/**
		 * Returns an array with the profile's fields
		 * @param String username: the username you want information from (required)
		 * @return array profilefields: an array containing the profilefields
		 */
		public function profile_get_profile() {
			header("Access-Control-Allow-Origin: *");
			$this->userid = $_GET['userid'];
			$this->username = $_GET['username'];
			$this->init('xprofile');
			$oReturn = new stdClass();
			$oReturn->success = '';
			$error=0;

			/*if(($this->userid=='' && $this->username === false) || ($this->username && !username_exists($this->username))) {
				return $this->error('xprofile', 1);
			}*/
			if($this->userid){
				$userid = $this->userid;
			}else{
				$oUser = get_user_by('login', $this->username);
				if(!empty($oUser)){
					$userid = $oUser->data->ID;
				} else  {
					$oUser = get_user_by('slug', $this->username);
					$userid = $oUser->data->ID;
				}


			}


			if (!bp_has_profile(array('user_id' => $userid))) {
				return $this->error('xprofile', 0);
			}
			while (bp_profile_groups(array('user_id' => $userid))) {
				bp_the_profile_group();
				if (bp_profile_group_has_fields()) {
					$sGroupName = bp_get_the_profile_group_name();
					while (bp_profile_fields()) {
						bp_the_profile_field();
						$sFieldName = bp_get_the_profile_field_name();
						if (bp_field_has_data()) {
						   $sFieldValue = strip_tags(bp_get_the_profile_field_value());
						}
						$oReturn->profilefields->$sGroupName->$sFieldName = trim($sFieldValue);
					}
				}
			}

			/* CUstom changes VAJ - 09-06-2015*/
			$user = new BP_Core_User( $userid );
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

				$bbp_cover_pic = get_user_meta( $userid, 'bbp_cover_pic',true);


				if(!$bbp_cover_pic){$bbp_cover_pic=$user_avatar_src;}
				$oReturn->profilefields->photo->avatar = $user_avatar_src;
				$oReturn->profilefields->photo->avatar_big = $user_avatar_src;
				$oReturn->profilefields->photo->avatar_thumb = $avatar_thumb_src;
				$oReturn->profilefields->photo->avatar_mini = $avatar_mini;

				$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' => $userid ) );
				$oReturn->profilefields->photo->cover_pic = $cover_image_url;
				$oReturn->profilefields->user->username = $user->profile_data['user_login'];
				$oReturn->profilefields->user->user_email = $user->profile_data['user_email'];
				$oReturn->profilefields->user->userid = $userid;

				if(function_exists('bp_follow_total_follow_counts')){
					$oReturn->profilefields->follow_counts  = bp_follow_total_follow_counts( array( 'user_id' => $userid ) );
				}
				$oReturn->profilefields->is_following = 0;
				if(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$userid,'follower_id'=>$_GET['cuserid']))){
					$oReturn->profilefields->is_following = 1;
				}
			}
			/* CUstom changes VAJ - 09-06-2015*/
			return $oReturn;

		}

    /**
     * Returns an array with messages for the current username
     * @param String box: the box you the messages are in (possible values are 'inbox', 'sentbox', 'notices', default is 'inbox')
     * @param int per_page: items to be displayed per page (default 10)
     * @param boolean limit: maximum numbers of emtries (default no limit)
     * @return array messages: contains the messages
     */
    public function messages_get_messages() {
		header("Access-Control-Allow-Origin: *");
		$this->init('messages');
        $oReturn = new stdClass();

		$page = $_GET['thepage'];
		if(!$page){$page=1;}

        $aParams ['box'] = $this->box;
        $aParams ['per_page'] = $this->per_page;
		$aParams ['page'] = $page;
        $aParams ['max'] = $this->limit;
		$aParams ['user_id'] = $_GET['userid'];
		//wp_set_current_user($_GET['userid']);
		//wp_set_auth_cookie($_GET['userid']);
		//do_action( 'wp_login', $user->user_login );
		add_filter('bp_loggedin_user_id','aheadzen_bp_loggedin_user_id_function');
		global $messages_template,$thread_template;
		$counter = 0;
        if (bp_has_message_threads($aParams)) {

			while (bp_message_threads()) {

				bp_message_thread();
				$aTemp = new stdClass();
				$oUser = new BP_Core_User($messages_template->thread->last_sender_id);
				$username = '';
				if($oUser->user_url){
					$username = str_replace('/','',str_replace(site_url('/members/'),'',$oUser->user_url));
				}
				//print "<pre>";print_r($messages_template);
				//echo $messages_template->threads[$counter]->messages[0]->id;
				//die;
				$thread_template = $messages_template;
				if(bp_get_thread_recipients_count() <= 1){
					$aTemp->conversation = __( 'You are alone', 'buddypress' );
				}else{
					$aTemp->conversation = sprintf( __( '%s and you', 'buddypress' ), bp_get_thread_recipients_list() );
				}

				$recipients_count = bp_get_thread_recipients_count();
				$aTemp->max_thread_recipients = $recipients_count;
				$aTemp->from->id = $oUser->id;
				$aTemp->from->username = $username;
                $aTemp->from->mail = $oUser->email;
                $aTemp->from->display_name = bpaz_user_name_from_email($oUser->fullname);
				//$aTemp->from->last_activity = bp_get_last_activity($oUser->id);


				preg_match_all('/(src)=("[^"]*")/i',$oUser->avatar, $user_avatar_result);
				$aTemp->from->avatar = str_replace('"','',$user_avatar_result[2][0]);
				preg_match("#>(.*?)<#", bp_get_message_thread_to(), $aTo);
				$oUser = get_user_by('login', $aTo[1]);
                $aTemp->to->id = $oUser->data->ID;
				$aTemp->to->username = $aTo[1];
                $aTemp->to->mail = $oUser->data->user_email;
                $aTemp->to->display_name = bpaz_user_name_from_email($oUser->data->display_name);
				//$aTemp->to->last_activity = bp_get_last_activity($oUser->data->ID);


				$message_id =  bp_get_message_thread_id();
				//echo "ID : ". bp_get_message_id();
				$aTemp->thread_id = $message_id;
				$aTemp->message_id = $messages_template->threads[$counter]->messages[0]->id;
				$aTemp->subject = bp_get_message_thread_subject();
                $aTemp->excerpt = bp_get_message_thread_excerpt();
				$aTemp->link = bp_get_message_thread_view_link();
				$aTemp->date = bp_get_message_thread_last_post_date_raw();
				$aTemp->unread = bp_message_thread_has_unread();
				$aTemp->thread_total_count = bp_get_message_thread_total_count($message_id);

				$aTemp->is_starred  =  bp_messages_is_message_starred( $aTemp->message_id, $_GET['userid'] );

				$oReturn->messages [$counter] = $aTemp;
				//global $wpdb,$bp_prefix;
				//$sql = "select unread_count from ".$bp_prefix."bp_messages_recipients where thread_id=\"\""
				$counter++;
            }

			/*global $wpdb,$table_prefix;
			$user_id = $_GET['userid'];
			$wpdb->query("update ".$table_prefix."bp_messages_recipients set unread_count='0' where user_id=\"$user_id\"");*/
        } else {
            return $this->error('messages');
        }
		//echo '<pre>';print_r($oReturn);echo '</pre>';exit;
		return $oReturn;
    }

	function messages_read_unread(){
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		$messageId = $_POST['threadId'];
		if(!$messageId){$oReturn->error = __('Wrong message ID.','aheadzen');}
		if(!$_POST['userId']){$oReturn->error = __('Wrong User ID.','aheadzen');}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		if($_POST['readUnread']=='read'){

			$sql = "UPDATE `wp_bp_messages_recipients` SET unread_count = 1 WHERE user_id = ".$_POST['userId']." AND thread_id in (".$messageId.")";
			$res = $conn->query($sql);
			//messages_mark_thread_read($messageId);
			$oReturn->success = __('Marked as read successfully.','aheadzen');
		}else if($_POST['readUnread']=='unread'){
			$sql = "UPDATE `wp_bp_messages_recipients` SET unread_count = 0 WHERE user_id = ".$_POST['userId']." AND thread_id in (".$messageId.")";
			$res = $conn->query($sql);
			//messages_mark_thread_unread($messageId);
			$oReturn->success = __('Marked as unread successfully.','aheadzen');
		}
		return $oReturn;
	}

	function messages_delete_messages(){
		header("Access-Control-Allow-Origin: *");
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		$thread_id = $_POST['thread_id'];
		if(!$thread_id){$oReturn->error = __('Wrong thread ID.','aheadzen');}
		if(!$_POST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen');}
		$sql = "UPDATE `wp_bp_messages_recipients` SET is_deleted = 1 WHERE user_id = ".$_POST['user_id']." AND thread_id in (".$thread_id.")";
		if($conn->query($sql)){
			$oReturn->success = __('Message Deleted Successfully.','aheadzen');
		}else{
			$oReturn->success = __('Cannot delete the message, something wrong.','aheadzen');
		}
		return $oReturn;
	}

	function messages_get_detail(){
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		$messageId = $_GET['messageId'];
		$userid = $_GET['userid'];
		if(!$messageId){$oReturn->error = __('Wrong message.','aheadzen');}
		if(!$userid){$oReturn->error = __('Wrong User.','aheadzen');}
		global $thread_template;
		if(bp_thread_has_messages(array('thread_id'=>$messageId))){
			$oReturn->message->id = $messageId;
			$oReturn->message->subject = bp_get_the_thread_subject();
			$oReturn->message->recipients_count = bp_get_thread_recipients_count();
			$oReturn->message->max_thread_recipients_to = bp_get_max_thread_recipients_to_list();
			if(bp_get_thread_recipients_count() <= 1){
				$oReturn->message->conversation = __( 'You are alone in this conversation.', 'buddypress' );
			}else if( bp_get_max_thread_recipients_to_list() <= bp_get_thread_recipients_count() ){
				$oReturn->message->conversation = sprintf( __( 'Conversation between %s recipients.', 'buddypress' ), number_format_i18n( bp_get_thread_recipients_count() ) );
			}else{
				$oReturn->message->conversation = sprintf( __( 'Conversation between %s and you.', 'buddypress' ), bp_get_thread_recipients_list() );
			}
			$oReturn->message->last_active = '';
			if(count($thread_template->thread->recipients)==2){
				foreach($thread_template->thread->recipients as $uid=>$val){
					if($userid!=$uid){
						$lastactivetime = bp_get_user_last_activity($uid);
						$oReturn->message->last_active->user_id = $uid;
						$oReturn->message->last_active->time = bp_get_last_activity($uid);
						$oReturn->message->last_active->datetime = $lastactivetime;
						$oReturn->message->last_active->seconds = strtotime($lastactivetime);
						$oReturn->message->last_active->user_name = bp_core_get_userlink($uid,true);
					}
				}
			}

			$counter = 0;
			while(bp_thread_messages()){
				bp_thread_the_message();
				preg_match_all('/(src)=("[^"]*")/i',bp_get_the_thread_message_sender_avatar_thumb(), $user_avatar_result);

				$avatar = str_replace('"','',$user_avatar_result[2][0]);
				if($avatar && !strstr($avatar,'http:')){ $avatar = 'http:'.$avatar;}
				$oReturn->message->threads[$counter]->avatar = $avatar;
				$oReturn->message->threads[$counter]->sender_id = bp_get_the_thread_message_sender_id();
				$oReturn->message->threads[$counter]->sender_name = bpaz_user_name_from_email(bp_get_the_thread_message_sender_name());
				$oReturn->message->threads[$counter]->time_since = bp_get_the_thread_message_time_since();
				//$oReturn->message->threads[$counter]->content = do_shortcode(bp_get_the_thread_message_content());
				$oReturn->message->threads[$counter]->content = bp_get_the_thread_message_content();

				$counter++;
			}
		}
		global $wpdb,$table_prefix;
		$user_id = $_GET['userid'];
		messages_mark_thread_unread($messageId);
		$wpdb->query("update ".$table_prefix."bp_messages_recipients set unread_count='0' where user_id=\"$user_id\" and thread_id=\"$messageId\"");
		//echo '<pre>';print_r($oReturn);
		return $oReturn;
	}

	function messages_set_reply(){
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		$_POST['text'] = trim($_POST['text']);
		if(!$_POST){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		if(!$_POST['text']){$oReturn->message = __('Please senter proper comments.','aheadzen'); return $oReturn;}
		if(!$_POST['userid']){$oReturn->message = __('Wrong user try.','aheadzen'); return $oReturn;}
		if(!$_POST['thread_id']){$oReturn->message = __('Wrong message trying.','aheadzen'); return $oReturn;}
		if(!$_POST['cookie']){$oReturn->message = __('Cookie parameter is required.','aheadzen'); return $oReturn;}

		//if(!aheadzen_check_valid_user($_POST['userid'],$_POST['pw'])){$oReturn->error = __('Security Error.','aheadzen'); return $oReturn;}
		$valid = wp_validate_auth_cookie($_POST['cookie'], 'logged_in');
		if($valid != $_POST['userid']){$oReturn->error = __('Authentication problem.','aheadzen'); return $oReturn;}

		$result = messages_new_message( array('thread_id'=>(int)$_POST['thread_id'], 'content' => $_POST['text'], 'sender_id' => $_POST['userid'] ) );
		if(!empty( $result )){
			$oReturn->success->msg = __('Message reply added successfully.','aheadzen');
			$oReturn->success->id = $result;
		}else{
			$oReturn->error = __('Message reply add error.','aheadzen');
		}
		//echo '<pre>';print_r($oReturn);
		return $oReturn;
	}
    /**
     * Returns an array with notifications for the current user
     * @param none there are no parameters to be used
     * @return array notifications: the notifications as a link
     */
    public function notifications_get_notifications() {
       header("Access-Control-Allow-Origin: *");
		$this->init('notifications');
        $oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';

		if(!$_GET['userid']){$oReturn->message = __('Not the post method.','aheadzen'); return $oReturn;}
		$user_id = $_GET['userid'];
		global $bp,$current_user,$table_prefix, $wpdb;
		wp_set_current_user($user_id);
		do_action('bp_init');

		$page = $_GET['page'];
		$per_page = $_GET['per_page'];
		$group_per_page = $_GET['group_per_page'];
		if(!$page){$page=1;}
		if(!$per_page){$per_page=20;}
		if(!$group_per_page){$group_per_page=20;}
		$arg = array(
			'user_id' => $user_id,
			'is_new' => 'both',
			'per_page' => $per_page,
			'page' => $page,
			'order_by' => 'date_notified',
			'sort_order' => 'DESC'
		);
		$aNotifications = BP_Notifications_Notification::get($arg);
		if($page==1){
			$aNotificationsCount = count($aNotifications);
			$memberGroupSql = "select group_id from ".$table_prefix."bp_groups_members where user_id='".$user_id."'";
			$memberGroups = $wpdb->get_col($memberGroupSql);
			if($memberGroups){
				$memberGroupsStr = implode(',',$memberGroups);
				$now_date = bp_core_current_time();
				/*$activitySql = "select * from ".$table_prefix."bp_activity where component='groups' and type in ('joined_group','activity_update','new_forum_topic') and is_spam=0 and hide_sitewide=0 and item_id in ($memberGroupsStr) and TIMESTAMPDIFF(HOUR,date_recorded,'".$now_date."')<2400 order by date_recorded desc  limit $group_per_page";*/

				$activitySql = "select * from ".$table_prefix."bp_activity where component='groups' and type in ('new_forum_topic') and is_spam=0 and hide_sitewide=0 and item_id in ($memberGroupsStr) and TIMESTAMPDIFF(HOUR,date_recorded,'".$now_date."')<2400 order by date_recorded desc  limit $group_per_page";
				$activityRes = $wpdb->get_results($activitySql);
				if($activityRes){
					foreach($activityRes as $activityResObj){
						$notificationObj = NULL;
						$notificationObj->id = $activityResObj->id;
						$notificationObj->user_id = $activityResObj->user_id;
						$notificationObj->item_id = $activityResObj->item_id;
						$notificationObj->secondary_item_id = $activityResObj->secondary_item_id;
						$notificationObj->component_name = 'customgroupnotification';
						$notificationObj->component_action = $activityResObj->type;
						$notificationObj->date_notified = $activityResObj->date_recorded;
						$notificationObj->is_new = 0;
						$aNotifications[] = $notificationObj;
						$aNotificationsCount++;
					}
				}
			}
		}
		usort($aNotifications, function($a, $b)
		{
			return strcmp($b->date_notified,$a->date_notified);
		});
		$counter = 0;
		$isNewCounter = 0;
		$userDataArr = array();
		foreach ($aNotifications as $sNotificationMessage) {
			if($sNotificationMessage->component_name == 'customgroupnotification'){
				$content = $sNotificationMessage->component_action;
			}elseif($sNotificationMessage->content){
				$content = $sNotificationMessage->content;
			}else{
				$notification = $sNotificationMessage;
				// Callback function exists
				if ( isset( $bp->{ $notification->component_name }->notification_callback ) && is_callable( $bp->{ $notification->component_name }->notification_callback ) ) {
					$content = call_user_func( $bp->{ $notification->component_name }->notification_callback, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 );
				// @deprecated format_notification_function - 1.5
				} elseif ( isset( $bp->{ $notification->component_name }->format_notification_function ) && function_exists( $bp->{ $notification->component_name }->format_notification_function ) ) {
					$content = call_user_func( $bp->{ $notification->component_name }->format_notification_function, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 );
				// Allow non BuddyPress components to hook in
				} else {
					$content = apply_filters_ref_array( 'bp_notifications_get_notifications_for_user', array( $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 ) );
				}
			}

			if($content){
				$oReturn->notifications[$counter]->id = $sNotificationMessage->id;
				$oReturn->notifications[$counter]->item_id = $sNotificationMessage->item_id;
				$oReturn->notifications[$counter]->secondary_item_id = $sNotificationMessage->secondary_item_id;
				$oReturn->notifications[$counter]->content = $content;
				$oReturn->notifications[$counter]->component_name = $sNotificationMessage->component_name;
				$oReturn->notifications[$counter]->component_action = $sNotificationMessage->component_action;
				$oReturn->notifications[$counter]->date_notified = $sNotificationMessage->date_notified;
				$oReturn->notifications[$counter]->is_new = $sNotificationMessage->is_new;
				if($sNotificationMessage->is_new){$isNewCounter++;}

				$userid = 0;
				$avatar_thumb_default = 'img/thumb_default.png';
				$activity_thumb = 'img/activity.png';
				$messages_thumb = 'img/messages.png';
				$vote_thumb = 'img/vote.png';
				$friend_thumb = 'img/friend.png';

				$avatar_thumb = '';
				if($notificationObj->component_name == 'customgroupnotification'){
					$userid = $sNotificationMessage->user_id;
				}elseif($sNotificationMessage->component_name=='follow' || $sNotificationMessage->component_name=='friends'){
					$userid = intval($sNotificationMessage->item_id);
					$avatar_thumb = $friend_thumb;
				}elseif($sNotificationMessage->component_name=='votes'){
					$component_action = $sNotificationMessage->component_action;
					$component_action_arr = explode('-+',$component_action);
					if(count($component_action_arr)<=1){
						$component_action_arr = explode('_',$component_action);
					}
					$type = $component_action_arr[0];
					$userid = intval($component_action_arr[1]);
					$avatar_thumb = $vote_thumb;
				}elseif($sNotificationMessage->component_name=='activity'){
					$oReturn->notifications[$counter]->user->avatar_thumb = $activity_thumb;
				}elseif($sNotificationMessage->component_name=='messages'){
					$oReturn->notifications[$counter]->user->avatar_thumb = $messages_thumb;
				}

				if($userid && $userid>0){
					if($userDataArr && $userDataArr[$userid]){
						$user = $userDataArr[$userid];
					}else{
						$user = new BP_Core_User($userid);
						$userDataArr[$userid] = $user;
					}
					if($user){
						if($user->avatar_thumb){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
							$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						}
						if(!$avatar_thumb){$avatar_thumb=$avatar_thumb_default;}
						$oReturn->notifications[$counter]->user->id = $user->id;
						$oReturn->notifications[$counter]->user->fullname = bpaz_user_name_from_email($user->fullname);
						$oReturn->notifications[$counter]->user->last_active = $user->last_active;
						$oReturn->notifications[$counter]->user->email = $user->email;
						$oReturn->notifications[$counter]->user->avatar_thumb = $avatar_thumb;
					}
				}
				$counter++;
			}
        }
		$oReturn->count = $counter;
		$oReturn->newCounter = $isNewCounter;

		if($counter){
			global $wpdb,$table_prefix;
			$is_new=0;
			$wpdb->query("update ".$table_prefix."bp_notifications set is_new=\"$is_new\" where user_id=\"$user_id\"");
			if (empty($aNotifications)) {
				return $this->error('notifications');
			}
		}

		//echo '<pre>';print_r($oReturn);
		return $oReturn;
    }

    /**
     * Returns an array with friends for the given user
     * @param String username: the username you want information from (required)
     * @return array friends: array with the friends the user got
     */
    public function friends_get_friends() {
        $this->init('friends');
        $oReturn = new stdClass();
		if($_GET['userid']){
			$oUser = get_user_by('id',$_GET['userid']);
		}else{
			if ($this->username === false || !username_exists($this->username)) {
				return $this->error('friends', 0);
			}
			$oUser = get_user_by('login', $this->username);
		}



        $sFriends = bp_get_friend_ids($oUser->data->ID);
        $aFriends = explode(",", $sFriends);
        if ($aFriends[0] == "")
            return $this->error('friends', 1);
        foreach ($aFriends as $sFriendID) {
            $oUser = get_user_by('id', $sFriendID);
            $oReturn->friends [(int) $sFriendID]->username = $oUser->data->user_login;
            $oReturn->friends [(int) $sFriendID]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
            $oReturn->friends [(int) $sFriendID]->mail = $oUser->data->user_email;
			$oReturn->friends [(int) $sFriendID]->mail = $oUser->data->user_email;
			$avatar = bp_core_fetch_avatar( array(
				'object'  => 'user',
				'item_id' => $sFriendID,
				'html'    => false,
				'type'    => 'full',
			) );
			$oReturn->friends [(int) $sFriendID]->avatar = $avatar;
        }
        $oReturn->count = count($aFriends);
        return $oReturn;
    }

    /**
     * Returns an array with friendship requests for the given user
     * @params String username: the username you want information from (required)
     * @return array friends: an array containing friends with some mor info
     */
    public function friends_get_friendship_request() {
        $this->init('friends');
        $oReturn = new stdClass();

        if ($this->username === false || !username_exists($this->username)) {
            return $this->error('friends', 0);
        }
        $oUser = get_user_by('login', $this->username);
		$ownUser =  $oUser;
		//print_r($oUser);die;
        //if (!is_user_logged_in() || get_current_user_id() != $oUser->data->ID)
        //    return $this->error('base', 0);

        $sFriends = bp_get_friendship_requests($oUser->data->ID);

        $aFriends = explode(",", $sFriends);
		if ($aFriends[0] == "0"){
			$oReturn->count = 0;
			$oReturn->msg = "No friendship requests found.";
			return $oReturn;
		}
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		//print_r($aFriends);
		foreach ($aFriends as $sFriendID) {
            $oUser = get_user_by('id', $sFriendID);

			$sql = "SELECT id FROM `wp_bp_friends` WHERE
					(`initiator_user_id` = ".$ownUser->data->ID." AND `friend_user_id` = ".$sFriendID."
					OR
					`initiator_user_id` = ".$sFriendID." AND `friend_user_id` = ".$ownUser->data->ID.")";
			$res = $conn->query($sql);
			$row3 = $res->fetch_assoc();
			$oReturn->friends [(int) $sFriendID]->ID =$sFriendID;
			$oReturn->friends [(int) $sFriendID]->username = $oUser->data->user_login;
            $oReturn->friends [(int) $sFriendID]->username = $oUser->data->user_login;
            $oReturn->friends [(int) $sFriendID]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
            $oReturn->friends [(int) $sFriendID]->mail = $oUser->data->user_email;
			$avatar = bp_core_fetch_avatar( array(
				'object'  => 'user',
				'item_id' => $sFriendID,
				'html'    => false,
				'type'    => 'full',
			) );
			$oReturn->friends [(int) $sFriendID]->avatar = $avatar;
        }
        $oReturn->count = count($oReturn->friends);
        return $oReturn;
    }

    /**
     * Returns a string with the status of friendship of the two users
     * @param String username: the username you want information from (required)
     * @param String friendname: the name of the possible friend (required)
     * @return string friendshipstatus: 'is_friend', 'not_friends' or 'pending'
     */
    public function friends_get_friendship_status() {
        $this->init('friends');
        $oReturn = new stdClass();

        if ($this->username === false || !username_exists($this->username)) {
            return $this->error('friends', 0);
        }

        if ($this->friendname === false || !username_exists($this->friendname)) {
            return $this->error('friends', 3);
        }

        $oUser = get_user_by('login', $this->username);
        $oUserFriend = get_user_by('login', $this->friendname);

        $oReturn->friendshipstatus = friends_check_friendship_status($oUser->data->ID, $oUserFriend->data->ID);
        return $oReturn;
    }

	function groups_get_groupdetail()
	{
		$this->init('forums');
		$oReturn = new stdClass();

		global $wpdb,$table_prefix;
		if($_GET['groupId']){
			$group_id = $_GET['groupId'];
		}elseif($_GET['groupSlug']){
			$groupSlug = $_GET['groupSlug'];
			$group = $wpdb->get_row("select id from ".$table_prefix."bp_groups where slug=\"$groupSlug\"");
			$group_id = $group->id;
		}

		if(!$group_id){ $oReturn->error = __('Wrong group id.','aheadzen'); return $oReturn;}
		$aGroup = groups_get_group( array( 'group_id' => $group_id ) );
		if($aGroup){
			$oReturn->groupfields->id = $aGroup->id;
			$oReturn->groupfields->name = stripcslashes($aGroup->name);
            $oReturn->groupfields->description = stripcslashes($aGroup->description);
            $oReturn->groupfields->status = $aGroup->status;

			$oUser = get_user_by('id', $aGroup->creator_id);
			$useravatar_url = bp_core_fetch_avatar(array('object'=>'user','item_id'=>$aGroup->creator_id, 'html'=>false, 'type'=>'full'));
            if($useravatar_url && !strstr($useravatar_url,'http:')){ $useravatar_url = 'http:'.$useravatar_url;}
			$oReturn->groupfields->creator->userid = $aGroup->creator_id;
			$oReturn->groupfields->creator->username = $oUser->data->user_login;
            $oReturn->groupfields->creator->mail = $oUser->data->user_email;
            $oReturn->groupfields->creator->display_name = bpaz_user_name_from_email($oUser->data->display_name);
			$oReturn->groupfields->creator->avatar = $useravatar_url;
            $oReturn->groupfields->slug = $aGroup->slug;
            $oReturn->groupfields->is_forum_enabled = $aGroup->enable_forum == "1" ? true : false;
            $oReturn->groupfields->date_created = $aGroup->date_created;
			$total_member_count = groups_get_groupmeta($aGroup->id,'total_member_count');
            $oReturn->groupfields->count_member = $total_member_count;

			$avatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$aGroup->id, 'html'=>false, 'type'=>'full'));
			if($avatar_url && !strstr($avatar_url,'http:')){ $avatar_url = 'http:'.$avatar_url;}
			$oReturn->groupfields->avatar = $avatar_url;

			if($iForumId = groups_get_groupmeta($aGroup->id, 'forum_id')){
				if(is_array($iForumId)){
					$iForumId = $iForumId[0];
				}
				if($iForumId){
					if(function_exists('bbp_get_forum')){
						$oForum = bbp_get_forum((int) $iForumId);
						if($oForum){
							$oReturn->groupfields->forum->id = $oForum->ID;
							$oReturn->groupfields->forum->name = $oForum->post_title;
							$oReturn->groupfields->forum->slug = $oForum->post_name;
							$oReturn->groupfields->forum->description = $oForum->post_content;
							$oReturn->groupfields->forum->topics_count = (int) bbp_get_forum_topic_count($iForumId,true,true );
							$oReturn->groupfields->forum->post_count = (int) bbp_get_forum_reply_count($iForumId,true,true);
						}
					}
				}
			}

			$isGroupAdmin = $isMember = $isBanned = 0;
			if($_GET['user_id']){
				if($aGroup->creator_id==$_GET['user_id']){
					$isGroupAdmin = 1;
				}
				$isMember = groups_is_user_member($_GET['user_id'],$aGroup->id);
				$isBanned = groups_is_user_banned($_GET['user_id'],$aGroup->id);
			}
			$oReturn->groupfields->is_admin = $isGroupAdmin;
			$oReturn->groupfields->is_member = $isMember;
			$oReturn->groupfields->is_banned = $isBanned;
		}

		return $oReturn;
	}

	public function groups_get_nameonly() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		$user_id = $_GET['userid'];
		if(!$user_id){ $oReturn->error = __('Wrong user id.','aheadzen'); return $oReturn;}
		$arg = array('user_id'=>$user_id,'orderby'=>'name','order'=>'ASC');
		$aGroups = groups_get_groups($arg);
		$counter=0;
		if($aGroups){
			foreach($aGroups['groups'] as $grpObj){
				$oReturn->group[$counter]->id = $grpObj->id;
				$oReturn->group[$counter]->name = $grpObj->name;
				$counter++;
			}
		}else{
			$oReturn->error = __('No data available.','aheadzen');
		}
		return $oReturn;
	}

	public function groups_join_unjoin_group() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		$user_id = $_GET['userid'];
		$groupid = $_GET['groupid'];
		$actionType = $_GET['actionType'];
		if(!$user_id){ $oReturn->error = __('Wrong User id.','aheadzen'); return $oReturn;}
		if(!$groupid){ $oReturn->error = __('Wrong Group id.','aheadzen'); return $oReturn;}

		if($actionType=='leave_group'){
			$member = new BP_Groups_Member( $user_id, $groupid );
			do_action( 'groups_remove_member', $groupid, $user_id );
			if ($member->remove()) {
				$oReturn->success->msg = __('Group Left Successfully.','aheadzen');
				$oReturn->success->group_id = $groupid;
				$oReturn->success->user_id = $user_id;
			} else {
				$oReturn->error = __('Group Unjoin Error.','aheadzen');
			}
		}else{
			if ( ! groups_join_group( $groupid, $user_id ) ) {
				$oReturn->error = __('Group Join Error.','aheadzen');
			} else {
				$oReturn->success->msg = __('Group Join Successfully.','aheadzen');
				$oReturn->success->group_id = $groupid;
				$oReturn->success->user_id = $user_id;
			}
		}

		return $oReturn;
	}

	public function user_get_groups() {
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		$user_id = $_GET['userid'];
		if(!$user_id){ $oReturn->error = __('Wrong user id.','aheadzen'); return $oReturn;}
		global $wpdb, $table_prefix;
		$res = $wpdb->get_results("select * from ".$table_prefix."bp_groups_members where user_id=\"$user_id\" and is_confirmed=1 order by group_id asc");
		$counter=0;
		if($res){
			foreach($res as $resObj){
				$oReturn->memberGroups[$counter]->id = $resObj->group_id;
				$oReturn->memberGroups->id = $aGroup->id;
			$oReturn->memberGroups->name = stripcslashes($aGroup->name);
            $oReturn->memberGroups->description = stripcslashes($aGroup->description);
            $oReturn->memberGroups->status = $aGroup->status;

			$oUser = get_user_by('id', $aGroup->creator_id);
			$useravatar_url = bp_core_fetch_avatar(array('object'=>'user','item_id'=>$aGroup->creator_id, 'html'=>false, 'type'=>'full'));
            if($useravatar_url && !strstr($useravatar_url,'http:')){ $useravatar_url = 'http:'.$useravatar_url;}
			$oReturn->memberGroups->creator->userid = $aGroup->creator_id;
			$oReturn->memberGroups->creator->username = $oUser->data->user_login;
            $oReturn->memberGroups->creator->mail = $oUser->data->user_email;
            $oReturn->memberGroups->creator->display_name = bpaz_user_name_from_email($oUser->data->display_name);
			$oReturn->memberGroups->creator->avatar = $useravatar_url;
            $oReturn->memberGroups->slug = $aGroup->slug;
            $oReturn->memberGroups->is_forum_enabled = $aGroup->enable_forum == "1" ? true : false;
            $oReturn->memberGroups->date_created = $aGroup->date_created;
			$total_member_count = groups_get_groupmeta($aGroup->id,'total_member_count');
            $oReturn->memberGroups->count_member = $total_member_count;

			$avatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$aGroup->id, 'html'=>false, 'type'=>'full'));
			if($avatar_url && !strstr($avatar_url,'http:')){ $avatar_url = 'http:'.$avatar_url;}
			$oReturn->groupfields->avatar = $avatar_url;

			if($iForumId = groups_get_groupmeta($aGroup->id, 'forum_id')){
				if(is_array($iForumId)){
					$iForumId = $iForumId[0];
				}
				if($iForumId){
					if(function_exists('bbp_get_forum')){
						$oForum = bbp_get_forum((int) $iForumId);
						if($oForum){
							$oReturn->memberGroups->forum->id = $oForum->ID;
							$oReturn->memberGroups->forum->name = $oForum->post_title;
							$oReturn->memberGroups->forum->slug = $oForum->post_name;
							$oReturn->memberGroups->forum->description = $oForum->post_content;
							$oReturn->memberGroups->forum->topics_count = (int) bbp_get_forum_topic_count($iForumId,true,true );
							$oReturn->memberGroups->forum->post_count = (int) bbp_get_forum_reply_count($iForumId,true,true);
						}
					}
				}
			}

			$isGroupAdmin = $isMember = $isBanned = 0;
			if($_GET['user_id']){
				if($aGroup->creator_id==$_GET['user_id']){
					$isGroupAdmin = 1;
				}
				$isMember = groups_is_user_member($_GET['user_id'],$aGroup->id);
				$isBanned = groups_is_user_banned($_GET['user_id'],$aGroup->id);
			}
			$oReturn->memberGroups->is_admin = $isGroupAdmin;
			$oReturn->memberGroups->is_member = $isMember;
			$oReturn->memberGroups->is_banned = $isBanned;

				$oReturn->memberGroups[$counter]->date_modified = $resObj->date_modified;
				$counter++;
			}
		}else{
			$oReturn->error = __('No data available.','aheadzen');
		}
		return $oReturn;
	}

	/**
     * Returns an array with groups matching to the given parameters
     * @param String username: the username you want information from (default => all groups)
     * @param Boolean show_hidden: Show hidden groups to non-admins (default: false)
     * @param String type: active, newest, alphabetical, random, popular, most-forum-topics or most-forum-posts (default active)
     * @param int page: The page to return if limiting per page (default 1)
     * @param int per_page: The number of results to return per page (default 20)
     * @return array groups: array with meta infos
     */
	function get_members_joined_groups($joindedMems)
	{
		if(!$_REQUEST['user_id']){ $oReturn->error = __('Wrong user id.','aheadzen'); return $oReturn;}
		$groups = groups_get_groups($joindedMems);
		$i=0;$arr=array();
		foreach($groups['groups'] as $row)
		{

			$useravatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$row->id, 'html'=>false, 'type'=>'full'));
			$arr[$i] = $row;
			$arr[$i]->avatar = $useravatar_url;
			$i++;

		}

		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$arr_comments = array();
		$finalResult = array();
		$i=0;
		foreach($arr as $row1)
		{


			$arr_comments[$i] = $row1;
			$sql = "SELECT id FROM `wp_bp_team_follow` WHERE author_id = ".$_REQUEST['user_id']." AND group_id = ".$row1->id."";
			$res = $conn->query($sql);
			if ($res->num_rows > 0) {
				$row3 = $res->fetch_assoc();

				if(!empty($row3))
				{

					$arr_comments[$i]->is_follow = true;
				}
				else
				{
					$arr_comments[$i]->is_follow = false;
				}
			}else{
				$arr_comments[$i]->is_follow = false;
			}
			$finalResult[] = $arr_comments[$i];
			$i++;
		}


		$oReturn = new stdClass();
		$oReturn->success = "ok";
		$oReturn->msg = "success";
		$oReturn->data = (array)$finalResult;

		return $oReturn;
	}

	public function groups_get_groups() {
        $this->init('forums');
		$oReturn = new stdClass();
		$aParams = array();
        if ($this->username !== false || username_exists($this->username)) {
            $oUser = get_user_by('login', $this->username);
            $aParams ['user_id'] = $oUser->data->ID;
        }


		$joinedGroups = array();
		$orderbyField = 'last_activity';
		$orderby = 'ASC';
        $aParams ['show_hidden'] = $this->show_hidden;
        $aParams ['type'] = $this->type;
        $aParams ['page'] = $this->page;
        $aParams ['per_page'] = $this->per_page;
		$aParams ['order'] = $orderby;
		$aParams ['orderby'] = $orderbyField;
		if($_GET['keyword']){
			$keyword = trim($_GET['keyword']);
			global $wpdb,$table_prefix;
			$groupIDs = $wpdb->get_col("select id from ".$table_prefix."bp_groups where name like \"$keyword%\"");
			$aParams['include'] = 'abc';
			if($groupIDs){
				$aParams['include'] = $groupIDs;
			}
		}elseif($_GET['currentUser']){
			global $table_prefix, $wpdb;
			$memberGroupSql = "select group_id,is_admin from ".$table_prefix."bp_groups_members where user_id='".$_GET['currentUser']."'";
			$memberGroups = $wpdb->get_col($memberGroupSql);

			if($memberGroups){
				$joindedMems = array();
				$joindedMems['show_hidden'] = $this->show_hidden;
				$joindedMems['type'] = $this->type;
				$joindedMems['page'] = $this->page;
				$joindedMems['per_page'] = $this->per_page;
				$joindedMems['order'] = $orderby;
				$joindedMems['orderby'] = $orderbyField;
				$joindedMems['include'] = $memberGroups;
				$joinedGroups = $this->get_members_joined_groups($joindedMems);
				$aParams['exclude'] = $memberGroups;
			}
		}
		$aGroups = array();
		$aGroups = groups_get_groups($aParams);

		if($joinedGroups && $joinedGroups['groups'] && $aGroups && $aGroups['groups'] && $aParams['page']==1){
			$aGroups['groups'] = array_merge($joinedGroups['groups'],$aGroups['groups']);
		}
		if ($aGroups['total'] == "0")
            return $this->error('groups', 0);

		$counter = 0;
		foreach ($aGroups['groups'] as $aGroup) { echo $aGroup->id;
			$oReturn->groups[$counter]->id = $aGroup->id;
			$oReturn->groups[$counter]->name = $aGroup->name;
            $oReturn->groups[$counter]->description = stripcslashes($aGroup->description);
            $oReturn->groups[$counter]->status = $aGroup->status;
            if ($aGroup->status == "private" && !is_user_logged_in() && !$aGroup->is_member === true)
                continue;
            $oUser = get_user_by('id', $aGroup->creator_id);
			$useravatar_url = bp_core_fetch_avatar(array('object'=>'user','item_id'=>$aGroup->creator_id, 'html'=>false, 'type'=>'full'));
			if($useravatar_url && !strstr($useravatar_url,'http:')){ $useravatar_url = 'http:'.$useravatar_url;}

			$oReturn->groups[$counter]->creator->userid = $aGroup->creator_id;
			$oReturn->groups[$counter]->creator->username = $oUser->data->user_login;
            $oReturn->groups[$counter]->creator->mail = $oUser->data->user_email;
            $oReturn->groups[$counter]->creator->display_name = bpaz_user_name_from_email($oUser->data->display_name);
			$oReturn->groups[$counter]->creator->avatar = $useravatar_url;
            $oReturn->groups[$counter]->slug = $aGroup->slug;
            $oReturn->groups[$counter]->is_forum_enabled = $aGroup->enable_forum == "1" ? true : false;
            $oReturn->groups[$counter]->date_created = $aGroup->date_created;
            $oReturn->groups[$counter]->count_member = $aGroup->total_member_count;
			$avatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$aGroup->id, 'html'=>false, 'type'=>'full'));
			$oReturn->groups[$counter]->avatar = $avatar_url;
			if($avatar_url && !strstr($avatar_url,'http:')){ $avatar_url = 'http:'.$avatar_url;}

			$iForumId = groups_get_groupmeta($aGroup->id, 'forum_id');
			if(is_array($iForumId)){
				$iForumId = $iForumId[0];
			}
			if($iForumId){
				if(function_exists('bbp_get_forum')){
					$oForum = bbp_get_forum((int) $iForumId);
					if($oForum){
						$oReturn->groups[$counter]->forum->id = $oForum->ID;
						$oReturn->groups[$counter]->forum->name = $oForum->post_title;
						$oReturn->groups[$counter]->forum->slug = $oForum->post_name;
						$oReturn->groups[$counter]->forum->description = $oForum->post_content;
						$oReturn->groups[$counter]->forum->topics_count = (int) bbp_get_forum_topic_count($iForumId,true,true);
						$oReturn->groups[$counter]->forum->post_count = (int) bbp_get_forum_reply_count($iForumId,true,true);
					}
				}
			}

			$isGroupAdmin = $isMember = $isBanned = 0;
			if($_GET['currentUser']){
				if($aGroup->creator_id==$_GET['currentUser']){
					$isGroupAdmin = 1;
				}
				$isMember = groups_is_user_member($_GET['currentUser'],$aGroup->id);
				$isBanned = groups_is_user_banned($_GET['currentUser'],$aGroup->id);
			}
			$groupavatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$aGroup->id, 'html'=>false, 'type'=>'full'));

			$oReturn->groups[$counter]->is_admin = $isGroupAdmin;
			$oReturn->groups[$counter]->is_member = $isMember;
			$oReturn->groups[$counter]->is_banned = $isBanned;
			$oReturn->groups[$counter]->avatar = $groupavatar_url;


			$counter++;
        }

		$oReturn->count = count($aGroups['groups']);
		return $oReturn;
    }


    /**
     * Returns a boolean depending on an existing invite
     * @param String username: the username you want information from (required)
     * @param int groupid: the groupid you are searching for (if not set, groupslug is searched; groupid or groupslug required)
     * @param String groupslug: the slug to search for (just used if groupid is not set; groupid or groupslug required)
     * @param String type: sent to check for sent invites, all to check for all
     * @return boolean is_invited: true if invited, else false
     */
    public function groups_check_user_has_invite_to_group() {
        $this->init('groups');

        $oReturn = new stdClass();

        if ($this->username === false || !username_exists($this->username)) {
            return $this->error('groups', 1);
        }
        $oUser = get_user_by('login', $this->username);

        $mGroupName = $this->get_group_from_params();

        if ($mGroupName !== true)
            return $this->error('groups', $mGroupName);

        if ($this->type === false || $this->type != "sent" || $this->type != "all")
            $this->type = 'sent';

        $oReturn->is_invited = groups_check_user_has_invite((int) $oUser->data->ID, $this->groupid, $this->type);
        $oReturn->is_invited = is_null($oReturn->is_invited) ? false : true;

        return $oReturn;
    }

    /**
     * Returns a boolean depending on an existing memebership request
     * @param String username: the username you want information from (required)
     * @param int groupid: the groupid you are searching for (if not set, groupslug is searched; groupid or groupslug required)
     * @param String groupslug: the slug to search for (just used if groupid is not set; groupid or groupslug required)
     * @return boolean membership_requested: true if requested, else false
     */
    public function groups_check_user_membership_request_to_group() {
        $this->init('groups');

        $oReturn = new stdClass();

        if ($this->username === false || !username_exists($this->username)) {
            return $this->error('groups', 1);
        }
        $oUser = get_user_by('login', $this->username);

        $mGroupName = $this->get_group_from_params();

        if ($mGroupName !== true)
            return $this->error('groups', $mGroupName);

        $oReturn->membership_requested = groups_check_for_membership_request((int) $oUser->data->ID, $this->groupid);
        $oReturn->membership_requested = is_null($oReturn->membership_requested) ? false : true;

        return $oReturn;
    }

    /**
     * Returns an array containing all admins for the given group
     * @param int groupid: the groupid you are searching for (if not set, groupslug is searched; groupid or groupslug required)
     * @param String groupslug: the slug to search for (just used if groupid is not set; groupid or groupslug required)
     * @return array group_admins: array containing the admins
     */
    public function groups_get_group_admins() {
        $this->init('groups');

        $oReturn = new stdClass();

        $mGroupExists = $this->get_group_from_params();

        if ($mGroupExists === false)
            return $this->error('base', 0);
        else if (is_int($mGroupExists) && $mGroupExists !== true)
            return $this->error('groups', $mGroupExists);

        $aGroupAdmins = groups_get_group_admins($this->groupid);
        foreach ($aGroupAdmins as $oGroupAdmin) {
            $oUser = get_user_by('id', $oGroupAdmin->user_id);
            $oReturn->group_admins[(int) $oGroupAdmin->user_id]->username = $oUser->data->user_login;
            $oReturn->group_admins[(int) $oGroupAdmin->user_id]->mail = $oUser->data->user_email;
            $oReturn->group_admins[(int) $oGroupAdmin->user_id]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
			$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$oGroupAdmin->user_id, 'html'=>false, 'type'=>'full'));
			$oReturn->group_admins[(int) $oGroupAdmin->user_id]->thumb = $useravatar_url2 ;
        }
        $oReturn->count = count($aGroupAdmins);
        return $oReturn;
    }

    /**
     * Returns an array containing all mods for the given group
     * @params int groupid: the groupid you are searching for (if not set, groupslug is searched; groupid or groupslug required)
     * @params String groupslug: the slug to search for (just used if groupid is not set; groupid or groupslug required)
     * @return array group_mods: array containing the mods
     */
    public function groups_get_group_mods() {
        $this->init('groups');

        $oReturn = new stdClass();

        $mGroupExists = $this->get_group_from_params();

        if ($mGroupExists === false)
            return $this->error('base', 0);
        else if (is_int($mGroupExists) && $mGroupExists !== true)
            return $this->error('groups', $mGroupExists);

        $oReturn->group_mods = groups_get_group_mods($this->groupid);
        $aGroupMods = groups_get_group_mods($this->groupid);
        foreach ($aGroupMods as $aGroupMod) {
            $oUser = get_user_by('id', $aGroupMod->user_id);
            $oReturn->group_mods[(int) $aGroupMod->user_id]->username = $oUser->data->user_login;
            $oReturn->group_mods[(int) $aGroupMod->user_id]->mail = $oUser->data->user_email;
            $oReturn->group_mods[(int) $aGroupMod->user_id]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
        }
        return $oReturn;
    }

    /**
     * Returns an array containing all members for the given group
     * @params int groupid: the groupid you are searching for (if not set, groupslug is searched; groupid or groupslug required)
     * @params String groupslug: the slug to search for (just used if groupid is not set; groupid or groupslug required)
     * @params int limit: maximum members displayed
     * @return array group_members: group members with some more info
     */
    public function groups_get_group_members() {
        $this->init('groups');

        $oReturn = new stdClass();

        /*$mGroupExists = $this->get_group_from_params();
		print "<pre>";
		print_r($mGroupExists);
		die;
		if ($mGroupExists === false)
            return $this->error('base', 0);
        else if (is_int($mGroupExists) && $mGroupExists !== true)
            return $this->error('groups', $mGroupExists);
		*/
		$page = $_GET['page'];
		if(!$page){$page=1;}
		$per_page = $_GET['per_page'];
		if(!$per_page){$per_page=20;}
		$arg = array();
		$arg['group_id'] = $_REQUEST['groupid'];
		$arg['per_page'] = $per_page;
		$arg['page'] = $page;
		$aMembers = groups_get_group_members($arg);
		$counter=0;
		$group_member_args = array(
					'group_id'	=>	$_REQUEST['groupid'],
					'page'		=>	$page,
					'per_page'	=>	$per_page,
				);
		// Perform the group member query (extends BP_User_Query)
		$members = new BP_Group_Member_Query( array(
			'group_id'       => $_REQUEST['groupid'],
			'per_page'       => $per_page,
			'page'           => $page,
			'group_role'     => $r['group_role'],
			'exclude'        => $r['exclude'],
			'search_terms'   => $r['search_terms'],
			'type'           => $r['type'],
		) );

		// Structure the return value as expected by the template functions
		$retval = array(
			'members' => array_values( $members->results ),
			'count'   => $members->total_users,
		);


		global $members_template;

			foreach($members->results as $row){

				$aMember = $row;
				$oReturn->group_members[$counter]->id = $aMember->ID;
				$oReturn->group_members[$counter]->username = $aMember->user_login;
				$oReturn->group_members[$counter]->mail = $aMember->user_email;
				$oReturn->group_members[$counter]->display_name = bpaz_user_name_from_email($aMember->display_name);
				$oReturn->group_members[$counter]->nicename = $aMember->user_nicename;
				$oReturn->group_members[$counter]->registered = $aMember->user_registered;
				$oReturn->group_members[$counter]->last_activity = $aMember->last_activity;
				$oReturn->group_members[$counter]->friend_count = $aMember->total_friend_count;


				$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				// Check connection
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				}
				$memberRecord = 0;
				$sql = "select group_id,is_mod, is_confirmed, is_banned, invite_sent  from wp_bp_groups_members where group_id = ".$_GET['groupid']." AND user_id = ".$aMember->ID."";
				$conn->query($sql);
				$resource = $conn->query($sql);
				if ($resource->num_rows > 0) {
					$memberRecord = $resource->fetch_assoc();

				}

				$oReturn->group_members[$counter]->is_banned  =$memberRecord['is_banned'];
				$user = new BP_Core_User($aMember->ID);
				if($user && $user->avatar){
					if($user->avatar_thumb){
						preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
						$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
						if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						$oReturn->group_members[$counter]->avatar = $avatar_thumb;
					}
				}
				$profile_data = $user->profile_data;
				if($profile_data){
					foreach($profile_data as $sFieldName => $val){
						if(is_array($val)){
							$oReturn->group_members[$counter]->$sFieldName = $val['field_data'];
						}
					}
				}
				if(function_exists('bp_follow_total_follow_counts')){
					$oReturn->group_members[$counter]->follow_counts  = bp_follow_total_follow_counts( array( 'user_id' => $aMember->ID ) );
				}
				$oReturn->group_members[$counter]->is_following = 0;
				if(function_exists('bp_follow_is_following') && $_GET['userid'] && bp_follow_is_following(array('leader_id'=>$aMember->ID,'follower_id'=>$_GET['userid']))){
					$oReturn->group_members[$counter]->is_following = 1;
				}
				$counter++;
			}

		$oReturn->count = $counter;
		//echo '<pre>';print_r($oReturn);
        return $oReturn;
    }

    /**
     * Returns an array containing info about the group forum
     * @param int forumid: the forumid you are searching for (if not set, forumslug is searched; forumid or forumslug required)
     * @param String forumslug: the slug to search for (just used if forumid is not set; forumid or forumslug required)
     * @return array forums: the group forum with metainfo
     */
    public function groupforum_get_forum() {
        $this->init('forums');

        $oReturn = new stdClass();

        $mForumExists = $this->groupforum_check_forum_existence();

        if ($mForumExists === false)
            return $this->error('base', 0);
        else if (is_int($mForumExists) && $mForumExists !== true)
            return $this->error('forums', $mForumExists);

		if($iForumId){
			if(function_exists('bbp_get_forum')){
				$oForum = bbp_get_forum((int) $iForumId);
				if($oForum){
					$oReturn->groups[(int) $oForum->ID]->name = $oForum->post_title;
					$oReturn->groups[(int) $oForum->ID]->slug = $oForum->post_name;
					$oReturn->groups[(int) $oForum->ID]->description = $oForum->post_content;
					$oReturn->groups[(int) $oForum->ID]->topics_count = (int) bbp_get_forum_topic_count( $iForumId ,true,true);
					$oReturn->groups[(int) $oForum->ID]->post_count = (int) bbp_get_forum_reply_count( $iForumId ,true,true);
				}
			}
		}
        return $oReturn;
    }

    /**
     * Returns an array containing info about the group forum
     * @param int groupid: the groupid you are searching for (if not set, groupslug is searched; groupid or groupslug required)
     * @param String groupslug: the slug to search for (just used if groupid is not set; groupid or groupslug required)
     * @return array forums: the group forum for the group
     */
    public function groupforum_get_forum_by_group() {
        $this->init('forums');

        $oReturn = new stdClass();

        $mGroupExists = $this->get_group_from_params();

        if ($mGroupExists === false)
            return $this->error('base', 0);
        else if (is_int($mGroupExists) && $mGroupExists !== true)
            return $this->error('forums', $mGroupExists);

        $oGroup = groups_get_group(array('group_id' => $this->groupid));
        if ($oGroup->enable_forum == "0")
            return $this->error('forums', 0);
        $iForumId = groups_get_groupmeta($oGroup->id, 'forum_id');
        if ($iForumId == "0")
            return $this->error('forums', 1);

		$oForum = bbp_get_forum((int) $iForumId);
		if($oForum){
			$oReturn->forums[(int) $oForum->ID]->name = $oForum->post_title;
			$oReturn->forums[(int) $oForum->ID]->slug = $oForum->post_name;
			$oReturn->forums[(int) $oForum->ID]->description = $oForum->post_content;
			$oReturn->forums[(int) $oForum->ID]->topics_count = (int) bbp_get_forum_topic_count( $iForumId );
			$oReturn->forums[(int) $oForum->ID]->post_count = (int) bbp_get_forum_reply_count( $iForumId );
		}

        return $oReturn;
    }

    /**
     * Returns an array containing the topics from a group's forum
     * @param int forumid: the forumid you are searching for (if not set, forumid is searched; forumid or forumslug required)
     * @param String forumslug: the forumslug to search for (just used if forumid is not set; forumid or forumslug required)
     * @param int page: the page number you want to display (default 1)
     * @param int per_page: the number of results you want per page (default 15)
     * @param String type: newest, popular, unreplied, tag (default newest)
     * @param String tagname: just used if type = tag
     * @param boolean detailed: true for detailed view (default false)
     * @return array topics: all the group forum topics found
     */
    public function groupforum_get_forum_topics() {
        $this->init('forums');

        $oReturn = new stdClass();
		$mForumExists = $this->groupforum_check_forum_existence();
		if ($mForumExists === false)
            return $this->error('base', 0);
        else if (is_int($mForumExists) && $mForumExists !== true)
            return $this->error('forums', $mForumExists);

		$aConfig = array();
        /*$aConfig['type'] = $this->type;
        $aConfig['filter'] = $this->type == 'tag' ? $this->tagname : false;
        $aConfig['forum_id'] = $this->forumid;
        $aConfig['page'] = $this->page;
        $aConfig['per_page'] = $this->per_page;*/

		$aConfig['post_type'] = bbp_get_topic_post_type();
		$aConfig['post_parent'] = $this->forumid;
		$aConfig['posts_per_page'] = $this->per_page;
		$aConfig['paged'] = $this->page;
		$aConfig['orderby'] = 'date';
		$aConfig['order'] = 'DESC';
		if ( bbp_has_topics( $aConfig ) ){
			global $post;
			while ( bbp_topics() ) {
				bbp_the_topic();
				$tid = $post->ID;
				$uid = $post->post_author;
				$oReturn->topics[(int)$tid]->title = stripcslashes($post->post_title);
				$oReturn->topics[(int)$tid]->content = stripcslashes(wp_strip_all_tags($post->post_content));
				$oReturn->topics[(int)$tid]->slug = $post->post_name;
				$oReturn->topics[(int)$tid]->guid = $post->guid;
				$oUser = get_user_by('id', $post->post_author);
				$oReturn->topics[(int)$tid]->poster->ID = $uid;
				$oReturn->topics[(int)$tid]->poster->username = $oUser->data->user_login;
				$oReturn->topics[(int)$tid]->poster->mail = $oUser->data->user_email;
				$oReturn->topics[(int)$tid]->poster->display_name = bpaz_user_name_from_email($oUser->data->display_name);
				$oReturn->topics[(int)$tid]->post_count = (int) bbp_get_topic_post_count($tid);
				$oReturn->topics[(int)$tid]->start_time = $post->post_date;
				$oReturn->topics[(int)$tid]->forum_id = (int) $post->post_parent;
				$oReturn->topics[(int)$tid]->topic_status = $post->post_status;
				$is_open = bbp_is_topic_open( $tid );
				$oReturn->topics[(int)$tid]->is_open = $is_open ? true : false;
				$is_sticky = bbp_is_topic_sticky($tid);
				$oReturn->topics[(int)$tid]->is_sticky = $is_sticky ? true : false;

				$user = new BP_Core_User($uid);
				if($user && $user->avatar){
					if($user->avatar_thumb){
						preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
						$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
						if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						$oReturn->topics[(int)$tid]->poster->avatar = $avatar_thumb;
					}
				}
				if ($this->detailed === true) {
					$oUserTopic = $oUser;
					$last_reply_id = bbp_get_topic_last_reply_id($tid);
					if($last_reply_id){
						$reply = bbp_get_reply($last_reply_id);
					}
					$oUser = get_user_by('id', $reply->post_author);
					if(!$oUser){
						$replyUser = $uid;
						$oUser = $oUserTopic;
					}

					$oReturn->topics[(int)$tid]->last_poster->ID = $reply->post_author;
					$oReturn->topics[(int)$tid]->last_poster->username = $oUser->data->user_login;
					$oReturn->topics[(int)$tid]->last_poster->mail = $oUser->data->user_email;
					$oReturn->topics[(int)$tid]->last_poster->display_name = bpaz_user_name_from_email($oUser->data->display_name);
					if($reply->post_date){
						$oReturn->topics[(int)$tid]->last_poster->post_date = $reply->post_date;
					}else{
						$oReturn->topics[(int)$tid]->last_poster->post_date = $post->post_date;
					}

					$user = new BP_Core_User($reply->post_author);
					if($user && $user->avatar){
						if($user->avatar_thumb){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
							$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
							$oReturn->topics[(int)$tid]->last_poster->avatar = $avatar_thumb;
						}
					}
				}

				$total_votes = $total_up = $total_down = 0;
				$uplink = $downlink = '#';
				$voteed_action = 'up';
				if(class_exists('VoterPluginClass'))
				{
					$arg = array(
						'item_id'=>$tid,
						'user_id'=>$post->post_author,
						'type'=>'topic',
						);

					$votes_str = VoterPluginClass::aheadzen_get_post_all_vote_details($arg);
					if($votes_str){
					$votes = json_decode($votes_str);
					$total_votes = $votes->total_votes;
					$total_up = $votes->total_up;
					$total_down = $votes->total_down;
					$uplink = $votes->post_voter_links->up;
					$downlink = $votes->post_voter_links->down;
					}
					if($_GET['userid']){
						$user_id = $post->post_author;
						$secondary_item_id = $tid;
						$type = 'topic';
						$item_id = 0;
						$component = 'forum';
						$voteed_action = $wpdb->get_var("SELECT action FROM `".$table_prefix."ask_votes` WHERE user_id=\"$user_id\" AND item_id=\"$item_id\" AND component=\"$component\" AND type=\"$type\" AND secondary_item_id=\"$secondary_item_id\"");
					}
				}
				$oReturn->topics[(int)$tid]->vote->total_votes = $total_votes;
				$oReturn->topics[(int)$tid]->vote->total_up = $total_up;
				$oReturn->topics[(int)$tid]->vote->total_down = $total_down;
				$oReturn->topics[(int)$tid]->vote->action = $voteed_action;
			}
		}
		$oReturn->count = count($aTopics);

		return $oReturn;
    }

    /**
     * Returns an array containing the posts from a group's forum
     * @param int topicid: the topicid you are searching for (if not set, topicslug is searched; topicid or topicslug required)
     * @param String topicslug: the slug to search for (just used if topicid is not set; topicid or topicslugs required)
     * @param int page: the page number you want to display (default 1)
     * @param int per_page: the number of results you want per page (default 15)
     * @param String order: desc for descending or asc for ascending (default asc)
     * @return array posts: all the group forum posts found
     */
    public function groupforum_get_topic_posts() {
        $this->init('forums');
        $oReturn = new stdClass();
		$mTopicExists = $this->groupforum_check_topic_existence();
		if ($mTopicExists === false){
			return $this->error('base', 0);
		}else if (is_int($mTopicExists) && $mTopicExists !== true){
			return $this->error('forums', $mTopicExists);
		}

		$aConfig = array();
       /*$aConfig['topic_id'] = $this->topicid;
        $aConfig['page'] = $this->page;
        $aConfig['per_page'] = $this->per_page;
        $aConfig['order'] = $this->order;*/
		if($_GET['topicid']){$this->topicid = $_GET['topicid'];}
		$response = bbp_get_topic($this->topicid);
		$oForum = bbp_get_forum($response->post_parent);

		$oReturn->topic->topicid = (int)$this->topicid;
		//$oReturn->topic->title = $response->post_title;
		//$oReturn->topic->content = $response->post_content;
		$oReturn->topic->slug = $response->post_name;
		$oReturn->topic->forum_name = $oForum->post_title;
		$oReturn->topic->forum_slug = $oForum->post_name;
		$oReturn->topic->forum_id = $oForum->ID;

		$oReturn->topic->title = stripcslashes($response->post_title);
		$oReturn->topic->content = stripcslashes($response->post_content);
		$oReturn->topic->slug = $response->post_name;
		$oUser = new BP_Core_User($response->post_author);
		$oReturn->topic->poster->ID = $oUser->id;
		$oReturn->topic->poster->username = $oUser->profile_data['user_login'];
		$oReturn->topic->poster->mail = $oUser->profile_data['user_email'];
		$oReturn->topic->poster->display_name = bpaz_user_name_from_email($oUser->profile_data['Name']['field_data']);
		//$oReturn->topic->post_count = (int) bbp_get_topic_post_count($this->topicid);
		$oReturn->topic->start_time = $response->post_date;
		if($oUser && $oUser->avatar_thumb){
			if($oUser->avatar_thumb){
				preg_match_all('/(src)=("[^"]*")/i',$oUser->avatar_thumb, $user_avatar_result);
				$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
				if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
				$oReturn->topic->poster->avatar = $avatar_thumb;
			}
		}

		$total_votes = $total_up = $total_down = 0;
		$uplink = $downlink = '#';
		$voteed_action = 'up';
		if(class_exists('VoterPluginClass'))
		{
			$arg = array(
				'item_id'=>$this->topicid,
				'user_id'=>$response->post_author,
				'type'=>'topic',
				);
			$votes_str = VoterPluginClass::aheadzen_get_post_all_vote_details($arg);
			if($votes_str){
			$votes = json_decode($votes_str);
			$total_votes = $votes->total_votes;
			$total_up = $votes->total_up;
			$total_down = $votes->total_down;
			$uplink = $votes->post_voter_links->up;
			$downlink = $votes->post_voter_links->down;
			}
			if($_GET['userid']){
				$user_id = $post->post_author;
				$secondary_item_id = $tid;
				$type = 'topic';
				$item_id = 0;
				$component = 'forum';
				$voteed_action = $wpdb->get_var("SELECT action FROM `".$table_prefix."ask_votes` WHERE user_id=\"$user_id\" AND item_id=\"$item_id\" AND component=\"$component\" AND type=\"$type\" AND secondary_item_id=\"$secondary_item_id\"");
			}
		}
		$oReturn->topic->vote->total_votes = $total_votes;
		$oReturn->topic->vote->total_up = $total_up;
		$oReturn->topic->vote->total_down = $total_down;
		$oReturn->topic->vote->action = $voteed_action;

		$oUserTopic = $oUser;
		$last_reply_id = bbp_get_topic_last_reply_id($this->topicid);
		if($last_reply_id){
			$reply = bbp_get_reply($last_reply_id);
		}
		$oUser = new BP_Core_User($reply->post_author);
		if(!$oUser){
			$replyUser = $uid;
			$oUser = $oUserTopic;
		}

		$oReturn->topic->last_poster->ID = $oUser->id;
		$oReturn->topic->last_poster->username = $oUser->profile_data['user_login'];
		$oReturn->topic->last_poster->mail = $oUser->profile_data['user_email'];
		$oReturn->topic->last_poster->display_name = bpaz_user_name_from_email($oUser->profile_data['Name']['field_data']);

		if($oUser && $oUser->avatar){
			if($oUser->avatar_thumb){
				preg_match_all('/(src)=("[^"]*")/i',$oUser->avatar_thumb, $user_avatar_result);
				$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
				if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
				$oReturn->topic->last_poster->avatar = $avatar_thumb;
			}
		}

		$orderby = 'DESC';
		if($_GET['orderby']){$orderby = $_GET['orderby'];}
		$aConfig['post_type'] = bbp_get_reply_post_type();
		$aConfig['post_parent'] = $this->topicid;
		$aConfig['posts_per_page'] = $this->per_page;
		$aConfig['paged'] = $this->page;
		$aConfig['orderby'] = 'date';
		$aConfig['order'] = $orderby;

		global $post;
		if(bbp_has_replies($aConfig)){
			while(bbp_replies()){
				bbp_the_reply();
				$oUser = new BP_Core_User($post->post_author);
				//$oUser = get_user_by('id', (int) $post->post_author);
				$oReturn->posts[(int) $post->ID]->poster->poster_id = $post->post_author;
				$oReturn->posts[(int) $post->ID]->poster->username = $oUser->profile_data['user_login'];
				$oReturn->posts[(int) $post->ID]->poster->mail = $oUser->profile_data['user_email'];
				$oReturn->posts[(int) $post->ID]->poster->display_name = bpaz_user_name_from_email($oUser->profile_data['Name']['field_data']);
				$oReturn->posts[(int) $post->ID]->post_text = stripcslashes($post->post_content);
				$oReturn->posts[(int) $post->ID]->post_time = $post->post_date;
				if($oUser && $oUser->avatar){
					if($oUser->avatar_thumb){
						preg_match_all('/(src)=("[^"]*")/i',$oUser->avatar_thumb, $user_avatar_result);
						$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
						if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						$oReturn->posts[(int) $post->ID]->poster->avatar = $avatar_thumb;
					}
				}

				$total_votes = $total_up = $total_down = 0;
				$uplink = $downlink = '#';
				$voteed_action = 'up';
				if(class_exists('VoterPluginClass'))
				{
					$arg = array(
						'item_id'=>$post->ID,
						'user_id'=>$post->post_author,
						'type'=>'topic-reply',
						);

					$votes_str = VoterPluginClass::aheadzen_get_post_all_vote_details($arg);
					if($votes_str){
					$votes = json_decode($votes_str);
					$total_votes = $votes->total_votes;
					$total_up = $votes->total_up;
					$total_down = $votes->total_down;
					$uplink = $votes->post_voter_links->up;
					$downlink = $votes->post_voter_links->down;
					}
					if($_GET['userid']){
						$user_id = $post->post_author;
						$secondary_item_id = $post->ID;
						$type = 'topic-reply';
						$item_id = 0;
						$component = 'forum';
						$voteed_action = $wpdb->get_var("SELECT action FROM `".$table_prefix."ask_votes` WHERE user_id=\"$user_id\" AND item_id=\"$item_id\" AND component=\"$component\" AND type=\"$type\" AND secondary_item_id=\"$secondary_item_id\"");
					}
				}
				$oReturn->posts[(int) $post->ID]->vote->total_votes = $total_votes;
				$oReturn->posts[(int) $post->ID]->vote->total_up = $total_up;
				$oReturn->posts[(int) $post->ID]->vote->total_down = $total_down;
				$oReturn->posts[(int) $post->ID]->vote->action = $voteed_action;

			}
		}
	    $oReturn->postcount = count($oReturn->posts);
		$oReturn->topic->post_count = $oReturn->postcount;
		return $oReturn;
    }

    /**
     * Returns an array containing info about the sitewide forum
     * @param int forumid: the forumid you are searching for (if not set, forumslug is searched; forumid or forumslug required)
     * @param String forumslug: the slug to search for (just used if forumid is not set; forumid or forumslug required)
     * @return array forums: sitewide forum with some infos
     */
    public function sitewideforum_get_forum() {
        $this->init('forums');

        $oReturn = new stdClass();

        $mForumExists = $this->sitewideforum_check_forum_existence();

        if ($mForumExists !== true)
            return $this->error('forums', $mForumExists);
        foreach ($this->forumid as $iId) {
            $oForum = bbp_get_forum((int) $iId);
            $oReturn->forums[$iId]->title = $oForum->post_title;
            $oReturn->forums[$iId]->name = $oForum->post_name;
            $oUser = get_user_by('id', $oForum->post_author);
            $oReturn->forums[$iId]->author[$oForum->post_author]->username = $oUser->data->user_login;
            $oReturn->forums[$iId]->author[$oForum->post_author]->mail = $oUser->data->user_email;
            $oReturn->forums[$iId]->author[$oForum->post_author]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
            $oReturn->forums[$iId]->date = $oForum->post_date;
            $oReturn->forums[$iId]->last_change = $oForum->post_modified;
            $oReturn->forums[$iId]->status = $oForum->post_status;
            $oReturn->forums[$iId]->name = $oForum->post_name;
            $iTopicCount = bbp_get_forum_topic_count((int) $this->forumid);
            $oReturn->forums[$iId]->topics_count = is_null($iTopicCount) ? 0 : (int) $iTopicCount;
            $iPostCount = bbp_get_forum_post_count((int) $this->forumid);
            $oReturn->forums[$iId]->post_count = is_null($iPostCount) ? 0 : (int) $iPostCount;
        }

        return $oReturn;
    }

    /**
     * Returns an array containing all sitewide forums
     * @params int parentid: all children of the given id (default 0 = all)
     * @return array forums: all sitewide forums
     */
    public function sitewideforum_get_all_forums() {
        $this->init('forums');

        $oReturn = new stdClass();
        global $wpdb;
        $sParentQuery = $this->parentid === false ? "" : " AND post_parent=" . (int) $this->parentid;
        $aForums = $wpdb->get_results($wpdb->prepare(
                        "SELECT ID, post_parent, post_author, post_title, post_date, post_modified
                 FROM   $wpdb->posts
                 WHERE  post_type='forum'" . $sParentQuery
                ));

        if (empty($aForums))
            return $this->error('forums', 9);

        foreach ($aForums as $aForum) {
            $iId = (int) $aForum->ID;
            $oUser = get_user_by('id', (int) $aForum->post_author);
            $oReturn->forums[$iId]->author[(int) $aForum->post_author]->username = $oUser->data->user_login;
            $oReturn->forums[$iId]->author[(int) $aForum->post_author]->mail = $oUser->data->user_email;
            $oReturn->forums[$iId]->author[(int) $aForum->post_author]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
            $oReturn->forums[$iId]->date = $aForum->post_date;
            $oReturn->forums[$iId]->last_changes = $aForum->post_modified;
            $oReturn->forums[$iId]->title = $aForum->post_title;
            $oReturn->forums[$iId]->parent = (int) $aForum->post_parent;
        }
        $oReturn->count = count($aForums);
        return $oReturn;
    }

    /**
     * Returns an array containing all topics of a sitewide forum
     * @param int forumid: the forumid you are searching for (if not set, forumslug is searched; forumid or forumslug required)
     * @param String forumslug: the slug to search for (just used if forumid is not set; forumid or forumslug required)
     * @param boolean display_content: set this to true if you want the content to be displayed too (default false)
     * @return array forums->topics: array of sitewide forums with the topics in it
     */
    public function sitewideforum_get_forum_topics() {
        $this->init('forums');

        $oReturn = new stdClass();

        $mForumExists = $this->sitewideforum_check_forum_existence();

        if ($mForumExists !== true)
            return $this->error('forums', $mForumExists);
        global $wpdb;
        foreach ($this->forumid as $iId) {
            $aTopics = $wpdb->get_results($wpdb->prepare(
                            "SELECT ID, post_parent, post_author, post_title, post_date, post_modified, post_content
                     FROM   $wpdb->posts
                     WHERE  post_type='topic'
                     AND post_parent='" . $iId . "'"
                    ));
            if (empty($aTopics)) {
                $oReturn->forums[(int) $iId]->topics = "";
                continue;
            }
            foreach ($aTopics as $aTopic) {
                $oUser = get_user_by('id', (int) $aTopic->post_author);
                $oReturn->forums[(int) $iId]->topics[(int) $aTopic->ID]->author[(int) $aTopic->post_author]->username = $oUser->data->user_login;
                $oReturn->forums[(int) $iId]->topics[(int) $aTopic->ID]->author[(int) $aTopic->post_author]->mail = $oUser->data->user_email;
                $oReturn->forums[(int) $iId]->topics[(int) $aTopic->ID]->author[(int) $aTopic->post_author]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
                $oReturn->forums[(int) $iId]->topics[(int) $aTopic->ID]->date = $aTopic->post_date;
                if ($this->display_content !== false)
                    $oReturn->forums[(int) $iId]->topics[(int) $aTopic->ID]->content = $aTopic->post_content;
                $oReturn->forums[(int) $iId]->topics[(int) $aTopic->ID]->last_changes = $aTopic->post_modified;
                $oReturn->forums[(int) $iId]->topics[(int) $aTopic->ID]->title = $aTopic->post_title;
            }
            $oReturn->forums[(int) $iId]->count = count($aTopics);
        }
        return $oReturn;
    }

    /**
     * Returns an array containing all replies to a topic from a sitewide forum
     * @param int topicid: the topicid you are searching for (if not set, topicslug is searched; topicid or topicsslug required)
     * @param String topicslug: the slug to search for (just used if topicid is not set; topicid or topicslug required)
     * @param boolean display_content: set this to true if you want the content to be displayed too (default false)
     * @return array topics->replies: an array containing the replies
     */
    public function sitewideforum_get_topic_replies() {
        $this->init('forums');

        $oReturn = new stdClass();

        $mForumExists = $this->sitewideforum_check_topic_existence();

        if ($mForumExists !== true)
            return $this->error('forums', $mForumExists);
        foreach ($this->topicid as $iId) {
            global $wpdb;
            $aReplies = $wpdb->get_results($wpdb->prepare(
                            "SELECT ID, post_parent, post_author, post_title, post_date, post_modified, post_content
                     FROM   $wpdb->posts
                     WHERE  post_type='reply'
                     AND post_parent='" . $iId . "'"
                    ));

            if (empty($aReplies)) {
                $oReturn->topics[$iId]->replies = "";
                $oReturn->topics[$iId]->count = 0;
                continue;
            }
            foreach ($aReplies as $oReply) {
                $oUser = get_user_by('id', (int) $oReply->post_author);
                $oReturn->topics[$iId]->replies[(int) $oReply->ID]->author[(int) $oReply->post_author]->username = $oUser->data->user_login;
                $oReturn->topics[$iId]->replies[(int) $oReply->ID]->author[(int) $oReply->post_author]->mail = $oUser->data->user_email;
                $oReturn->topics[$iId]->replies[(int) $oReply->ID]->author[(int) $oReply->post_author]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
                $oReturn->topics[$iId]->replies[(int) $oReply->ID]->date = $oReply->post_date;
                if ($this->display_content !== false)
                    $oReturn->topics[$iId]->replies[(int) $oReply->ID]->content = $oReply->post_content;
                $oReturn->topics[$iId]->replies[(int) $oReply->ID]->last_changes = $oReply->post_modified;
                $oReturn->topics[$iId]->replies[(int) $oReply->ID]->title = $oReply->post_title;
            }
            $oReturn->topics[$iId]->count = count($aReplies);
        }

        return $oReturn;
    }

    /**
     * Returns the settings for the current user
     * @params none no parameters
     * @return object settings: an object full of the settings
     */
    public function settings_get_settings() {
        $this->init('settings');
        $oReturn = new stdClass();

        if ($this->username === false || !username_exists($this->username)) {
            return $this->error('settings', 0);
        }

        $oUser = get_user_by('login', $this->username);

        //if (!is_user_logged_in() || get_current_user_id() != $oUser->data->ID)
         //    return $this->error('base', 0);

        $oReturn->user->mail = $oUser->data->user_email;

        $sNewMention = bp_get_user_meta($oUser->data->ID, 'notification_activity_new_mention', true);
        $sNewReply = bp_get_user_meta($oUser->data->ID, 'notification_activity_new_reply', true);
        $sSendRequests = bp_get_user_meta($oUser->data->ID, 'notification_friends_friendship_request', true);
        $sAcceptRequests = bp_get_user_meta($oUser->data->ID, 'notification_friends_friendship_accepted', true);
        $sGroupInvite = bp_get_user_meta($oUser->data->ID, 'notification_groups_invite', true);
        $sGroupUpdate = bp_get_user_meta($oUser->data->ID, 'notification_groups_group_updated', true);
        $sGroupPromo = bp_get_user_meta($oUser->data->ID, 'notification_groups_admin_promotion', true);
        $sGroupRequest = bp_get_user_meta($oUser->data->ID, 'notification_groups_membership_request', true);
        $sNewMessages = bp_get_user_meta($oUser->data->ID, 'notification_messages_new_message', true);
        $sNewNotices = bp_get_user_meta($oUser->data->ID, 'notification_messages_new_notice', true);

        $oReturn->settings->new_mention = $sNewMention == 'yes' ? true : false;
        $oReturn->settings->new_reply = $sNewReply == 'yes' ? true : false;
        $oReturn->settings->send_requests = $sSendRequests == 'yes' ? true : false;
        $oReturn->settings->accept_requests = $sAcceptRequests == 'yes' ? true : false;
        $oReturn->settings->group_invite = $sGroupInvite == 'yes' ? true : false;
        $oReturn->settings->group_update = $sGroupUpdate == 'yes' ? true : false;
        $oReturn->settings->group_promo = $sGroupPromo == 'yes' ? true : false;
        $oReturn->settings->group_request = $sGroupRequest == 'yes' ? true : false;
        $oReturn->settings->new_message = $sNewMessages == 'yes' ? true : false;
        $oReturn->settings->new_notice = $sNewNotices == 'yes' ? true : false;

        return $oReturn;
    }

	/************************************************
	Follwers
	************************************************/
	 public function user_followers_users() {

		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_GET['userid']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}

		if($_GET['getdata']=='ids'){
			$args = array('user_id' => $_GET['userid']);
			$followers = bp_follow_get_followers($args);
			$oReturn->followers = $followers;
			return  $oReturn;
		}else{
			$thepage = 1;
			$perpage = 20;
			$args = array('user_id' => $_GET['userid']);
			$followers = bp_follow_get_followers($args);
			if(!$followers){$followers = array('9999999999');}
			$members_args = array(
				'page'		      => $thepage,
				'per_page'        => $perpage,
				'include'		  => $followers,
			);
			global $members_template;
			$counter = 0;
			if(bp_has_members($members_args)){
				while(bp_members()){
					bp_the_member();
					$user = new BP_Core_User($members_template->member->ID);
					if($user){
						$username = $avatar_big = $avatar_thumb = '';
						if($user->user_url){
							$username = str_replace('/','',str_replace(site_url('/members/'),'',$user->user_url));
						}
						if($user->avatar){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar, $user_avatar_result);
							$avatar_big = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_big && !strstr($avatar_big,'http:')){ $avatar_big = 'http:'.$avatar_big;}
						}
						if($user->avatar_thumb){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
							$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						}
						$oReturn->members[$counter]->id 		= $user->id;
						$oReturn->members[$counter]->username 	= $username;
						$oReturn->members[$counter]->fullname 	= bpaz_user_name_from_email($user->fullname);
						$oReturn->members[$counter]->email 		= $user->email;
						$oReturn->members[$counter]->last_active= $user->last_active;
						$oReturn->members[$counter]->avatar_thumb = $avatar_thumb;

						$profile_data = $user->profile_data;
						if($profile_data){
							foreach($profile_data as $sFieldName => $val){
								if(is_array($val)){
									$oReturn->members[$counter]->$sFieldName = $val['field_data'];
								}
							}
						}
						if(function_exists('bp_follow_total_follow_counts')){
							$oReturn->members[$counter]->follow_counts  = bp_follow_total_follow_counts( array( 'user_id' => $user->id ) );
						}
						$oReturn->members[$counter]->is_following = 0;
						if(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$user->id,'follower_id'=>$_GET['userid']))){
							$oReturn->members[$counter]->is_following = 1;
						}
						$counter++;
					}
				}
			}
		}
		return  $oReturn;
	}

	/************************************************
	Follwings
	************************************************/
	 public function user_followings_users() {

		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->success = '';
		$oReturn->error = '';
		if(!$_GET['userid']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		$args = array('user_id' => $_GET['userid']);
		if($_GET['getdata']=='ids'){
			$followings = bp_follow_get_following($args);
			$oReturn->followings = $followings;
			return  $oReturn;
		}else{
			global $bp,$wpdb;
			$thepage = 1;
			$perpage = 20;
			if($_GET['thepage']){$thepage = $_GET['thepage'];}
			if($_GET['perpage']){$perpage = $_GET['perpage'];}
			$args = array('user_id' => $_GET['userid']);
			$followings = bp_follow_get_following($args);
			if(!$followings){$followings = array('9999999999');}
			$members_args = array(
				'page'		      => $thepage,
				'per_page'        => $perpage,
				'include'		  => $followings,
			);
			global $members_template;
			$counter = 0;
			if(bp_has_members($members_args)){
				while(bp_members()){
					bp_the_member();
					$user = new BP_Core_User($members_template->member->ID);
					if($user){
						$username = $avatar_big = $avatar_thumb = '';
						if($user->user_url){
							$username = str_replace('/','',str_replace(site_url('/members/'),'',$user->user_url));
						}
						if($user->avatar){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar, $user_avatar_result);
							$avatar_big = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_big && !strstr($avatar_big,'http:')){ $avatar_big = 'http:'.$avatar_big;}
						}
						if($user->avatar_thumb){
							preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
							$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
							if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
						}
						$oReturn->members[$counter]->id 		= $user->id;
						$oReturn->members[$counter]->username 	= $username;
						$oReturn->members[$counter]->fullname 	= bpaz_user_name_from_email($user->fullname);
						$oReturn->members[$counter]->email 		= $user->email;
						$oReturn->members[$counter]->last_active= $user->last_active;
						$oReturn->members[$counter]->avatar_thumb = $avatar_thumb;

						$profile_data = $user->profile_data;
						if($profile_data){
							foreach($profile_data as $sFieldName => $val){
								if(is_array($val)){
									$oReturn->members[$counter]->$sFieldName = $val['field_data'];
								}
							}
						}
						if(function_exists('bp_follow_total_follow_counts')){
							$oReturn->members[$counter]->follow_counts  = bp_follow_total_follow_counts( array( 'user_id' => $user->id ) );
						}
						$oReturn->members[$counter]->is_following = 0;
						if(function_exists('bp_follow_is_following') && bp_follow_is_following(array('leader_id'=>$user->id,'follower_id'=>$_GET['userid']))){
							$oReturn->members[$counter]->is_following = 1;
						}
						$counter++;
					}
				}
			}
		}
		return  $oReturn;
	}

	public function members_get_lastactive()
	{
		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';
		$per_page=5;
		if($_GET['per_page']){$per_page = $_GET['per_page'];}
		$members_args = array(
			'user_id'         => 0,
			'type'            => 'active',
			'per_page'        => $per_page,
			'max'             => $per_page,
			'populate_extras' => true,
			'search_terms'    => false,
		);

		$counter=0;
		global $members_template;
		if(bp_has_members($members_args)){
			while(bp_members()){
				bp_the_member();
				$oReturn->data[$counter]->id = $members_template->member->ID;
				$oReturn->data[$counter]->link = bp_get_member_permalink();
				$oReturn->data[$counter]->name = bp_get_member_name();
				$oReturn->data[$counter]->last_active = bp_get_member_last_active();
				$oReturn->data[$counter]->registered_on = bp_get_member_registered();
				$member_avatar = bp_get_member_avatar();
				preg_match_all('/(src)=("[^"]*")/i',$member_avatar, $avatar_result);
				$member_avatar = str_replace('"','',$avatar_result[2][0]);
				if($member_avatar && !strstr($member_avatar,'http:')){ $member_avatar = 'http:'.$member_avatar;}
				$oReturn->data[$counter]->avatar = $member_avatar;
			}
		}
		return $oReturn;
	}

	public function cancel_friend_request()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!isset($_POST['friend_user_id'])){$oReturn->error = __('Wrong Friend User ID.','aheadzen'); return $oReturn;}
		try{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "Delete from wp_bp_friends where initiator_user_id = ".$_POST['friend_user_id']." AND friend_user_id = ".$_POST['user_id']."";
		$conn->query($sql);
		$sql = "Delete from wp_bp_friends where initiator_user_id = ".$_POST['user_id']." AND friend_user_id = ".$_POST['friend_user_id']."";
		if ($conn->query($sql) === TRUE) {
			$oReturn = new stdClass();
			$oReturn->msg = 'Request cancelled successfully.';
			$oReturn->success = 'ok';

		} else {

			$oReturn = new stdClass();
			$oReturn->msg = $conn->error;
			$oReturn->success = '';
			$oReturn->error = 'error';

		}
		}catch(Exception $e)
		{
			if(!isset($_POST['friend_user_id'])){$oReturn->error = __($e->getMessage(),'aheadzen'); return $oReturn;}
		}
		return $oReturn;

	}

	public function accept_friend_request()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['bp_friend_id'])){$oReturn->error = __('Wrong Friendship ID passed.','aheadzen'); return $oReturn;}
		if(!isset($_POST['user_id'])){$oReturn->error = __('Wrong User ID passed.','aheadzen'); return $oReturn;}
		try{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "SELECT id FROM `wp_bp_friends` WHERE (
				`initiator_user_id` = ".$_POST['user_id']."
				AND `friend_user_id` = ".$_POST['bp_friend_id']."
				OR `initiator_user_id` = ".$_POST['bp_friend_id'] ."
				AND `friend_user_id` =". $_POST['user_id']."
				)";
		$resource = $conn->query($sql);
		if ($resource->num_rows > 0) {
				$row = $resource->fetch_assoc();

		}
		else
		{
			$oReturn = new stdClass();
			$oReturn->msg ="Something went wrong. Please check your passed parameters.";
			$oReturn->success = '';
			$oReturn->error = 'error';
			return $oReturn;
			die;
		}
		if(isset($row['id']) && $row['id'] != '')
		{
			$sql = "UPDATE wp_bp_friends SET is_confirmed = 1 where id = ".$row['id']."";
			if ($conn->query($sql) === TRUE) {
				friends_notification_accepted_request($row['id'],$_POST['user_id'],$_POST['friend_user_id']);

				// Notification data row
				$userDataObj= get_userdata($row['initiator_user_id']);
				$friendDataObj = get_userdata($row['friend_user_id']);
				$userData = $userDataObj->data;
				$friendData = $friendDataObj->data;
				$friendData->notification_type_id = 2;

				$message = $friendData->display_name. " accepted your friend request.";
				$this->sendPushNotification($message, $userData->device_token, $friendData, $userData->device_type, $userData);

				$oReturn = new stdClass();
				$oReturn->msg = 'Request accepted successfully.';
				$oReturn->success = 'ok';

			} else {

				$oReturn = new stdClass();
				$oReturn->msg = $conn->error;
				$oReturn->success = '';
				$oReturn->error = 'error';

			}
		 }
		 else
		 {
			 $oReturn = new stdClass();
			$oReturn->msg ="Something went wrong. Please check your passed parameters.";
			$oReturn->success = '';
			$oReturn->error = 'error';
			return $oReturn;
			die;
		 }
		}catch(Exception $e)
		{
			if(!isset($_POST['friend_user_id'])){$oReturn->error = __($e->getMessage(),'aheadzen'); return $oReturn;}
		}
		return $oReturn;

	}

	public function sendFriendRequest()
	{
		error_reporting(0);
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!isset($_POST['friend_user_id'])){$oReturn->error = __('Wrong Friend ID.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "INSERT INTO wp_bp_friends (initiator_user_id,friend_user_id,is_confirmed,is_limited,date_created)
		VALUES ('".$_POST['user_id']."', '".$_POST['friend_user_id']."', 0,0,'".date("Y-m-d H:i:s")."')";

		if ($conn->query($sql) === TRUE) {
			$last_id = mysqli_insert_id($conn);
			friends_notification_new_request($last_id,$_POST['user_id'],$_POST['friend_user_id']);

			// Notification data making
			$userDataObj= get_userdata($_POST['user_id']);
			$friendDataObj = get_userdata($_POST['friend_user_id']);
			$userData = $userDataObj->data;
			$friendData = $friendDataObj->data;
			$friendData->notification_type_id = 1;

			$message = $userData->display_name. " sent you a friend request.";
			$res = $this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);

			$oReturn = new stdClass();
			$oReturn->msg = 'Request sent successfully.';
			$oReturn->success = 'ok';

		} else {
			$oReturn = new stdClass();
			$oReturn->msg = $conn->error;
			$oReturn->success = '';
			$oReturn->error = 'error';

		}
		return $oReturn;

	}

	public function delete_notification()
	{

		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['notification_id'])){$oReturn->error = __('Wrong Notification ID.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "DELETE FROM wp_bp_notifications WHERE id in (".$_POST['notification_id'].")";

		if ($conn->query($sql) === TRUE) {
			$oReturn = new stdClass();
			$oReturn->msg = 'Notificatin deleted successfully.';
			$oReturn->success = 'ok';
		}else {
			$oReturn = new stdClass();
			$oReturn->msg = $conn->error;
			$oReturn->success = '';
			$oReturn->error = 'error';

		}
		return $oReturn;
	}


	public function logout()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		check_admin_referer('log-out');
		wp_logout();
	}

	public function activity_get_favorite_activity()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_REQUEST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {

			$oReturn = new stdClass();
			$oReturn->msg = "Connection failed: " . $conn->connect_error;
			$oReturn->error = 'error';
			return $oReturn;

		}
		/*$sql = "SELECT * FROM wp_bp_activity WHERE id IN (
				SELECT activity_id FROM `wp_bp_activity_meta` WHERE
				activity_id IN ( SELECT id FROM `wp_bp_activity` WHERE user_id = ".$_REQUEST['user_id']." ))"; */

		$sql = "SELECT * FROM wp_bp_activity bp_activiry
				#INNER JOIN `wp_users`
				#	ON `wp_users`.`ID` = `bp_activiry`.`user_id`
				WHERE bp_activiry.id IN (
				SELECT activity_id FROM `wp_bp_activity_meta` WHERE
				activity_id IN ( SELECT id FROM `wp_bp_activity` WHERE user_id = ".$_REQUEST['user_id']." ))";

		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
				// output data of each row
				$arr = array();
				while($row = $result->fetch_assoc()) {

					$sql = "SELECT COUNT(1) AS total_comments FROM wp_bp_activity WHERE item_id = ".$row['id']." AND TYPE = 'activity_comment'";
					$res = $conn->query($sql);
					$user = get_userdata($row['user_id']);
					$row['user'] = $user->data;
					$total_comment_count = 0;
					if ($res->num_rows > 0) {
						$total_comment_count = $res->fetch_assoc();
					}
					$row['total_comments'] = $total_comment_count['total_comments'] ;

					$comments = $this->get_activity_comments($row['id']);
					$row['comments'] = $comments ;
					$sql = "SELECT meta_value FROM `wp_bp_activity_meta` WHERE meta_key = 'favorite_count' AND meta_value > 0 AND activity_id = ".$row['id'];
					$res = $conn->query($sql);

					$total_comment_count = 0;
					if ($res->num_rows > 0) {

						$row['favorite'] = true;
					}
					else
					{
						$row['favorite'] = false;
					}
					$arr[] = $row;
				}
			} else {
				$oReturn = new stdClass();
				$oReturn->msg = $conn->error;
				$oReturn->error = 'error';
			}
		$oReturn = new stdClass();
		$oReturn->msg = 'Success';
		$oReturn->success = 'ok';
		$oReturn->data = $arr;
		return $oReturn;

	}

	function add_favorite_activity()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_REQUEST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!isset($_REQUEST['activity_id'])){$oReturn->error = __('Wrong Activity ID.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {

			$oReturn = new stdClass();
			$oReturn->msg = "Connection failed: " . $conn->connect_error;
			$oReturn->error = 'error';
			return $oReturn;

		}


		$user_id = $_REQUEST['user_id'];$activity_id = $_REQUEST['activity_id'];
		// Fallback to logged in user if no user_id is passed.
		if ( empty( $user_id ) ) {
			$user_id = bp_loggedin_user_id();
		}

		$my_favs = bp_get_user_meta( $user_id, 'bp_favorite_activities', true );
		if ( empty( $my_favs ) || ! is_array( $my_favs ) ) {
			$my_favs = array();
		}

		// Bail if the user has already favorited this activity item.
		if ( in_array( $activity_id, $my_favs ) ) {
			return false;
		}

		// Add to user's favorites.
		$my_favs[] = $activity_id;

		// Update the total number of users who have favorited this activity.
		$fav_count = bp_activity_get_meta( $activity_id, 'favorite_count' );
		$fav_count = !empty( $fav_count ) ? (int) $fav_count + 1 : 1;

		// Update user meta.
		bp_update_user_meta( $user_id, 'bp_favorite_activities', $my_favs );

		// Update activity meta counts.
		if ( bp_activity_update_meta( $activity_id, 'favorite_count', $fav_count ) ) {

			/**
			 * Fires if bp_activity_update_meta() for favorite_count is successful and before returning a true value for success.
			 * @param int $activity_id ID of the activity item being favorited.
			 * @param int $user_id     ID of the user doing the favoriting.
			 */
			do_action( 'bp_activity_add_user_favorite', $activity_id, $user_id );

			// Success.
			$oReturn = new stdClass();
			$oReturn->msg = "Successfully added in favorites.";
			$oReturn->success = 'ok';
			return $oReturn;

		// Saving meta was unsuccessful for an unknown reason.
		} else {

			/**
			 * Fires if bp_activity_update_meta() for favorite_count is unsuccessful and before returning a false value for failure.
			 * @param int $activity_id ID of the activity item being favorited.
			 * @param int $user_id     ID of the user doing the favoriting.
			 */
			do_action( 'bp_activity_add_user_favorite_fail', $activity_id, $user_id );

			$oReturn = new stdClass();
			$oReturn->msg = "error occurs";
			$oReturn->error = 'error';
			return $oReturn;
		}
	}

	function remove_favorite_activity()
	{

		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_REQUEST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!isset($_REQUEST['activity_id'])){$oReturn->error = __('Wrong Activity ID.','aheadzen'); return $oReturn;}

		$user_id = $_REQUEST['user_id']; $activity_id = $_REQUEST['activity_id'];

		$my_favs = bp_get_user_meta( $user_id, 'bp_favorite_activities', true );
		$my_favs = array_flip( (array) $my_favs );

		// Bail if the user has not previously favorited the item.
		if ( ! isset( $my_favs[ $activity_id ] ) ) {
			return false;
		}

		// Remove the fav from the user's favs.
		unset( $my_favs[$activity_id] );
		$my_favs = array_unique( array_flip( $my_favs ) );

		// Update the total number of users who have favorited this activity.
		$fav_count = bp_activity_get_meta( $activity_id, 'favorite_count' );
		if ( ! empty( $fav_count ) ) {

			// Deduct from total favorites.
			if ( bp_activity_update_meta( $activity_id, 'favorite_count', (int) $fav_count - 1 ) ) {

				// Update users favorites.
				if ( bp_update_user_meta( $user_id, 'bp_favorite_activities', $my_favs ) ) {

					/**
					 * Fires if bp_update_user_meta() is successful and before returning a true value for success.
					 * @param int $activity_id ID of the activity item being unfavorited.
					 * @param int $user_id     ID of the user doing the unfavoriting.
					 */
					do_action( 'bp_activity_remove_user_favorite', $activity_id, $user_id );

					// Success.
					$oReturn = new stdClass();
					$oReturn->msg = "Successfully removed from favorites.";
					$oReturn->success = 'ok';
					return $oReturn;

				// Error updating.
				} else {
					$oReturn = new stdClass();
					$oReturn->msg = "error";
					$oReturn->error = 'error';
					return $oReturn;
				}

			// Error updating favorite count.
			} else {
				$oReturn = new stdClass();
				$oReturn->msg = "error";
				$oReturn->error = 'error';
				return $oReturn;
			}

		// Error getting favorite count.
		} else {
			$oReturn = new stdClass();
			$oReturn->msg = "error.";
			$oReturn->error = 'error';
			return $oReturn;
		}
	}

	function delete_activity()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		//if(!isset($_REQUEST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!isset($_REQUEST['activity_id'])){$oReturn->error = __('Wrong Activity ID.','aheadzen'); return $oReturn;}
		if(!isset($_REQUEST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {

			$oReturn = new stdClass();
			$oReturn->msg = "Connection failed: " . $conn->connect_error;
			$oReturn->error = 'error';
			return $oReturn;

		}
		$count = 0;
		$sql = "SELECT * FROM wp_bp_activity WHERE id = ".$_REQUEST['activity_id'];
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			if($row['user_id'] == $_REQUEST['user_id']){
				$sql = "DELETE FROM wp_bp_activity WHERE id = ".$_REQUEST['activity_id'];
				$result = $conn->query($sql);

				$sql = "DELETE FROM wp_bp_activity_meta WHERE activity_id = ".$_REQUEST['activity_id'];
				$result = $conn->query($sql);

				$oReturn = new stdClass();
				$oReturn->msg = "Successfully deleted.";
				$oReturn->success = 'ok';
				return $oReturn;
			} else {
				$oReturn = new stdClass();
				$oReturn->msg = "You are not the owner of the activity.";
				$oReturn->error = 'error';
				return $oReturn;
			}

		}
		else
		{
			$oReturn = new stdClass();
			$oReturn->msg = "You have passed wrong Activity ID.";
			$oReturn->error = 'error';
			return $oReturn;
		}
	}

	function get_suggested_friends()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_REQUEST['user_id'])){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		$user_id = $_REQUEST['user_id'];

		$my_friends = (array) friends_get_friend_user_ids( $user_id ); //get all friend ids

		$my_friend_req = (array) friend_suggest_get_friendship_requested_user_ids( $user_id ); //get all friend request by me

		$possible_friends = array(); //we will store the possible friend ids here

		foreach ( $my_friends as $friend_id ) {
			$possible_friends = array_merge( $possible_friends, (array) friends_get_friend_user_ids( $friend_id ) );
		}


		//we have the list of friends of friends, we will just remove
		//now get only udifferent friend ids(unique)
		$possible_friends = array_unique( $possible_friends );

		//intersect my friends with this array
		//$my_friends[] = get_current_user_id(); //include me to

		$excluded_users = get_user_meta( $user_id, 'hidden_friend_suggestions', true );

		$excluded_users = array_merge( $my_friends, (array) $excluded_users, (array) $my_friend_req );

		//we may check the preference of the user regarding , like not add

		$possible_friends = array_diff( $possible_friends, $excluded_users ); //get those user who are not my friend and also exclude me too

		if ( ! empty( $possible_friends ) ) {

			shuffle( $possible_friends ); //randomize
			$possible_friends = array_slice( $possible_friends, 0, $limit );
		}


		foreach ($possible_friends as $sFriendID) {

			if($user_id != $sFriendID){
            $oUser = get_user_by('id', $sFriendID);
            $oReturn->friends [(int) $sFriendID]->username = $oUser->data->user_login;
            $oReturn->friends [(int) $sFriendID]->display_name = bpaz_user_name_from_email($oUser->data->display_name);
            $oReturn->friends [(int) $sFriendID]->mail = $oUser->data->user_email;
			$avatar = bp_core_fetch_avatar( array(
				'object'  => 'user',
				'item_id' => $sFriendID,
				'html'    => false,
				'type'    => 'full',
			) );
			$oReturn->friends [(int) $sFriendID]->avatar = $avatar;
			}
		}
		$oReturn->count = count($oReturn->friends);
		if($oReturn->count == 0)
		{
			$oReturn->msg = "No suggested friends found.";
		}
        return $oReturn;

	}


	public function create_team()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['user_id'])){$oReturn->error = __('Wrong User ID passed.','aheadzen'); return $oReturn;}
		if(!isset($_POST['name'])){$oReturn->error = __('Team name is required.','aheadzen'); return $oReturn;}
		if(!isset($_POST['description'])){$oReturn->error = __('Team description is required.','aheadzen'); return $oReturn;}
		if(!isset($_POST['location_state'])){$oReturn->error = __('Team location_state is required.','aheadzen'); return $oReturn;}
		if(!isset($_POST['location_city'])){$oReturn->error = __('Team location_city is required.','aheadzen'); return $oReturn;}
		if(!isset($_POST['status'])){$oReturn->error = __('Team status is required.','aheadzen'); return $oReturn;}
		if(!isset($_POST['sport'])){$oReturn->error = __('Team sport is required.','aheadzen'); return $oReturn;}
		try{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$_POST['slug'] = str_replace(' ', '-', strtolower($_POST['name']));

		if(isset($_POST['status']) && $_POST['status'] == "Private")
		{
			$_POST['status'] == "private";
		}
		if(isset($_POST['status']) && $_POST['status'] == "Public")
		{
			$_POST['status'] == "public";
		}

		$sql = "INSERT INTO wp_bp_groups ( creator_id, NAME, slug, description, `status`, enable_forum, date_created,
location_state, location_city, sport) VALUES ('".$_POST['user_id']."','".$_POST['name']."','".trim($_POST['slug'])."','".$_POST['description']."','".$_POST['status']."',0,'".date('Y-m-d H:i:s')."','".$_POST['location_state']."','".$_POST['location_city']."','".$_POST['sport']."');";
		if ($conn->query($sql) === TRUE) {

			 $last_id = $conn->insert_id;
			 $sql2 = "INSERT INTO wp_bp_groups_members ( group_id, user_id, inviter_id, is_admin, is_mod, user_title, date_modified,
comments, is_confirmed, is_banned, invite_sent) VALUES ('".$last_id."','".$_POST['user_id']."',0,1,0,'Group Admin','".date("Y-m-d H:i:s")."','',1,0,0);";
			 $conn->query($sql2);

			 $sql3 = "INSERT INTO wp_bp_groups_groupmeta ( group_id, meta_key, meta_value) VALUES ($last_id,'total_member_count',1);";
			 $conn->query($sql3);


			 $sql4 = "INSERT INTO wp_bp_groups_groupmeta ( group_id, meta_key, meta_value) VALUES ($last_id,'last_activity','".date("Y-m-d H:i:s")."');";
			 $conn->query($sql4);

			 $sql5 = "INSERT INTO wp_bp_groups_groupmeta ( group_id, meta_key, meta_value) VALUES ($last_id,'invite_status','members');";
			 $conn->query($sql5);

			 $oUser = get_user_by('id', $_POST['user_id']);

			 $activity_link = '<a href="'.get_site_url().'/members/'.$oUser->data->user_nicename.'/" title="'.$oUser->data->display_name.'">'.$oUser->data->display_name.'</a> <span class="activity-bold">created the team<span> <a href="'.get_site_url().'/teams/'.$_POST['slug'].'/">'.$_POST['name'].'</a></span></span>';
			 $member_link = ''.get_site_url().'/members/'.$oUser->data->user_nicename.'/';

			 $sql6 = "INSERT INTO `wp_bp_activity` (user_id, component, `type`, `action`, content, primary_link, item_id, `secondary_item_id`, `date_recorded`)
VALUES ('".$_POST['user_id']."','groups', 'created_group', '".$activity_link."','','".$member_link."','".$last_id."',0,'".date("Y-m-d H:i:s")."');";
			 $conn->query($sql6);

			if(isset($_POST['avatar']) && $_POST['avatar'] != '')
			{
				$this->upload_group_avatar($_POST['avatar'],$last_id,'group_pic');
			}
			if(isset($_POST['cover_avatar']) && $_POST['cover_avatar'] != '')
			{
				$this->upload_group_cover($_POST['cover_avatar'],$last_id,'cover_pic');
			}
			$data = $_POST;
			unset($data['avatar']);
			unset($data['cover_avatar']);
			$data['group_id'] = $last_id;
			$oReturn = new stdClass();
			$oReturn->msg = 'Team created successfully.';
			$oReturn->success = 'ok';
			$oReturn->data = $data;


		} else {

			$oReturn = new stdClass();
			$oReturn->msg = $conn->error;
			$oReturn->success = '';
			$oReturn->error = 'error';

		}
		}catch(Exception $e)
		{
			$oReturn->error = __($e->getMessage(),'aheadzen'); return $oReturn;
		}
		return $oReturn;

	}

	function groups_invite_user() {

	if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['inviter_id']){$oReturn->error = __('inviter_id is required.','aheadzen'); return $oReturn;}

	$user_ids = explode(",",$_REQUEST['user_id']);

	foreach($user_ids as $user_id){
		$defaults = array(
			'user_id'       => $user_id,
			'group_id'      => $_POST['group_id'],
			'inviter_id'    => $_POST['inviter_id'],
			'date_modified' => bp_core_current_time(),
			'is_confirmed'  => 0
		);
		 $group_id = $_POST['group_id'];
		 //$user_id = $_POST['user_id'];
		 $inviter_id = $_POST['inviter_id'];
		 $date_modified = bp_core_current_time();
		 $is_confirmed = 0;

		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		$group = groups_get_group( array( 'group_id' => (int) $group_id ) );
		if ( empty( $user_id ) || empty( $group_id ) ){
			$oReturn = new stdClass();
				$oReturn->error = 'error';
				$oReturn->status = '';
				return $oReturn;
		}

		// if the user has already requested membership, accept the request
		if ( $membership_id = groups_check_for_membership_request( $user_id, $group_id ) ) {
			groups_accept_membership_request( $membership_id, $user_id, $group_id );

		// Otherwise, create a new invitation
		} elseif ( ! groups_is_user_member( $user_id, $group_id ) && ! groups_check_user_has_invite( $user_id, $group_id, 'all' ) ) {
			$invite                = new BP_Groups_Member;
			$invite->group_id      = $group_id;
			$invite->user_id       = $user_id;
			$invite->date_modified = $date_modified;
			$invite->inviter_id    = $inviter_id;
			$invite->is_confirmed  = $is_confirmed;
			$invite->invite_sent  = 1;

			if ( !$invite->save() ){
				$oReturn = new stdClass();
				$oReturn->error = 'error';
				$oReturn->status = '';
				return $oReturn;
			}





		// @todo $inviter_ud may be used for caching, test without it
		$inviter_ud   = bp_core_get_core_userdata( $inviter_id );
		$inviter_name = bp_core_get_userlink( $inviter_id, true, false, true );
		$inviter_link = bp_core_get_user_domain( $inviter_id );
		$group_link   = bp_get_group_permalink( $group_id );

		// Setup the ID for the invited user
		$invited_user_id = $user_id;

		// Trigger a BuddyPress Notification
		if ( bp_is_active( 'notifications' ) ) {
			bp_notifications_add_notification( array(
				'user_id'          => $inviter_id,
				'item_id'          => $group_id,
				'component_name'   => buddypress()->groups->id,
				'component_action' => 'group_invite'
			) );
		}

		// Bail if member opted out of receiving this email
		if ( 'no' === bp_get_user_meta( $inviter_id, 'notification_groups_invite', true ) ) {
			$oReturn = new stdClass();
				$oReturn->error = 'error';
				$oReturn->status = '';
				return $oReturn;
		}

		$invited_ud    = bp_core_get_core_userdata( $inviter_id );
		$settings_slug = function_exists( 'bp_get_settings_slug' ) ? bp_get_settings_slug() : 'settings';
		$settings_link = bp_core_get_user_domain( $inviter_id ) . $settings_slug . '/notifications/';
		$invited_link  = bp_core_get_user_domain( $inviter_id );
		$invites_link  = trailingslashit( $invited_link . bp_get_groups_slug() . '/invites' );

		// Set up and send the message
		$to       = $invited_ud->user_email;
		$subject  = bp_get_email_subject( array( 'text' => sprintf( __( 'You have an invitation to the group: "%s"', 'buddypress' ), $group->name ) ) );
		$message  = sprintf( __(
				'One of your friends %1$s has invited you to the group: "%2$s".

				To view your group invites visit: %3$s

				To view the group visit: %4$s

				To view %5$s\'s profile visit: %6$s

				---------------------
				', 'buddypress' ), $inviter_name, $group->name, $invites_link, $group_link, $inviter_name, $inviter_link );


		// Only show the disable notifications line if the settings component is enabled
		if ( bp_is_active( 'settings' ) ) {
			$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'buddypress' ), $settings_link );
		}


		$to      = apply_filters( 'groups_notification_group_invites_to', $to );
		$subject = apply_filters_ref_array( 'groups_notification_group_invites_subject', array( $subject, &$group ) );
		$message = apply_filters_ref_array( 'groups_notification_group_invites_message', array( $message, &$group, $inviter_name, $inviter_link, $invites_link, $group_link, $settings_link ) );

		wp_mail( $to, $subject, $message );

		// Notification data making
		$userDataObj= get_userdata($inviter_id);
		$friendDataObj = get_userdata($user_id);
		$userData = $userDataObj->data;
		$friendData = $friendDataObj->data;
		$friendData->notification_type_id = 3;  // group_invite

		$message = "You have an invitation to the group: ".$group->name;
		$this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);


		}
	}
	$oReturn = new stdClass();
	$oReturn->success = 'success';
	$oReturn->status = 'ok';
	return $oReturn;
	}

	function accept_team_invitation()
	{
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "UPDATE `wp_bp_groups_members` SET is_confirmed = 1 WHERE group_id = ".$_REQUEST['group_id']." AND user_id = ".$_REQUEST['user_id']."";
		if($conn->query($sql))
		{
			$oReturn = new stdClass();
			$oReturn->success = 'success';
			$oReturn->status = 'ok';
		}
		else
		{
			$oReturn = new stdClass();
			$oReturn->error = 'error';
			$oReturn->status = '';
		}

		return $oReturn;
	}

	function reject_team_invitation()
	{
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "DELETE FROM `wp_bp_groups_members` WHERE group_id = ".$_REQUEST['group_id']." AND user_id = ".$_REQUEST['user_id']."";
		if($conn->query($sql))
		{
			$oReturn = new stdClass();
			$oReturn->success = 'success';
			$oReturn->status = 'ok';
		}
		else
		{
			$oReturn = new stdClass();
			$oReturn->error = 'error';
			$oReturn->status = '';
		}

		return $oReturn;
	}


	function upload_group_avatar($picture_code,$group_id,$clicked_pic)
	{

		//$bp_upload2 = xprofile_avatar_upload_dir('',$user_id);
		$bp_upload = bp_core_avatar_upload_path();

		$basedir = $bp_upload."/group-avatars/".$group_id;
		//echo $baseurl = $bp_upload['url'];
		if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
		//$filename = $clicked_pic.'_'.$user_id.'.jpg';
		$filename = $clicked_pic.'_'.$group_id.'.jpg';
		$outputFile = $basedir.'/'.$filename;

		$imageurl = $outputFileURL = $baseurl.'/'.$filename;

		if(strstr($picture_code,'data:image/')){
			 $picture_code_arr = explode(',', $picture_code);
			$picture_code = $picture_code_arr[1];
		}

		$quality = 70;
		if(file_exists($outputFile)){@unlink($outputFile);}
		$data = base64_decode($picture_code);
		$image = imagecreatefromstring($data);
		$imageSave = imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
		if(!$imageSave){$oReturn->error = 'Image Save Error'; return  $oReturn;}
		if($outputFile && $clicked_pic=='cover_pic'){
			upload_group_cover_picture($picture_code,$group_id,$clicked_pic);
		}elseif($outputFile && $clicked_pic=='group_pic'){
			$imgdata = @getimagesize( $outputFile );
			$img_width = $imgdata[0];
			$img_height = $imgdata[1];
			$upload_dir = wp_upload_dir();
			$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );


		if ( !bp_core_avatar_handle_crop( array( 'object' => 'group', 'avatar_dir' => 'group-avatars', 'item_id' => $group_id, 'original_file' => $existing_avatar_path, 'crop_x' =>0, 'crop_y' => 0, 'crop_w' => $img_width , 'crop_h' => $img_height ) ) )
						return json_encode(array("status"=>-1,"message"=>"There was an error saving the group profile photo, please try uploading again."));
					else

						return json_encode(array("status"=>1,"message"=>"The group profile photo was uploaded successfully."));
				}

	}

	function upload_group_cover($picture_code,$group_id,$clicked_pic)
	{

		//$bp_upload2 = xprofile_avatar_upload_dir('',$user_id);
		$bp_upload = bp_core_avatar_upload_path();

		$basedir = $bp_upload."/buddypress/groups/".$group_id."/cover-image";

		//$basedir = "wp-content/uploads/buddypress/members/".$member_id."/cover-image";
		$dirHandle = opendir($basedir);

        while ($file = readdir($dirHandle)) {

            if($file) {

                @unlink($basedir.'/'.$file);
            }
        }

        closedir($dirHandle);


		//echo $baseurl = $bp_upload['url'];
		if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
		//$filename = $clicked_pic.'_'.$user_id.'.jpg';
		//$filename = $clicked_pic.'_'.$group_id.'.jpg';

		$ext     = strtolower( 'jpg' );
		$name    = wp_hash( $file . time() ) . '-bp-cover-image';
		$filename = trailingslashit( $dir ) . "{$name}.{$ext}";

		$outputFile = $basedir.'/'.$filename;

		$imageurl = $outputFileURL = $baseurl.'/'.$filename;

		if(strstr($picture_code,'data:image/')){
			 $picture_code_arr = explode(',', $picture_code);
			$picture_code = $picture_code_arr[1];
		}

		$quality = 70;
		if(file_exists($outputFile)){@unlink($outputFile);}
		$data = base64_decode($picture_code);
		$image = imagecreatefromstring($data);
		$imageSave = imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
		if(!$imageSave){$oReturn->error = 'Image Save Error'; return  $oReturn;}
		if($outputFile && $clicked_pic=='group_pic'){
			upload_group_cover_picture($picture_code,$group_id,$clicked_pic);
		}elseif($outputFile && $clicked_pic=='cover_pic'){
			$imgdata = @getimagesize( $outputFile );
			$img_width = $imgdata[0];
			$img_height = $imgdata[1];
			$upload_dir = wp_upload_dir();
			$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );


		if ( !bp_core_avatar_handle_crop( array( 'object' => 'group', 'avatar_dir' => 'buddypress/groups/84/cover-image', 'item_id' => 75, 'original_file' => $existing_avatar_path, 'crop_x' =>0, 'crop_y' => 0, 'crop_w' => $img_width , 'crop_h' => $img_height ) ) )
						return json_encode(array("status"=>-1,"message"=>"There was an error saving the group profile photo, please try uploading again."));
					else

						return json_encode(array("status"=>1,"message"=>"The group profile photo was uploaded successfully."));
				}

	}


	function upload_member_cover($picture_code,$member_id,$clicked_pic)
	{

		//$bp_upload2 = xprofile_avatar_upload_dir('',$user_id);
		error_reporting(0);
		$basedir = "wp-content/uploads/buddypress/members/".$member_id."/cover-image";
		$dirHandle = opendir($basedir);

        while ($file = readdir($dirHandle)) {

            if($file) {

                @unlink($basedir.'/'.$file);
            }
        }

        closedir($dirHandle);


		$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' => $member_id ) );

		//echo $baseurl = $bp_upload['url'];
		if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
		//$filename = $clicked_pic.'_'.$user_id.'.jpg';
		//$filename = $clicked_pic.'_'.$group_id.'.jpg';

		$ext     = strtolower( 'jpg' );
		$name    = wp_hash( $file . time() ) . '-bp-cover-image';
		$filename = trailingslashit( $basedir ) . "{$name}.{$ext}";

		$outputFile = $filename;

		$imageurl = $outputFileURL = $basedir.'/'.$filename;

		if(strstr($picture_code,'data:image/')){
			 $picture_code_arr = explode(',', $picture_code);
			$picture_code = $picture_code_arr[1];
		}

		$quality = 70;
		if(file_exists($outputFile)){@unlink($outputFile);}
		$data = base64_decode($picture_code);
		$image = @imagecreatefromstring($data);

		$imageSave = @imagejpeg($image, $outputFile, $quality);
		@imagedestroy($image);
		if(!$imageSave){$oReturn->error = 'Image Save Error'; return  $oReturn;}
		if($outputFile && $clicked_pic=='group_pic'){
			upload_group_cover_picture($picture_code,$group_id,$clicked_pic);
		}elseif($outputFile && $clicked_pic=='cover_pic'){
			$imgdata = @getimagesize( $outputFile );
			$img_width = $imgdata[0];
			$img_height = $imgdata[1];
			$upload_dir = wp_upload_dir();
			$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );
			bp_attachments_uploads_dir_get( 'dir' );
			$bp_attachments_uploads_dir = bp_attachments_uploads_dir_get();
			$cover_subdir = "members" . '/' . $member_id . '/cover-image';
			$cover_dir    = trailingslashit( $bp_attachments_uploads_dir['basedir'] ) . $cover_subdir;

			//die;
		if ( !bp_core_avatar_handle_crop( array( 'object' => 'members', 'avatar_dir' => 'buddypress/members/'.$member_id.'/cover-image', 'item_id' => $member_id, 'original_file' => $existing_avatar_path, 'crop_x' =>0, 'crop_y' => 0, 'crop_w' => $img_width , 'crop_h' => $img_height ) ) )


					return true;
					else

						return true;
				}

	}

	function upload_team_cover_image()
	{
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['picture_code']){$oReturn->error = __('picture_code is required.','aheadzen'); return $oReturn;}
		$res = $this->upload_group_cover($_REQUEST['picture_code'],$_REQUEST['group_id'],'cover_pic');
		$cover_pic = $this->get_team_cover_photo_return($_REQUEST['group_id']);
		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $cover_pic;
		return $oReturn;
	}

	function upload_team_profile_image()
	{
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['picture_code']){$oReturn->error = __('picture_code is required.','aheadzen'); return $oReturn;}
		$res = $this->upload_group_avatar($_REQUEST['picture_code'],$_REQUEST['group_id'],'group_pic');
		$useravatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$_REQUEST['group_id'], 'html'=>false, 'type'=>'full'));
		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $useravatar_url;
		return $oReturn;
	}


	public function update_team()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['user_id'])){$oReturn->error = __('Wrong User ID passed.','aheadzen'); return $oReturn;}
		if(!isset($_POST['group_id'])){$oReturn->error = __('Wrong Group ID passed.','aheadzen'); return $oReturn;}
		try{
		$name = "";
		if(isset($_POST['name']))
		{
			$name = "name = '". $_POST['name']."',";
			$_POST['slug'] = str_replace(' ', '-', strtolower($_POST['name']));
		}

		$description = "";
		if(isset($_POST['description']))
		{
			$description = "description = '". $_POST['description']."',";
		}
		$location_state = "";
		if(isset($_POST['location_state']))
		{
			$location_state = "location_state = '". $_POST['location_state']."',";
		}
		$location_city = "";
		if(isset($_POST['location_city']))
		{
			$location_city = "location_city = '". $_POST['location_city']."',";
		}
		$status = "";
		if(isset($_POST['status']))
		{
			$status = "status = '". $_POST['status']."',";
		}
		$sport = "";
		if(isset($_POST['sport']))
		{
			$sport = "sport = '". $_POST['sport']."',";
		}


		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "UPDATE wp_bp_groups SET $name $description  $location_state  $location_city $status $sport";
		$sql = rtrim($sql, " ,");
		$sql2 = "where id = ".$_POST['group_id']."";
		$query = $sql.' '.$sql2;
		if ($conn->query($query) === TRUE) {

			 $oUser = get_user_by('id', $_POST['user_id']);

			 $activity_link = '<a href="'.get_site_url().'/members/'.$oUser->data->user_nicename.'/" title="'.$oUser->data->display_name.'">'.$oUser->data->display_name.'</a> <span class="activity-bold">updated the team<span> <a href="'.get_site_url().'/teams/'.$_POST['slug'].'/">'.$_POST['name'].'</a></span></span>';
			 $member_link = get_site_url().'/members/'.$oUser->data->user_nicename.'/';

			 $sql6 = "INSERT INTO `wp_bp_activity` (user_id, component, `type`, `action`, content, primary_link, item_id, `secondary_item_id`, `date_recorded`)
VALUES ('".$_POST['user_id']."','groups', 'updated_group', '".$activity_link."','','".$member_link."','".$last_id."',0,'".date("Y-m-d H:i:s")."');";
			 $conn->query($sql6);

			if(isset($_POST['avatar']) && $_POST['avatar'] != '')
			{
				$this->upload_group_avatar($_POST['avatar'],$last_id,'group_pic');
			}
			if(isset($_POST['cover_avatar']) && $_POST['cover_avatar'] != '')
			{
				$this->upload_group_cover($_POST['cover_avatar'],$last_id,'cover_pic');
			}

			$oReturn = new stdClass();
			$oReturn->msg = 'Team updated successfully.';
			$oReturn->success = 'ok';

		} else {

			$oReturn = new stdClass();
			$oReturn->msg = $conn->error;
			$oReturn->success = '';
			$oReturn->error = 'error';

		}
		}catch(Exception $e)
		{
			$oReturn->error = __($e->getMessage(),'aheadzen'); return $oReturn;
		}
		return $oReturn;

	}


	public function delete_team()
	{
		$error = '';
		header("Access-Control-Allow-Origin: *");
		if(!isset($_POST['user_id'])){$oReturn->error = __('Wrong User ID passed.','aheadzen'); return $oReturn;}
		if(!isset($_POST['group_id'])){$oReturn->error = __('Wrong Group ID passed.','aheadzen'); return $oReturn;}
		try{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "DELETE FROM wp_bp_groups WHERE id = ".$_POST['group_id'];
		if ($conn->query($sql) === TRUE) {

			$oUser = get_user_by('id', $_POST['user_id']);

			$activity_link = '<a href="'.get_site_url().'/members/'.$oUser->data->user_nicename.'/" title="'.$oUser->data->display_name.'">'.$oUser->data->display_name.'</a> <span class="activity-bold">updated the team<span> <a href="'.get_site_url().'/teams/'.$_POST['name'].'/">'.$_POST['name'].'</a></span></span>';
			$member_link = get_site_url().'/members/'.$oUser->data->user_nicename.'/';


			$sql = "DELETE FROM `wp_bp_groups_groupmeta` WHERE group_id = ".$_POST['group_id'];
			$res = $conn->query($sql);

			$sql = "DELETE FROM `wp_bp_groups_members` WHERE group_id = ".$_POST['group_id'];
			$res = $conn->query($sql);

			$sql = "DELETE FROM `wp_bp_groups_calendars` WHERE group_id = ".$_POST['group_id'];
			$res = $conn->query($sql);



			$oReturn = new stdClass();
			$oReturn->msg = 'Team deleted successfully.';
			$oReturn->success = 'ok';

		} else {

			$oReturn = new stdClass();
			$oReturn->msg = $conn->error;
			$oReturn->success = '';
			$oReturn->error = 'error';

		}
		}catch(Exception $e)
		{
			$oReturn->error = __($e->getMessage(),'aheadzen'); return $oReturn;
		}
		return $oReturn;

	}

	function get_activities()
	{

		header("Access-Control-Allow-Origin: *");
		$oReturn = new stdClass();
		$oReturn->msg = '';
		$oReturn->success = '';
		$oReturn->error = '';

		if(!isset($_REQUEST['limit']))
		{
			$limit = 20; // records per page
		}
		else
		{
			$limit = $_REQUEST['limit'];
		}
		if(!isset($_REQUEST['page']))
		{
			$page = 1;
		}
		else
		{
			$page = $_REQUEST['page'];
		}
		if($page == 1)
		{
			$offset = 0;
		}
		else
		{
			$offset = ($page - 1) * $limit;
		}
		$is_personal = 0;
		if(isset($_REQUEST['is_personal']) && $_REQUEST['is_personal'] == 1)
		{
			$is_personal = 1;
		}
		if(!$_REQUEST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "SELECT `initiator_user_id` AS ids FROM wp_bp_friends WHERE friend_user_id = ".$_REQUEST['user_id']." AND is_confirmed = 1 UNION
SELECT `friend_user_id` AS ids FROM wp_bp_friends WHERE  is_confirmed = 1  AND initiator_user_id = ".$_REQUEST['user_id'];
		$resource = $conn->query($sql);


		//if ($resource->num_rows > 0) {
				//$row = $resource->fetch_assoc();
				$ids = array();
				 while($row = $resource->fetch_assoc()) {
        			if($is_personal === 0)
					{
						$ids[] =  $row["ids"];
					}

				}
				$ids[] =  $_REQUEST['user_id'];

				if(isset($_REQUEST['type']) && $_REQUEST['type'] == "checkin")
				{

					$sql = "SELECT * FROM wp_bp_activity WHERE user_id IN  ( ".implode(',',$ids)." ) AND ( `type` != 'last_activity' AND `type` != 'activity_comment') AND is_checkedin = 1  ORDER BY date_recorded DESC  LIMIT $offset, $limit";

					$sql_count = "SELECT count(1) as total FROM wp_bp_activity WHERE user_id IN  ( ".implode(',',$ids)." ) AND ( `type` != 'last_activity' AND `type` != 'activity_comment') AND is_checkedin = 1  ORDER BY date_recorded DESC";
				}
				else
				{
					$sql = "SELECT * FROM wp_bp_activity WHERE user_id IN  ( ".implode(',',$ids)." ) AND ( `type` != 'last_activity' AND `type` != 'activity_comment')  ORDER BY date_recorded DESC  LIMIT $offset, $limit";

					$sql_count = "SELECT count(1) as total FROM wp_bp_activity WHERE user_id IN  ( ".implode(',',$ids)." ) AND ( `type` != 'last_activity' AND `type` != 'activity_comment')  ORDER BY date_recorded DESC";
				}

				$res = $conn->query($sql);
				$resource = $conn->query($sql_count);
				$total = $resource->fetch_assoc();
				if ($res->num_rows > 0) {
					//$row = $resource->fetch_assoc();
					$resultData = array();
					 while($row2 = $res->fetch_assoc()) {

						$resultData[] =  $row2;
					}
					$arr = array();
					$i=0;
					foreach($resultData as $row3)
					{
						$row3['session_user'] = $_REQUEST['user_id'];
						$arr[$i] = $this->get_formatted_activity_data($row3);
						$i++;
					}


					//$arr['total'] = $total['total'];
					//$arr['page'] = $page;

					echo json_encode(array("status"=>"ok","success"=>"success","data"=>$arr,'total_count'=>$total['total'],'current_page'=>$page),JSON_PRETTY_PRINT);
					die;


				}
				else
				{
					$oReturn = new stdClass();
					$oReturn->success = 'No activity found.';
					$oReturn->status = 'ok';
					$oReturn->data = array();

				}

		//}

		return $oReturn;

	}

	public function get_group_activities()
	{error_reporting(0);
		if(!$_REQUEST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('Wrong Group ID.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$arr = array();
		$sql = "SELECT * FROM wp_bp_activity WHERE user_id = ".$_REQUEST['user_id']." AND component = 'groups' AND item_id = ".$_REQUEST['group_id']." order by date_recorded desc";
		$resource = $conn->query($sql);
		if ($resource->num_rows > 0) {
			$i=0;
			 while($row = $resource->fetch_assoc()) {

					$result = $this->get_formatted_activity_data2($row);
					//if(!empty($result)){
					$arr[$i] = $result;
					//}$result
					$i++;

			 }
		}
		$oReturn = new stdClass();
		$oReturn->status = 'ok';
		$oReturn->success = 'success';
		if(empty($arr)){
			$oReturn->data = array();
		} else {
			$oReturn->data = $arr;
		}

		return $oReturn;

	}


	function get_user_data($user_id=NULL)
	{
		$user = get_userdata($user_id);
		preg_match('|src="(.+?)"|', get_avatar( $user_id, 32 ), $avatar);

		$userData = array(
				"id" => $user->ID,
				//"username" => $user->user_login,
				"nicename" => $user->user_nicename,
				//"email" => $user->user_email,
				"url" => $user->user_url,
				"display_name" => $user->display_name,
				"firstname" => $user->user_firstname,
				"lastname" => $user->last_name,
				"nickname" => $user->nickname,
				"avatar_thumb" => $avatar[1]
		   );
		   return $userData;
	}

	function get_formatted_activity_data($row)
	{
		$data = array();
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$arr = array();
		$arr['id'] = $row['id'];
		$arr['user_id'] = $row['user_id'];
		$arr['component'] = $row['component'];
		$arr['type'] = $row['type'];

		$userData = $this->get_user_data($row['user_id']);

		preg_match('|src="(.+?)"|', get_avatar( $row['user_id'], 32 ), $avatar);

		if($row['type']=='new_avatar'){
			$url = get_site_url() . '/members/'.$userData['user_login'];
			//$arr['action']  = '<a href="'.$url.'">'.$userData['display_name'].'</a> changed their profile picture. <br /><img src="'.$avatar[1].'" alt="" />';
			if($row['is_shared'] == 0 )
			{
			//$arr['action']  = '<a href="'.$url.'">'.$userData['display_name'].'</a> <span class="activity-bold">changed their profile picture.</span>';
			}
			else
			{

			}
            $arr['action'] = trim($row['action']);

//'<a href=\"http://52.35.93.122/kinectem/members/\">jack</a> changed their profile picture. <br /><img src=\"http://52.35.93.122/kinectem/wp-content/uploads/avatars/72/3bc542520d57470ac10480dad6931b99-bpthumb.jpg\" alt=\"\" />changed their profile picture.';
			//$arr['action']  = trim('Changed their profile picture.  /n<img class="full-image" src="'.$avatar[1].'" alt="" />');
		}else if($row['type']=='updated_profile'){
			if($oActivity->action=='' && $oActivity->content==''){
				$arr['action']  = 'Changed their profile';
			}
		}
		else {
			$arr['action'] = trim($row['action']);

		}


		$arr['content'] = base64_encode(do_shortcode($row['content']));
		$arr['primary_link'] = htmlentities($row['primary_link']);
		$arr['item_id'] = $row['item_id'];
		$arr['secondary_item_id'] = $row['secondary_item_id'];
		$arr['date_recorded'] = $row['date_recorded'];
		$arr['hide_sitewide'] = $row['hide_sitewide'];
		$arr['mptt_left'] = $row['mptt_left'];
		$arr['mptt_right'] = $row['mptt_right'];
		$arr['is_spam'] = $row['is_spam'];
		$arr['is_shared'] = $row['is_shared'];
		$arr['is_checkedin'] = $row['is_checkedin'];
		$arr['latitude'] = $row['latitude'];
		$arr['longitude'] = $row['longitude'];
		$arr['location'] = $row['location'];

		$sql = "SELECT COUNT(1) AS total_comments FROM wp_bp_activity WHERE item_id = ".$row['id']." AND TYPE = 'activity_comment'";
		$res = $conn->query($sql);

		$total_comment_count = 0;
		if ($res->num_rows > 0) {
			$total_comment_count = $res->fetch_assoc();
		}
		$arr['total_comments'] = $total_comment_count['total_comments'] ;



		//$user = new BP_Core_User($row3['user_id']);
		//$userData = $this->get_user_data($row['user_id']);
		$arr['user'] = $userData ;
		$comments = $this->get_activity_comments($row['id']);
		$arr['comments'] = $comments ;
		$sql = "SELECT meta_value FROM `wp_bp_activity_meta` WHERE meta_key = 'favorite_count' AND meta_value > 0 AND activity_id = ".$row['id'];
		$res = $conn->query($sql);

		$favData = bp_activity_get_user_favorites($row['session_user']);
		//echo "USER ID : ".$row['session_user'];print_r($favData);
		//echo "<br/>";
		$total_comment_count = 0;
		if (in_array($row['id'],$favData)) {
			$favoriteCount = $res->fetch_assoc();
			$arr['favorite'] = true;
			$arr['favorite_count'] = $favoriteCount['meta_value'];
		}
		else
		{
			$favoriteCount = $res->fetch_assoc();
			$arr['favorite'] = false;
			if(isset($favoriteCount['meta_value']) && $favoriteCount['meta_value'] != '')
			{
				$arr['favorite_count'] = $favoriteCount['meta_value'];
			}
			else
			{
				$arr['favorite_count'] = 0;
			}

		}

		return $arr;
	}

	function get_formatted_activity_data2($row)
	{
		$data = array();
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$arr = array();
		$arr['id'] = $row['id'];
		$arr['user_id'] = $row['user_id'];
		$arr['component'] = $row['component'];
		$arr['type'] = $row['type'];

		$userData = $this->get_user_data($row['user_id']);

		preg_match('|src="(.+?)"|', get_avatar( $row['user_id'], 32 ), $avatar);


		if($row['type']=='new_avatar'){
			$url = get_site_url() . '/members/'.$userData['user_login'];
			//$arr['action']  = '<a href="'.$url.'">'.$userData['display_name'].'</a> changed their profile picture. <br /><img src="'.$avatar[1].'" alt="" />';
			if($row['is_shared'] == 0 )
			{
			$arr['action']  = '<a href="'.$url.'">'.$userData['display_name'].'</a> <span class="activity-bold">changed their profile picture.</span>';
			}
			else
			{
				$arr['action'] = trim($row['action']);
			}


//'<a href=\"http://52.35.93.122/kinectem/members/\">jack</a> changed their profile picture. <br /><img src=\"http://52.35.93.122/kinectem/wp-content/uploads/avatars/72/3bc542520d57470ac10480dad6931b99-bpthumb.jpg\" alt=\"\" />changed their profile picture.';
			//$arr['action']  = trim('Changed their profile picture.  /n<img class="full-image" src="'.$avatar[1].'" alt="" />');
		}else if($row['type']=='updated_profile'){
			if($oActivity->action=='' && $oActivity->content==''){
				$arr['action']  = 'Changed their profile';
			}
		}
		else {
			$arr['action'] = trim($row['action']);

		}


		$arr['content'] = base64_encode(do_shortcode($row['content']));
		$arr['primary_link'] = htmlentities($row['primary_link']);
		$arr['item_id'] = $row['item_id'];
		$arr['secondary_item_id'] = $row['secondary_item_id'];
		$arr['date_recorded'] = $row['date_recorded'];
		$arr['hide_sitewide'] = $row['hide_sitewide'];
		$arr['mptt_left'] = $row['mptt_left'];
		$arr['mptt_right'] = $row['mptt_right'];
		$arr['is_spam'] = $row['is_spam'];
		$arr['is_shared'] = $row['is_shared'];
		$arr['is_checkedin'] = $row['is_checkedin'];
		$arr['latitude'] = $row['latitude'];
		$arr['longitude'] = $row['longitude'];
		$arr['location'] = $row['location'];

		$sql = "SELECT COUNT(1) AS total_comments FROM wp_bp_activity WHERE item_id = ".$row['id']." AND TYPE = 'activity_comment'";
		$res = $conn->query($sql);

		$total_comment_count = 0;
		if ($res->num_rows > 0) {
			$total_comment_count = $res->fetch_assoc();
		}
		$arr['total_comments'] = $total_comment_count['total_comments'] ;

		//$user = new BP_Core_User($row3['user_id']);
		//$userData = $this->get_user_data($row['user_id']);
		$arr['user'] = $userData ;
		$comments = $this->get_activity_comments($row['id']);
		$arr['comments'] = $comments ;
		$sql = "SELECT meta_value FROM `wp_bp_activity_meta` WHERE meta_key = 'favorite_count' AND meta_value > 0 AND activity_id = ".$row['id'];
		$res = $conn->query($sql);

		$total_comment_count = 0;
		if ($res->num_rows > 0) {
			$favoriteCount = $res->fetch_assoc();
			$arr['favorite'] = true;
			$arr['favorite_count'] = $favoriteCount['meta_value'];
		}
		else
		{
			$arr['favorite'] = false;
			$arr['favorite_count'] = "0";
		}

		return  $arr;
	}

	function get_activity_comments($id=NULL)
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$arr_comments = array();
		$sql = "SELECT *,CASE WHEN `item_id` = `secondary_item_id` THEN 'comment'
  ELSE 'replied' END AS comment_type  FROM wp_bp_activity WHERE `type` = 'activity_comment' AND item_id = ".$id." ORDER BY date_recorded ASC";
		$res = $conn->query($sql);

		if ($res->num_rows > 0) {
			 while($row = $res->fetch_assoc()) {

				$arr = array();
				$arr['id'] = $row['id'];
				$arr['user_id'] = $row['user_id'];
				$arr['component'] = $row['component'];
				$arr['type'] = $row['type'];
				$arr['action'] = addslashes(trim($row['action']));

				if($row['type']=='new_avatar'){
			//$oActivity->action = '<a href="'.$oActivity->primary_link.'">'.$oActivity->user_fullname.'</a> changed their profile picture. <br /><img src="'.$oActivity->avatar_thumb.'" alt="" />';
			$arr['action']  = addslashes(trim('Changed their profile picture. <br /><img class="full-image" src="'.$oActivity->avatar_full.'" alt="" />'));
		}else if($row['type']=='updated_profile'){
			if($oActivity->action=='' && $oActivity->content==''){
				$arr['action']  = 'Changed their profile';
			}
		}
		else {
			$arr['action'] = addslashes(trim($row['action']));

		}
				$arr['content'] = base64_encode(do_shortcode($row['content']));
				$arr['primary_link'] = htmlentities($row['primary_link']);
				$arr['item_id'] = $row['item_id'];
				$arr['secondary_item_id'] = $row['secondary_item_id'];
				$arr['date_recorded'] = $row['date_recorded'];
				$arr['hide_sitewide'] = $row['hide_sitewide'];
				$arr['mptt_left'] = $row['mptt_left'];
				$arr['mptt_right'] = $row['mptt_right'];
				$arr['is_spam'] = $row['is_spam'];
				$arr['comment_type'] = $row['comment_type'];
				$arr['is_shared'] = $row['is_shared'];
				$arr['is_checkedin'] = $row['is_checkedin'];
				$arr['latitude'] = $row['latitude'];
				$arr['longitude'] = $row['longitude'];
				$arr['location'] = $row['location'];

				$userData = $this->get_user_data($row['user_id']);
				$arr['user'] = $userData ;

				$arr_comments[] = $arr;
				$this->get_activity_comments($row['id']);
			 }
		}
		return $arr_comments;
	}

	function get_my_teams()
	{
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!isset($_REQUEST['page']))
		{
			$page = 1;
		}
		else
		{
			$page = $_REQUEST['page'];
		}
		if(!isset($_REQUEST['per_page']))
		{
			$per_page = 20;
		}
		else
		{
			$per_page = $_REQUEST['per_page'];
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type'] != 'all')
		{
			$user_id = $_REQUEST['user_id'];
		}
		else
		{
			$user_id = false;
		}

		$defaults = array(
		'type'              => false,    // active, newest, alphabetical, random, popular, most-forum-topics or most-forum-posts
		'order'             => 'DESC',   // 'ASC' or 'DESC'
		'orderby'           => 'date_created', // date_created, last_activity, total_member_count, name, random
		'user_id'           => $user_id,    // Pass a user_id to limit to only groups that this user is a member of
		'include'           => false,    // Only include these specific groups (group_ids)
		'exclude'           => false,    // Do not include these specific groups (group_ids)
		'search_terms'      => false,    // Limit to groups that match these search terms
		'meta_query'        => false,    // Filter by groupmeta. See WP_Meta_Query for syntax
		'show_hidden'       => true,    // Show hidden groups to non-admins
		'per_page'          => $per_page,       // The number of results to return per page
		'page'              => $page,        // The page to return if limiting per page
		'populate_extras'   => true,     // Fetch meta such as is_banned and is_member
		'update_meta_cache' => true,   // Pre-fetch groupmeta for queried groups
	);

		$r = wp_parse_args( $args, $defaults );

		$groups = BP_Groups_Group::get( array(
			'type'              => $r['type'],
			'user_id'           => $r['user_id'],
			'include'           => $r['include'],
			'exclude'           => $r['exclude'],
			'search_terms'      => $r['search_terms'],
			'meta_query'        => $r['meta_query'],
			'show_hidden'       => $r['show_hidden'],
			'per_page'          => $r['per_page'],
			'page'              => $r['page'],
			'populate_extras'   => $r['populate_extras'],
			'update_meta_cache' => $r['update_meta_cache'],
			'order'             => $r['order'],
			'orderby'           => $r['orderby'],

		) );

		$i=0;$arr=array();
		foreach($groups['groups'] as $row)
		{

			$useravatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$row->id, 'html'=>false, 'type'=>'full'));
			$arr[$i] = $row;
			$arr[$i]->avatar = $useravatar_url;
			$i++;

		}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$arr_comments = array();
		$finalResult = array();
		$i=0;
		foreach($arr as $row1)
		{


			$arr_comments[$i] = $row1;

			if($row1->creator_id == $_REQUEST['user_id'])
			{
				$arr_comments[$i]->is_admin = 1;
			}
			else
			{
				$arr_comments[$i]->is_admin = 0;
			}
			$isMember = groups_is_user_member($_REQUEST['user_id'],$row1->id);
			$isBanned = groups_is_user_banned($_REQUEST['user_id'],$row1->id);
			//echo "<br/>".$isMember . "  -> ". $_REQUEST['user_id'] . " -> ". $row1->id;
			$arr_comments[$i]->is_member = $isMember;
			$arr_comments[$i]->is_banned = is_null($isBanned) ? 0 : 1;


			$oReturn->is_invited = groups_check_user_has_invite((int) $_REQUEST['user_id'], $row1->id, $this->type);
			$arr_comments[$i]->is_invited = is_null($oReturn->is_invited) ? 0 : 1;

			$oReturn->membership_requested = groups_check_for_membership_request((int) $_REQUEST['user_id'], $row1->id);
			$arr_comments[$i]->is_pending = $oReturn->membership_requested;

			$sql = "SELECT id FROM `wp_bp_team_follow` WHERE author_id = ".$_REQUEST['user_id']." AND group_id = ".$row1->id."";
			$res = $conn->query($sql);
			if ($res->num_rows > 0) {
				$row3 = $res->fetch_assoc();

				if(!empty($row3))
				{

					$arr_comments[$i]->is_follow = 1;
				}
				else
				{
					$arr_comments[$i]->is_follow = 0;
				}
			}else{
				$arr_comments[$i]->is_follow = 0;
			}
			$arr_comments['total'] = $groups['total'];

			$finalResult[] = $arr_comments[$i];
			$i++;
		}

		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $finalResult;
		$oReturn->current_page  = $page;
		$oReturn->total_count = $groups['total'];
		return $oReturn;
	}

	function search_team()
	{

		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		$type = "all";
		$oReturn = new stdClass();
		if(isset($_REQUEST['type']))
		{
			$type = $_REQUEST['type'];
		}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$condition = "";
		if(isset($_REQUEST['keyword']) &&  $_REQUEST['keyword'] != '')
		{
			$condition = "WHERE `name` LIKE '%".$_REQUEST['keyword']."%' and status != 'Hidden'";
		}

		$sql = "SELECT * FROM `wp_bp_groups` wg
INNER JOIN `wp_users` wu ON wu.`ID` = wg.`creator_id` $condition ";
		$res = $conn->query($sql);
		$arr = array();

		if ($res->num_rows > 0) {
			$i=0;
			while($row = $res->fetch_assoc()) {

				$isMember = groups_is_user_member($_REQUEST['user_id'],$row['id']);

				$arr[$i] = $row;
				if($row['creator_id'] == $_REQUEST['user_id'])
				{
					$arr[$i]['is_admin'] = 1;
				}
				else
				{
					$arr[$i]['is_admin'] = 0;
				}


				$useravatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$row['id'], 'html'=>false, 'type'=>'full'));
				$arr[$i]['avatar'] = $useravatar_url;
				//$isMember = groups_is_user_member($_REQUEST['user_id'],$row['id']);
				$isBanned = groups_is_user_banned($_REQUEST['user_id'],$row['id']);
				//echo "<br/>".$isMember . "  -> ". $_REQUEST['user_id'] . " -> ". $row1->id;
				$arr[$i]['is_member'] = $isMember;
				$arr[$i]['is_banned'] = is_null($isBanned) ? 0 : 1;


				$oReturn->is_invited = groups_check_user_has_invite((int) $_REQUEST['user_id'], $row['id'], $this->type);
				$arr[$i]['is_invited'] = is_null($oReturn->is_invited) ? 0 : 1;

				$oReturn->membership_requested = groups_check_for_membership_request((int) $_REQUEST['user_id'], $row['id']);
				$arr[$i]['is_pending'] = $oReturn->membership_requested;

				$sql = "SELECT id FROM `wp_bp_team_follow` WHERE author_id = ".$_REQUEST['user_id']." AND group_id = ".$row['id']."";
				$re2s = $conn->query($sql);
				if ($res2->num_rows > 0) {
					$row3 = $res2->fetch_assoc();

					if(!empty($row3))
					{

						$arr[$i]['is_follow'] = 1;
					}
					else
					{
						$arr[$i]['is_follow'] = 0;
					}
				}else{
					$arr[$i]['is_follow'] = 0;
				}

				$i++;

			}
		}

		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $arr;
		return $oReturn;

	}

	function update_user_email() {

	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	if(!$_REQUEST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['pwd']){$oReturn->error = __('Password is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['email']){$oReturn->error = __('Email is required.','aheadzen'); return $oReturn;}
	// Define local defaults
	$bp            = buddypress(); // The instance
	$email_error   = false;        // invalid|blocked|taken|empty|nochange
	$pass_error    = false;        // invalid|mismatch|empty|nochange
	$pass_changed  = false;        // true if the user changes their password
	$email_changed = false;        // true if the user changes their email
	$feedback_type = 'error';      // success|error
	$feedback      = array();      // array of strings for feedback

	// Nonce check
	//check_admin_referer('bp_settings_general');
	$update_user = get_userdata($_POST['user_id']);

	// Validate the user again for the current password when making a big change
	if (  ( !empty( $_POST['pwd'] ) && wp_check_password( $_POST['pwd'], $update_user->user_pass, $_POST['user_id'] ) ) ) {

		//$update_user = get_userdata( $_POST['user_id'] );

		/** Email Change Attempt ******************************************/

		if ( !empty( $_POST['email'] ) ) {

			// What is missing from the profile page vs signup -
			// let's double check the goodies
			$user_email     = sanitize_email( esc_html( trim( $_POST['email'] ) ) );
			$old_user_email = $update_user->user_email;

			// User is changing email address
			if ( $old_user_email != $user_email ) {

				// Run some tests on the email address
				$email_checks = bp_core_validate_email_address( $user_email );
				if ( true !== $email_checks ) {
					if ( isset( $email_checks['invalid'] ) ) {
						$email_error = 'invalid';
					}

					if ( isset( $email_checks['domain_banned'] ) || isset( $email_checks['domain_not_allowed'] ) ) {
						$email_error = 'blocked';
					}

					if ( isset( $email_checks['in_use'] ) ) {
						$email_error = 'taken';
					}
				}

				// Store a hash to enable email validation
				if ( false === $email_error ) {
					$hash = wp_hash( $_POST['email'] );

					$pending_email = array(
						'hash'     => $hash,
						'newemail' => $user_email,
					);

					bp_update_user_meta( $_POST['user_id'], 'pending_email_change', $pending_email );

					$email_text = sprintf(
						__( 'Dear %1$s,

You recently changed the email address associated with your account on %2$s.
If this is correct, please click on the following link to complete the change:
%3$s

You can safely ignore and delete this email if you do not want to take this action or if you have received this email in error.

This email has been sent to %4$s.

Regards,
%5$s
%6$s', 'buddypress' ),
						bp_core_get_user_displayname( $_POST['user_id'] ),
						bp_get_site_name(),
						esc_url( get_site_url() . '/members/'.$update_user->user_login.'/settings?verify_email_change=' . $hash ),
						$user_email,
						bp_get_site_name(),
						bp_get_root_domain()
					);


					$content = apply_filters( 'bp_new_user_email_content', $email_text, $user_email, $old_user_email, $update_user );


					// Send the verification email
					wp_mail( $user_email, sprintf( __( '[%s] Verify your new email address', 'buddypress' ), wp_specialchars_decode( bp_get_site_name() ) ), $content );

					// We mark that the change has taken place so as to ensure a
					// success message, even though verification is still required
					$_POST['email'] = $update_user->user_email;
					$email_changed = true;
				}

			// No change
			} else {
				$email_error = false;
			}

		// Email address cannot be empty
		} else {
			$email_error = 'empty';
		}


		// The structure of the $update_user object changed in WP 3.3, but
		// wp_update_user() still expects the old format
		if ( isset( $update_user->data ) && is_object( $update_user->data ) ) {
			$update_user = $update_user->data;
			$update_user = get_object_vars( $update_user );


			// user's user_pass field in the database.
			// @see wp_update_user()
			if ( false === $pass_changed ) {
				unset( $update_user['user_pass'] );
			}
		}

		// Clear cached data, so that the changed settings take effect
		// on the current page load
		if ( ( false === $email_error ) && ( false === $pass_error ) && ( wp_update_user( $update_user ) ) ) {
			wp_cache_delete( 'bp_core_userdata_' . bp_displayed_user_id(), 'bp' );
			$bp->displayed_user->userdata = bp_core_get_core_userdata( bp_displayed_user_id() );
		}

	// Password Error
	} else {
		$pass_error = 'invalid';
	}

	// Email feedback
	switch ( $email_error ) {
		case 'invalid' :
			$feedback['email_invalid']  = __( 'That email address is invalid. Check the formatting and try again.', 'buddypress' );
			break;
		case 'blocked' :
			$feedback['email_blocked']  = __( 'That email address is currently unavailable for use.', 'buddypress' );
			break;
		case 'taken' :
			$feedback['email_taken']    = __( 'That email address is already taken.', 'buddypress' );
			break;
		case 'empty' :
			$feedback['email_empty']    = __( 'Email address cannot be empty.', 'buddypress' );
			break;
		case false :
			// No change
			break;
	}

	// Password feedback
	switch ( $pass_error ) {
		case 'invalid' :
			$feedback['pass_error']    = __( 'Your current password is invalid.', 'buddypress' );
			break;
		case 'mismatch' :
			$feedback['pass_mismatch'] = __( 'The new password fields did not match.', 'buddypress' );
			break;
		case 'empty' :
			$feedback['pass_empty']    = __( 'One of the password fields was empty.', 'buddypress' );
			break;
		case 'same' :
			$feedback['pass_same'] 	   = __( 'The new password must be different from the current password.', 'buddypress' );
			break;
		case false :
			// No change
			break;
	}


	// No errors so show a simple success message
	if ( ( ( false === $email_error ) || ( false == $pass_error ) ) && ( ( true === $pass_changed ) || ( true === $email_changed ) ) ) {
		$feedback[]    = __( 'Your settings have been saved.', 'buddypress' );
		$feedback_type = 'success';
		$oReturn = new stdClass();
		$oReturn->success = 'Your email have been updated.';
		$oReturn->status = 'ok';
		$oReturn->data = array();
		return $oReturn;


	// Some kind of errors occurred
	} elseif ( ( ( false === $email_error ) || ( false === $pass_error ) ) && ( ( false === $pass_changed ) || ( false === $email_changed ) ) ) {
		if ( bp_is_my_profile() ) {
			$feedback['nochange'] = __( 'No changes were made to your account.', 'buddypress' );
			$oReturn = new stdClass();
			$oReturn->error = 'No changes were made to your account.';
			$oReturn->status = 'error';
			$oReturn->data = array();
			return $oReturn;
		} else {
			//$feedback['nochange'] = __( 'No changes were made to this account.', 'buddypress' );
			$oReturn = new stdClass();
			$oReturn->error = 'No changes were made to your account.';
			$oReturn->status = 'error';
			$oReturn->data = array();
			return $oReturn;
		}
	}

	return $oReturn;
}


	function update_email() {

	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	if(!$_REQUEST['user_id']){$oReturn->error = __('Wrong User ID.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['cookie']){$oReturn->error = __('Cookie is required.','aheadzen'); return $oReturn;}
	//if(!$_REQUEST['pwd']){$oReturn->error = __('Password is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['email']){$oReturn->error = __('Email is required.','aheadzen'); return $oReturn;}
	// Define local defaults
	$bp            = buddypress(); // The instance
	$email_error   = false;        // invalid|blocked|taken|empty|nochange
	$pass_error    = true;        // invalid|mismatch|empty|nochange
	$pass_changed  = true;        // true if the user changes their password
	$email_changed = true;        // true if the user changes their email
	$feedback_type = 'error';      // success|error
	$feedback      = array();      // array of strings for feedback

	// Nonce check
	//check_admin_referer('bp_settings_general');
	$update_user = get_userdata($_POST['user_id']);

	$valid = wp_validate_auth_cookie($_REQUEST['cookie'], 'logged_in');

	// Validate the user again for the current password when making a big change
	if (  $valid == $_REQUEST['user_id'] ) {

		//$update_user = get_userdata( $_POST['user_id'] );

		/** Email Change Attempt ******************************************/

		if ( !empty( $_POST['email'] ) ) {

			// What is missing from the profile page vs signup -
			// let's double check the goodies
			$user_email     = sanitize_email( esc_html( trim( $_POST['email'] ) ) );
			$old_user_email = $update_user->user_email;

			// User is changing email address
			if ( $old_user_email != $user_email ) {

				// Run some tests on the email address
				$email_checks = bp_core_validate_email_address( $user_email );
				if ( true !== $email_checks ) {
					if ( isset( $email_checks['invalid'] ) ) {
						$email_error = 'invalid';
					}

					if ( isset( $email_checks['domain_banned'] ) || isset( $email_checks['domain_not_allowed'] ) ) {
						$email_error = 'blocked';
					}

					if ( isset( $email_checks['in_use'] ) ) {
						$email_error = 'taken';
					}
				}

				// Store a hash to enable email validation
				if ( false === $email_error ) {
					$hash = wp_hash( $_POST['email'] );

					$pending_email = array(
						'hash'     => $hash,
						'newemail' => $user_email,
					);

					bp_update_user_meta( $_POST['user_id'], 'pending_email_change', $pending_email );

					$email_text = sprintf(
						__( 'Dear %1$s,

You recently changed the email address associated with your account on %2$s.
If this is correct, please click on the following link to complete the change:
%3$s

You can safely ignore and delete this email if you do not want to take this action or if you have received this email in error.

This email has been sent to %4$s.

Regards,
%5$s
%6$s', 'buddypress' ),
						bp_core_get_user_displayname( $_POST['user_id'] ),
						bp_get_site_name(),
						esc_url( get_site_url() . '/members/'.$update_user->user_login.'/settings?verify_email_change=' . $hash ),
						$user_email,
						bp_get_site_name(),
						bp_get_root_domain()
					);


					$content = apply_filters( 'bp_new_user_email_content', $email_text, $user_email, $old_user_email, $update_user );


					// Send the verification email
					wp_mail( $user_email, sprintf( __( '[%s] Verify your new email address', 'buddypress' ), wp_specialchars_decode( bp_get_site_name() ) ), $content );

					// We mark that the change has taken place so as to ensure a
					// success message, even though verification is still required
					$_POST['email'] = $update_user->user_email;
					$email_changed = true;
				}

			// No change
			} else {
				$email_error = 'taken';
			}

		// Email address cannot be empty
		} else {
			$email_error = 'empty';
		}


		// The structure of the $update_user object changed in WP 3.3, but
		// wp_update_user() still expects the old format
		if ( isset( $update_user->data ) && is_object( $update_user->data ) ) {
			$update_user = $update_user->data;
			$update_user = get_object_vars( $update_user );


			// user's user_pass field in the database.
			// @see wp_update_user()
			if ( false === $pass_changed ) {
				unset( $update_user['user_pass'] );
			}
		}

		// Clear cached data, so that the changed settings take effect
		// on the current page load
		if ( ( false === $email_error ) && ( false === $pass_error ) && ( wp_update_user( $update_user ) ) ) {
			wp_cache_delete( 'bp_core_userdata_' . bp_displayed_user_id(), 'bp' );
			$bp->displayed_user->userdata = bp_core_get_core_userdata( bp_displayed_user_id() );
		}

	// Password Error
	} else {
		$pass_error = 'invalid';
	}

	// Email feedback
	switch ( $email_error ) {
		case 'invalid' :
			$feedback['email_invalid']  = __( 'That email address is invalid. Check the formatting and try again.', 'buddypress' );
			break;
		case 'blocked' :
			$feedback['email_blocked']  = __( 'That email address is currently unavailable for use.', 'buddypress' );
			break;
		case 'taken' :
			$feedback['email_taken']    = __( 'That email address is already taken.', 'buddypress' );
			break;
		case 'empty' :
			$feedback['email_empty']    = __( 'Email address cannot be empty.', 'buddypress' );
			break;
		case false :
			// No change
			break;
	}

	// Password feedback
	switch ( $pass_error ) {
		case 'invalid' :
			$feedback['pass_error']    = __( 'Your current password is invalid.', 'buddypress' );
			break;
		case 'mismatch' :
			$feedback['pass_mismatch'] = __( 'The new password fields did not match.', 'buddypress' );
			break;
		case 'empty' :
			$feedback['pass_empty']    = __( 'One of the password fields was empty.', 'buddypress' );
			break;
		case 'same' :
			$feedback['pass_same'] 	   = __( 'The new password must be different from the current password.', 'buddypress' );
			break;
		case false :
			// No change
			break;
	}

	// No errors so show a simple success message
	if ( ( ( false === $email_error ) ) && ( ( true === $pass_changed ) || ( true === $email_changed ) ) ) {
		$feedback[]    = __( 'Your settings have been saved.', 'buddypress' );
		$feedback_type = 'success';
		$oReturn = new stdClass();
		$oReturn->success = 'Your email have been updated.';
		$oReturn->status = 'ok';
		$oReturn->data = array();
		return $oReturn;


	// Some kind of errors occurred
	} elseif ( ( ( false === $email_error ) || ( false === $pass_error ) ) && ( ( false === $pass_changed ) || ( false === $email_changed ) ) ) {
		if ( bp_is_my_profile() ) {
			$feedback['nochange'] = __( 'No changes were made to your account.', 'buddypress' );
			$oReturn = new stdClass();
			$oReturn->error = 'No changes were made to your account.';
			$oReturn->status = 'error';
			$oReturn->data = array('email_error'=>$email_error,'pass_changed'=>$pass_changed, 'email_changed' => $email_changed);
			return $oReturn;
		} else {
			//$feedback['nochange'] = __( 'No changes were made to this account.', 'buddypress' );
			$oReturn = new stdClass();
			$oReturn->error = 'No changes were made to your account.';
			$oReturn->status = 'error';
			$oReturn->data = array('email_error'=>$email_error,'pass_changed'=>$pass_changed, 'email_changed' => $email_changed);
			return $oReturn;
		}
	} else {
			$oReturn = new stdClass();
			$oReturn->error = 'No changes were made to your account.';
			$oReturn->status = 'error';
			$oReturn->data = array('email_error'=>$email_error,'pass_changed'=>$pass_changed, 'email_changed' => $email_changed);
			return $oReturn;
	}

	//return $oReturn;
}


function follow_team()
{
	if(!$_REQUEST['author_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
	global $wpdb;
    $table_check=$wpdb->prefix."bp_team_follow";
    $authorid=$_REQUEST['author_id'];
    $groupid=$_REQUEST['group_id'];

	$data=$wpdb->insert(
            $table_check,
            array('author_id' => $authorid,'group_id' => $groupid),
            array('%d','%d')
          );
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	$sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$_REQUEST['group_id'];
	$res = $conn->query($sql);

	if ($res->num_rows > 0)
			 $row = $res->fetch_assoc();

	// Notification data making
	$userDataObj= get_userdata($authorid);
	$friendDataObj = get_userdata($row['creator_id']);
	$userData = $userDataObj->data;
	$friendData = $friendDataObj->data;
	$friendData->notification_type_id = 6;

	$message = $userData->display_name. " follows your group ".$row['name'];
	$this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);

    $oReturn = new stdClass();
	$oReturn->success = 'success';
	$oReturn->status = 'ok';
	$oReturn->data = array();
	return $oReturn;
}


function unfollow_team(){
    global $wpdb;
	if(!$_REQUEST['author_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
    $table_check=$wpdb->prefix."bp_team_follow";
    $authorid=$_REQUEST['author_id'];
    $groupid=$_REQUEST['group_id'];
    $wpdb->query($wpdb->prepare(" DELETE FROM $table_check WHERE author_id = %d AND group_id = %d", $authorid, $groupid));
    $oReturn = new stdClass();
	$oReturn->success = 'success';
	$oReturn->status = 'ok';
	$oReturn->data = array();
	return $oReturn;

}


function request_membership_for_group()
{
	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}

	$res = groups_send_membership_request( $_REQUEST['user_id'], $_POST['group_id'] ) ;

	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$arr = array();
	$sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$_REQUEST['group_id'];
	$res = $conn->query($sql);

	if ($res->num_rows > 0) {
			 $i=0;
			 $row = $res->fetch_assoc();
			 // Notification data making
			$userDataObj= get_userdata($_REQUEST['user_id']);
			$friendDataObj = get_userdata($row['creator_id']);
			$userData = $userDataObj->data;
			$friendData = $friendDataObj->data;
			$friendData->notification_type_id = 7;

			$message = $userData->display_name. " sent you a membership request for the group ".$row['name'];
			$this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);
	}

	if($res)
	{
		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = array();
		return $oReturn;
	}
	else
	{
		$oReturn = new stdClass();
		$oReturn->error = 'error';
		$oReturn->status = '';
		$oReturn->data = array();
		return $oReturn;
	}


}


function get_team_cover_photo_return($group_id=NULL)
{
	$r = bp_parse_args( $args, array(
		'object_dir' => 'groups',
		'item_id'    => $group_id,
		'type'       => 'cover-image',
		'file'       => '',
	), 'attachments_get_attachment_src' );
		// Get BuddyPress Attachments Uploads Dir datas
	$bp_attachments_uploads_dir = bp_attachments_uploads_dir_get();

	// The BP Attachments Uploads Dir is not set, stop.
	if ( ! $bp_attachments_uploads_dir ) {
		return $attachment_data;
	}

	$type_subdir = $r['object_dir'] . '/' . $r['item_id'] . '/' . $r['type'];
	$type_dir    = trailingslashit( $bp_attachments_uploads_dir['basedir'] ) . $type_subdir;

	if ( ! is_dir( $type_dir ) ) {
		return $attachment_data;
	}

	if ( ! empty( $r['file'] ) ) {
		if ( ! file_exists( trailingslashit( $type_dir ) . $r['file'] ) ) {
			return $attachment_data;
		}

		if ( 'url' === $data ) {
			$attachment_data = trailingslashit( $bp_attachments_uploads_dir['baseurl'] ) . $type_subdir . '/' . $r['file'];
		} else {
			$attachment_data = trailingslashit( $type_dir ) . $r['file'];
		}

	} else {
		$file = false;

		// Open the directory and get the first file
		if ( $att_dir = opendir( $type_dir ) ) {

			while ( false !== ( $attachment_file = readdir( $att_dir ) ) ) {
				// Look for the first file having the type in its name
				if ( false !== strpos( $attachment_file, $r['type'] ) && empty( $file ) ) {
					$file = $attachment_file;
					break;
				}
			}
		}

		if ( empty( $file ) ) {
			return $attachment_data;
		}

		if ( 'url' === $data ) {
			$attachment_data = trailingslashit( $bp_attachments_uploads_dir['baseurl'] ) . $type_subdir . '/' . $file;
		} else {
			$attachment_data = trailingslashit( $type_dir ) . $file;
		}
	}
	return get_site_url()."/wp-content/uploads/buddypress/groups/".$_REQUEST['group_id']."/cover-image/".$file;


}


function get_team_cover_photo()
{
	if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
	$group_id = $_REQUEST['group_id'];
	$r = bp_parse_args( $args, array(
		'object_dir' => 'groups',
		'item_id'    => $group_id,
		'type'       => 'cover-image',
		'file'       => '',
	), 'attachments_get_attachment_src' );
		// Get BuddyPress Attachments Uploads Dir datas
	$bp_attachments_uploads_dir = bp_attachments_uploads_dir_get();

	// The BP Attachments Uploads Dir is not set, stop.
	if ( ! $bp_attachments_uploads_dir ) {
		return $attachment_data;
	}

	$type_subdir = $r['object_dir'] . '/' . $r['item_id'] . '/' . $r['type'];
	$type_dir    = trailingslashit( $bp_attachments_uploads_dir['basedir'] ) . $type_subdir;

	if ( ! is_dir( $type_dir ) ) {
		return $attachment_data;
	}

	if ( ! empty( $r['file'] ) ) {
		if ( ! file_exists( trailingslashit( $type_dir ) . $r['file'] ) ) {
			return $attachment_data;
		}

		if ( 'url' === $data ) {
			$attachment_data = trailingslashit( $bp_attachments_uploads_dir['baseurl'] ) . $type_subdir . '/' . $r['file'];
		} else {
			$attachment_data = trailingslashit( $type_dir ) . $r['file'];
		}

	} else {
		$file = false;

		// Open the directory and get the first file
		if ( $att_dir = opendir( $type_dir ) ) {

			while ( false !== ( $attachment_file = readdir( $att_dir ) ) ) {
				// Look for the first file having the type in its name
				if ( false !== strpos( $attachment_file, $r['type'] ) && empty( $file ) ) {
					$file = $attachment_file;
					break;
				}
			}
		}

		if ( empty( $file ) ) {
			return $attachment_data;
		}

		if ( 'url' === $data ) {
			$attachment_data = trailingslashit( $bp_attachments_uploads_dir['baseurl'] ) . $type_subdir . '/' . $file;
		} else {
			$attachment_data = trailingslashit( $type_dir ) . $file;
		}
	}
	$oReturn = new stdClass();
	$oReturn->error = 'success';
	$oReturn->status = 'ok';
	$oReturn->cover_photo =  get_site_url()."/wp-content/uploads/buddypress/groups/".$_REQUEST['group_id']."/cover-image/".$file;
	return $oReturn;


}

	function get_cover_pic()
	{
		// variable usage 
		//echo do_shortcode( '[iscorrect]' . $text_to_be_wrapped_in_shortcode . '[/iscorrect]' );
		    $arr = array();
			$arr['content'] =  stripcslashes('Text and image\n<div class="bpfb_images">\r\n\t\t\t\t\t\t\t\t<a href="http: \/\/52.35.93.122\/kinectem\/wp-content\/uploads\/bpfb\/24_0-94325100-1472556011_curren-brand-quartz-watches-men-military-fashion-black-men-watch-analog-clock-men-sports-wrist-watch13.jpg" class="thickbox" rel="1c0846f4258d82f5ec61d6f994e75204" >\r\n\t\t\t<img src="http: \/\/52.35.93.122\/kinectem\/wp-content\/uploads\/bpfb\/24_0-94325100-1472556011_curren-brand-quartz-watches-men-military-fashion-black-men-watch-analog-clock-men-sports-wrist-watch13-bpfbt.jpg" \/>\r\n\t\t<\/a>\r\n\t<\/div>');
             $arr['primary_link'] = "http:\/\/52.35.93.122\/kinectem\/members\/nimisha\/";
			 echo json_encode($arr,JSON_PRETTY_PRINT);
			 die;


		echo do_shortcode('
[bpfb_video]http://www.ted.com/talks/james_green_3_moons_and_a_planet_that_could_have_alien_life[/bpfb_video]');
		//$result = do_shortcode('[bpfb_video]'); 
		//echo $result;
		die;

	}

	function prettyPrint( $json )
	{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}

function star_unstar_messages() {



	if(!$_REQUEST['action']){$oReturn->error = __('action is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['message_id']){$oReturn->error = __('message_id is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['thread_id']){$oReturn->error = __('thread_id is required.','aheadzen'); return $oReturn;}
	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	try{

		$arr = explode(",",$_REQUEST['message_id']);
		$arr2 = explode(",",$_REQUEST['thread_id']);
		$i=0;
		foreach($arr as $row){
			// Mark the star.
			bp_messages_star_set_action( array(
				'action'     => $_REQUEST['action'],
				'thread_id' => $row2[$i],
				'message_id' => $row,
				'user_id'    => $_REQUEST['user_id']


			) );
			$i++;
		}

		// Redirect back to previous screen.
		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = array();
	}catch(Exception $e)
	{
		$oReturn = new stdClass();
		$oReturn->error = 'error';
		$oReturn->status = 'error';
		$oReturn->data = array();

	}
	return $oReturn;

}


function get_list_of_starred_messages()
{
	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$arr = array();
	 /*$sql = "SELECT wpm.*,wu.`display_name` as sender_display_name, wu.`user_login` as sender_username, wu.`user_nicename` as sender_nickname FROM
   `wp_bp_messages_messages` wpm

  INNER JOIN `wp_users` wu ON wu.`ID` = wpm.`sender_id`
	WHERE wpm.`id` IN
	  (SELECT message_id FROM
		`wp_bp_messages_meta`
	  WHERE meta_key = 'starred_by_user'
		AND meta_value = ".$_REQUEST['user_id'].") AND sender_id = ".$_REQUEST['user_id']."";*/

	$sql = "SELECT
  wpm.*,
  wu.`display_name` AS sender_display_name,
  wu.`user_login` AS sender_username,
  wu.`user_nicename` AS sender_nickname
FROM
  `wp_bp_messages_messages` wpm
  INNER JOIN `wp_users` wu
    ON wu.`ID` = wpm.`sender_id`
  INNER JOIN `wp_bp_messages_recipients` wr
    ON wr.`thread_id` = wpm.`thread_id`
WHERE wpm.`id` IN
  (SELECT
    message_id
  FROM
    `wp_bp_messages_meta`
  WHERE meta_key = 'starred_by_user'
    AND meta_value = ".$_REQUEST['user_id'].")
   AND wr.`is_deleted` = 0 AND wr.`user_id` = ".$_REQUEST['user_id']."";
	$res = $conn->query($sql);

	if ($res->num_rows > 0) {
			 $i=0;
			 while($row = $res->fetch_assoc()) {
			 	$useravatar_url = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$_REQUEST['user_id'], 'html'=>false, 'type'=>'full'));
				$arr[$i] = $row;
				$arr[$i]['sender_avatar'] = $useravatar_url;

				$sql = "SELECT wu.ID,wu.`display_name` AS receiver_display_name, wu.`user_login` AS receiver_username, wu.`user_nicename` AS receiver_nickname FROM `wp_bp_messages_recipients` wr
INNER JOIN `wp_users` wu ON wu.`ID` = wr.`user_id`
WHERE wr.user_id != ".$_REQUEST['user_id']." AND wr.thread_id = ".$row['thread_id']."";
				$res2 = $conn->query($sql);
				$row2 = $res2->fetch_assoc();
				$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row2['ID'], 'html'=>false, 'type'=>'full'));

				$sql3 = "SELECT unread_count FROM `wp_bp_messages_recipients` WHERE user_id = ".$_REQUEST['user_id']." AND thread_id = ".$row['thread_id']."
";
				$res3 = $conn->query($sql3);
				$row3 = $res3->fetch_assoc();
				if(empty($row3) || $row3['unread_count'] == 0){
					$arr[$i]['is_read'] = false;
				} else {
					$arr[$i]['is_read'] = true;
				}

				$sql_count = "SELECT COUNT(*) as thread_count FROM `wp_bp_messages_messages` WHERE thread_id = ".$row['thread_id']."";
				$res_count = $conn->query($sql_count);
				$threadCountData = $res_count->fetch_assoc();
				$arr[$i]['thread_count'] = $threadCountData['thread_count'];
				$arr[$i]['receiver_id'] = $row2['ID'];
				$arr[$i]['receiver_display_name'] = $row2['receiver_display_name'];
				$arr[$i]['receiver_username'] = $row2['receiver_username'];
				$arr[$i]['receiver_nickname'] = $row2['receiver_nickname'];
				$arr[$i]['receiver_avatar'] = $useravatar_url2;

				//$arr[$i] = $useravatar_url;
				$i++;
			 }

			$oReturn = new stdClass();
			$oReturn->success = 'success';
			$oReturn->status = 'ok';
			$oReturn->data = $arr;
	}
	else
	{
			$oReturn = new stdClass();
			$oReturn->success = 'success';
			$oReturn->status = 'ok';
			$oReturn->data = array();
	}

	return $oReturn;


}

function get_list_of_sent_messages()
{
	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$arr = array();
	/*$sql = "SELECT wm.*, wu.`user_nicename` as sender_nicename,wu.`display_name` AS sender_display_name, wu.`user_login` as sender_username, wu.`user_email` as sender_email FROM `wp_bp_messages_messages` wm
INNER JOIN wp_users wu ON wu.`ID` = wm.`sender_id`
WHERE wm.sender_id = ".$_REQUEST['user_id']."";*/
  $sql = "SELECT
  wm.*,
  wu.`user_nicename` AS sender_nicename,
  wu.`display_name` AS sender_display_name,
  wu.`user_login` AS sender_username,
  wu.`user_email` AS sender_email,
  wr.`user_id`,wr.`is_deleted`
FROM
  `wp_bp_messages_messages` wm
  INNER JOIN wp_users wu
    ON wu.`ID` = wm.`sender_id`
  INNER JOIN `wp_bp_messages_recipients` wr
    ON wr.`thread_id` = wm.`thread_id`
WHERE wm.sender_id = ".$_REQUEST['user_id']." AND wr.`is_deleted` = 0 AND wr.`user_id` = ".$_REQUEST['user_id']." GROUP BY thread_id";
	$res = $conn->query($sql);

	if ($res->num_rows > 0) {
			 $i=0;
			 while($row = $res->fetch_assoc()) {
			 	$useravatar_url = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$_REQUEST['user_id'], 'html'=>false, 'type'=>'full'));


				//$arr[$i] = $row;
				$arr[$i]['sender_avatar'] = $useravatar_url;
				$arr[$i]['sender_nicename'] = $row['sender_nicename'];
				$arr[$i]['sender_display_name'] = $row['sender_display_name'];
				$arr[$i]['sender_username'] = $row['sender_username'];
				$arr[$i]['sender_email'] = $row['sender_email'];
				$arr[$i]['thread_id'] = $row['thread_id'];
				$arr[$i]['message_id'] = $row['id'];
				$arr[$i]['date_sent'] = $row['date_sent'];
				$arr[$i]['subject'] = $row['subject'];
				$arr[$i]['message'] = $row['message'];

				$sql_rec = "SELECT * FROM `wp_bp_messages_recipients` WHERE thread_id = ".$row['thread_id'];
				$res_rec= $conn->query($sql_rec);
				$recipientData = array();
				$j=0;
				while($row22 = $res_rec->fetch_assoc()){

					$user = get_userdata( $row22['user_id'] );
					$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row22['user_id'], 'html'=>false, 'type'=>'full'));
					$user->data->avatar = $useravatar_url2;

					if(isset($user->data)){
						$arr[$i]['recipients'][$j] = $user->data;
					}else {
						$arr[$i]['recipients'][$j] = object;
					}
					$j++;
				}

				$sql2 = "SELECT message_id FROM
							`wp_bp_messages_meta`
						  WHERE meta_key = 'starred_by_user'
							AND message_id = ".$row['id']." AND meta_value = ".$_REQUEST['user_id']."";
				$res2 = $conn->query($sql2);

				$row2 = $res2->fetch_assoc();
				if(empty($row2)){
					$arr[$i]['is_starred'] = false;
				} else {
					$arr[$i]['is_starred'] = true;
				}


				$sql_count = "SELECT COUNT(*) as thread_count FROM `wp_bp_messages_messages` WHERE thread_id = ".$row['thread_id']."";
				$res_count = $conn->query($sql_count);
				$threadCountData = $res_count->fetch_assoc();
				$arr[$i]['thread_count'] = $threadCountData['thread_count'];

				$sql3 = "SELECT unread_count FROM `wp_bp_messages_recipients` WHERE user_id = ".$_REQUEST['user_id']." AND thread_id = ".$row['thread_id']."
";
				$res3 = $conn->query($sql3);
				$row3 = $res3->fetch_assoc();
				if(empty($row3) || $row3['unread_count'] == 0){
					$arr[$i]['is_read'] = false;
				} else {
					$arr[$i]['is_read'] = true;
				}



				$sql4 = "SELECT wu.ID,wu.`display_name` AS receiver_display_name, wu.`user_login` AS receiver_username, wu.`user_nicename` AS receiver_nickname FROM `wp_bp_messages_recipients` wr
INNER JOIN `wp_users` wu ON wu.`ID` = wr.`user_id`
WHERE wr.user_id != ".$_REQUEST['user_id']." AND wr.thread_id = ".$row['thread_id']."";
				$res4 = $conn->query($sql4);
				$row4 = $res4->fetch_assoc();
				$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row4['ID'], 'html'=>false, 'type'=>'full'));
				$arr[$i]['receiver_id'] = $row4['ID'];
				$arr[$i]['receiver_display_name'] = $row4['receiver_display_name'];
				$arr[$i]['receiver_username'] = $row4['receiver_username'];
				$arr[$i]['receiver_nickname'] = $row4['receiver_nickname'];
				$arr[$i]['receiver_avatar'] = $useravatar_url2;
				//$arr[] = $row;
				//$arr[$i] = $useravatar_url;
				$i++;
			 }

			$oReturn = new stdClass();
			$oReturn->success = 'success';
			$oReturn->status = 'ok';
			$oReturn->data = $arr;
	}
	else
	{
			$oReturn = new stdClass();
			$oReturn->success = 'success';
			$oReturn->status = 'ok';
			$oReturn->data = array();
	}

	return $oReturn;


}

function delete_message()
{
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	if ( ! isset($_POST['thread_ids']) ) {
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem deleting messages.', 'buddypress' ) . '</p></div>';

	} else {

		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0, $count = count( $thread_ids ); $i < $count; ++$i ) {
			BP_Messages_Thread::mark_as_read( (int) $thread_ids[$i] );
		}

		$thread_ids = wp_parse_id_list( $_POST['thread_ids'] );
		messages_delete_thread( $thread_ids );

		_e( 'Messages deleted.', 'buddypress' );
	}

}


	public function save_group()
	{

		$clicked_pic = $_POST['clicked_pic'];
		$user_id = $_POST['user_id'];
		$picture_code = $_POST['picture_code'];
		//$bp_upload2 = xprofile_avatar_upload_dir('',$user_id);
		$bp_upload = bp_core_avatar_upload_path();

		$basedir = $bp_upload."/buddypress/groups/".$_POST['group_id']."/cover-image/";
		//echo $baseurl = $bp_upload['url'];
		if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
		//$filename = $clicked_pic.'_'.$user_id.'.jpg';
		$filename = $clicked_pic.'_75.jpg';
		$outputFile = $basedir.'/'.$filename;

		$imageurl = $outputFileURL = $baseurl.'/'.$filename;

		if(strstr($picture_code,'data:image/')){
			 $picture_code_arr = explode(',', $picture_code);
			$picture_code = $picture_code_arr[1];
		}

		$quality = 70;
		if(file_exists($outputFile)){@unlink($outputFile);}
		$data = base64_decode($picture_code);
		$image = imagecreatefromstring($data);
		$imageSave = imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
		if(!$imageSave){$oReturn->error = 'Image Save Error'; return  $oReturn;}
		if($outputFile && $clicked_pic=='cover_pic'){
			update_user_meta( $user_id, 'bbp_cover_pic', $imageurl);
		}elseif($outputFile && $clicked_pic=='profile_pic'){
			$imgdata = @getimagesize( $outputFile );
			$img_width = $imgdata[0];
			$img_height = $imgdata[1];
			$upload_dir = wp_upload_dir();
			$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );

		if ( !bp_core_avatar_handle_crop( array( 'object' => 'group', 'avatar_dir' => 'buddypress/groups/cover-image', 'item_id' => 75, 'original_file' => $existing_avatar_path, 'crop_x' =>0, 'crop_y' => 0, 'crop_w' => $img_width , 'crop_h' => $img_height ) ) )
						bp_core_add_message( __( 'There was an error saving the group profile photo, please try uploading again.', 'buddypress' ), 'error' );
					else
						bp_core_add_message( __( 'The group profile photo was uploaded successfully.', 'buddypress' ) );
				}



		die;

		$new_group_id = isset( $bp->groups->new_group_id ) ? $bp->groups->new_group_id : 0;

		$id = groups_create_group( array( 'group_id' => $new_group_id, 'name' => "BYPT", 'description' => "BYPT", 'location_state' => "Gujarat",'location_city' => "Ahmedabad",'slug' => "BYPT", 'date_created' => bp_core_current_time(), 'status' => 'private' ) ) ;
		print_r($id);
		die;



			$bp = buddypress();
			setcookie( 'bp_new_group_id', false, time() - 1000, COOKIEPATH );
			$reset_steps = true;
			// Set the ID of the new group, if it has already been created in a previous step
			if ( isset( $_COOKIE['bp_new_group_id'] ) ) {
				$bp->groups->new_group_id = (int) $_COOKIE['bp_new_group_id'];
				$bp->groups->current_group = groups_get_group( array( 'group_id' => $bp->groups->new_group_id ) );

				// Only allow the group creator to continue to edit the new group
				if ( ! bp_is_group_creator( $bp->groups->current_group, $_POST['user_id'] ) ) {
					bp_core_add_message( __( 'Only the group creator may continue editing this group.', 'buddypress' ), 'error' );
					$oReturn = new stdClass();
					$oReturn->msg = 'Only the group creator may continue editing this group.';
					$oReturn->error = 'error';
					return $oReturn;
					//bp_core_redirect( trailingslashit( bp_get_groups_directory_permalink() . 'create' ) );
				}
			}

			// If the save, upload or skip button is hit, lets calculate what we need to save


				// Check the nonce
				//check_admin_referer( 'groups_create_save_' . bp_get_groups_current_create_step() );

				//if ( 'group-details' == bp_get_groups_current_create_step() ) {
					if ( empty( $_POST['group-name'] ) || empty( $_POST['group-desc'] ) || empty( $_POST['group-location-state'] ) || empty( $_POST['group-location-city'] ) || !strlen( trim( $_POST['group-name'] ) ) || !strlen( trim( $_POST['group-desc'] ) )  || !strlen( trim( $_POST['group-location-state'] ) ) || !strlen( trim( $_POST['group-location-city'] ) ) ) {
						bp_core_add_message( __( 'Please fill in all of the required fields', 'buddypress' ), 'error' );
						//bp_core_redirect( trailingslashit( bp_get_groups_directory_permalink() . 'create/step/' . bp_get_groups_current_create_step() ) );
						$oReturn = new stdClass();
						$oReturn->msg = 'Please fill in all of the required fields.';
						$oReturn->error = 'error';
						return $oReturn;
					}

					$new_group_id = isset( $bp->groups->new_group_id ) ? $bp->groups->new_group_id : 0;

					$id = groups_create_group( array( 'group_id' => $new_group_id, 'name' => $_POST['group-name'], 'description' => $_POST['group-desc'], 'location_state' => $_POST['group-location-state'],'location_city' => $_POST['group-location-city'],'slug' => groups_check_slug( sanitize_title( esc_attr( $_POST['group-name'] ) ) ), 'date_created' => bp_core_current_time(), 'status' => 'private' ) ) ;


					if ( !$bp->groups->new_group_id = groups_create_group( array( 'group_id' => $new_group_id, 'name' => $_POST['group-name'], 'description' => $_POST['group-desc'], 'location_state' => $_POST['group-location-state'],'location_city' => $_POST['group-location-city'],'slug' => groups_check_slug( sanitize_title( esc_attr( $_POST['group-name'] ) ) ), 'date_created' => bp_core_current_time(), 'status' => 'private' ) ) ) {

						$oReturn = new stdClass();
						$oReturn->msg = 'There was an error saving group details. Please try again.';
						$oReturn->error = 'error';
						return $oReturn;
						//bp_core_redirect( trailingslashit( bp_get_groups_directory_permalink() . 'create/step/' . bp_get_groups_current_create_step() ) );
					}
					die($bp->groups->new_group_id);
					$invite_status = "members";
					groups_update_groupmeta( $bp->groups->new_group_id, 'invite_status', $invite_status );
				//}

				//if ( 'group-settings' == bp_get_groups_current_create_step() ) {
					$group_status = 'public';
					$group_enable_forum = 1;

					if ( !isset($_POST['group-show-forum']) ) {
						$group_enable_forum = 0;
					} else {
						// Create the forum if enable_forum = 1
						if ( bp_is_active( 'forums' ) && !groups_get_groupmeta( $bp->groups->new_group_id, 'forum_id' ) ) {
							groups_new_group_forum();
						}
					}

					if ( 'private' == $_POST['group-status'] )
						$group_status = 'private';
					elseif ( 'hidden' == $_POST['group-status'] )
						$group_status = 'hidden';

					if ( !$bp->groups->new_group_id = groups_create_group( array( 'group_id' => $bp->groups->new_group_id, 'status' => $group_status, 'enable_forum' => $group_enable_forum ) ) ) {
						bp_core_add_message( __( 'There was an error saving group details. Please try again.', 'buddypress' ), 'error' );
						$oReturn = new stdClass();
					$oReturn->msg = 'There was an error saving group details. Please try again.';
					$oReturn->error = 'error';
					return $oReturn;
						//bp_core_redirect( trailingslashit( bp_get_groups_directory_permalink() . 'create/step/' . bp_get_groups_current_create_step() ) );
					}

					/**
					 * Filters the allowed invite statuses.
					 *
					 * @since 1.5.0
					 *
					 * @param array $value Array of statuses allowed.
					 *                     Possible values are 'members,
					 *                     'mods', and 'admins'.
					 */
					$allowed_invite_status = apply_filters( 'groups_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
					$invite_status	       = !empty( $_POST['group-invite-status'] ) && in_array( $_POST['group-invite-status'], (array) $allowed_invite_status ) ? $_POST['group-invite-status'] : 'members';

					groups_update_groupmeta( $bp->groups->new_group_id, 'invite_status', $invite_status );
				//}

				//if ( 'group-invites' === bp_get_groups_current_create_step() ) {
					if ( ! empty( $_POST['friends'] ) ) {
						foreach ( (array) $_POST['friends'] as $friend ) {
							groups_invite_user( array(
								'user_id'  => (int) $friend,
								'group_id' => $bp->groups->new_group_id,
							) );
						}
					}

					groups_send_invites( $_POST['user_id'] , $bp->groups->new_group_id );
				//}



				// Reset cookie info
				setcookie( 'bp_new_group_id', $bp->groups->new_group_id, time()+60*60*24, COOKIEPATH );
				setcookie( 'bp_completed_create_steps', base64_encode( json_encode( $bp->groups->completed_create_steps ) ), time()+60*60*24, COOKIEPATH );

				// If we have completed all steps and hit done on the final step we

				setcookie( 'bp_new_group_id', false, time() - 3600, COOKIEPATH );
					setcookie( 'bp_completed_create_steps', false, time() - 3600, COOKIEPATH );

					// Once we completed all steps, record the group creation in the activity stream.
					groups_record_activity( array(
						'type' => 'created_group',
						'item_id' => $bp->groups->new_group_id
					) );



			// Remove invitations
			if ( 'group-invites' === bp_get_groups_current_create_step() && ! empty( $_REQUEST['user_id'] ) && is_numeric( $_REQUEST['user_id'] ) ) {
				if ( ! check_admin_referer( 'groups_invite_uninvite_user' ) ) {
					return false;
				}

				$message = __( 'Invite successfully removed', 'buddypress' );
				$error   = false;

				if( ! groups_uninvite_user( (int) $_REQUEST['user_id'], $bp->groups->new_group_id ) ) {
					$message = __( 'There was an error removing the invite', 'buddypress' );
					$error   = 'error';
					$oReturn = new stdClass();
					$oReturn->msg = $message;
					$oReturn->error = $error;
					return $oReturn;
				}

				bp_core_add_message( $message, $error );
				bp_core_redirect( trailingslashit( bp_get_groups_directory_permalink() . 'create/step/group-invites' ) );
			}

			// Group avatar is handled separately
			//if ( 'group-avatar' == bp_get_groups_current_create_step() && isset( $_POST['upload'] ) ) {
				if ( ! isset( $bp->avatar_admin ) ) {
					$bp->avatar_admin = new stdClass();
				}

				if ( !empty( $_FILES ) && isset( $_POST['upload'] ) ) {
					// Normally we would check a nonce here, but the group save nonce is used instead

					// Pass the file to the avatar upload handler
					if ( bp_core_avatar_handle_upload( $_FILES, 'groups_avatar_upload_dir' ) ) {
						$bp->avatar_admin->step = 'crop-image';

						// Make sure we include the jQuery jCrop file for image cropping
						add_action( 'wp_print_scripts', 'bp_core_add_jquery_cropper' );
					}
				}

				// If the image cropping is done, crop the image and save a full/thumb version
				if ( isset( $_POST['avatar-crop-submit'] ) && isset( $_POST['upload'] ) ) {
					// Normally we would check a nonce here, but the group save nonce is used instead

					if ( !bp_core_avatar_handle_crop( array( 'object' => 'group', 'avatar_dir' => 'group-avatars', 'item_id' => $bp->groups->current_group->id, 'original_file' => $_POST['image_src'], 'crop_x' => $_POST['x'], 'crop_y' => $_POST['y'], 'crop_w' => $_POST['w'], 'crop_h' => $_POST['h'] ) ) )
						bp_core_add_message( __( 'There was an error saving the group profile photo, please try uploading again.', 'buddypress' ), 'error' );
					else
						bp_core_add_message( __( 'The group profile photo was uploaded successfully.', 'buddypress' ) );
				}
			//}

			/**
			 * Filters the template to load for the group creation screen.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value Path to the group creation template to load.
			 */
			$oReturn = new stdClass();
			$oReturn->msg = 'Success.';
			$oReturn->success = 'ok';
			return $oReturn;

	}

	function share_post()
	{
		if(!$_REQUEST['activity_id']){$oReturn->error = __('activity_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "SELECT * FROM `wp_bp_activity` WHERE id = ".$_REQUEST['activity_id'];
		$res = $conn->query($sql);
		$activityData = $res->fetch_assoc();

		$oUser = get_user_by('id', $_REQUEST['user_id']);

		 $activity_link = '<a href="'.get_site_url().'/members/'.$oUser->data->user_nicename.'/" title="'.$oUser->data->display_name.'">'.$oUser->data->display_name.'</a> <span class="activity-bold">shared an activity<span></span></span>';
			 $member_link = ''.get_site_url().'/members/'.$oUser->data->user_nicename.'/';

		if(isset($activityData['content']) && $activityData['content'] == '')
		{
			$activityData['content'] = addslashes($activityData['action']);
		}

		$sql2 = "INSERT INTO `wp_bp_activity` (user_id, component, `type`, `action`, content, primary_link, item_id, secondary_item_id, date_recorded, hide_sitewide, mptt_left, mptt_right, is_spam, is_shared, is_checkedin, latitude, longitude, location)
VALUES
(".$_REQUEST['user_id'].",'".$activityData['component']."','".$activityData['type']."','".$activity_link."','".htmlentities(addslashes($activityData['content']))."','".$member_link."','".$activityData['item_id']."','".$activityData['secondory_id']."','".date("Y-m-d- H:i:s")."','".$activityData['hide_sitewide']."','".$activityData['mptt_left']."', '".$activityData['mptt_right']."', '".$activityData['is_spam']."', 1, '".$activityData['is_checkedin']."', '".$activityData['latitude']."', '".$activityData['longitude']."', '".$activityData['location']."');";
		$res = $conn->query($sql2);

		$sql = "";
		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = array();
		return $oReturn;
	}

	//request parameters for checkIns - latitude, longitude, location , type (team or user), id (team_id or user_id)
	function checkin()
	{
		if(!$_REQUEST['latitude']){$oReturn->error = __('latitude is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['longitude']){$oReturn->error = __('longitude is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['location']){$oReturn->error = __('location is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['type']){$oReturn->error = __('type is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['id']){$oReturn->error = __('id is required.','aheadzen'); return $oReturn;}

		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		if($_REQUEST['type'] == "user")
		{
			$oUser = get_user_by('id', $_REQUEST['id']);

			 $activity_link = '<a href="'.get_site_url().'/members/'.$oUser->data->user_nicename.'/" title="'.$oUser->data->display_name.'">'.$oUser->data->display_name.'</a> <span class="activity-bold">checked in to '.$_REQUEST['location'].'<span></span></span>';
				 $member_link = ''.get_site_url().'/members/'.$oUser->data->user_nicename.'/';


		$sql2 = "INSERT INTO `wp_bp_activity` (user_id, component, `type`, `action`, content, primary_link, item_id, secondary_item_id, date_recorded, hide_sitewide, mptt_left, mptt_right, is_spam, is_shared, is_checkedin, latitude, longitude, location)
	VALUES
	(".$_REQUEST['id'].",'member','checkin','".$activity_link."','".$activity_link."','".$member_link."','','','".date("Y-m-d H:i:s")."','0','0', '0', '0', 0, 1, ".$_REQUEST['latitude'].", ".$_REQUEST['longitude'].", '".$_REQUEST['location']."' );";
			$res = $conn->query($sql2);
		}
		if($_REQUEST['type'] == "team")
		{
			if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
			$sql2 = "SELECT * FROM `wp_bp_groups` WHERE id = ".$_REQUEST['group_id']."";
			$res = $conn->query($sql2);
			$row2 = $res->fetch_assoc();
			 $activity_link = '<a href="'.get_site_url().'/teams/'.$row2['slug'].'/" title="'.$row2['name'].'">'.$row2['name'].'</a> <span class="activity-bold">checked in to '.$_REQUEST['location'].'<span></span></span>';
				 $member_link = ''.get_site_url().'/teams/'.$row2['slug'].'/';


		$sql2 = "INSERT INTO `wp_bp_activity` (user_id, component, `type`, `action`, content, primary_link, item_id, secondary_item_id, date_recorded, hide_sitewide, mptt_left, mptt_right, is_spam, is_shared, is_checkedin, latitude, longitude, location)
	VALUES
	(".$_REQUEST['id'].",'groups','checkin','".$activity_link."','".$activity_link."','".$member_link."',".$_REQUEST['group_id'].",'','".date("Y-m-d H:i:s")."','0','0', '0', '0', 0,  1, ".$_REQUEST['latitude'].", ".$_REQUEST['longitude'].", '".$_REQUEST['location']."');";
			$res = $conn->query($sql2);
		}

		$sql = "";
		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = array();
		return $oReturn;
	}



	function set_profile_visibility() {

	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	// Bail if not a POST action.
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		$oReturn->error = __('Request method is invalid.','aheadzen'); return $oReturn;
	}

	// Only save if there are field ID's being posted.
	if ( ! empty( $_POST['field_ids'] ) ) {
		//print "<pre>";print_r($_POST);die;
		// Get the POST'ed field ID's.
		$posted_field_ids = explode( ',', $_POST['field_ids'] );

		// Backward compatibility: a bug in BP 2.0 caused only a single
		// group's field IDs to be submitted. Look for values submitted
		// in the POST request that may not appear in 'field_ids', and
		// add them to the list of IDs to save.
		foreach ( $_POST as $posted_key => $posted_value ) {
			preg_match( '/^field_([0-9]+)_visibility$/', $posted_key, $matches );
			if ( ! empty( $matches[1] ) && ! in_array( $matches[1], $posted_field_ids ) ) {
				$posted_field_ids[] = $matches[1];
			}
		}
		// Save the visibility settings.
		foreach ( $posted_field_ids as $field_id ) {

			$visibility_level = 'public';

			if ( !empty( $_POST['field_' . $field_id . '_visibility'] ) ) {
				$visibility_level = $_POST['field_' . $field_id . '_visibility'];
			}

			xprofile_set_field_visibility_level( $field_id, $_POST['user_id'], $visibility_level );

		}

		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = array();
		return $oReturn;
	}



	}


	function get_profile_visibility() {

	if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}

	$field_ids = "1,25,6,18,26,29,32,33,34,35,36,15,16,14,17";
	// Only save if there are field ID's being posted.
	if ( ! empty( $field_ids ) ) {
		//print "<pre>";print_r($_POST);die;
		// Get the POST'ed field ID's.
		$posted_field_ids = explode( ',', $field_ids );

		// Backward compatibility: a bug in BP 2.0 caused only a single
		// group's field IDs to be submitted. Look for values submitted
		// in the POST request that may not appear in 'field_ids', and
		// add them to the list of IDs to save.
		foreach ( $_POST as $posted_key => $posted_value ) {
			preg_match( '/^field_([0-9]+)_visibility$/', $posted_key, $matches );
			if ( ! empty( $matches[1] ) && ! in_array( $matches[1], $posted_field_ids ) ) {
				$posted_field_ids[] = $matches[1];
			}
		}
		// Save the visibility settings.
		$arr =array();
		foreach ( $posted_field_ids as $field_id ) {

			$visibility_level = 'public';

			if ( !empty( $_POST['field_' . $field_id . '_visibility'] ) ) {
				$visibility_level = $_POST['field_' . $field_id . '_visibility'];
			}

			$current_level = xprofile_get_field_visibility_level( $field_id, $_REQUEST['user_id']);
			$arr[$field_id] = $current_level;
		}

		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $arr;
		return $oReturn;
	}



	}


	function get_team_followers()
	{
		if(!$_REQUEST['group_id']){$oReturn->error = __('Group Id is required.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$oReturn = new stdClass();
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "SELECT * FROM `wp_bp_team_follow` WHERE group_id = ".$_REQUEST['group_id'];
		$res2 = $conn->query($sql);
		if ($res2->num_rows > 0) {
			 $i=0;
			 while($row = $res2->fetch_assoc()) {

					$sql = "select ID,user_login,display_name from wp_users where ID = ".$row['author_id']."";
					$res = $conn->query($sql);

			if($res->num_rows > 0){
				$counter=0;
				foreach($res as $resobj){
					if($resobj['display_name']){
						$user = new BP_Core_User($resobj['ID']);

						if($user){
							$avatar_thumb = '';
							if($user->avatar_thumb){
								preg_match_all('/(src)=("[^"]*")/i',$user->avatar_thumb, $user_avatar_result);
								$avatar_thumb = str_replace('"','',$user_avatar_result[2][0]);
								if($avatar_thumb && !strstr($avatar_thumb,'http:')){ $avatar_thumb = 'http:'.$avatar_thumb;}
							}
						}
						$oReturn->success = "success";
						$oReturn->members[$i]->id = $resobj['ID'];
						$oReturn->members[$i]->login = $resobj['user_login'];
						$oReturn->members[$i]->name = bpaz_user_name_from_email($resobj['display_name']);
						$oReturn->members[$i]->thumb = $avatar_thumb;
						$counter = $counter + 1;
					}
				}
			 }
			 $i++;
		}
		}
		return $oReturn;
	}





	function mass_message_team()
	{
		if(!$_REQUEST['subject']){$oReturn->error = __('subject is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['email_content']){$oReturn->error = __('email_content is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}

		global $wpdb, $current_user, $bp;

    	//$email_capabilities = $this->bp_group_email_get_capabilities();

      	//prepare fields
      	//$email_subject = strip_tags(stripslashes(trim($_POST['subject'])));
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "SELECT user_id FROM `wp_bp_groups_members` WHERE group_id = ".$_REQUEST['group_id'];
		$res = $conn->query($sql);


		$sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$_REQUEST['group_id'];
		$res2 = $conn->query($sql);
		$row2 = $res2->fetch_object();
		//send emails
        $group_link = bp_get_group_permalink( $row2  ) . '/';

        $user_ids = BP_Groups_Member::get_group_member_ids($_REQUEST['group_id']);
	 	//echo "<pre>"; print_r($user_ids);
       $email_count = 0;
    	foreach( $user_ids  as  $user_id ){
    	  //skip opt-outs
    		if ( 'no' == get_user_meta( $user_id, 'notification_groups_email_send', true ) ) continue;

    		$ud = get_userdata( $user_id );

    		// Set up and send the message
    		$to = $ud->user_email;

    		$group_link = site_url( $row2->slug . '/' . $row2->slug . '/' );
    		$settings_link = bp_core_get_user_domain( $user_id ) . 'settings/notifications/';

    		$message = sprintf( __(
  '%s


  Sent by %s from the "%s" group: %s

  ---------------------
  ', 'groupemail' ), $_REQUEST['email_content'], get_blog_option( BP_ROOT_BLOG, 'blogname' ), stripslashes( esc_attr( $row2->name ) ), $group_link );

    		$message .= sprintf( __( 'To unsubscribe from these emails please log in and go to: %s', 'groupemail' ), $settings_link );


    		// Send it
    		wp_mail( $to, $_REQUEST['subject'], $message );

    		unset( $message, $to );
    		$email_count++;
    	}

      //show success message
      if ($email_count) {

			$oReturn = new stdClass();
			$oReturn->success = 'The email was successfully sent to '.$email_count.' group members';
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;
		}
		else
		{
			$oReturn = new stdClass();
			$oReturn->success = 'The email was successfully sent to 0 group members';
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;
		}
	}


	function bp_follower_email_send() {
    global $wpdb, $current_user, $bp;

    $email_capabilities = $this->bp_follower_email_get_capabilities();

    if (isset($_POST['send_email'])) {
      if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'bp_group_email')) {
        bp_core_add_message( __('There was a security problem', 'followeremail'), 'error' );
        return false;
      }

      //reject unqualified users
      if (!$email_capabilities) {
        bp_core_add_message( __("You don't have permission to send emails", 'followeremail'), 'error' );
        return false;
      }

      //prepare fields
      $email_subject = strip_tags(stripslashes(trim($_POST['email_subject'])));

      //check that required title isset after filtering
      if (empty($email_subject)) {
        bp_core_add_message( __("A subject is required", 'followeremail'), 'error' );
        return false;
      }

      $email_text = strip_tags(stripslashes(trim($_POST['email_text'])));

      //check that required title isset after filtering
      if (empty($email_text)) {
        bp_core_add_message( __("Email text is required", 'followeremail'), 'error' );
        return false;
      }
      //send emails
      $group_link = bp_get_group_permalink( $bp->groups->current_group ) . '/';

      //$user_ids = BP_Groups_Member::get_group_member_ids($bp->groups->current_group->id);
      $table_name = $wpdb->prefix . 'bp_team_follow';
      $curnt_grp_ID  = $bp->groups->current_group->id;
      $sql = "SELECT author_id FROM $table_name WHERE group_id = $curnt_grp_ID";
      $result = $wpdb->get_results($sql,ARRAY_A);
      if(!empty($result))  {
      foreach ($result as $user_get_id) {
        $user_ids[] = $user_get_id['author_id'];
      }
      $remove_admin = array(1);
      $user_ids = array_diff($user_ids, $remove_admin);
      $email_count = 0;
      foreach ($user_ids as $user_id) {
        //skip opt-outs
        if ( 'no' == get_user_meta( $user_id, 'notification_follower_email_send', true ) ) continue;

        $ud = get_userdata( $user_id );

        // Set up and send the message
        $to = $ud->user_email;

        $group_link = site_url( $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/' );
        $settings_link = bp_core_get_user_domain( $user_id ) . 'settings/notifications/';

        $message = sprintf( __(
  '%s


  Sent by %s from the "%s" group: %s

  ---------------------
  ', 'followeremail' ), $email_text, get_blog_option( BP_ROOT_BLOG, 'blogname' ), stripslashes( esc_attr( $bp->groups->current_group->name ) ), $group_link );

        $message .= sprintf( __( 'To unsubscribe from these emails please log in and go to: %s', 'followeremail' ), $settings_link );

        // Send it
        wp_mail( $to, $email_subject, $message );

        unset( $message, $to );
        $email_count++;
      }
    } else {
      bp_core_add_message( __("Team has no any Follower", 'followeremail'), 'error' );
        return false;
    }
      //show success message
      if ($email_count) {
        bp_core_add_message( sprintf( __("The email was successfully sent to %d Follower", 'followeremail'), $email_count) );
        return true;
      }

    } else {
      return false;
    }
  }

    function mass_message_follower()
	{   error_reporting(E_ALL);
		if(!$_REQUEST['subject']){$oReturn->error = __('subject is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['email_content']){$oReturn->error = __('email_content is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}

		global $wpdb, $current_user, $bp;


		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
		}
		  //prepare fields
		  $email_subject = strip_tags(stripslashes(trim($_POST['subject'])));
		  $email_text = strip_tags(stripslashes(trim($_POST['email_content'])));

		  $sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$_REQUEST['group_id'];
		  $res2 = $conn->query($sql);
		  $row2 = $res2->fetch_object();
		  //send emails
		  $group_link = bp_get_group_permalink( $row2 ) . '/';

		  //$user_ids = BP_Groups_Member::get_group_member_ids($bp->groups->current_group->id);
		  $table_name = $wpdb->prefix . 'bp_team_follow';
		  $curnt_grp_ID  = $_REQUEST['group_id'];
		  $sql = "SELECT author_id FROM $table_name WHERE group_id = $curnt_grp_ID";
		  $result = $wpdb->get_results($sql,ARRAY_A);
		  if(!empty($result))  {
		  foreach ($result as $user_get_id) {
			$user_ids[] = $user_get_id['author_id'];
		  }

		  $remove_admin = array(1);
		  //$user_ids = array_diff($user_ids, $remove_admin);
		  $email_count = 0;
		  foreach ($user_ids as $user_id) {
			//skip opt-outs
			if ( 'no' == get_user_meta( $user_id, 'notification_follower_email_send', true ) ) continue;

			$ud = get_userdata( $user_id );

			// Set up and send the message
			$to = $ud->user_email;

			$group_link = site_url( $row2->slug . '/' . $row2->slug . '/' );
			$settings_link = bp_core_get_user_domain( $user_id ) . 'settings/notifications/';

			$message = sprintf( __(
	  '%s


	  Sent by %s from the "%s" group: %s

	  ---------------------
	  ', 'followeremail' ), $email_text, get_blog_option( BP_ROOT_BLOG, 'blogname' ), stripslashes( esc_attr( $row2->name ) ), $group_link );

			$message .= sprintf( __( 'To unsubscribe from these emails please log in and go to: %s', 'followeremail' ), $settings_link );

			// Send it
			wp_mail( $to, $email_subject, $message );

			unset( $message, $to );
			$email_count++;
		  }
		} else {
		 	$oReturn = new stdClass();
			$oReturn->success = 'Team has no any Follower.';
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;
		}
		  //show success message
		  if ($email_count) {

			$oReturn = new stdClass();
			$oReturn->success = 'The email was successfully sent to '.$email_count.' Follower.';
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;
		  }
		  else
			{
				$oReturn = new stdClass();
				$oReturn->success = 'The email was successfully sent to 0 group members';
				$oReturn->status = 'ok';
				$oReturn->data = array();
				return $oReturn;
			}


	}


	function groups_ban_member() {

		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		try{

			$member = new BP_Groups_Member( $_REQUEST['user_id'], $_REQUEST['group_id'] );
			$oReturn = new stdClass();
			$oReturn->success = $member->ban();
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;

		}catch(Exception $e){
			$oReturn = new stdClass();
			$oReturn->error = 'error';
			$oReturn->status = 'error';
			$oReturn->message = $e->getMessage();
			return $oReturn;
		}

	}


	function groups_unban_member() {

		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		try{

			$member = new BP_Groups_Member( $_REQUEST['user_id'], $_REQUEST['group_id'] );
			$oReturn = new stdClass();
			$oReturn->success = $member->unban();
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;

		}catch(Exception $e){
			$oReturn = new stdClass();
			$oReturn->error = 'error';
			$oReturn->status = 'error';
			$oReturn->message = $e->getMessage();
			return $oReturn;
		}

	}


	function groups_remove_member()
	{
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		try{
			$member = new BP_Groups_Member( $_REQUEST['user_id'], $_REQUEST['group_id'] );
			$oReturn = new stdClass();
			$oReturn->success = $member->remove();
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;
		}catch(Exception $e){
			$oReturn = new stdClass();
			$oReturn->error = 'error';
			$oReturn->status = 'error';
			$oReturn->message = $e->getMessage();
			return $oReturn;
		}
	}

	/*
	status : mod or admin
	*/
	function groups_promote_member()
	{

		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['status']){$oReturn->error = __('status is required.','aheadzen'); return $oReturn;}
		try{
			$member = new BP_Groups_Member( $_REQUEST['user_id'], $_REQUEST['group_id'] );
			// code for notific// Notification data making
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

	    	$sql = "SELECT * FROM `wp_bp_groups` WHERE `id` = ".$_REQUEST['group_id'];
	    	$res2 = $conn->query($sql);
	    	$groupData = $res2->fetch_assoc();

	    	if(empty(groupData))
	    	{
	    		$oReturn = new stdClass();
				$oReturn->error = 'error';
				$oReturn->status = 'error';
				$oReturn->message = "Group is not exist.";
				return $oReturn;
	    	}
			$userDataObj= get_userdata($groupData['creator_id']);
			$friendDataObj = get_userdata($_REQUEST['user_id']);
			$userData = $userDataObj->data;
			$friendData = $friendDataObj->data;
			$friendData->notification_type_id = 9;

			$message = $userData->display_name. " have promted you in his group ".$groupData['name'];
			$this->sendPushNotification($message, $friendData->device_token, $userData, $friendData->device_type, $friendData);

			$oReturn = new stdClass();
			$oReturn->success = $member->promote( $_REQUEST['status'] );
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;
		}catch(Exception $e){
			$oReturn = new stdClass();
			$oReturn->error = 'error';
			$oReturn->status = 'error';
			$oReturn->message = $e->getMessage();
			return $oReturn;
		}
	}

	function groups_demote_member()
	{

		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['status']){$oReturn->error = __('status is required.','aheadzen'); return $oReturn;}
		try{
			$member = new BP_Groups_Member( $_REQUEST['user_id'], $_REQUEST['group_id'] );
			$oReturn = new stdClass();
			$oReturn->success = $member->demote($_REQUEST['status']);
			$oReturn->status = 'ok';
			$oReturn->data = array();
			return $oReturn;
		}catch(Exception $e){
			$oReturn = new stdClass();
			$oReturn->error = 'error';
			$oReturn->status = 'error';
			$oReturn->message = $e->getMessage();
			return $oReturn;
		}
	}

	function get_all_messages_by_thread()
	{error_reporting(E_ALL);
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['thread_id']){$oReturn->error = __('thread_id is required.','aheadzen'); return $oReturn;}

		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
		}

	    $sql = "SELECT * FROM `wp_bp_messages_messages` WHERE thread_id = ".$_REQUEST['thread_id']."";
	    $res2 = $conn->query($sql);
	    $arr = array();$i=0;
	    while($row2 = $res2->fetch_assoc())
	    {
		  $arr[$i] = $row2;
		  $user = get_userdata( $row2['sender_id'] );
		  $useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row2['sender_id'], 'html'=>false, 'type'=>'full'));
		  $user->data->avatar = $useravatar_url2 ;
		  $arr[$i]['user'] =  $user->data;

		  /*echo $sql3 = "SELECT wu.ID,wu.`display_name` AS receiver_display_name, wu.`user_login` AS receiver_username, wu.`user_nicename` AS receiver_nickname FROM `wp_bp_messages_recipients` wr
INNER JOIN `wp_users` wu ON wu.`ID` = wr.`user_id`
WHERE wr.user_id != ".$row2['sender_id']." AND wr.thread_id = ".$_REQUEST['thread_id']."";
		 $res3 = $conn->query($sql3);
		 $row3 = $res3->fetch_assoc();

		 if ($res3->num_rows > 0) {
			 $k=0;$recepient = array();
		 	foreach($row3 as $rec)
			{
				print_r($rec);die;
		 		$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$rec['ID'], 'html'=>false, 'type'=>'full'));
		 		$recepient[$k] = $rec;
				$recepient[$k]['avatar'] = $useravatar_url2;


			}
		 }*/
		 $sql_rec = "SELECT * FROM `wp_bp_messages_recipients` WHERE thread_id = ".$_REQUEST['thread_id'];
				$res_rec= $conn->query($sql_rec);
				$recipientData = array();
				$j=0;
				while($row22 = $res_rec->fetch_assoc()){

					$user = get_userdata( $row22['user_id'] );
					$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row22['user_id'], 'html'=>false, 'type'=>'full'));
					$user->data->avatar = $useravatar_url2;

					if(isset($user->data)){
						$recipientData[$j] = $user->data;
					}else {
						$recipientData[$j] = object;
					}
					$j++;
				}

		 $sql4 = "SELECT message_id FROM
							`wp_bp_messages_meta`
						  WHERE meta_key = 'starred_by_user'
							AND message_id = ".$row2['id']." AND meta_value = ".$_REQUEST['user_id']."";
		$res4 = $conn->query($sql4);

		$row4 = $res4->fetch_assoc();
		if(empty($row4)){
			$arr[$i]['is_starred'] = false;
		} else {
			$arr[$i]['is_starred'] = true;
		}


		  $i++;
	    }

		$oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $arr;
		$oReturn->recipients = $recipientData;
		return $oReturn;

	}

	public function getGroupInvitList()
	{
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->error = __('group_id is required.','aheadzen'); return $oReturn;}

		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
		}

	    $sql = "SELECT * FROM `wp_bp_groups_members` WHERE group_id = ".$_REQUEST['group_id']." AND inviter_id = ".$_REQUEST['user_id']."";
	    $res2 = $conn->query($sql);
	    $arr = array();$i=0;
	    while($row2 = $res2->fetch_assoc())
	    {
				$user = get_userdata( $row2['user_id'] );
				$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row2['user_id'], 'html'=>false, 'type'=>'full'));
				$user->data->avatar = $useravatar_url2;
				$arr[] = $user->data;

	    }

	    $oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $arr;
		return $oReturn;
	}


	public function getGroupInvitListByUser()
	{
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}

		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
		}

	    $sql = "SELECT * FROM `wp_bp_groups_members` WHERE  user_id = ".$_REQUEST['user_id']." AND invite_sent = 1 AND is_confirmed = 0";
	    $res2 = $conn->query($sql);
	    $arr = array();$i=0;
	    while($row2 = $res2->fetch_assoc())
	    {
				$user = get_userdata( $row2['inviter_id'] );
				$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row2['user_id'], 'html'=>false, 'type'=>'full'));
				$user->data->avatar = $useravatar_url2;

				$sql = "SELECT * FROM wp_bp_groups WHERE id = ".$row2['group_id'];
	    		$res3 = $conn->query($sql);
	    		$groupData = $res3->fetch_assoc();

	    		$groupavatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$row2['group_id'], 'html'=>false, 'type'=>'full'));
	    		$groupData['group_avatar'] = $groupavatar_url;
				$arr[$i]['userData'] = $user->data;
				$arr[$i]['groupData'] = $groupData ;
				$i++;
	    }

	    $oReturn = new stdClass();
		$oReturn->success = 'success';
		$oReturn->status = 'ok';
		$oReturn->data = $arr;
		return $oReturn;
	}

	public $search_helpers = array();
	public $search_args = array();
	public $search_results = array();
	public function search()
	{
			//check_ajax_referer( 'bboss_global_search_ajax', 'nonce' );

			if( isset($_POST["view"]) && $_POST["view"] == "content") {

				$_GET["s"] = $_POST["s"];
				if(!empty($_POST["subset"])) {
					$_GET["subset"] = $_POST["subset"];
				}

				if(!empty($_POST["list"])) {
					$_GET["list"] = $_POST["list"];
				}

				$content = "";

				buddyboss_global_search()->search->prepare_search_page();
				$content = buddyboss_global_search_buffer_template_part( 'results-page-content', '', false );

				echo $content;

				die();
			}
			$per_page = 1;
			if(isset($_REQUEST['per_page']))
			{
				$per_page = $_REQUEST['per_page'];
			}
			$page = 1;
			if(isset($_REQUEST['per_page']))
			{
				$page = $_REQUEST['per_page'];
			}
			$this->load_search_helpers();
			$args = array(
				'search_term'	=> $_REQUEST['search_term'],
				//How many results should be displyed in autosuggest?
				//@todo: give a settings field for this value
				'per_page'		=> $per_page,
				'current_page' => $page,
				'count_total'	=> false,
				'template_type'	=> 'ajax',
			);

			$this->do_search( $args );

			$search_results = array();
			if( isset( $this->search_results['all']['items'] ) && !empty( $this->search_results['all']['items'] ) ){
				/* ++++++++++++++++++++++++++++++++++
				group items of same type together
				++++++++++++++++++++++++++++++++++ */
				$types = array();
				foreach( $this->search_results['all']['items'] as $item_id=>$item ){
					$type = $item['type'];
					if( empty( $types ) || !in_array( $type, $types ) ){
						$types[] = $type;
					}
				}

				$new_items = array();
				foreach( $types as $type ){
					$first_html_changed = false;
					foreach( $this->search_results['all']['items'] as $item_id=>$item ){
						if( $item['type']!= $type )
							continue;

						//add group/type title in first one
						/*
						if( !$first_html_changed ){
							//this filter can be used to change display of 'posts' to 'Blog Posts' etc..
							$label = apply_filters( 'bboss_global_search_label_search_type', $type );

							//$item['html'] = "<div class='results-group results-group-{$type}'><span class='results-group-title'>{$label}</span></div>" . $item['html'];
							$first_html_changed = true;
						}

						*/

						$new_items[$item_id] = $item;
					}
				}

				$this->search_results['all']['items'] = $new_items;

				/* _______________________________ */
				$url = $this->search_page_search_url();
				$url = esc_url(add_query_arg( array( 'no_frame' => '1' ), $url ));
				$type_mem = "";
				foreach( $this->search_results['all']['items'] as $item_id=>$item ){
					$new_row = array( 'value'=>$item['html'] );
					$type_label = apply_filters( 'bboss_global_search_label_search_type', $item['type'] );
					$new_row['type'] = $item['type'];
					$new_row['type_label'] = "";
					$new_row['value'] = $item['html'];
					$new_row['user'] = $item['user'];
					if( isset( $item['title'] ) ){
						$new_row['label'] = $item['title'];
					}

					if($item['type']=="groups")
					{
						$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						// Check connection
						if ($conn->connect_error) {
								die("Connection failed: " . $conn->connect_error);
						}
						$sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$item['id'];
					    $resActivity = $conn->query($sql);
					    $actData = $resActivity->fetch_assoc();
						$new_row['groupData'] = $actData;

						$useravatar_url = bp_core_fetch_avatar(array('object'=>'group','item_id'=>$item['id'], 'html'=>false, 'type'=>'full'));
						$new_row['groupData']['group_photo'] = $useravatar_url;

					}

					if($type_mem != $new_row['type']) {
						$type_mem = $new_row['type'];
						$cat_row = $new_row;
						$cat_row["type"] = $item['type'];
						$cat_row['type_label'] = $type_label;
						$category_search_url = esc_url(add_query_arg( array( 'subset' => $item['type'] ), $url ));
						$html = "<span><a href='" . esc_url( $category_search_url ) . "'>" . $type_label . "</a></span>";
                        $cat_row["value"] = apply_filters('buddypress_gs_autocomplete_category', $html, $item['type'], $url, $type_label);
						$search_results[] = $cat_row;
					}

					$search_results[] = $new_row;
				}

				$all_results_row = array(
					"value" => "<div class='bboss_ajax_search_item allresults'><a href='" . esc_url( $url ) . "'>" . sprintf( __( "View all results for '%s'", "buddypress-global-search" ), $_REQUEST['search_term'] ) . "</a></div>",
					"type"	=> 'view_all_type',
					"type_label"	=> ''
				);
				$search_results[] = $all_results_row;
			} else {
				//@todo give a settings screen for this field
				$search_results[] = array(
					'value' => '<div class="bboss_ajax_search_item noresult">' . sprintf( __( "Nothing found for '%s'", "buddypress-global-search" ), $_REQUEST['search_term'] ) . '</div>',
					'label'	=> $_REQUEST['search_term']
				);
			}

			die( json_encode( array("status"=>"ok", "success"=>"success", "data"=>$search_results) ) );

	}


	public function do_search( $args='' ){
		//error_reporting(E_ALL);
		global $wpdb;
		$defaults = array(
			//the search term
			'search_term'		=> '',
			//Restrict search results to only this subset. eg: posts, members, groups, etc.
			//See Setting > what to search?
			'search_subset'		=> 'all',//
			//What all to search for. e.g: members.
			//See Setting > what to search?
			//The options passed here must be a subset of all options available on Setting > what to search, nothing extra can be passed here.
			//
			//This is different from search_subset.
			//If search_subset is 'all', then search is performed for all searchable items.
			//If search_subset is 'members' then only total match count for other searchable_items is calculated( so that it can be displayed in tabs)
			//members(23) | posts(201) | groups(2) and so on.
			'searchable_items'	=> buddyboss_global_search()->option('items-to-search'),
			//how many search results to display per page
			'per_page'			=> 10,
			//current page
			'current_page'		=> 1,
			//should we calculate total match count for all different types?
			//it should be set to false while calling this function for ajax search
			'count_total'		=> true,
			//template type to load for each item
			//search results will be styled differently(minimal) while in ajax search
			//options ''|'minimal'
			'template_type'		=> '',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->search_args = $args;//save it for using in other methods

		//bail out if nothing to search for
		if( !$args['search_term'] )
			return;

		if( 'all' == $args['search_subset'] ){

			/**
			 * 1. Generate a 'UNION' sql query for all searchable items with only ID, RANK, TYPE(posts|members|..) as columns, order by RANK DESC.
			 * 3. Generate html for each of them
			 */
			/* an example UNION query :-
			-----------------------------------------------------
			(
				SELECT
				wp_posts.id , 'posts' as type, wp_posts.post_title LIKE '%ho%' AS relevance, wp_posts.post_date as entry_date
				FROM
					wp_posts
				WHERE
					1=1
					AND (
							(
									(wp_posts.post_title LIKE '%ho%')
								OR 	(wp_posts.post_content LIKE '%ho%')
							)
						)
					AND wp_posts.post_type IN ('post', 'page', 'attachment')
					AND (
						wp_posts.post_status = 'publish'
						OR wp_posts.post_author = 1
						AND wp_posts.post_status = 'private'
					)
			)
			UNION
			(
				SELECT
					DISTINCT g.id, 'groups' as type, g.name LIKE '%ho%' AS relevance, gm2.meta_value as entry_date
				FROM
					wp_bp_groups_groupmeta gm1, wp_bp_groups_groupmeta gm2, wp_bp_groups g
				WHERE
					1=1
					AND g.id = gm1.group_id
					AND g.id = gm2.group_id
					AND gm2.meta_key = 'last_activity'
					AND gm1.meta_key = 'total_member_count'
					AND ( g.name LIKE '%ho%' OR g.description LIKE '%ho%' )
			)

			ORDER BY
				relevance DESC, entry_date DESC LIMIT 0, 10
			----------------------------------------------------
			*/

			$sql_queries = array();
			foreach( $args['searchable_items'] as $search_type ){
				if( !isset($this->search_helpers[$search_type])){
					continue;
				}

				/**
				 * the following variable will be an object of current search type helper class
				 * e.g: an object of BBoss_Global_Search_Groups or BBoss_Global_Search_Posts etc.
				 * so we can safely call the public methods defined in those classes.
				 * This also means that all such classes must have a common set of methods.
				 */
				$obj = $this->search_helpers[$search_type];
				$sql_queries[] = "( " . $obj->union_sql( $args['search_term'] ) . " ) ";
			}

			if( empty( $sql_queries ) ){
				//thigs will get messy if program reaches here!!
				return;
			}

			$pre_search_query = implode( ' UNION ', $sql_queries) . " ORDER BY relevance DESC, entry_date DESC ";

			if( $args['per_page']> 0 ){
				$offset = ( $args['current_page'] * $args['per_page'] ) - $args['per_page'];
				$pre_search_query .= " LIMIT {$offset}, {$args['per_page']} ";

			}

			$results = $wpdb->get_results( $pre_search_query );

			/* $results will have a structure like below */
			/*
			id | type | relevance | entry_date
			45 | groups | 1 | 2014-10-28 17:05:18
			40 | posts | 1 | 2014-10-26 13:52:06
			4 | groups | 0 | 2014-10-21 15:15:36
			*/
			if( !empty( $results ) ){
				$this->search_results['all'] = array( 'total_match_count' => 0, 'items' => array(), 'items_title'=> array() );
				//segregate items of a type together and pass it to corresponsing search handler, so that an aggregate query can be done
				//e.g one single wordpress loop can be done for all posts

				foreach( $results as $item ){
					$obj = $this->search_helpers[$item->type];
					$obj->add_search_item( $item->id );
				}

				//now get html for each item
				foreach( $results as $item ){

					$obj = $this->search_helpers[$item->type];

					if($item->type == "members"){
						$user = get_userdata( $item->id );
						$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$item->id, 'html'=>false, 'type'=>'full'));
						$user->data->avatar = $useravatar_url2;
					}
					else
					{

						$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						// Check connection
						if ($conn->connect_error) {
								die("Connection failed: " . $conn->connect_error);
						}

					    $sql = "SELECT * FROM `wp_bp_activity` WHERE id = ".$item->id;
						$res2 = $conn->query($sql);
						$resultUser = $res2->fetch_assoc();
						$user = get_userdata( $resultUser['user_id'] );
						$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$item->id, 'html'=>false, 'type'=>'full'));
						$user->data->avatar = $useravatar_url2;

					}
					$result = array(
						'id'	=> $item->id,
						'type'	=> $item->type,
						'html'	=> $obj->get_html( $item->id, $args['template_type'] ),
						'user'  => $user->data,
						'title'	=> $obj->get_title( $item->id )
					);

					$this->search_results['all']['items'][$item->id] = $result;
				}
				//now we've html saved for search results

				if( !empty( $this->search_results['all']['items'] ) && $args['template_type']!='ajax' ){
					/* ++++++++++++++++++++++++++++++++++
					group items of same type together
					++++++++++++++++++++++++++++++++++ */
					//create another copy, of items, this time, items of same type grouped together
					$ordered_items_group = array();
					foreach( $this->search_results['all']['items'] as $item_id=>$item ){
						$type = $item['type'];
						if( !isset( $ordered_items_group[$type] ) ){
							$ordered_items_group[$type] = array();
						}

						$ordered_items_group[$type][$item_id] = $item;
					}

					foreach( $ordered_items_group as $type=>&$items ){
						//now prepend html (opening tags) to first item of each type
						$first_item = reset($items);
						$start_html = "<div class='results-group results-group-{$type}'>"
								.	"<h2 class='results-group-title'><span>" . apply_filters( 'bboss_global_search_label_search_type', $type ) . "</span></h2>"
								.	"<ul id='{$type}-stream' class='item-list {$type}-list'>";

						$group_start_html = apply_filters( "bboss_global_search_results_group_start_html", $start_html, $type );

						$first_item['html'] = $group_start_html . $first_item['html'];
						$items[$first_item['id']] = $first_item;

						//and append html (closing tags) to last item of each type
						$last_item = end($items);
						$end_html = "</ul></div>";

						$group_end_html = apply_filters( "bboss_global_search_results_group_end_html", $end_html, $type );

						$last_item['html'] = $last_item['html'] . $group_end_html;
						$items[$last_item['id']] = $last_item;
					}

					//replace orginal items with this new, grouped set of items
					$this->search_results['all']['items'] = array();
					foreach( $ordered_items_group as $type=>$grouped_items ){
						foreach( $grouped_items as $item_id=>$item ){
							$this->search_results['all']['items'][$item_id] = $item;
						}
					}
					/* ________________________________ */
				}
			}
		} else {
			//if subset not in searchable items, bail out.
			if( !in_array( $args['search_subset'], $args['searchable_items'] ) )
				return;

			if( !isset($this->search_helpers[$args['search_subset']]))
				return;

			/**
			 * 1. Search top top 20( $args['per_page'] ) item( posts|members|..)
			 * 2. Generate html for each of them
			 */

			$obj = $this->search_helpers[$args['search_subset']];
			$pre_search_query = $obj->union_sql( $args['search_term'] ) . " ORDER BY relevance DESC, entry_date DESC ";

			if( $args['per_page']> 0 ){
				$offset = ( $args['current_page'] * $args['per_page'] ) - $args['per_page'];
				$pre_search_query .= " LIMIT {$offset}, {$args['per_page']} ";
			}

			$results = $wpdb->get_results( $pre_search_query );


			/* $results will have a structure like below */
			/*
			id | type | relevance | entry_date
			45 | groups | 1 | 2014-10-28 17:05:18
			40 | posts | 1 | 2014-10-26 13:52:06
			4 | groups | 0 | 2014-10-21 15:15:36
			*/
			if( !empty( $results ) ){
				$obj = $this->search_helpers[$args['search_subset']];
				$this->search_results[$args['search_subset']] = array( 'total_match_count' => 0, 'items' => array() );
				//segregate items of a type together and pass it to corresponsing search handler, so that an aggregate query can be done
				//e.g one single wordpress loop can be done for all posts
				foreach( $results as $item ){
					$obj->add_search_item( $item->id );
				}

				//now get html for each item
				foreach( $results as $item ){
					$html = $obj->get_html( $item->id, $args['template_type'] );

					$result = array(
						'id'	=> $item->id,
						'type'	=> $args['search_subset'],
						'html'	=> $obj->get_html( $item->id, $args['template_type'] ),
						'title'	=> $obj->get_title( $item->id ),
					);

					$this->search_results[$args['search_subset']]['items'][$item->id] = $result;
				}

				//now prepend html (opening tags) to first item of each type
				$first_item = reset($this->search_results[$args['search_subset']]['items']);
				$start_html = "<div class='results-group results-group-{$args['search_subset']}'>"
						.	"<ul id='{$args['search_subset']}-stream' class='item-list {$args['search_subset']}-list'>";

				$group_start_html = apply_filters( "bboss_global_search_results_group_start_html", $start_html, $args['search_subset'] );

				$first_item['html'] = $group_start_html . $first_item['html'];
				$this->search_results[$args['search_subset']]['items'][$first_item['id']] = $first_item;

				//and append html (closing tags) to last item of each type
				$last_item = end($this->search_results[$args['search_subset']]['items']);
				$end_html = "</ul></div>";

				$group_end_html = apply_filters( "bboss_global_search_results_group_end_html", $end_html, $args['search_subset'] );

				$last_item['html'] = $last_item['html'] . $group_end_html;
				$this->search_results[$args['search_subset']]['items'][$last_item['id']] = $last_item;
			}
		}

		//html for search results is generated.
		//now, lets calculate the total number of search results, for all different types
		if( $args['count_total'] ){
			$all_items_count = 0;
			foreach( $args['searchable_items'] as $search_type ){
				if( !isset($this->search_helpers[$search_type]))
					continue;

				$obj = $this->search_helpers[$search_type];
				$total_match_count = $obj->get_total_match_count( $this->search_args['search_term'] );
				$this->search_results[$search_type]['total_match_count'] = $total_match_count;

				$all_items_count += $total_match_count;
			}

			$this->search_results['all']['total_match_count'] = $all_items_count;
		}
	}

		public function load_search_helpers(){
			$searchable_types = buddyboss_global_search()->option('items-to-search');

			if( !empty( $searchable_types ) ){
				//load the helper type parent class
				require_once( BUDDYBOSS_GLOBAL_SEARCH_PLUGIN_DIR . 'includes/search-types/class.BBoss_Global_Search_Type.php' );

				//load and associate helpers one by one
				if( in_array( 'posts', $searchable_types ) ){
					require_once( BUDDYBOSS_GLOBAL_SEARCH_PLUGIN_DIR . 'includes/search-types/class.BBoss_Global_Search_Posts.php' );
					$this->search_helpers['posts'] = BBoss_Global_Search_Posts::instance();
				}

				if( in_array( 'groups', $searchable_types ) ){
					require_once( BUDDYBOSS_GLOBAL_SEARCH_PLUGIN_DIR . 'includes/search-types/class.BBoss_Global_Search_Groups.php' );
					$this->search_helpers['groups'] = BBoss_Global_Search_Groups::instance();
				}

				if( in_array( 'members', $searchable_types ) ){
					require_once( BUDDYBOSS_GLOBAL_SEARCH_PLUGIN_DIR . 'includes/search-types/class.BBoss_Global_Search_Members.php' );
					$this->search_helpers['members'] = BBoss_Global_Search_Members::instance();
				}

				if( in_array( 'forums', $searchable_types ) ){
					require_once( BUDDYBOSS_GLOBAL_SEARCH_PLUGIN_DIR . 'includes/search-types/class.BBoss_Global_Search_Forums.php' );
					$this->search_helpers['forums'] = BBoss_Global_Search_Forums::instance();
				}

				if( in_array( 'activity', $searchable_types ) ){
					require_once( BUDDYBOSS_GLOBAL_SEARCH_PLUGIN_DIR . 'includes/search-types/class.BBoss_Global_Search_Activities.php' );
					$this->search_helpers['activity'] = BBoss_Global_Search_Activities::instance();
				}

				if( in_array( 'messages', $searchable_types ) ){
					require_once( BUDDYBOSS_GLOBAL_SEARCH_PLUGIN_DIR . 'includes/search-types/class.BBoss_Global_Search_Messages.php' );
					$this->search_helpers['messages'] = BBoss_Global_Search_Messages::instance();
				}

				/**
				 * Hook to load helper classes for additional search types.
				 */
				$additional_search_helpers = apply_filters( 'bboss_global_search_additional_search_helpers', array() );
				if( !empty( $additional_search_helpers ) ){
					foreach( $additional_search_helpers as $search_type=>$helper_object ){
						/**
						 * All helper classes must inherit from BBoss_Global_Search_Type
						 */
						if( !isset( $this->search_helpers[$search_type] ) && is_a( $helper_object, 'BBoss_Global_Search_Type' ) ){
							$this->search_helpers[$search_type] = $helper_object;
						}
					}
				}
			}
		}

		public function ajax_search(){
			check_ajax_referer( 'bboss_global_search_ajax', 'nonce' );

			if( isset($_POST["view"]) && $_POST["view"] == "content") {

				$_GET["s"] = $_POST["s"];
				if(!empty($_POST["subset"])) {
					$_GET["subset"] = $_POST["subset"];
				}

				if(!empty($_POST["list"])) {
					$_GET["list"] = $_POST["list"];
				}

				$content = "";

				buddyboss_global_search()->search->prepare_search_page();
				$content = buddyboss_global_search_buffer_template_part( 'results-page-content', '', false );

				echo $content;

				die();
			}

			$args = array(
				'search_term'	=> $_REQUEST['search_term'],
				//How many results should be displyed in autosuggest?
				//@todo: give a settings field for this value
				'per_page'		=> 5,
				'count_total'	=> false,
				'template_type'	=> 'ajax',
			);

			$this->do_search( $args );

			$search_results = array();
			if( isset( $this->search_results['all']['items'] ) && !empty( $this->search_results['all']['items'] ) ){
				/* ++++++++++++++++++++++++++++++++++
				group items of same type together
				++++++++++++++++++++++++++++++++++ */
				$types = array();
				foreach( $this->search_results['all']['items'] as $item_id=>$item ){
					$type = $item['type'];
					if( empty( $types ) || !in_array( $type, $types ) ){
						$types[] = $type;
					}
				}

				$new_items = array();
				foreach( $types as $type ){
					$first_html_changed = false;
					foreach( $this->search_results['all']['items'] as $item_id=>$item ){
						if( $item['type']!= $type )
							continue;

						//add group/type title in first one
						/*
						if( !$first_html_changed ){
							//this filter can be used to change display of 'posts' to 'Blog Posts' etc..
							$label = apply_filters( 'bboss_global_search_label_search_type', $type );

							//$item['html'] = "<div class='results-group results-group-{$type}'><span class='results-group-title'>{$label}</span></div>" . $item['html'];
							$first_html_changed = true;
						}

						*/

						$new_items[$item_id] = $item;
					}
				}

				$this->search_results['all']['items'] = $new_items;

				/* _______________________________ */
				$url = $this->search_page_search_url();
				$url = esc_url(add_query_arg( array( 'no_frame' => '1' ), $url ));
				$type_mem = "";
				foreach( $this->search_results['all']['items'] as $item_id=>$item ){
					$new_row = array( 'value'=>$item['html'] );
					$type_label = apply_filters( 'bboss_global_search_label_search_type', $item['type'] );
					$new_row['type'] = $item['type'];
					$new_row['type_label'] = "";
					$new_row['value'] = $item['html'];
					if( isset( $item['title'] ) ){
						$new_row['label'] = $item['title'];
					}

					if($type_mem != $new_row['type']) {
						$type_mem = $new_row['type'];
						$cat_row = $new_row;
						$cat_row["type"] = $item['type'];
						$cat_row['type_label'] = $type_label;
						$category_search_url = esc_url(add_query_arg( array( 'subset' => $item['type'] ), $url ));
						$html = "<span><a href='" . esc_url( $category_search_url ) . "'>" . $type_label . "</a></span>";
                        $cat_row["value"] = apply_filters('buddypress_gs_autocomplete_category', $html, $item['type'], $url, $type_label);
						$search_results[] = $cat_row;
					}

					$search_results[] = $new_row;
				}

				$all_results_row = array(
					"value" => "<div class='bboss_ajax_search_item allresults'><a href='" . esc_url( $url ) . "'>" . sprintf( __( "View all results for '%s'", "buddypress-global-search" ), $_REQUEST['search_term'] ) . "</a></div>",
					"type"	=> 'view_all_type',
					"type_label"	=> ''
				);
				$search_results[] = $all_results_row;
			} else {
				//@todo give a settings screen for this field
				$search_results[] = array(
					'value' => '<div class="bboss_ajax_search_item noresult">' . sprintf( __( "Nothing found for '%s'", "buddypress-global-search" ), $_REQUEST['search_term'] ) . '</div>',
					'label'	=> $_REQUEST['search_term']
				);
			}

			die( json_encode( $search_results ) );
		}


		/**
		 * setup everything before starting to display content for search page.
		 */
		public function prepare_search_page(){
			$args = array();
			if( isset( $_GET['subset'] ) && !empty( $_GET['subset'] ) ){
				$args['search_subset'] = $_GET['subset'];
			}

			if( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ){
				$args['search_term'] = $_GET['s'];
			}

			if( isset( $_GET['list'] ) && !empty( $_GET['list'] ) ){
				$current_page = (int)$_GET['list'];
				if( $current_page > 0 ){
					$args['current_page'] = $current_page;
				}
			}

			$args = apply_filters( 'bboss_global_search_search_page_args', $args );
			$this->do_search( $args );
		}


		public function search_page_url($value=""){
			$url = home_url( '/' );

			if(!empty($value)){
				$url = esc_url(add_query_arg( 's',urlencode($value), $url ));
			}

			return $url;
		}

		/**
		 * function to return full search url, added with search terms and other filters
		 */
		public function search_page_search_url(){
			$base_url = $this->search_page_url();
			$full_url = esc_url(add_query_arg( 's', urlencode( $this->search_args['search_term'] ), $base_url ));
			//for now we only have one filter in url
			return $full_url;
		}

		public function print_tabs(){
			$search_url = $this->search_page_search_url();

			//first print the 'all results' tab
			$class = 'all'==$this->search_args['search_subset'] ? 'active current' : '';
			//this filter can be used to change display of 'all' to 'Everything' etc..
			$label = apply_filters( 'bboss_global_search_label_search_type', 'all' );

			if( $this->search_args['count_total'] ){
				$label .= "<span class='count'>" . $this->search_results['all']['total_match_count'] . "</span>";
			}

			$tab_url = $search_url;
			echo "<li class='{$class}'><a href='" . esc_url($tab_url) . "'>{$label}</a></li>";

			//then other tabs
			foreach( $this->search_args['searchable_items'] as $item ){
				$class = $item==$this->search_args['search_subset'] ? 'active current' : '';
				//this filter can be used to change display of 'posts' to 'Blog Posts' etc..
				$label = apply_filters( 'bboss_global_search_label_search_type', $item );

				if(empty($this->search_results[$item]['total_match_count'])) {
					continue; //skip tab
				}

				if( $this->search_args['count_total'] ){
					$label .= "<span class='count'>" . (int)$this->search_results[$item]['total_match_count'] . "</span>";
				}

				$tab_url = esc_url(add_query_arg( 'subset', $item, $search_url ));
				echo "<li class='{$class} {$item}' data-item='{$item}'><a href='" . esc_url($tab_url) . "'>{$label}</a></li>";
			}
		}

		public function print_results(){
			$current_tab = $this->search_args['search_subset'];
			if( isset( $this->search_results[$current_tab]['items'] ) && !empty( $this->search_results[$current_tab]['items'] ) ){
				foreach( $this->search_results[$current_tab]['items'] as $item_id=>$item ){
					echo $item['html'];
				}

				if( function_exists( 'emi_generate_paging_param' ) ){
					$page_slug = untrailingslashit( str_replace( home_url(), '', $this->search_page_url() ) );
					emi_generate_paging_param(
						$this->search_results[$current_tab]['total_match_count'],
						$this->search_args['per_page'],
						$this->search_args['current_page'],
						$page_slug
					);
				}
			} else {
				buddyboss_global_search_buffer_template_part( 'no-results', $current_tab );
			}
		}

		public function get_search_term(){
			return isset( $this->search_args['search_term'] ) ? $this->search_args['search_term'] : '';
		}

	const BOOKING_YES = 'yes';
	const BOOKING_MAYBE = 'maybe';
	const BOOKING_NO = 'no';

	public function remove_invite()
	{
		error_reporting(E_ALL);
		if(!$_REQUEST['group_id']){$oReturn->message = __('group_id is Required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['user_id']){$oReturn->message = __('user_id is Required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['friend_id']){$oReturn->message = __('friend_id is Required.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "DELETE FROM `wp_bp_groups_members` WHERE group_id = ".$_REQUEST['group_id']." AND user_id = ".$_REQUEST['friend_id']." AND inviter_id = ".$_REQUEST['user_id']."";


		if ($conn->query($sql) === TRUE) {
		    $oReturn = new stdClass();
			$oReturn->status = "ok";
			$oReturn->success = "success";
			$oReturn->data = array();
		} else {

		    $oReturn = new stdClass();
			$oReturn->status = "";
			$oReturn->error = "Error deleting record: " . $conn->error;
			$oReturn->data = array();
		}


		return $oReturn;
	}

	function get_members_for_invite()
	{
		error_reporting(E_ALL);
		if(!$_REQUEST['user_id']){$oReturn->message = __('user_id is Required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['group_id']){$oReturn->message = __('group_id is Required.','aheadzen'); return $oReturn;}
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$members = array();
		$sql = "SELECT * FROM `wp_bp_friends` WHERE `initiator_user_id` = ".$_REQUEST['user_id']." AND is_confirmed = 1 UNION ALL
SELECT * FROM `wp_bp_friends` WHERE friend_user_id = ".$_REQUEST['user_id']." AND is_confirmed = 1";
		$res = $conn->query($sql);
		if ($res->num_rows > 0) {
			$i=0;
			while($row = $res->fetch_assoc())
			{
				if($row['initiator_user_id'] != $_REQUEST['user_id'])
				{
					$user_id = $row['initiator_user_id'];
				}
				else
				{
					$user_id = $row['friend_user_id'];
				}

				$sql = "SELECT * FROM `wp_bp_groups_members` WHERE user_id = ".$user_id." AND group_id = ".$_REQUEST['group_id'].";";
				$res3 = $conn->query($sql);
				$row2 = $res3->fetch_assoc();
				$member[$i] = $row;
				if(isset($row2['id']) && $row2['id'] != "")
				{
					$member[$i]['is_invited']	= 1;
				}
				else
				{
					$member[$i]['is_invited']	= 0;
				}
				if($row['initiator_user_id'] != $_REQUEST['user_id'])
				{
					$user = get_userdata( $row['initiator_user_id'] );
					$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row['initiator_user_id'], 'html'=>false, 'type'=>'full'));
					$user->data->avatar = $useravatar_url2;
				}
				else
				{
					$user = get_userdata( $row['friend_user_id'] );
					$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$row['friend_user_id'], 'html'=>false, 'type'=>'full'));
					$user->data->avatar = $useravatar_url2;
				}

				$member[$i]['user'] = $user->data;

				$i++;
			}
		}

		$oReturn = new stdClass();
		$oReturn->status = "ok";
		$oReturn->success = "success";
		$oReturn->data = $member;
		return $oReturn;

	}

	public function get_rsvp()
	{
		error_reporting(E_ALL);
		global $wpdb;
		$oReturn = new stdClass();

		if(!$_REQUEST['event_id']){$oReturn->message = __('Event ID is Required.','aheadzen'); return $oReturn;}
		$rsvps = array(
			self::BOOKING_YES => array(),
			self::BOOKING_MAYBE => array(),
			self::BOOKING_NO => array(),
		);

		$bookings = $wpdb->get_results($wpdb->prepare("SELECT user_id, status FROM wp_eab_bookings  WHERE event_id = %d ORDER BY timestamp;", $_REQUEST['event_id']));
		foreach ($bookings as $booking) {
			$user_data = get_userdata($booking->user_id);
			$useravatar_url2 = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$booking->user_id, 'html'=>false, 'type'=>'full'));
			$user_data->data->avatar = $useravatar_url2;
			$rsvps[$booking->status][] = $user_data->data;
		}
		$oReturn->status = "ok";
		$oReturn->success = "success";
		$oReturn->data = $rsvps;

		return $oReturn;
	}

	function get_organized_event()
   {
		$res =  do_shortcode('eab_my_events', 22);
  		print "<pre>";
  		print_r($res);
  		die;
  		$events = Eab_CollectionFactory::get_user_organized_events(22);
		//$events = Eab_CollectionFactory::get_user_organized_events($user_id);
	  	//$events = Eab_Template::get_user_organized_events(46);

	  	//$events = get_user_organized_events(22);
		//	return apply_filters('eab-collection', $events );
		print "<pre>"; print_r($events);die;
	  	$me = new Eab_OrganizerCollection(22);
		$res =  $me->to_collection();
	  	$events = get_user_organized_events(22);
		print_r($res);die("vishal");
		//return apply_filters('eab-collection', $events );
	  	print_r($events);
	  	die("vishal");
   }

	public function set_action_for_event()
  	{
  		error_reporting(E_ALL);
  	 	global $wpdb, $current_user;
		if (isset($_REQUEST['event_id']) && isset($_REQUEST['user_id'])) {
		    $booking_actions = array('yes' => 'yes', 'maybe' => 'maybe', 'no' => 'no');

		    $event_id = intval($_POST['event_id']);
		    $booking_action = $booking_actions[$_POST['event_action']];
		    $user_id = apply_filters('eab-rsvp-user_id', $current_user->ID, $_POST['user_id']);
			$user_id = $_REQUEST['user_id'];
		   // do_action( 'incsub_event_booking', $event_id, $user_id, $booking_action );
		    if (isset($_POST['event_action']) && $_POST['event_action'] == 'yes') {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(self::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'yes') ON DUPLICATE KEY UPDATE `status` = 'yes';", $event_id, $user_id)
				);
				// --todo: Add to BP activity stream
				//do_action( 'incsub_event_booking_yes', $event_id, $user_id );
				$this->recount_bookings($event_id);
				//wp_redirect('?eab_success_msg=' . Eab_Template::get_success_message_code(Eab_EventModel::BOOKING_YES));

		    }
		    if (isset($_POST['event_action'])  && $_POST['event_action'] == 'maybe') {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(self::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'maybe') ON DUPLICATE KEY UPDATE `status` = 'maybe';", $event_id, $user_id)
				);
				// --todo: Add to BP activity stream
				//do_action( 'incsub_event_booking_maybe', $event_id, $user_id );
				$this->recount_bookings($event_id);
				//wp_redirect('?eab_success_msg=' . Eab_Template::get_success_message_code(Eab_EventModel::BOOKING_MAYBE));

		    }
		    if (isset($_POST['event_action'])  && $_POST['event_action'] == 'no') {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(self::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'no') ON DUPLICATE KEY UPDATE `status` = 'no';", $event_id, $user_id)
				);
				// --todo: Remove from BP activity stream
				//do_action( 'incsub_event_booking_no', $event_id, $user_id );
				$this->recount_bookings($event_id);

		    }
			$oReturn = new stdClass();
		    $oReturn->status = "ok";
			$oReturn->success = "success";
			$oReturn->data = array();
			return $oReturn;
		}
    }


   function recount_bookings($event_id) {
		global $wpdb;

		// Yes
		$yes_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".self::tablename(self::BOOKING_TABLE)." WHERE `status` = 'yes' AND event_id = %d;", $event_id));
	    	update_post_meta($event_id, 'incsub_event_yes_count', $yes_count);

		// Maybe
		$maybe_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".self::tablename(self::BOOKING_TABLE)." WHERE `status` = 'maybe' AND event_id = %d;", $event_id));
	    	update_post_meta($event_id, 'incsub_event_maybe_count', $maybe_count);
		update_post_meta($event_id, 'incsub_event_attending_count', $maybe_count+$yes_count);

		// No
		$no_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".self::tablename(self::BOOKING_TABLE)." WHERE `status` = 'no' AND event_id = %d;", $event_id));
		update_post_meta($event_id, 'incsub_event_no_count', $no_count);
    }

	function get_membership_request_list()
	{
		error_reporting(E_ALL);
		global $wpdb;
		$oReturn = new stdClass();

		if(!$_REQUEST['group_id']){
			$oReturn->message = __('Group ID is Required.','aheadzen'); return $oReturn;
		}

		$user_ids = BP_Groups_Member::get_all_membership_request_user_ids( $_REQUEST['group_id'] );

		$user = array();
		foreach($user_ids as $ID)
		{
			$res = get_userdata($ID);
			$avatar_url = bp_core_fetch_avatar(array('object'=>'member','item_id'=>$ID, 'html'=>false, 'type'=>'full'));

			$res->data->avatar = $avatar_url;

			$user[] = $res->data;
		}
		$oReturn = new stdClass();
	    $oReturn->status = "ok";
		$oReturn->success = "success";
		$oReturn->data = $user;
		return $oReturn;
		die;
	}

	function accept_membership_request()
	{
		error_reporting(E_ALL);
		global $wpdb;
		$oReturn = new stdClass();

		if(!$_REQUEST['group_id']){
			$oReturn->message = __('Group ID is Required.','aheadzen'); return $oReturn;
		}
		if(!$_REQUEST['user_id']){
			$oReturn->message = __('User ID is Required.','aheadzen'); return $oReturn;
		}
		$res = groups_accept_membership_request( false, $_REQUEST['user_id'], $_REQUEST['group_id'] );
		if($res)
		{
			$oReturn = new stdClass();
		    $oReturn->status = "ok";
			$oReturn->success = "success";
			$oReturn->data = array();
			return $oReturn;
		}
		else
		{
			$oReturn = new stdClass();
		    $oReturn->status = "false";
			$oReturn->error = "error";
			$oReturn->data = array();
			return $oReturn;

		}
	}

	function reject_membership_request()
	{
		error_reporting(E_ALL);
		global $wpdb;
		$oReturn = new stdClass();

		if(!$_REQUEST['group_id']){
			$oReturn->message = __('Group ID is Required.','aheadzen'); return $oReturn;
		}
		if(!$_REQUEST['user_id']){
			$oReturn->message = __('User ID is Required.','aheadzen'); return $oReturn;
		}
		$res = groups_delete_membership_request( false, $_REQUEST['user_id'], $_REQUEST['group_id'] );
		if($res)
		{
			$oReturn = new stdClass();
		    $oReturn->status = "ok";
			$oReturn->success = "success";
			$oReturn->data = array();
			return $oReturn;
		}
		else
		{
			$oReturn = new stdClass();
		    $oReturn->status = "false";
			$oReturn->error = "error";
			$oReturn->data = array();
			return $oReturn;

		}
	}

	public function get_recent_checkins()
	{
		error_reporting(0);
		global $wpdb;
		$oReturn = new stdClass();
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}


		if(!$_REQUEST['user_id']){
			$oReturn->message = __('User ID is Required.','aheadzen'); return $oReturn;
		}
		if(isset($_REQUEST['group_id']) && $_REQUEST['group_id'] != "")
		{
			$sql = "SELECT DISTINCT
						  location,`id`, `user_id`, `component`, `type`, `action`, `content`,
						  `primary_link`, `item_id`, `secondary_item_id`, `date_recorded`,
						  `is_spam`, `is_shared`, `latitude`, `longitude`
						FROM
						  wp_bp_activity
						WHERE TYPE = 'checkin'
						  AND user_id = ".$_REQUEST['user_id']." AND item_id = ".$_REQUEST['group_id']."
						GROUP BY location
						ORDER BY date_recorded DESC LIMIT 10";

		}
		else
		{
			$sql = "SELECT DISTINCT location, `id`, `user_id`, `component`, `type`, `action`, `content`, `primary_link`,
					  `item_id`,`secondary_item_id`,`date_recorded`,`is_spam`,`is_shared`,`latitude`,`longitude`
					FROM
					  wp_bp_activity
					WHERE TYPE = 'checkin'   AND component != 'groups'
					  AND user_id = ".$_REQUEST['user_id']." GROUP BY location ORDER BY date_recorded DESC LIMIT 10";



		}

		$res = $conn->query($sql);
		$response = array();
		while($row = $res->fetch_assoc())
		{
			$response[] = $row;
		}

		if($response)
		{
			$oReturn = new stdClass();
		    $oReturn->status = "ok";
			$oReturn->success = "success";
			$oReturn->data = $response;
			return $oReturn;
		}
		else
		{
			$oReturn = new stdClass();
		    $oReturn->status = "false";
			$oReturn->error = "error";
			$oReturn->data = array();
			return $oReturn;

		}

	}

	public function get_team_by_id()
	{
		error_reporting(0);
		global $wpdb;
		$oReturn = new stdClass();
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}


		if(!$_REQUEST['user_id']){
			$oReturn->message = __('User ID is Required.','aheadzen'); return $oReturn;
		}
		if(!$_REQUEST['slug']){
			$oReturn->message = __('Slug is Required.','aheadzen'); return $oReturn;
		}

		$sql = "SELECT * FROM wp_bp_groups WHERE slug = '".$_REQUEST['slug']."'";
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

		if($groupData['creator_id'] == $_REQUEST['user_id'])
			{
				$groupData['is_admin'] = 1;
			}
			else
			{
				$groupData['is_admin'] = 0;
			}

			$sql = "SELECT id FROM `wp_bp_team_follow` WHERE author_id = ".$_REQUEST['user_id']." AND group_id = ".$groupData['id']."";
			$re3 = $conn->query($sql);
			if ($re3->num_rows > 0) {
				$row3 = $re3->fetch_assoc();

				if(!empty($row3))
				{

					$groupData['is_follow'] = 1;
				}
				else
				{
					$groupData['is_follow'] = 0;
				}
			}else{
				$groupData['is_follow'] = 0;
			}

			$isMember = groups_is_user_member($_REQUEST['user_id'],$groupData['id']);
			$isBanned = groups_is_user_banned($_REQUEST['user_id'],$groupData['id']);
			//echo "<br/>".$isMember . "  -> ". $_REQUEST['user_id'] . " -> ". $row1->id;
			$groupData['is_member'] = $isMember;
			$groupData['is_banned'] = is_null($isBanned) ? 0 : 1;


			$oReturn->is_invited = groups_check_user_has_invite((int) $_REQUEST['user_id'], $row1->id, $this->type);
			$groupData['is_invited'] = is_null($oReturn->is_invited) ? 0 : 1;

			$oReturn->membership_requested = groups_check_for_membership_request((int) $_REQUEST['user_id'], $row1->id);
			$groupData['is_pending'] = $oReturn->membership_requested;

			$oReturn = new stdClass();
		    $oReturn->status = "ok";
			$oReturn->success = "success";
			$oReturn->data = $groupData;
			return $oReturn;
	}


	public function test()
	{
		$data = bp_activity_get_user_favorites(44);
		if(in_array("2225", $data))
		{
			echo "true";
		}
		else{
			echo "false";
		}
		print "<pre>";
		print_r($data);
		die;

	}

    public function __call($sName, $aArguments) {
        if (class_exists("BUDDYPRESS_JSON_API_FUNCTION") &&
                method_exists(BUDDYPRESS_JSON_API_FUNCTION, $sName) &&
                is_callable("BUDDYPRESS_JSON_API_FUNCTION::" . $sName)) {
            try {
                return call_user_func_array("BUDDYPRESS_JSON_API_FUNCTION::" . $sName, $aArguments);
            } catch (Exception $e) {
                $oReturn = new stdClass();
                $oReturn->status = "error";
                $oReturn->msg = $e->getMessage();
                die(json_encode($oReturn));
            }
        }
        else
            return NULL;
    }

    public function __get($sName) {
        return isset(BUDDYPRESS_JSON_API_FUNCTION::$sVars[$sName]) ? BUDDYPRESS_JSON_API_FUNCTION::$sVars[$sName] : NULL;
    }

    function sendPushNotification($push_message, $endpointArn, $senderdata, $reciever_user_type ,$recieverdata) {


        //error_reporting(E_ALL);
        /*print "<pre>";
        echo $push_message;
        print_r($endpointArn);
        print_r($senderdata);
        echo $reciever_user_type;
        print_r($recieverdata);*/



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
               // if ($reciever_user_type == 1) { // User is Driver
                    if ($recieverdata->device_type == 1) {  // device is andriod

                        $msgpayload = json_encode(array('data' => array('message' => $push_message, 'sound' => $sound, $senderdata, 'notification_type_id' => $recieverdata->notification_type_id)));

                        //$type= 'GCM';
                        $response = $sns->publish(array(
                            'TargetArn' => $endpointArn,
                            'MessageStructure' => 'json',
                            'Message' => json_encode(array(
                                'default' => $push_message,
                                'GCM' => $msgpayload
                            ))
                        ));
                    } else if ($recieverdata->device_type == 2) { // device is iphone

                        $msgpayload = json_encode(array('aps' => array('alert' => $push_message, 'sound' => $sound, $senderdata, 'notification_type_id' => $recieverdata->notification_type_id)));

                        $response = $sns->publish(array(
                            'TargetArn' => $endpointArn,
                            'MessageStructure' => 'json',
                            'Message' => json_encode(array(
                                'default' => $push_message,
                                'APNS_SANDBOX' => $msgpayload
                            ))
                        ));

                    }
                //}

                /*$pushData = array();
                $pushData['rideID'] = $senderdata['rideID'];
                $pushData['payload'] = $msgpayload;
                $pushData['response'] = $response;
                $pushData['notificationType'] = $senderdata['tripId'];
                $pushData['status'] = 1;
                $pushData['created'] = date("Y-m-d H:i:s");
                $pushObj = new Tblpushnotificationhistory();
                $pushObj->setData($pushData);
                $pushObj->insertData();*/
                //error_log(print_r($msgpayload,true),3,"payload.txt");
                //error_log(print_r($response,true),3,"push.txt");
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

}



function bp_insert_activity_meta_fun($new_content, $content){
	return $content;
}
function bp_activity_truncate_entry_fun($excerpt, $text, $append_text){
	$excerpt = str_replace($append_text,'',$excerpt);
	return $excerpt;
}

function bpaz_user_name_from_email($text){

	if($thepos = strpos($text,'@')){
		$text = substr($text,0,$thepos);
	}
	return $text;
}
function aheadzen_check_valid_user($userid,$pw){
	$user = get_userdata($userid);
	if($user && wp_check_password($pw,$user->user_pass,$user->ID)){
		return true;
	}
	return false;
}

function aheadzen_bp_loggedin_user_id_function($user_id){
	if(empty($user_id) && !empty($_GET['userid'])){
		$user_id = $_GET['userid'];
	}
	return $user_id;
}
