<?php
/*
Custom sidebars
http://marquex.es/698/custom-sidebars-1-0
1.1
*/

if(!class_exists('CustomSidebars')):

class CustomSidebars{
	
	var $message = '';
	var $message_class = '';
	
	//The name of the option that stores the info of the new bars.
	var $option_name = "cs_sidebars";
	//The name of the option that stores which bars are replaceable, and the default
	//replacements. The value is stored in $this->options
	var $option_modifiable = "cs_modifiable";
	
	
	var $sidebar_prefix = 'cs-';
	var $postmeta_key = '_cs_replacements';
	var $cap_required = 'switch_themes';
	var $ignore_post_types = array('attachment', 'revision', 'nav_menu_item', 'pt-widget');
	var $options = array();
	
	var $replaceable_sidebars = array();
	var $replacements = array();
	var $replacements_todo;
	
	function CustomSidebars(){
		$this->retrieveOptions();
		$this->replaceable_sidebars = $this->getModifiableSidebars();
		$this->replacements_todo = sizeof($this->replaceable_sidebars);
		foreach($this->replaceable_sidebars as $sb)
			$this->replacements[$sb] = FALSE;
	}
	
	function retrieveOptions(){
		$this->options = get_option($this->option_modifiable);
	}
	
	function getCustomSidebars(){
		$sidebars = get_option($this->option_name);
		if($sidebars)
			return $sidebars;
		return array();
	}
	
	function getThemeSidebars($include_custom_sidebars = FALSE){
		
		global $wp_registered_sidebars;		
		$allsidebars = $wp_registered_sidebars;
		ksort($allsidebars);
		if($include_custom_sidebars)
			return $allsidebars;
		
		$themesidebars = array();
		foreach($allsidebars as $key => $sb){
			if(substr($key, 0, 3) != $this->sidebar_prefix)
				$themesidebars[$key] = $sb;
		}
		
		return $themesidebars;
	}
	
	function registerCustomSidebars(){
		$sb = $this->getCustomSidebars();
		if(!empty($sb)){
			foreach($sb as $sidebar){
				register_sidebar($sidebar);
			}
		}
	}
	
	function checkAndFixSidebar($sidebar, $replacement, $method, $extra_index){
		global $wp_registered_sidebars;
		
		
		if(isset($wp_registered_sidebars[$replacement]))
			return true;
		
		if($method == 'particular'){
			global $post;
			$sidebars = get_post_meta($post->ID, $this->postmeta_key, TRUE);
			if($sidebars && isset($sidebars[$sidebar])){
				unset($sidebars[$sidebar]);
				update_post_meta($post->ID, $this->postmeta_key, $sidebars);	
			}
		}
		else{
			if(isset($this->options[$method])){
				if($extra_index != -1 && isset($this->options[$method][$extra_index]) && isset($this->options[$method][$extra_index][$sidebar])){
					unset($this->options[$method][$extra_index][$sidebar]);
					update_option($this->option_modifiable, $this->options);
				}
				if($extra_index == 1 && isset($this->options[$method]) && isset($this->options[$method][$sidebar])){
					unset($this->options[$method][$sidebar]);
					update_option($this->option_modifiable, $this->options);				
				}
			}
		}
		
		return false;
	}
	
	function deleteSidebar(){
		if(! current_user_can($this->cap_required) )
			return new WP_Error('cscantdelete', __('You do not have permission to delete sidebars','gp_lang'));
		
                if(! DOING_AJAX && ! wp_verify_nonce($_REQUEST['_n'], 'custom-sidebars-delete') ) 
                        die('Security check stop your request.'); 
		
		$newsidebars = array();
		$deleted = FALSE;
		
		$custom = $this->getCustomSidebars();
		
		if(!empty($custom)){
		
		foreach($custom as $sb){
			if($sb['id']!=$_REQUEST['delete'])
				$newsidebars[] = $sb;
			else
				$deleted = TRUE;
		}
		}//endif custom
		
		//update option
		update_option( $this->option_name, $newsidebars );

		$this->refreshSidebarsWidgets();
		
		if($deleted)
			$this->setMessage(sprintf(__('The sidebar "%s" has been deleted.','gp_lang'), $_REQUEST['delete']));
		else
			$this->setError(sprintf(__('There was not any sidebar called "%s" and it could not been deleted.','gp_lang'), $_GET['delete']));
	}
	
