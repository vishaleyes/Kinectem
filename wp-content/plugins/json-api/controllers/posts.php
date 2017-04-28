<?php
/*
Controller name: Posts
Controller description: Data manipulation methods for posts
*/
//header('Content-Type: application/json');
class JSON_API_Posts_Controller {

    /**
     * Returns an Array with registered userid & valid cookie
     * @param String username: username to register
     * @param String email: email address for user registration
     * @param String user_pass: user_pass to be set (optional)
     * @param String display_name: display_name for user
     */
    public function __construct() {
        global $json_api;
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        // allow only connection over https. because, well, you care about your passwords and sniffing.
        // turn this sanity-check off if you feel safe inside your localhost or intranet.
        // send an extra POST parameter: insecure=cool
        /* API LOGS */
        $dt = date('Y-m-d H:i:s');
        $fp = fopen('kinectem.txt', 'a+');
        fwrite($fp, "\r\r\n<div style='background-color:#F2F2F2; color:#222279; font-weight: bold; padding:10px;box-shadow: 0 5px 2px rgba(0, 0, 0, 0.25);'>");
        fwrite($fp, "<b>API call Time</b> : <font size='6' style='color:orange;'><b><i>" . $dt . "</i></b></font> <br>");
        fwrite($fp, "<b>Function Name</b> : <font size='6' style='color:orange;'><b><i>".$_SERVER[REQUEST_URI]."</i></b></font>");
        fwrite($fp, "\r\r\n\n");
        fwrite($fp, "<b>PARAMS</b> : " . print_r($_REQUEST, true));
        fwrite($fp, "\r\r\n");
        $link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . '' . print_r($_SERVER['REQUEST_URI'], true) . "";
        fwrite($fp, "<b>URL</b> :<a style='text-decoration:none;color:#4285F4' target='_blank' href='" . $link . "'> " . $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . '' . print_r($_SERVER['REQUEST_URI'], true) . "</a>");
        fwrite($fp, "</div>\r\r\n");
        fclose($fp);
        /* API LOGS */

    }

  public function create_post() {
    global $json_api;
   /* if (!current_user_can('edit_posts')) {
      $json_api->error("You need to login with a user that has 'edit_posts' capacity.", 403);
    }*/
    /*if (!$json_api->query->nonce) {
      $json_api->error("You must include a 'nonce' value to create posts. Use the `get_nonce` Core API method.", 403);
    }
    $nonce_id = $json_api->get_nonce_id('posts', 'create_post');
    if (!wp_verify_nonce($json_api->query->nonce, $nonce_id)) {
      $json_api->error("Your 'nonce' value was incorrect. Use the 'get_nonce' API method.", 403);
    }
    nocache_headers();*/
    $post = new JSON_API_Post();
    $id = $post->create($_REQUEST);
    if (empty($id)) {
      $json_api->error("Could not create post.", 500);
    }
    return array(
      'post' => $post
    );
  }
  
  public function update_post() {
    global $json_api;
    $post = $json_api->introspector->get_current_post();
    if (empty($post)) {
      $json_api->error("Post not found.");
    }
    if (!current_user_can('edit_post', $post->ID)) {
      $json_api->error("You need to login with a user that has the 'edit_post' capacity for that post.", 403);
    }
    if (!$json_api->query->nonce) {
      $json_api->error("You must include a 'nonce' value to update posts. Use the `get_nonce` Core API method.", 403);
    }
    $nonce_id = $json_api->get_nonce_id('posts', 'update_post');
    if (!wp_verify_nonce($json_api->query->nonce, $nonce_id)) {
      $json_api->error("Your 'nonce' value was incorrect. Use the 'get_nonce' API method.", 403);
    }
    nocache_headers();
    $post = new JSON_API_Post($post);
    $post->update($_REQUEST);
    return array(
      'post' => $post
    );
  }
  
