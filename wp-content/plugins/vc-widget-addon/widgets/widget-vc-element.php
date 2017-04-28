<?php

class Widget_VC_Element extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        //  Construct
        parent::__construct(
            'visual_composer_addon',
            __('Visual Composer Element', PLUGIN_VCA_TEXT_DOMAIN),
            array('description' => __('Visual Composer Element Addon with Widgets', PLUGIN_VCA_TEXT_DOMAIN))
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

        //  Get the Post ID
        $selected_post = $instance['selected_post'];

        //  Get Post
        $sPost = ($selected_post > 0 ? get_post($selected_post) : null);

        //  Check
        if(!$sPost) return;

        //  Display Title
        $display_title = (bool)(isset($instance['display_title']) ? $instance['display_title'] : 1);

        //  Use Wrapper
        $use_wrapper = (bool)(isset($instance['use_wrapper']) ? $instance['use_wrapper'] : 1);

        //  Global Post
        global $post;

        //  Main Post
        $mainPost = $post;

        //  Assign
        $post = $sPost;

        //  Setup Post
        setup_postdata($post);

        //  Get the Widget Title
        $title = apply_filters('widget_title', $sPost->post_title);

        //  Get Widget Contents
        $contents = apply_filters('the_content', $sPost->post_content);

        //  Validate
        if(!$contents || empty($contents))  return;

        //  Print Before Widget
        if($use_wrapper)    echo $args['before_widget'];

        //  Check for Title
        if (!empty($title) && $display_title) {

            //  Print Widget Title
            echo $args['before_title'] . $title . $args['after_title'];
        }

        //  Print Contents
        echo '<div class="vcw-wrapper">' . $contents . '</div>';

        //  Print After Widget
        if($use_wrapper)    echo $args['after_widget'];

        //  Assign
        $post = $mainPost;

        //  Setup Post
        setup_postdata($post);
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return string|void
     */
    public function form($instance) {

        //  Get the Posts
        $posts = get_posts(array(
            'nopaging' => true,
            'post_type' => POST_TYPE_VC_ELEMENT,
            'status' => 'publish'
        ));

        //  Options List
        $options_list = array();

        //  Loop Each Posts
        foreach($posts as $post) {

            //  Add
            $options_list[$post->ID] = $post->post_title;
        }

        //  Reset Postdata
        wp_reset_postdata();

        //  Select Post
        $selected_post = (isset($instance['selected_post']) ? $instance['selected_post'] : 0);

        //  Display Title
        $display_title = (bool)(isset($instance['display_title']) ? $instance['display_title'] : '1');

        //  Use Wrapper
        $use_wrapper = (bool)(isset($instance['use_wrapper']) ? $instance['use_wrapper'] : '1');
    ?>
        <div class="vc-addon-widget-be">
            <p>
                <label for="<?php echo $this->get_field_id('selected_post'); ?>"><?php _e('VC Element:'); ?></label>
                <select class="widefat vc-addon-select" id="<?php echo $this->get_field_id('selected_post'); ?>" name="<?php echo $this->get_field_name('selected_post'); ?>">
                <?php foreach($options_list as $optKey => $optLabel) { ?>
                    <option value="<?php echo $optKey; ?>" <?php echo ($optKey == $selected_post ? 'selected="selected"' : ''); ?>><?php echo $optLabel; ?></option>
                <?php } ?>
                </select>
            </p>
            <!--<p>
                <input type="hidden" name="<?php echo $this->get_field_name('display_title'); ?>" value="0" />
                <input type="checkbox" id="<?php echo $this->get_field_id('display_title'); ?>" name="<?php echo $this->get_field_name('display_title'); ?>" value="1" <?php echo ($display_title == '1' ? 'checked="checked"' : ''); ?> />
                <label for="<?php echo $this->get_field_id('display_title'); ?>"><?php _e('Display Title'); ?></label>
            </p>
            <p>
                <input type="hidden" name="<?php echo $this->get_field_name('use_wrapper'); ?>" value="0" />
                <input type="checkbox" id="<?php echo $this->get_field_id('use_wrapper'); ?>" name="<?php echo $this->get_field_name('use_wrapper'); ?>" value="1" <?php echo ($use_wrapper == '1' ? 'checked="checked"' : ''); ?> />
                <label for="<?php echo $this->get_field_id('use_wrapper'); ?>"><?php _e('Use Wrapper'); ?></label>
            </p>-->
        </div>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {

        //  Create Instance Data
        $instance = array();
        $instance['selected_post'] = (!empty($new_instance['selected_post']) ? strip_tags($new_instance['selected_post']) : 0);
        $instance['display_title'] = (isset($new_instance['display_title']) ? $new_instance['display_title'] : 1);
        $instance['use_wrapper'] = (isset($new_instance['use_wrapper']) ? $new_instance['use_wrapper'] : 1);

        //  Return
        return $instance;
    }

}
