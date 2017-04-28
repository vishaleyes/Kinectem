<?php if(!defined('ABSPATH')) exit;
/* TODO:
	- Threads
		Support more than 100 people in a single thread
			
	- Emailing
		Send emails if messaging not enabled
		
	- Groups
		Show mass messaging to group members on group (complements above)
		
	- Access
		Custom Role(s)
		User Meta
		Member Time
		s2member integration?
	
	- Searching
		Searching filters on frontend
		
	- Filters
		Filter which members / groups / blogs are displayed
		
	- Custom sections
		Setup custom message lists (dynamic)
		
	- History
		Mass reply and such like
		
	- Events
		Support for eventspress / events manager etc...
*/
class Mass_Messaging_in_BuddyPress_Settings {
	private static $_instance = null;
	public $parent = null;
	public $base = '';
	public $settings = array();
	
	public $site = array();

	public function __construct($parent){
		$this->parent = $parent;

		$this->base = $this->parent->_token.'_';

		$this->site['members'] = true;
		$this->site['groups'] = bp_is_active('groups');
		$this->site['blogs'] = is_multisite();
		
		// Initialise settings
		add_action('init', array($this, 'init_settings'));

		// Initialise menus
		if($this->can_access()){
			add_action('admin_bar_menu', array($this, 'setup_wordpress_navigation'), 100);
			add_action('bp_setup_nav', array($this, 'setup_buddypress_navigation'));
		}
				
		// Register plugin settings
		add_action('admin_init' , array($this, 'register_settings'));

		// Add settings page to menu
		add_action('admin_menu' , array($this, 'add_menu_item'));

		// Add settings link to plugins page
		add_filter('plugin_action_links_'.plugin_basename($this->parent->file), array($this, 'add_settings_link'));
				
		$this->content();
				
		do_action($this->base.'init');
	}

	public function init_settings(){
		$features = array();
		$features[] = $this->get_option('ordering_first', 'text');
		$features[] = $this->get_option('ordering_second', 'text');
		$features[] = $this->get_option('ordering_third', 'text');
		$features = array_unique($features);
				
		$position = (array_search('members', $features) + 1) * 10;
		add_filter($this->parent->_token.'_settings_fields', array($this, 'members_settings_fields'), $position);
		add_action($this->parent->_token.'_members_action', array($this, 'members_action'), $position, 3);

		if($this->site['groups']){
			$position = (array_search('groups', $features) + 1) * 10;
			add_filter($this->parent->_token.'_settings_fields', array($this, 'groups_settings_fields'), $position);
			add_action($this->parent->_token.'_groups_action', array($this, 'groups_action'), $position, 3);
		}
		
		if($this->site['blogs']){
			$position = (array_search('blogs', $features) + 1) * 10;
			add_filter($this->parent->_token.'_settings_fields', array($this, 'multisite_settings_fields'), $position);
			add_action($this->parent->_token.'_blogs_action', array($this, 'blogs_action'), $position, 3);
		}
						
		add_filter($this->parent->_token.'_settings_fields', array($this, 'ordering_settings_fields'), 50);
		add_filter($this->parent->_token.'_settings_fields', array($this, 'user_access_settings_fields'), 40);
		add_filter($this->parent->_token.'_settings_fields', array($this, 'reading_settings_fields'), 40);
				
		$this->settings = $this->settings_fields();
	}

	public function add_menu_item(){
		$page = add_options_page(__('Mass Messaging', 'mass-messaging-in-buddypress'), __('Mass Messaging', 'mass-messaging-in-buddypress'), 'manage_options', $this->parent->_token.'_settings',  array($this, 'settings_page'));
		add_action('admin_print_styles-'.$page, array($this, 'settings_assets'));
	}

	public function settings_assets($hook){		
    	wp_register_script($this->parent->_token.'-settings-js', $this->parent->assets_url.'js/settings'.$this->parent->script_suffix.'.js', array('jquery'), $this->parent->_version);
    	
    	wp_register_style($this->parent->_token.'-settings-css', $this->parent->assets_url.'css/admin'.$this->parent->style_suffix.'.css', array(), $this->parent->_version);
    	
    	wp_enqueue_script($this->parent->_token.'-settings-js');
    	wp_enqueue_style($this->parent->_token.'-settings-css');
    	
		wp_enqueue_script('jquery-ui-sortable');
	}

