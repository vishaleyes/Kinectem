<?php
/*
Plugin Name: Visual Composer Widgets
Plugin URI: http://www.wishweb.co.uk
Description: Plugin for displaying Visual Composer Elements in a widget area.
Author: WP WishWeb
Version: 1.0.7
Author URI: http://www.wishweb.co.uk
*/

//  Constants
define('PLUGIN_VCA_DIR', plugin_dir_path(__FILE__));
define('PLUGIN_VCA_URL', plugin_dir_url(__FILE__));
define('PLUGIN_VCA_WIDGETS_DIR', PLUGIN_VCA_DIR . 'widgets/');
define('PLUGIN_VCA_ELEMENTS_DIR', PLUGIN_VCA_DIR . 'elements/');
define('PLUGIN_VCA_TEXT_DOMAIN', 'vc_addon');

//  Post Types
define('POST_TYPE_VC_ELEMENT', 'vc-element');


//  Admin Notice Action Callback
function vca_showVcVersionNotice() {
    $plugin_data = get_plugin_data(__FILE__);
    echo '
    <div class="error">
      <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=wpwishweb" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
    </div>';
}

// Check if Visual Composer is installed
if ( ! defined( 'WPB_VC_VERSION' ) ) {

    // Display notice that Visual Compser is required
    add_action('admin_notices', 'vca_showVcVersionNotice');

    //  Return
    return false;
}


//  Loading the Packed Elements
require_once PLUGIN_VCA_ELEMENTS_DIR . 'ExtensionLoginForm.php';

//  Initiate the Elements
new ExtensionLoginForm();


//  Add Action to Register Widgets
add_action('widgets_init', 'register_vca_widgets');

//  Callback to Action
function register_vca_widgets() {

    //  Check Exists
    if (!function_exists('vc_backend_editor'))
        return false;

    //  Load Widget Files
    require_once PLUGIN_VCA_WIDGETS_DIR . 'widget-vc-element.php';

    //  Register Widgets
    register_widget('Widget_VC_Element');
}

//  Listen Init
add_action('init', 'vca_init');

//  Callback to Action
function vca_init() {

    //  Labels
    $labels = array(
        'name' => _x('VC Elements', 'post type general name', PLUGIN_VCA_TEXT_DOMAIN),
        'singular_name' => _x('VC Element', 'post type singular name', PLUGIN_VCA_TEXT_DOMAIN),
        'menu_name' => _x('VC Elements', 'admin menu', PLUGIN_VCA_TEXT_DOMAIN),
        'name_admin_bar' => _x('VC Element', 'add new on admin bar', PLUGIN_VCA_TEXT_DOMAIN),
        'add_new' => _x('Add New', 'vc element', PLUGIN_VCA_TEXT_DOMAIN),
        'add_new_item' => __('Add New VC Element', PLUGIN_VCA_TEXT_DOMAIN),
        'new_item' => __('New VC Element', PLUGIN_VCA_TEXT_DOMAIN),
        'edit_item' => __('Edit VC Element', PLUGIN_VCA_TEXT_DOMAIN),
        'view_item' => __('View VC Element', PLUGIN_VCA_TEXT_DOMAIN),
        'all_items' => __('All VC Elements', PLUGIN_VCA_TEXT_DOMAIN),
        'search_items' => __('Search VC Elements', PLUGIN_VCA_TEXT_DOMAIN),
        'parent_item_colon' => __('Parent VC Elements:', PLUGIN_VCA_TEXT_DOMAIN),
        'not_found' => __('No vc elements found.', PLUGIN_VCA_TEXT_DOMAIN),
        'not_found_in_trash' => __('No vc elements found in Trash.', PLUGIN_VCA_TEXT_DOMAIN)
    );

    //  Arguments
    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => false,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor'),
        'register_meta_box_cb' => 'vca_register_meta_box_cb'
    );

    //  Register the Post Type
    register_post_type(POST_TYPE_VC_ELEMENT, $args);

    //  Get the Post Type Options
    $ptOptions = get_option('wpb_js_content_types');

    //  Check
    if(!$ptOptions) $ptOptions = array('page');

    //  Assign
    $ptOptions = array_unique(array_merge($ptOptions, array(POST_TYPE_VC_ELEMENT)));

    //  Update Option
    update_option('wpb_js_content_types', $ptOptions);

    //  Get Page Now
    global $pagenow;

    //  Check for the VC Element Page
    if($pagenow == 'edit.php' && @$_GET['post_type'] == POST_TYPE_VC_ELEMENT) {

        //  Remove the Filter
        remove_filter('post_row_actions', array(vc_frontend_editor(), 'renderRowAction'));
    }
}

//  Callback to Custom Post Type CB
function vca_register_meta_box_cb($post) {

    //  Add Metabox
    add_meta_box(
			'vca-metabox',
			__( 'Widget Integration', PLUGIN_VCA_TEXT_DOMAIN),
			'vca_widget_selection_metabox',
			POST_TYPE_VC_ELEMENT,
			'side', 'high'
		);
}

