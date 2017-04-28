<?php
 function wpb_widgets_init() {	 

	    register_sidebar( array(

	        'name' => __( 'Right Sidebar', 'wpb' ),

	        'id' => 'right-sidebar',

	        'description' => __( 'The right sidebar appears on the left on Activity Page', 'wpb' ),

	        'before_widget' => '<div class="widget_list"><div class="widget_lft">',

	        'after_widget' => '</div><div class="clear"></div></div>',

	        'before_title' => '<h3 class="right_widget_title">',

	        'after_title' => '</h3>',

	    ) );

	 

	    register_sidebar( array(

	        'name' =>__( 'Front page sidebar', 'wpb'),

	        'id' => 'sidebar-2',

	        'description' => __( 'Appears on the static front page template', 'wpb' ),

	        'before_widget' => '<aside id="%1$s" class="widget %2$s">',

	        'after_widget' => '</aside>',
	        'before_title' => '<h3 class="widget-title">',
	        'after_title' => '</h3>',

	    ) );

	    }

	 

	add_action( 'widgets_init', 'wpb_widgets_init' );
	
	function my_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentysixteen-style' for the Twenty Sixteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
	
	