  public function delete_post() {
    global $json_api;
    $post = $json_api->introspector->get_current_post();
    if (empty($post)) {
      $json_api->error("Post not found.");
    }
    if (!current_user_can('edit_post', $post->ID)) {
      $json_api->error("You need to login with a user that has the 'edit_post' capacity for that post.", 403);
    }
    if (!current_user_can('delete_posts')) {
      $json_api->error("You need to login with a user that has the 'delete_posts' capacity.", 403);
    }
    if ($post->post_author != get_current_user_id() && !current_user_can('delete_other_posts')) {
      $json_api->error("You need to login with a user that has the 'delete_other_posts' capacity.", 403);
    }
    if (!$json_api->query->nonce) {
      $json_api->error("You must include a 'nonce' value to update posts. Use the `get_nonce` Core API method.", 403);
    }
    $nonce_id = $json_api->get_nonce_id('posts', 'delete_post');
    if (!wp_verify_nonce($json_api->query->nonce, $nonce_id)) {
      $json_api->error("Your 'nonce' value was incorrect. Use the 'get_nonce' API method.", 403);
    }
    nocache_headers();
    wp_delete_post($post->ID);
    return array();
  }
  
  
  public function save_event () {  
		error_reporting(0);
		global $current_user;
		header('Content-type: application/json');
		if (!isset($_POST)) die(json_encode(array(
			'status' => 0,
			'message' => __('No data received', Eab_EventsHub::TEXT_DOMAIN),
		)));
		if (!isset($_POST['user_id'])) die(json_encode(array(
			'status' => 0,
			'message' => __('user_id parameter is required.', Eab_EventsHub::TEXT_DOMAIN),
		)));
		if (!isset($_POST['title'])) die(json_encode(array(
			'status' => 0,
			'message' => __('title parameter is required.', Eab_EventsHub::TEXT_DOMAIN),
		)));
		if (!isset($_POST['content'])) die(json_encode(array(
			'status' => 0,
			'message' => __('content parameter is required.', Eab_EventsHub::TEXT_DOMAIN),
		)));
		if (!isset($_POST['start'])) die(json_encode(array(
			'status' => 0,
			'message' => __('start parameter is required.', Eab_EventsHub::TEXT_DOMAIN),
		)));
		

		//$data = $_POST['data'];
		$data = $_POST;
		/*if (!$this->_check_perms((int)$data['id'])) die(json_encode(array(
			'status' => 0,
			'message' => __('Insufficient privileges', Eab_EventsHub::TEXT_DOMAIN),
		)));*/
		$post = array();

		$start = date('Y-m-d H:i', strtotime($data['start']));
		//$end = date('Y-m-d H:i', strtotime('+'.$data['duration'].' hours',strtotime($data['start'])));
        $end = date('Y-m-d H:i', strtotime($data['end']));

		$post_type = get_post_type_object(Eab_EventModel::POST_TYPE);
		$post['post_title'] = strip_tags($data['title']);
		$post['post_content'] = current_user_can('unfiltered_html') ? $data['content'] : wp_filter_post_kses($data['content']);
		$post['post_status'] = current_user_can($post_type->cap->publish_posts) ? 'publish' : 'publish';
		$post['post_type'] = Eab_EventModel::POST_TYPE;
		$post['post_author'] = $_POST['user_id'];

		$data['featured'] = !empty($data['featured'])
			? (is_numeric($data['featured']) ? (int)$data['featured'] : false)
			: false
		;
		/*print "<pre>";
		print_r($_POST);
		die;*/

		if (isset($data['id']) &&  (int)$data['id']) {
			$post['ID'] = $post_id = $data['id'];
			
			wp_update_post($post);
			/* Added by Ashok */
			update_post_meta($post_id, '_thumbnail_id', $data['featured']);
			/* End of adding by Ashok */
		} else {
			$post_id = wp_insert_post($post);
			/* Added by Ashok */
			update_post_meta($post_id, '_thumbnail_id', $data['featured']);
			/* End of adding by Ashok */
		}
		if (!$post_id) die(json_encode(array(
			'status' => 0,
			'message' => __('There has been an error saving this Event', Eab_EventsHub::TEXT_DOMAIN),
		)));

		update_post_meta($post_id, 'incsub_event_start', $start);
		update_post_meta($post_id, 'incsub_event_end', $end);
		if(isset($data['status']))
			update_post_meta($post_id, 'incsub_event_status', strip_tags($data['status']));
		if(isset($data['duration']))
			update_post_meta($post_id, 'incsub_event_duration', strip_tags($data['duration']));
		if(isset($data['notes']))
		{
			update_post_meta($post_id, 'incsub_event_notes', strip_tags($data['notes']));
		}
		if(isset($data['reoccurance']))
			update_post_meta($post_id, 'incsub_event_reoccurance', strip_tags($data['reoccurance']));
		if(isset($data['opponent']))
			update_post_meta($post_id, 'incsub_event_opponent', strip_tags($data['opponent']));
		
		if(isset($data['is_group_event']) && $data['is_group_event'] == 1)
			update_post_meta($post_id, 'eab_event-bp-group_event', strip_tags($data['team_id']));	
			


		$venue_map = get_post_meta($post_id, 'agm_map_created', true);
		if (!$venue_map && $data['venue'] && class_exists('AgmMapModel')) {
			$model = new AgmMapModel;
			$model->autocreate_map($post_id, false, false, $data['venue']);
		}
		update_post_meta($post_id, 'incsub_event_venue', strip_tags(utf8_encode($data['venue'])));


		$is_paid = (int)$data['is_premium'];
		$fee = $is_paid ? strip_tags($data['fee']) : '';
		update_post_meta($post_id, 'incsub_event_paid', ($is_paid ? '1' : ''));
		update_post_meta($post_id, 'incsub_event_fee', $fee);
		do_action('eab-events-fpe-save_meta', $post_id, $data);

		wp_set_post_terms($post_id, array((int)$data['category']), 'eab_events_category', false);


		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$arr_comments = array();
		$sql = "INSERT INTO wp_eab_bookings ( event_id, user_id, `timestamp`, `status`) VALUES
(".$post_id.", ".$_POST['user_id'].", '".date("Y-m-d H:i:s")."', 'yes');";
		$res = $conn->query($sql);

		$message = current_user_can($post_type->cap->publish_posts)
			? __('Event saved and published', Eab_EventsHub::TEXT_DOMAIN)
			: __('Event saved and waiting for approval', Eab_EventsHub::TEXT_DOMAIN);


		//$email_grp_member = $this->_data->get_option('eab_event_bp_group_event_email_grp_member');
		//var_dump($email_grp_member);die;
		$email_grp_member = 1;
		if( isset( $email_grp_member ) &&  $email_grp_member == 1 ) {
			$grp_members = groups_get_group_members( array( 'group_id' => $data['team_id'], 'exclude_admins_mods' => false ) );
			
			foreach( $grp_members['members'] as $member ){
				//echo $member->user_email;
				$subject = __( 'Information about a group event', Eab_EventsHub::TEXT_DOMAIN );
				$subject = apply_filters( 'eab_bp_grp_events_member_mail_subject', $subject, $member, $post_id );
				$message = __( 'Dear ' . $member->display_name . ',<br><br>An event is created/updated. I hope you will join in that event. Check the event here: ' . get_permalink( $post_id ), Eab_EventsHub::TEXT_DOMAIN );
				$message = apply_filters( 'eab_bp_grp_events_member_mail_message', $message, $member, $post_id );
				/*echo $message;
				echo $member->user_email;*/
				wp_mail( $member->user_email, $subject, $message );
			}
		}			
		die(json_encode(array(
			'status' => 'ok',
			'success'=> 'success',
			'post_id' => $post_id,
			'permalink' => get_permalink($post_id),
			'message' => 'Event created successfully',
		)));
	}
	
  
  public function get_event()
  {     error_reporting(0);
 	  	global $wpdb;
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		if(!$_REQUEST['status']){$oReturn->error = __('status is required.','aheadzen'); return $oReturn;}

		$user_id = $_REQUEST['user_id'];
		$status = $_REQUEST['status'];
		
		$statuses = array(
			Eab_EventModel::BOOKING_YES => __('Attending', Eab_EventsHub::TEXT_DOMAIN),
			Eab_EventModel::BOOKING_MAYBE => __('Maybe', Eab_EventsHub::TEXT_DOMAIN),
			Eab_EventModel::BOOKING_NO => __('No', Eab_EventsHub::TEXT_DOMAIN)
		);
		
		$group_id = 0;
		if(isset($_REQUEST['group_id']) && $_REQUEST['group_id'] != '')
		{
			$group_id = $_REQUEST['group_id'];	
		}
		
		if (!in_array($status, array_keys($statuses))) return false; // Unknown status
		$status_name = $statuses[$status];
		$bookings =  $wpdb->get_col($wpdb->prepare("SELECT event_id FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE user_id = %d AND status = %s ORDER BY timestamp desc;", $user_id, $status));
			
		if (!count($bookings)) {
			echo  json_encode(array("status"=>"ok","success"=>"No data found.", "data"=>array()));	
			die;
		}
		
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
		

		$ret = '<div class="wpmudevevents-user_bookings wpmudevevents-user_bookings-' . $status . '">';
		$response = array();
		$i=0;
		foreach ($bookings as $event_id) {
			$event = new Eab_EventModel(get_post($event_id));

			$sql = "SELECT * FROM `wp_posts` WHERE ID = $event_id;";
			$evn = $conn->query($sql);
			$event_post = $evn->fetch_assoc();
			
			
			$response[$i]['id'] = $event_id;
			$response[$i]['event_title'] = htmlspecialchars($event_post['post_title']);
			$response[$i]['event_description'] = htmlspecialchars($event_post['post_content']);
			$response[$i]['is_group_event'] = 0;
			$response[$i]['group_name'] =  "";
			$response[$i]['group_slug'] =  "";

			$sql = "SELECT * FROM `wp_users` WHERE ID = ".$event_post['post_author'];
			$res = $conn->query($sql);
			if ($res->num_rows > 0) {
				$userData = $res->fetch_assoc();
				$response[$i]['hostname'] =  $userData['display_name'];
			}
	 		
			$arr_comments = array();
			$sql = "SELECT * FROM `wp_postmeta` WHERE post_id = $event_id;";

			$res = $conn->query($sql);
			if ($res->num_rows > 0) {
			 while($row = $res->fetch_assoc()) {
				 
					
				 	$bool = true;
					if($row['meta_key'] == 'incsub_event_start') 
					{
						$event_start =  date("Y-m-d",strtotime($row['meta_value']));
						$response[$i]['event_start'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_end') 
					{
						$event_end =  date("Y-m-d",strtotime($row['meta_value']));
						$response[$i]['event_end'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_status') 
					{
						$response[$i]['event_status'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_duration') 
					{
						$response[$i]['event_duration'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_notes') 
					{
						$response[$i]['event_notes'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_reoccurance') 
					{
						$response[$i]['event_reoccurance'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_opponent') 
					{
						$response[$i]['event_opponent'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'agm_map_created') 
					{
						$response[$i]['agm_map_created'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_venue') 
					{
						$response[$i]['event_venue'] =  htmlspecialchars($row['meta_value']);
						
					}
					if($row['meta_key'] == 'incsub_event_paid') 
					{
						if(isset($row['meta_value']) && $row['meta_value'] != '')
						{
							$response[$i]['event_paid'] =  $row['meta_value'];
						}
						else
						{
							$response[$i]['event_paid'] = "0";
						}
					}
					if($row['meta_key'] == 'incsub_event_fee') 
					{
						
						if(isset($row['meta_value']) && $row['meta_value'] != '')
						{
							$response[$i]['event_fee'] =  $row['meta_value'];
						}
						else
						{
							$response[$i]['event_fee'] = "0";
						}
					}
					/*if($row['meta_key'] == 'eab_event-bp-group_event') 
					{
						if($group_id != 0){
							$response[$i]['group_id'] =  $group_id;
						}else{
							$response[$i]['group_id'] =  $row['meta_value'];
						}
						$sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$response[$i]['group_id'];
						$res = $conn->query($sql);
						if ($res->num_rows > 0) {
							$groupData = $res->fetch_assoc();
						}
				 		$response[$i]['group_name'] =  $groupData['name'];
				 		$response[$i]['group_slug'] =  $groupData['slug'];
					}
					else if($group_id != 0)
					{
						unset($response[$i]);
						$bool = false;
					}*/
					if($row['meta_key'] == 'eab_event-bp-group_event') 
					{
						$response[$i]['is_group_event'] = 1;
						if($group_id == 0){
							if($row['meta_value'] != 0){
								$response[$i]['group_id'] =  $row['meta_value'];
							}
							else
							{
								$response[$i]['group_id'] = 0;
							}
						}else{
							$response[$i]['group_id'] =  $group_id;
						}
						if($response[$i]['group_id'] != 0)
						{
							$groupData = array();
							$sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$response[$i]['group_id'];
							$res2 = $conn->query($sql);
							if ($res2->num_rows > 0) {
								$groupData = $res2->fetch_assoc();
								$response[$i]['group_name'] = htmlspecialchars($groupData['name']) ;
								$response[$i]['group_slug'] =  htmlspecialchars($groupData['slug']);
								//$response[$i]['group_id'] =  $response[$i]['group_id'];
							}
							else
							{
								//$response[$i]['group_id'] = 0;
								$response[$i]['group_name'] =  "";
								$response[$i]['group_slug'] =  "";
							}
						}
						else
						{
							$response[$i]['is_group_event'] = 0;
							$response[$i]['group_name'] =  "";
							$response[$i]['group_slug'] =  "";
						}
						
					
					}
					/*else if($group_id != 0 && $row['meta_key'] != 'eab_event-bp-group_event')
					{
						//unset($response[$i]);
						$bool = false;
					}*/
					/*else
					{
						$response[$i]['is_group_event'] = 0;
						if($group_id != 0)
						{	
							$bool = false;
						}
						$response[$i]['group_id'] = 0;
						$response[$i]['group_name'] =  "";
						$response[$i]['group_slug'] =  "";
					}*/
					if($row['meta_key'] == 'incsub_event_yes_count') 
					{
						$response[$i]['event_yes_count'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_maybe_count') 
					{
						$response[$i]['event_maybe_count'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_attending_count') 
					{
						$response[$i]['event_attending_count'] =  $row['meta_value'];
					}

					if(isset($_REQUEST['event_start']) && isset($_REQUEST['event_end'])){
						
						if(strtotime($event_start) >= strtotime($_REQUEST['event_start']) && strtotime($event_end) <= strtotime($_REQUEST['event_end'])){
							
						}else {
							//unset($response[$i]);
							$bool = false;
						}
					} 
			 }
			}
			
			$userData = array();
			
			$response[$i]['event_link'] = urlencode(trim(Eab_Template::get_event_link($event)));
			$response[$i]['event_dates'] =  urlencode(Eab_Template::get_event_dates($event)) ;
			//$response[$i]['event_venue'] =  urlencode(Eab_Template::get_venue_location($event)));

			if(!isset($response[$i]['group_name'])){
				$response[$i]['group_name']  = "";
			}
			if(!isset($response[$i]['group_slug'])){
				$response[$i]['group_slug']  = "";
			}

			
			/*if(isset($bool) && $bool == false)
			{
				$index[] = $i;
				//unset($response[$i]);
			}
			if($group_id != 0)
			{
				if(!isset($response[$i]['is_group_event']))
				{
					$index[] = $i;
					//unset($response[$i]);
				}
			}*/
					
			
			$i++;

		}
		
		
        $arr = array();
		foreach($response as $row)
		{
			if(isset($_REQUEST['event_start']) && $_REQUEST['event_start'] != '' && isset($_REQUEST['event_end']) && $_REQUEST['event_end'] != '') {
				
				if(strtotime($row['event_start']) >= strtotime($_REQUEST['event_start']) && strtotime($row['event_end']) <= strtotime($_REQUEST['event_end'])){
						$arr[] = $row;
				}
				
		 	}else
		 	{
		 		$arr[] = $row;
		 	}
		}
		
		
		/*$arr = array();
		$j=0;
		foreach($response as $r)
		{
			if(isset($index) && is_array($index) && !in_array($j,$index))
			{
				if(isset($_REQUEST['group_id']) && $_REQUEST['group_id'] > 0)
				{
					if(isset($r['group_id']) && $r['group_id'] == $_REQUEST['group_id'])
					{
						
						$arr[] = $r;
					}
				}
				else
				{
					
					$arr[] = $r;
				}
			}
			$j++;
		}*/
		
		//$ret .= '</div>';
		echo json_encode(array("status"=>"ok", "success"=>"success", "data"=>$arr));
		die;
  }

  public function update_event_action()
    {
    	
    	if (isset($_REQUEST['event_id']) && isset($_REQUEST['user_id'])) { 
    		$booking_actions = array('yes' => 'yes', 'maybe' => 'maybe', 'no' => 'no');
    		echo $event_id = intval($_POST['event_id']);
		    echo $booking_action = $booking_actions[$_POST['action_yes']];
		    echo $user_id = $_REQUEST['user_id'];
    		die;
    	}else {
    		print_r($_REQUEST);die;
    	}
    }
  
   function tablename($table) {
		global $wpdb;
    	// We use per-blog tables for network events
		return $wpdb->prefix.'eab_'.$table;
    }
	const TEXT_DOMAIN = 'eab';

	const BOOKING_TABLE = 'bookings';
	const BOOKING_META_TABLE = 'booking_meta';
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
				    $wpdb->prepare("INSERT INTO ".self::tablename(Eab_EventsHub::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'yes') ON DUPLICATE KEY UPDATE `status` = 'yes';", $event_id, $user_id)
				);
				// --todo: Add to BP activity stream
				//do_action( 'incsub_event_booking_yes', $event_id, $user_id );
				$this->recount_bookings($event_id);
				//wp_redirect('?eab_success_msg=' . Eab_Template::get_success_message_code(Eab_EventModel::BOOKING_YES));
				
		    }
		    if (isset($_POST['event_action'])  && $_POST['event_action'] == 'maybe') {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(Eab_EventsHub::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'maybe') ON DUPLICATE KEY UPDATE `status` = 'maybe';", $event_id, $user_id)
				);
				// --todo: Add to BP activity stream
				//do_action( 'incsub_event_booking_maybe', $event_id, $user_id );
				$this->recount_bookings($event_id);
				//wp_redirect('?eab_success_msg=' . Eab_Template::get_success_message_code(Eab_EventModel::BOOKING_MAYBE));
				
		    }
		    if (isset($_POST['event_action'])  && $_POST['event_action'] == 'no') {
				$wpdb->query(
				    $wpdb->prepare("INSERT INTO ".self::tablename(Eab_EventsHub::BOOKING_TABLE)." VALUES(null, %d, %d, NOW(), 'no') ON DUPLICATE KEY UPDATE `status` = 'no';", $event_id, $user_id)
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
  

  function get_organized_event()
  {
  	
  		error_reporting(E_ALL);
 	  	global $wpdb;$oReturn = new stdClass();
		if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
		//if(!$_REQUEST['status']){$oReturn->error = __('status is required.','aheadzen'); return $oReturn;}

		$user_id = $_REQUEST['user_id'];
		//$status = $_REQUEST['status'];
		
		$statuses = array(
			Eab_EventModel::BOOKING_YES => __('Attending', Eab_EventsHub::TEXT_DOMAIN),
			Eab_EventModel::BOOKING_MAYBE => __('Maybe', Eab_EventsHub::TEXT_DOMAIN),
			Eab_EventModel::BOOKING_NO => __('No', Eab_EventsHub::TEXT_DOMAIN)
		);
		
		$group_id = 0;
		if(isset($_REQUEST['group_id']) && $_REQUEST['group_id'] != '')
		{
			$group_id = $_REQUEST['group_id'];	
		}
		
		//if (!in_array($status, array_keys($statuses))) return false; // Unknown status
		//$status_name = $statuses[$status];
		
		
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
      	$month = 0;
		if(isset($_REQUEST['event_start']))
		{
			$month = date("m",strtotime($_REQUEST['event_start']));
            $year = date("Y",strtotime($_REQUEST['event_start']));
			if($month != 0)
			{
                $_REQUEST['event_start'] = $year.'-'.$month.'-01 00:00:00';
                $_REQUEST['event_end'] = $year.'-'.$month.'-31 24:00:00';
			}
		}

		
		//$ret = '<div class="wpmudevevents-user_bookings wpmudevevents-user_bookings-' . $status . '">';
		$response = array();
		$i=0;

			if(isset($_REQUEST['group_id']) && $_REQUEST['group_id'] != '') {
                //$event = new Eab_EventModel(get_post($event_id));
                $sql = "SELECT 
						  * 
						FROM
						  `wp_posts` 
						WHERE post_author = " . $user_id . " 
						  AND post_type = 'incsub_event' 
						UNION
						SELECT 
						  * 
						FROM
						  `wp_posts` 
						WHERE ID IN 
						  (SELECT 
							post_id 
						  FROM
							wp_postmeta me 
						  WHERE me.meta_key = 'eab_event-bp-group_event' 
							AND me.meta_value = ".$_REQUEST['group_id'].")";
            } else{
                $sql = "SELECT * FROM `wp_posts` WHERE post_author = " . $user_id . " AND post_type = 'incsub_event'";
			}

			$evn = $conn->query($sql);
			//print "<pre>";//print_r($evn);
			foreach ($evn as $event) {
			
				$response[$i]['id'] = $event['ID'];
				$response[$i]['event_title'] = $event['post_title'];
				$response[$i]['event_description'] = htmlspecialchars($event['post_content']);
				$sql = "SELECT * FROM `wp_users` WHERE ID = ".$event['post_author'];
				$res = $conn->query($sql);
				if ($res->num_rows > 0) {
					$userData = $res->fetch_assoc();
					$response[$i]['hostname'] =  $userData['display_name'];
				}
				
				//print_r($userData);
				/*if(isset($groupData['slug'])){
					$response[$i]['group_slug'] =  $groupData['slug'];
				} else { 
					$response[$i]['group_slug'] =  "";
				}*/
				$evt = (object)$event;
				$response[$i]['event_link'] = urlencode(trim(Eab_Template::get_event_link($evt)));
				$response[$i]['event_dates'] =  urlencode(Eab_Template::get_event_dates($evt)) ;
				$response[$i]['is_group_event'] = 0;
				$response[$i]['group_name'] =  "";
				$response[$i]['group_slug'] =  "";

				//$response[$i]['event_venue'] =  urlencode($evt->get_venue_location());
				//$response[$i]['event_type'] = 1;
			
				$arr_comments = array();
				$sql = "SELECT * FROM `wp_postmeta` WHERE post_id = ".$event['ID'];
				$res = $conn->query($sql);
				if ($res->num_rows > 0) {
					$event_start = 0;
					$event_end = 0;
					
				 while($row = $res->fetch_assoc()) {
				
				 	$bool = true;
				 	
					if($row['meta_key'] == 'incsub_event_start') 
					{
						$event_start =  date("Y-m-d",strtotime($row['meta_value']));
						$response[$i]['event_start'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_end') 
					{
						$event_end =  date("Y-m-d",strtotime($row['meta_value']));
						$response[$i]['event_end'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_status') 
					{
						$response[$i]['event_status'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_duration') 
					{
						$response[$i]['event_duration'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_notes') 
					{
						$response[$i]['event_notes'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_reoccurance') 
					{
						$response[$i]['event_reoccurance'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_opponent') 
					{
						$response[$i]['event_opponent'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'agm_map_created') 
					{
						$response[$i]['agm_map_created'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_venue') 
					{

                            $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
                                'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
                                'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
                                'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                                'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
                            $str = strtr($row['meta_value'], $unwanted_array);
                            //echo str_replace(array_keys($replace), $replace, $row['meta_value']);
                            $response[$i]['event_venue'] = htmlspecialchars($str);


					}
					if($row['meta_key'] == 'incsub_event_paid') 
					{
						$response[$i]['event_paid'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_fee') 
					{
						$response[$i]['event_fee'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'eab_event-bp-group_event') 
					{
						$response[$i]['is_group_event'] = 1;
						if($group_id == 0){
							if($row['meta_value'] != 0){
								$response[$i]['group_id'] =  $row['meta_value'];
							}
							else
							{
								$response[$i]['group_id'] = 0;
							}
						}else{
							$response[$i]['group_id'] =  $group_id;
						}
						if($response[$i]['group_id'] != 0)
						{
							if($row['meta_value'] != 0 && $row['meta_value'] != $response[$i]['group_id']){
								$response[$i]['group_id'] = $row['meta_value'];
							}	
							$groupData = array();
							$sql = "SELECT * FROM `wp_bp_groups` WHERE id = ".$response[$i]['group_id'];
							$res2 = $conn->query($sql);
							if ($res2->num_rows > 0) {
								$groupData = $res2->fetch_assoc();
								$response[$i]['group_name'] = htmlspecialchars($groupData['name']) ;
								$response[$i]['group_slug'] =  htmlspecialchars($groupData['slug']);
								//$response[$i]['group_id'] =  $response[$i]['group_id'];
							}
							else
							{
								//$response[$i]['group_id'] = 0;
								$response[$i]['group_name'] =  "";
								$response[$i]['group_slug'] =  "";
							}
						}
						else
						{
							$response[$i]['is_group_event'] = 0;
							$response[$i]['group_name'] =  "";
							$response[$i]['group_slug'] =  "";
						}
						
					
					}
					/*else if($group_id != 0 && $row['meta_key'] != 'eab_event-bp-group_event')
					{
						//unset($response[$i]);
						$bool = false;
						$response[$i]['group_id'] = 0;
						$response[$i]['group_name'] =  "";
						$response[$i]['group_slug'] =  "";
					}*/
					
					if($row['meta_key'] == 'incsub_event_yes_count') 
					{
						$response[$i]['event_yes_count'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_maybe_count') 
					{
						$response[$i]['event_maybe_count'] =  $row['meta_value'];
					}
					if($row['meta_key'] == 'incsub_event_attending_count') 
					{
						$response[$i]['event_attending_count'] =  $row['meta_value'];
					}

					if(isset($_REQUEST['event_start']) && isset($_REQUEST['event_end'])){
						
						if(strtotime($event_start) >= strtotime($_REQUEST['event_start']) && strtotime($event_start) <= strtotime($_REQUEST['event_end'])){
							
						}else {
							//unset($response[$i]);
							$bool = false;
						}
					}
					
					
				
			
			 	}  // while end
				/*if(isset($bool) && $bool == false)
					{
						$index[] = $i;
						//unset($response[$i]);
					}
					if($group_id != 0)
					{
						if(!isset($response[$i]['is_group_event']))
						{
							$index[] = $i;
							//unset($response[$i]);
						}
					}*/
					
				}
			$userData = array();
			
			
			$i++;

		}
		
		$arr = array();
		foreach($response as $row)
		{
			if(isset($_REQUEST['event_start']) && $_REQUEST['event_start'] != '' && isset($_REQUEST['event_end']) && $_REQUEST['event_end'] != '') {
				/*echo strtotime($row['event_start'])." - ";
				echo strtotime($_REQUEST['event_start']);
				echo "<br/>";
				echo strtotime($row['event_end'])." - ";
				echo strtotime($_REQUEST['event_end']);*/
				if(isset($row['event_start']) && strtotime($row['event_start']) >= strtotime($_REQUEST['event_start']) && strtotime($row['event_start']) <= strtotime($_REQUEST['event_end'])){
						$arr[] = $row;
				}
			}else {
				$arr[] = $row;
			}
		}
		$arr2 = array();
		if(isset($_REQUEST['group_id']))
		{
			foreach($arr as $row)
			{
				if($row['group_id'] == $_REQUEST['group_id'])
				{
					$arr2[] = $row;
				}
			}
		}
		else
		{
			$arr2 = $arr;
		}

		//print_r($arr);
		//echo "<br/>";
		//print_r($arr2);
		/*$arr = array();
		$j=0;
		foreach($response as $r)
		{
			if(!in_array($j,$index))
			{
				if(isset($_REQUEST['group_id']) && $_REQUEST['group_id'] > 0)
				{
					if(isset($r['group_id']) && $r['group_id'] == $_REQUEST['group_id'])
					{
						
						$arr[] = $r;
					}
				}
				else
				{
					
					$arr[] = $r;
				}
			}
			$j++;
		}*/
		//print_r($arr);die;
		//print_r($arr);
		//$ret .= '</div>';
      header('Content-type: application/json');
		echo json_encode(array("status"=>"ok", "success"=>"success", "data"=>$arr2));
		die;
  }
  
  function get_event_status()
  {
	
	  error_reporting(E_ALL);
	  global $wpdb;
	  if(!$_REQUEST['user_id']){$oReturn->error = __('user_id is required.','aheadzen'); return $oReturn;}
	  if(!$_REQUEST['event_id']){$oReturn->error = __('event_id is required.','aheadzen'); return $oReturn;}
	  $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	  // Check connection
	  if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	  } 
	  $sql = "SELECT * FROM `wp_eab_bookings` WHERE event_id = ".$_REQUEST['event_id']." AND user_id = ".$_REQUEST['user_id'];
	  $res = $conn->query($sql);
	  if ($res->num_rows > 0) {
	  	  $eventData = $res->fetch_assoc();
		  echo json_encode(array("status"=>"ok", "success"=>"success", "data"=>$eventData));
	  }else {
		  echo json_encode(array("status"=>"ok", "success"=>"no data found", "data"=>array()));
	  }
	  die;
  }

}

?>