//  Callback to Add Metabox for VC Element
function vca_widget_selection_metabox($post) {

    //  Get Sidebars Collections
    global $wp_registered_sidebars;

    //  Check Sidebars
    if(sizeof($wp_registered_sidebars) < 1) {

        //  Print Message & Return
        echo '<em>No sidebars available in current theme. Please register one or use one with widgets.</em>';
        return;
    }

    //  Get Assigned Sidebars
    $assigned_sidebars = (array)get_post_meta($post->ID, '_assigned_sidebars', true);

    //  Loop Each Sidebar
    foreach($wp_registered_sidebars as $sidebar_key => $sidebar) {

        //  Check Empty
        if(empty($sidebar_key))    continue;
?>
<p>
    <label>
        <input type="checkbox" name="selected_sidebars[]" value="<?php echo $sidebar_key; ?>" <?php echo (array_key_exists($sidebar_key, $assigned_sidebars) ? 'checked="checked"' : '') ; ?> />
        <?php echo $sidebar['name']; ?>
    </label>
</p>
<?php } ?>
<input type="hidden" name="clear_sidebars_unassigned" value="1" />
<!--<p>
<hr/>
    <label>
        <input type="checkbox" name="clear_sidebars_unassigned" value="1" />
        Clear Existing but Un-Assigned Widgets
    </label>
</p>-->
<?php
}

//  Listen the Post Save
add_action('save_post', 'vca_vc_element_saved');

//  Callback to Action
function vca_vc_element_saved($ID) {

    //  Get Post
    $post = get_post($ID);

    //  Check for Post Type
    if($post->post_status != 'auto-draft' && $post->post_type == POST_TYPE_VC_ELEMENT) {

        //  Get the Selected Sidebars
        $selected_sidebars = (isset($_POST['selected_sidebars']) ? $_POST['selected_sidebars'] : array());

        //  Get Saved Widget Details
        $assigned_sidebars = get_post_meta($ID, '_assigned_sidebars', true);
        if(!is_array($assigned_sidebars))   $assigned_sidebars = array();

        //  Get Sidebars
        //$sidebars = get_option('sidebars_widgets');
        $sidebars = wp_get_sidebars_widgets();

        //  Widget Base Key
        $wBaseKey = 'visual_composer_addon';

        //  Widget Settings
        $widgetSettings = get_option('widget_' . $wBaseKey);

        //  Index Start from
        $wKeys = array_keys($widgetSettings);
        $widgetIndex = (sizeof($wKeys) > 1 ? $wKeys[sizeof($wKeys) - 2] : 2) + 2;

        //  Loop Each Requested Sidebars
        foreach($selected_sidebars as $sSidebar) {

            //  Found In Sidebar
            $fISidebar = false;

            //  Sidebar Index
            $thisIndex = $widgetIndex;

            //  Check Key Exists
            if(!isset($sidebars[$sSidebar]))  $sidebars[$sSidebar] = array();

            //  Check for Already Assigned
            if(isset($assigned_sidebars[$sSidebar])) {

                //  Change Flag
                $fISidebar = true;

                //  Change Index
                $thisIndex = $assigned_sidebars[$sSidebar];
            }

            //  Instance ID
            $thisInstanceID = $wBaseKey . '-' . $thisIndex;

            //  Check
            if(!$fISidebar || ($fISidebar && !in_array($thisInstanceID, $sidebars[$sSidebar]))) {

                //  Assign Widget
                $assigned_sidebars[$sSidebar] = $thisIndex;
    
                //  Add Widget Instance
                $widgetSettings[$thisIndex] = array(
                    'selected_post' => (string)$ID
                );
    
                //  Add Widget Info
                $sidebars[$sSidebar][] = $thisInstanceID;
            }

            //  Filter Unique Only
            $sidebars[$sSidebar] = array_unique($sidebars[$sSidebar]);

            //  Check Not Found
            if(!$fISidebar) {

                //  Increment
                $widgetIndex++;
            }
        }

        //  Check for Clear
        if(isset($_POST['clear_sidebars_unassigned']) && $_POST['clear_sidebars_unassigned'] == '1') {

            //  Loop Each Already Assigned
            foreach($assigned_sidebars as $sKey => $sNum) {

                //  Check not Exists
                if(!in_array($sKey, $selected_sidebars)) {

                    //  Remove
                    unset($assigned_sidebars[$sKey]);
                    unset($widgetSettings[$sNum]);

                    //  Check
                    if(isset($sidebars[$sKey])) {

                        //  Loop Each Widgets
                        foreach($sidebars[$sKey] as $tmpI => $tmpWidget) {

                            //  Check
                            if($tmpWidget == $wBaseKey . '-' . $sNum) {

                                //  Unset
                                //unset($sidebars[$sKey][$tmpI]);
                                break;
                            }
                        }
                    }
                }
            }
        }

        //  Check for Multiwidget Key
        if(!isset($widgetSettings['_multiwidget'])) $widgetSettings['_multiwidget'] = 1;

        //  Loop Each Inactive
        foreach(array_values($assigned_sidebars) as $l => $sNum) {

            //  Check
            if(in_array($wBaseKey . '-' . $sNum, $sidebars['wp_inactive_widgets'])) {

                //  Unset
                unset($sidebars['wp_inactive_widgets'][$l]);
                break;
            }
        }

        //  Save Options
        update_option('widget_' . $wBaseKey, $widgetSettings);
        //update_option('sidebar_widgets', $sidebars);
        wp_set_sidebars_widgets($sidebars);

        //  Update Post Meta
        update_post_meta($ID, '_assigned_sidebars', $assigned_sidebars);

        //  Retrieve Widgets
        //retrieve_widgets();
    }
}