	public function add_settings_link($links){
		$settings_link = '<a href="options-general.php?page='.$this->parent->_token.'_settings">'.__('Settings', 'mass-messaging-in-buddypress').'</a>';
  		array_push($links, $settings_link);
  		return $links;
	}

	private function settings_fields(){
		$settings['features'] = array(
			'title'				=> __('Features', 'mass-messaging-in-buddypress'),
			'description'		=> __('Configure which features are enabled or disabled. (Drag to reorder)', 'mass-messaging-in-buddypress'),
			'fields'			=> array()
		);
		
		$settings['access'] = array(
			'title'				=> __('Access', 'mass-messaging-in-buddypress'),
			'description'		=> __('Configure who can access the mass messaging.', 'mass-messaging-in-buddypress'),
			'fields'			=> array()
		);
		
		$settings['reading'] = array(
			'title'				=> __('Reading', 'mass-messaging-in-buddypress'),
			'description'		=> __('Manage mass messaging reading and replies.', 'mass-messaging-in-buddypress'),
			'fields'			=> array()
		);
		
		$settings['help'] = array(
			'title'				=> __('Help', 'mass-messaging-in-buddypress'),
			'description'		=> __('For help and support please visit the WordPress plugin support forums<br /><br /><a href="https://wordpress.org/support/plugin/mass-messaging-in-buddypress">https://wordpress.org/support/plugin/mass-messaging-in-buddypress</a>', 'mass-messaging-in-buddypress'),
			'fields'			=> array()
		);
		
		$settings['donate'] = array(
			'title'				=> __('Donate', 'mass-messaging-in-buddypress'),
			'description'		=> __('Mass Messaging will be free, always. Please consider donating :)<br /><br /><a href="https://www.paypal.me/eliottrobson/10">Donate via PayPal</a>', 'mass-messaging-in-buddypress'),
			'fields'			=> array()
		);
					
		$settings = apply_filters($this->parent->_token.'_settings_fields', $settings);

		return $settings;
	}
	