	function createPage(){
		
		//$this->refreshSidebarsWidgets();
		if(!empty($_POST)){
			if(isset($_POST['create-sidebars'])){
				check_admin_referer('custom-sidebars-new');
				$this->storeSidebar();
			}
			else if(isset($_POST['update-sidebar'])){
				check_admin_referer('custom-sidebars-update');
				$this->updateSidebar();
			}		
			else if(isset($_POST['update-modifiable'])){
				$this->updateModifiable();
                                $this->retrieveOptions();
                                $this->replaceable_sidebars =  $this->getModifiableSidebars();
                        }
			else if(isset($_POST['update-defaults-posts']) OR isset($_POST['update-defaults-pages'])){
				$this->storeDefaults();
			
			}
				
			else if(isset($_POST['reset-sidebars']))
				$this->resetSidebars();			
				
			$this->retrieveOptions();
		}
		else if(!empty($_GET['delete'])){
			$this->deleteSidebar();
			$this->retrieveOptions();			
		}
		else if(!empty($_GET['p'])){
			if($_GET['p']=='edit' && !empty($_GET['id'])){
				$customsidebars = $this->getCustomSidebars();
				if(! $sb = $this->getSidebar($_GET['id'], $customsidebars))
					return new WP_Error('cscantdelete', __('You do not have permission to delete sidebars','gp_lang'));
				include('views/edit.php');
				return;	
			}
		}
		
		$customsidebars = $this->getCustomSidebars();
		$themesidebars = $this->getThemeSidebars();
		$allsidebars = $this->getThemeSidebars(TRUE);
		$defaults = $this->getDefaultReplacements();
		$modifiable = $this->replaceable_sidebars;
		$post_types = $this->getPostTypes();
		
		$deletenonce = wp_create_nonce('custom-sidebars-delete');
		
		//var_dump($defaults);
		
		//Form
		if(!empty($_GET['p'])){
			if($_GET['p']=='defaults'){
				$categories = get_categories(array('hide_empty' => 0));
				if(sizeof($categories)==1 && $categories[0]->cat_ID == 1)
					unset($categories[0]);
					
				//include('views/defaults.php');
			}
			else if($_GET['p']=='edit')
				include('views/edit.php');
                        else if($_GET['p']=='removebanner')
                            return $this->removeBanner();
			else
				include('views/settings.php');	
				
		}
		else		
                    include('views/settings.php');		
	}
	
	function addSubMenus(){
		$page = add_submenu_page('themes.php', __('Sidebars', 'gp_lang'), __('Sidebars','gp_lang'), $this->cap_required, 'sidebars', array($this, 'createPage'));
		
                add_action('admin_print_scripts-' . $page, array($this, 'addScripts'));
                
                //global $workingcode;
               //$workingcode = $this->getWorkingCode();
	}
	
	function addScripts(){
		wp_enqueue_script('post');
	}

	function getReplacements($postid){
		$replacements = get_post_meta($postid, $this->postmeta_key, TRUE);
		if($replacements == '')
			$replacements = array();
		else
			$replacements = $replacements;
		return $replacements;
	}
	
	function getModifiableSidebars(){
		if( $modifiable = $this->options ) //get_option($this->option_modifiable) )
			return $modifiable['modifiable'];
		return array(); 
	}
	
	function getDefaultReplacements(){
		if( $defaults = $this->options ){//get_option($this->option_modifiable) )
			$defaults['post_type_posts'] = $defaults['defaults'];
			unset($defaults['modifiable']);
			unset($defaults['defaults']);
			return $defaults;
		}
		return array(); 
	}
	