//  Add Filter to Updated Messages
add_filter('post_updated_messages', 'vca_element_updated_messages');

//  Callback to Filter
function vca_element_updated_messages($messages) {

    //  Get Post Details
    $post = get_post();

    //  Assign Messages
    $messages[POST_TYPE_VC_ELEMENT] = array(
        0 => '', // Unused. Messages start at index 1.
        1 => __('VC Element updated.', PLUGIN_VCA_TEXT_DOMAIN),
        2 => __('Custom field updated.', PLUGIN_VCA_TEXT_DOMAIN),
        3 => __('Custom field deleted.', PLUGIN_VCA_TEXT_DOMAIN),
        4 => __('VC Element updated.', PLUGIN_VCA_TEXT_DOMAIN),
        /* translators: %s: date and time of the revision */
        5 => isset($_GET['revision']) ? sprintf(__('VC Element restored to revision from %s', PLUGIN_VCA_TEXT_DOMAIN), wp_post_revision_title((int) $_GET['revision'], false)) : false,
        6 => __('VC Element published.', PLUGIN_VCA_TEXT_DOMAIN),
        7 => __('VC Element saved.', PLUGIN_VCA_TEXT_DOMAIN),
        8 => __('VC Element submitted.', PLUGIN_VCA_TEXT_DOMAIN),
        9 => sprintf(
                __('VC Element scheduled for: <strong>%1$s</strong>.', PLUGIN_VCA_TEXT_DOMAIN),
                // translators: Publish box date format, see http://php.net/date
                date_i18n(__('M j, Y @ G:i', PLUGIN_VCA_TEXT_DOMAIN), strtotime($post->post_date))
        ),
        10 => __('VC Element draft updated.', PLUGIN_VCA_TEXT_DOMAIN)
    );

    //  Return Updated Messages
    return $messages;
}

//  Add Action to Admin Footer
add_action('admin_footer', 'vca_admin_print_styles');

//  Print Styles Callback
function vca_admin_print_styles() {

    //  Get Global
    global $pagenow, $post;

    //  Check for VC Element Page & Disable Front Editor
    if(($pagenow == 'post-new.php' || $pagenow == 'post.php')
            && (filter_input(INPUT_GET, 'post_type') == POST_TYPE_VC_ELEMENT || $post->post_type == POST_TYPE_VC_ELEMENT)) {

        //  Enqueue Style
        wp_enqueue_style('vc-addon-style', PLUGIN_VCA_URL . 'assets/css/vc_addon.css');
?>
<script>

//  Disable Frontend Editor
vc_frontend_enabled = false;

jQuery(function($) {

    //  Keep Pinging
    var pingID = setInterval(function() {

        //  Switch Handle
        var $switchBtn = $(".wpb_switch-to-composer");

        //  Check
        if($switchBtn.length > 0) {

            //  Clear Interval
            clearInterval(pingID);

            //  Check
            if(!$("#wpb_visual_composer").is(':visible')) {

                //  Trigger the Switch to Open Visual Editor
                $(".wpb_switch-to-composer").click();
            }

            //  Hide the Panel
            //$(".wpb_switch-to-composer").parent().hide(0);

            //  Hide the Buttons
            $('#vc_post-settings-button').parent().prev().remove();
            $('#vc_post-settings-button').parent().remove();
        }
    }, 1000);
});
</script>
<?php
    }
}

//  Add Filter to VC Nav Controls
add_filter('vc_nav_controls', 'vca_vc_nav_controls');

//  Callback to Filter
function vca_vc_nav_controls($buttons) {

    //  Get Global
    global $pagenow, $post;

    //  Check for VC Element Page & Disable Front Editor
    if(($pagenow == 'post-new.php' || $pagenow == 'post.php')
            && (filter_input(INPUT_GET, 'post_type') == POST_TYPE_VC_ELEMENT || $post->post_type == POST_TYPE_VC_ELEMENT)) {

        //  Loop Each
        foreach($buttons as $i => $button) {

            //  Check for Frontend Edit
            if($button[0] == 'edit_inline') {

                //  Unset
                unset($buttons[$i]);
                break;
            }
        }
    }

    //  Return
    return $buttons;
}

//  Add Action to WP Footer
add_action('wp_footer', 'vca_wp_footer');

//  Callback to Action
function vca_wp_footer() {

    //  Check Function Exists
    if (function_exists('visual_composer')) {

        //  Register Front CSS & JS
        visual_composer()->frontCss();
        visual_composer()->frontJsRegister();

        //  Enqueue Styles
        wp_enqueue_style('js_composer_front');
    }
}