	public function members_settings_fields($settings){
		$members = array(
			/* Members */
			array(
				'id' 			=> 'enable_members',
				'label'			=> __('Members', 'mass-messaging-in-buddypress'),
				'description'	=> __('Allow mass messaging to members.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
			array(
				'id' 			=> 'enable_all_members',
				'description'	=> __('Allow the ability to select all members.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
			array(
				'id' 			=> 'enable_show_all_members',
				'description'	=> __('Show all members, not just friends.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),	
		);
		
		$settings['features']['fields'] = array_merge($settings['features']['fields'], $members);
		
		return $settings;
	}
	
	public function groups_settings_fields($settings){
		$groups = array(
			/* Groups */
			array(
				'id' 			=> 'enable_groups',
				'label'			=> __('Groups', 'mass-messaging-in-buddypress'),
				'description'	=> __('Allow mass messaging to groups.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
			array(
				'id' 			=> 'enable_all_groups',
				'description'	=> __('Allow the ability to select all groups.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
			array(
				'id' 			=> 'enable_show_all_groups',
				'description'	=> __('Show all groups, not just those with membership.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
		);
		
		$settings['features']['fields'] = array_merge($settings['features']['fields'], $groups);
		
		return $settings;
	}

	public function multisite_settings_fields($settings){
		$blogs = array(
			/* Blogs */
			array(
				'id' 			=> 'enable_blogs',
				'label'			=> __('Blogs', 'mass-messaging-in-buddypress'),
				'description'	=> __('Allow mass messaging to blogs.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
			array(
				'id' 			=> 'enable_all_blogs',
				'description'	=> __('Allow mass messaging to select all blogs.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
			array(
				'id' 			=> 'enable_show_all_blogs',
				'description'	=> __('Show all blogs, not just those with membership.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox',
				'default'		=> ''
			),
		);
		
		$settings['features']['fields'] = array_merge($settings['features']['fields'], $blogs);
		
		return $settings;
	}
	
	public function user_access_settings_fields($settings){
		$capabilities = array(
			'activate_plugins'	=> 'Administrator (activate_plugins)',
			'manage_categories' => 'Editor (manage_categories)',
			'publish_posts'		=> 'Author (publish_posts)',
			'edit_posts'		=> 'Contributor (edit_posts)',
			'read'				=> 'Subscriber (read)',
		);
				
		if($this->site['blogs']){
			$capabilities = array('manage_network' => 'Super Admin (manage_network)') + $capabilities;
		}
							
		$access = array(
			/* User Access */
			array(
				'id' 			=> 'minimum_access',
				'label'			=> __('Minimum Access', 'mass-messaging-in-buddypress'),
				'description'	=> __('Who can use mass messaging.', 'mass-messaging-in-buddypress'),
				'type'			=> 'select',
				'options'		=> $capabilities
			),
			
		);
		
		if($this->site['groups']){
			$groups = array(
				'creator'			=> 'Group Creator',
				'admins'			=> 'Group Admins',
				'mods'				=> 'Group Mods',
				'members'			=> 'Group Members',
				'any'				=> 'Anyone',
			);
			
			$access[] = array(
				'id' 			=> 'groups_access',
				'label'			=> __('Minimum Groups Access', 'mass-messaging-in-buddypress'),
				'description'	=> __('Who can use the groups messaging.', 'mass-messaging-in-buddypress'),
				'type'			=> 'select',
				'options'		=> $groups
			);
		}
				
		$settings['access']['fields'] = array_merge($settings['access']['fields'], $access);
		
		return $settings;
	}
	
	public function ordering_settings_fields($settings){							
		$access = array(
			/* Ordering */
			array(
				'id' 			=> 'ordering_first',
				'label'			=> __('Ordering', 'mass-messaging-in-buddypress'),
				'description'	=> '',
				'type'			=> 'hidden'
			)
		);
		
		if($this->site['groups'])
			$access[] = array(
				'id' 			=> 'ordering_second',
				'label'			=> '',
				'description'	=> '',
				'type'			=> 'hidden'
			);
			
		if($this->site['blogs'])
			$access[] = array(
				'id' 			=> 'ordering_third',
				'label'			=> '',
				'description'	=> '',
				'type'			=> 'hidden'
			);
				
		$settings['features']['fields'] = array_merge($settings['features']['fields'], $access);
		
		return $settings;
	}
	
	public function reading_settings_fields($settings){							
		$reading = array(
			/* Reading */
			array(
				'id' 			=> 'read_count',
				'label'			=> __('Read Count', 'mass-messaging-in-buddypress'),
				'description'	=> __('Show how many recipients have read the messages.', 'mass-messaging-in-buddypress'),
				'type'			=> 'checkbox'
			)
		);
						
		$settings['reading']['fields'] = array_merge($settings['reading']['fields'], $reading);
		
		return $settings;
	}
	
	public function setup_wordpress_navigation($wp_admin_nav){
		global $bp;
		
		$parent = 'messages';
		$user_domain = $bp->loggedin_user->domain;
		$parent_slug = $bp->{$parent}->slug;
		$link = trailingslashit($user_domain.$parent_slug);
				
		$menu = array(
			'name'		  => 'Mass Messaging',
			'slug' 		  => 'mass-messaging',
			'parent_id'   => $parent,
			'link' 		  => $link,
		);
		$this->parent->wordpress->add_subnav_item($wp_admin_nav, $menu);
	}
	
	public function setup_buddypress_navigation(){
		global $bp;
				
		$parent = 'messages';
		$user_domain = $bp->loggedin_user->domain;
		$parent_slug = $bp->{$parent}->slug;
		$link = trailingslashit($user_domain.$parent_slug);				
				
		$menu = array(
			'name'	   	  => 'Mass Messaging',
			'slug' 	   	  => 'mass-messaging',
			'link'     	  => $link,
			'parent_slug' => $parent,
			'screen'   	  => array($this, 'mass_messaging_screen'),
			'position' 	  => 90
		);
								
		$this->parent->buddypress->add_subnav_item($menu);
	}
	
	public function mass_messaging_screen(){
		add_action('bp_template_title', array($this, 'mass_messaging_page_screen_title'));
		add_action('bp_template_content', array($this, 'mass_messaging_page_screen_content'));
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}
	
	public function mass_messaging_page_screen_title(){
		return 'Mass Messaging';
	}
	
	public function mass_messaging_page_screen_content(){
		global $bp;
				
		$user = $bp->loggedin_user->id;
		$allClass = $this->base.'select_all';
		$divClass = $this->base.'list_';
		
		$features = array();
		$features[] = $this->get_option('ordering_first', 'text');
		$features[] = $this->get_option('ordering_second', 'text');
		$features[] = $this->get_option('ordering_third', 'text');
		$features = array_unique($features);
		?>
		<div id="send_message_notice"></div>
		<div id="send_message_form" class="standard-form <?php echo $this->parent->_token; ?>">
			<label for="subject" class="subject">Subject</label>
			<input type="text" name="subject" id="subject" value="" />

			<label for="content" class="content">Message</label>
			<textarea name="content" id="message_content" rows="15" cols="50"></textarea>
			<div id="<?php echo $this->base; ?>checkboxes">
			<?php
			foreach($features as $feature){
				do_action($this->parent->_token.'_'.$feature.'_action', $user, $allClass, $divClass);
			}
			?>
			<br />
			<label><input type="checkbox" id="thread" name="thread" class="thread" value="1"> Send as single thread?<br />
			</div>
			<input type="button" value="Send Message &rarr;" name="<?php echo $this->base.'submit'; ?>" id="send" />
		</div>
		<div id="send_message_progress"></div>
		<?php
	}
	
	public function members_action($user, $allClass, $divClass){
		$enableMembers = $this->get_option('enable_members', 'checkbox');
		if($enableMembers){
			$selectAllMembers = $this->get_option('enable_all_members', 'checkbox');
			$showAllMembers = $this->get_option('enable_show_all_members', 'checkbox');
			
			echo '<h3>Users</h3>';

			$membersFilter = array('per_page' => 99999, 'type' => 'alphabetical', 'exclude' => $user);
			if(!$showAllMembers){
				$membersFilter['user_id'] = $user;
			}
			
			echo '<div id="'.$divClass.'members" class="'.$divClass.'scroll">';
			if($selectAllMembers){
				echo '<label class="'.$allClass.'"><input type="checkbox" name="all_members" value="ignore"> Select All Users</label>';
			}
				
			if(bp_has_members($membersFilter)){
				while(bp_members()){
					bp_the_member();
					echo '<label><input type="checkbox" name="members[]" value="'.bp_get_member_user_id().'"> '.bp_get_member_name().'</label>';
				}
			}
			echo '</div>';
		}
	}
	
	public function groups_action($user, $allClass, $divClass){
		$enableGroups = $this->site['groups'] && $this->get_option('enable_groups', 'checkbox');
		if($enableGroups){
			$selectAllGroups = $this->get_option('enable_all_groups', 'checkbox');
			$showAllGroups = $this->get_option('enable_show_all_groups', 'checkbox');
			$filterGroups = $this->get_option('groups_access', 'select');
			
			echo '<h3>Groups</h3>';
			
			$groupsFilter = array('per_page' => 99999, 'type' => 'alphabetical', 'show_hidden' => true);
			if($showAllGroups && $filterGroups === 'any'){
				$groupsFilter['user_id'] = NULL;
			}else{
				$groupsFilter['user_id'] = $user;
			}
								
			echo '<div id="'.$divClass.'groups" class="'.$divClass.'scroll">';
			if($selectAllGroups){
				echo '<label class="'.$allClass.'"><input type="checkbox" name="all_groups" value="ignore"> Select All Groups</label>';
			}
								
			if(bp_has_groups($groupsFilter)){
				while(bp_groups()){
					bp_the_group();
					
					switch($filterGroups){
						case 'mods':
							if(!bp_group_is_mod()) continue;
						case 'admins':
							if(!bp_group_is_admin()) continue;
						case 'creator':
							if(!bp_is_group_creator()) continue;
					}
										
					echo '<label><input type="checkbox" name="groups[]" value="'.bp_get_group_id().'"> '.bp_get_group_name().'</label>';
				}
			}
			echo '</div>';
		}
	}
	
	public function blogs_action($user, $allClass, $divClass){
		$enableBlogs = $this->site['blogs'] && $this->get_option('enable_blogs', 'checkbox');
		if($enableBlogs){
			$selectAllBlogs = $this->get_option('enable_all_blogs', 'checkbox');
			$showAllBlogs = $this->get_option('enable_show_all_blogs', 'checkbox');
			
			echo '<h3>Blogs</h3>';
			
			$blogsFilter = array('per_page' => 99999, 'type' => 'alphabetical');
				
			if($showAllBlogs){
				$blogsFilter['user_id'] = false;
			}else{
				$blogsFilter['user_id'] = $user;
			}					
												
			echo '<div id="'.$divClass.'blogs" class="'.$divClass.'scroll">';
			if($selectAllBlogs){
				echo '<label class="'.$allClass.'"><input type="checkbox" name="all_blogs" value="ignore"> Select All Blogs</label>';
			}
				
			if(bp_has_blogs($blogsFilter)){
				while(bp_blogs()){
					bp_the_blog();
					echo '<label><input type="checkbox" name="blogs[]" value="'.bp_get_blog_id().'"> '.bp_get_blog_name().'</label>';
				}
			}
			echo '</div>';
		}
	}
	
	public function content(){
		add_action('wp_ajax_get_message_recipients', array($this, 'get_message_recipients'));
		add_action('wp_ajax_chunk_send_messages', array($this, 'chunk_send_messages'));
		
		$showRead = get_option('read_count', 'checkbox');
		if($showRead){
			add_action('bp_before_message_thread_list', array($this, 'read_count'));
		}
	}
	
	public function read_count(){
		global $thread_template;
		
		$thread = $thread_template->thread;
		
		$userId = bp_loggedin_user_id();
		
		// Show to sender only
		if((int) $thread->messages[0]->sender_id == $userId){
					
			$total = 0;
			$read = 0;
			foreach($thread->recipients as $recipient){
				$id = (int) $recipient->user_id;
				$unread = (int) $recipient->unread_count;
				
				if($id != $userId){
					if($unread == 0){
						$read++;
					}
					$total ++;
				}
			}
			
			echo '<p id="'.$this->parent->_token.'_read_count'.'"><span class="highlight">Read By: '.$read.'/'.$total.'</span></p>';
		}
	}
	
	public function get_message_recipients(){
		global $bp, $wpdb;
		
		$result = array(
			'success' => true
		);		
		
		$members = array();
		
		if(isset($_POST['groups'])){
			$groups = array();
			foreach($_POST['groups'] as $group){
				$groups[] = (int) $group;
			}
			$query = $wpdb->get_col("SELECT `user_id` FROM `{$bp->groups->table_name_members}` WHERE `group_id` IN (".implode(',', $groups).") AND is_confirmed = 1 AND is_banned = 0");
			$members = array_merge($members, $query);
		}
		
		if(isset($_POST['blogs'])){
			$blogs = array();
			foreach($_POST['blogs'] as $blog){
				$blogs[] = (int) $blog;
			}
			$query = $wpdb->get_col("SELECT `user_id` FROM `{$bp->blogs->table_name}` WHERE `blog_id` IN (".implode(',', $blogs).")");
			$members = array_merge($members, $query);
		}
		
		$members = array_map('intval', $members);
		$members = array_values($members);
										
		$total = count($members);
		
		$result['members'] = $members;
				
		echo json_encode($result);		
		wp_die();
	}
	
	public function chunk_send_messages(){
		global $bp, $wpdb;
				
		$result = array();
		
		$subject = $_POST['subject'];
		$content = $_POST['content'];
		$thread = $_POST['thread'] === "true";
		$members = $_POST['members'];
		
		$result['success'] = true;
						
		$sender = $bp->loggedin_user->id;
		if($members != null){
			if($thread){
				messages_new_message(array('sender_id' => $sender, 'subject' => $subject, 'content' => $content, 'recipients' => $members));
				$members = array();
			}else{
				foreach($members as $member){
					messages_new_message(array('sender_id' => $sender, 'subject' => $subject, 'content' => $content, 'recipients' => $member));
				}
			}
		}else{
			$result['success'] = false;
		}
				
		echo json_encode($result);
		wp_die();
	}
	
	public function users_to_id($user){
		return $user->data->ID;
	}
	
	public function can_access(){
		$minimum = $this->get_option('minimum_access', 'select');
		$access = !empty($minimum) && current_user_can($minimum);
		return apply_filters($this->parent->_token.'_can_access', $access);
	}
	
	public function register_settings(){
		if(is_array($this->settings)){

			// Check posted/selected tab
			$current_section = '';
			if(isset($_POST['tab']) && $_POST['tab']){
				$current_section = $_POST['tab'];
			}else{
				if(isset($_GET['tab']) && $_GET['tab']){
					$current_section = $_GET['tab'];
				}
			}

			foreach($this->settings as $section => $data){
				if($current_section && $current_section != $section) continue;

				// Add section to page
				add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->parent->_token.'_settings');

				foreach($data['fields'] as $field){

					// Validation callback for field
					$validation = '';
					if(isset($field['callback'])){
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base.$field['id'];
					register_setting($this->parent->_token.'_settings', $option_name, $validation);

					// Add field to page
					add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token.'_settings', $section, array('field' => $field, 'prefix' => $this->base));
				}

				if(!$current_section) break;
			}
		}
	}

	public function settings_section($section){
		$html = '<p> '.$this->settings[$section['id']]['description'].'</p>'."\n";
		echo $html;
	}

	public function settings_page(){
		// Build page HTML
		$html = '<div class="wrap" id="'.$this->parent->_token.'_settings">'."\n";
			$html .= '<h2>'.__('Mass Messaging in BuddyPress Options', 'mass-messaging-in-buddypress').'</h2>'."\n";

			$tab = '';
			if(isset($_GET['tab']) && $_GET['tab']){
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if(is_array($this->settings) && 1 < count($this->settings)){

				$html .= '<h2 class="nav-tab-wrapper">'."\n";

				$c = 0;
				foreach($this->settings as $section => $data){

					// Set tab class
					$class = 'nav-tab';
					if(!isset($_GET['tab'])){
						if(0 == $c){
							$class .= ' nav-tab-active';
						}
					}else{
						if(isset($_GET['tab']) && $section == $_GET['tab']){
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg(array('tab' => $section));
					if(isset($_GET['settings-updated'])){
						$tab_link = remove_query_arg('settings-updated', $tab_link);
					}

					// Output tab
					$html .= '<a href="'.$tab_link.'" class="'.esc_attr($class).'">'.esc_html($data['title']).'</a>'."\n";
					++$c;
				}
				$html .= '</h2>'."\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">'."\n";

				// Get settings fields
				ob_start();
				settings_fields($this->parent->_token.'_settings');
				do_settings_sections($this->parent->_token.'_settings');
				$html .= ob_get_clean();

				if(count($this->settings[$tab]['fields']) > 0){

					$html .= '<p class="submit">'."\n";
						$html .= '<input type="hidden" name="tab" value="'.esc_attr($tab).'" />'."\n";
						$html .= '<input name="Submit" type="submit" class="button-primary" value="'.esc_attr(__('Save Settings' , 'mass-messaging-in-buddypress')).'" />'."\n";
					$html .= '</p>'."\n";
				
				}
				
			$html .= '</form>'."\n";
		$html .= '</div>'."\n";

		echo $html;
	}
	
	public function get_option($id, $type, $default = false){
		$data = get_option($this->base.$id, $default);
		switch ($type) {
			case 'checkbox':
				return $data == 'on';
			default:
				return $data;
		}
	}

	public static function instance($parent){
		if(is_null(self::$_instance)){
			self::$_instance = new self($parent);
		}
		return self::$_instance;
	}

	public function __clone () {
		_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
	}

	public function __wakeup () {
		_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
	}
}