	function updateModifiable(){
		check_admin_referer('custom-sidebars-options', 'options_wpnonce');
		$options = $this->options ? $this->options : array();
		
		//Modifiable bars
		if(isset($_POST['modifiable']) && is_array($_POST['modifiable']))
			$options['modifiable'] = $_POST['modifiable'];

		
		if($this->options !== FALSE)
			update_option($this->option_modifiable, $options);
		else
			add_option($this->option_modifiable, $options);
			
		$this->setMessage(__('The custom sidebars settings has been updated successfully.','gp_lang'));
	}
	
	function storeSidebar(){
		$name = trim($_POST['sidebar_name']);
		$description = trim($_POST['sidebar_description']);
		if(empty($name) OR empty($description))
			$this->setError(__('You have to fill all the fields to create a new sidebar.','gp_lang'));
		else{
			$id = $this->sidebar_prefix . sanitize_html_class(sanitize_title_with_dashes($name));
			$sidebars = get_option($this->option_name, FALSE);
			if($sidebars !== FALSE){
				$sidebars = $sidebars;
				if(! $this->getSidebar($id,$sidebars) ){
					//Create a new sidebar
					$sidebars[] = array(
						'name' => $name,
						'id' => $id,
						'description' => $description,
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h3 class="widgettitle">',
						'after_title'   => '</h3>',
						) ;
						
					
					//update option
					update_option( $this->option_name, $sidebars );
						
					$this->refreshSidebarsWidgets();
					
					$this->setMessage( __('The sidebar has been created successfully.','gp_lang'));
					
					
				}
				else
					$this->setError(__('There is already a sidebar registered with that name, please choose a different one.','gp_lang'));
			}
			else{
				$id = $this->sidebar_prefix . sanitize_html_class(sanitize_title_with_dashes($name));
				$sidebars= array(array(
						'name' => $name,
						'id' => $id,
						'description' => $description,
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h3 class="widgettitle">',
						'after_title'   => '</h3>',	
						) );
				add_option($this->option_name, $sidebars);
				
				
				$this->refreshSidebarsWidgets();
				
				$this->setMessage( __('The sidebar has been created successfully.','gp_lang'));					
			}
		}
	}
	
	function updateSidebar(){
		$id = trim($_POST['cs_id']);
		$name = trim($_POST['sidebar_name']);
		$description = trim($_POST['sidebar_description']);

		
		$sidebars = $this->getCustomSidebars();
		
		//Check the id		
		$url = parse_url($_POST['_wp_http_referer']);
		if(! DOING_AJAX){
                    if(isset($url['query'])){
                            parse_str($url['query'], $args);
                            if($args['id'] != $id)
                                    return new WP_Error(__('The operation is not secure and it cannot be completed.','gp_lang'));
                    }
                    else
                            return new WP_Error(__('The operation is not secure and it cannot be completed.','gp_lang'));
                }
		
		$newsidebars = array();
		foreach($sidebars as $sb){
			if($sb['id'] != $id)
				$newsidebars[] = $sb;
			else
				$newsidebars[] = array(
						'name' => $name,
						'id' => $id,
						'description' => $description,
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h3 class="widgettitle">',
						'after_title'   => '</h3>',	
						) ;
		}
		
		//update option
		update_option( $this->option_name, $newsidebars );
		$this->refreshSidebarsWidgets();
		
		$this->setMessage( sprintf(__('The sidebar "%s" has been updated successfully.','gp_lang'), $id ));
	}
	
	function getSidebar($id, $sidebars){
		$sidebar = false;
		$nsidebars = sizeof($sidebars);
		$i = 0;
		while(! $sidebar && $i<$nsidebars){
			if($sidebars[$i]['id'] == $id)
				$sidebar = $sidebars[$i];
			$i++;
		}
		return $sidebar;
	}
	
	function message($echo = TRUE){
		$message = '';
		if(!empty($this->message))
			$message = '<div id="message" class="' . $this->message_class . '"><p><strong>' . $this->message . '</strong></p></div>';
		
		if($echo)
			echo $message;
		else
			return $message;		
	}
	
	function setMessage($text){
		$this->message = $text;
		$this->message_class = 'updated';
	}
	
	function setError($text){
		$this->message = $text;
		$this->message_class = 'error';
	}
	
	function getPostTypes(){
		$pt = get_post_types();
		$ptok = array();
		
		foreach($pt as $t){
			if(array_search($t, $this->ignore_post_types) === FALSE)
				$ptok[] = $t;
		}
		
		return $ptok; 
	}
	
	function getEmptyWidget(){
		return array(
			'name' => 'CS Empty Widget',
			'id' => 'csemptywidget',
			'callback' => array(new CustomSidebarsEmptyPlugin(), 'display_callback'),
			'params' => array(array('number' => 2)),
			'classname' => 'CustomSidebarsEmptyPlugin',
			'description' => 'CS dummy widget'
		);
	}
	
	function refreshSidebarsWidgets(){
		$widgetized_sidebars = get_option('sidebars_widgets');
		$delete_widgetized_sidebars = array();
		$cs_sidebars = get_option($this->option_name);
		
		foreach($widgetized_sidebars as $id => $bar){
			if(substr($id,0,3)=='cs-'){
				$found = FALSE;
				foreach($cs_sidebars as $csbar){
					if($csbar['id'] == $id)
						$found = TRUE;
				}
				if(! $found)
					$delete_widgetized_sidebars[] = $id;
			}
		}
		
		
		foreach($cs_sidebars as $cs){
			if(array_search($cs['id'], array_keys($widgetized_sidebars))===FALSE){
				$widgetized_sidebars[$cs['id']] = array(); 
			}
		}
		
		foreach($delete_widgetized_sidebars as $id){
			unset($widgetized_sidebars[$id]);
		}
		
		update_option('sidebars_widgets', $widgetized_sidebars);
		
	}
	
	function resetSidebars(){
		if(! current_user_can($this->cap_required) )
			return new WP_Error('cscantdelete', __('You do not have permission to delete sidebars','gp_lang'));
			
		if (! wp_verify_nonce($_REQUEST['reset-n'], 'custom-sidebars-delete') ) die('Security check stopped your request.'); 
		
		delete_option($this->option_modifiable);
		delete_option($this->option_name);
		
		$widgetized_sidebars = get_option('sidebars_widgets');	
		$delete_widgetized_sidebars = array();	
		foreach($widgetized_sidebars as $id => $bar){
			if(substr($id,0,3)=='cs-'){
				$found = FALSE;
				if(empty($cs_sidebars))
					$found = TRUE;
				else{
					foreach($cs_sidebars as $csbar){
						if($csbar['id'] == $id)
							$found = TRUE;
					}
				}
				if(! $found)
					$delete_widgetized_sidebars[] = $id;
			}
		}
		
		foreach($delete_widgetized_sidebars as $id){
			unset($widgetized_sidebars[$id]);
		}
		
		update_option('sidebars_widgets', $widgetized_sidebars);
		
		$this->setMessage( __('The custom sidebars data has been removed successfully,','gp_lang'));	
	}

}
endif; //exists class


if(!isset($plugin_sidebars)){
	$plugin_sidebars = new CustomSidebars();	
	add_action( 'widgets_init', array($plugin_sidebars,'registerCustomSidebars') );
	add_action( 'admin_menu', array($plugin_sidebars,'addSubMenus'));
}

if(! class_exists('CustomSidebarsEmptyPlugin')){
class CustomSidebarsEmptyPlugin extends WP_Widget {
	function CustomSidebarsEmptyPlugin() {
		parent::WP_Widget(false, $name = 'CustomSidebarsEmptyPlugin');
	}
	function form($instance) {
		//Nothing, just a dummy plugin to display nothing
	}
	function update($new_instance, $old_instance) {
		//Nothing, just a dummy plugin to display nothing
	}
	function widget($args, $instance) {		
		echo '';
	}
} //end class
} //end if class exists