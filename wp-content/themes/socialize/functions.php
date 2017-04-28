<?php

/////////////////////////////////////// File directories ///////////////////////////////////////

$gp_template_dir = get_template_directory();
$gp_template_dir_uri = get_template_directory_uri();
define( 'socialize', $gp_template_dir . '/' );
define( 'socialize_uri', $gp_template_dir_uri . '/' );
define( 'socialize_css_uri', $gp_template_dir_uri . '/lib/css/' );
define( 'socialize_images', $gp_template_dir_uri . '/lib/images/' );
define( 'socialize_inc', $gp_template_dir . '/lib/inc/' );
define( 'socialize_plugins', $gp_template_dir . '/lib/plugins/' );
define( 'socialize_scripts', $gp_template_dir . '/lib/scripts/' );
define( 'socialize_scripts_uri', $gp_template_dir_uri . '/lib/scripts/' );
define( 'socialize_widgets', $gp_template_dir . '/lib/widgets/' );
define( 'socialize_vc', $gp_template_dir . '/lib/vc-templates/' );


/////////////////////////////////////// Localisation ///////////////////////////////////////

load_theme_textdomain( 'socialize', socialize . 'languages' );
$gp_locale = get_locale();
$gp_locale_file = socialize . 'languages/$gp_locale.php';
if ( is_readable( $gp_locale_file ) ) { require_once( $gp_locale_file ); }
		
		
/////////////////////////////////////// Theme setup ///////////////////////////////////////

if ( ! function_exists( 'socialize_theme_setup' ) ) {
	function socialize_theme_setup() {

		global $content_width;
		
		// Set the content width based on the theme's design and stylesheet
		if ( ! isset( $content_width ) ) {
			$content_width = 730;
		}
		
		// Featured images
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 150, 150, true );

		// Background customizer
		add_theme_support( 'custom-background' );

		// Add shortcode support to Text widget
		add_filter( 'widget_text', 'do_shortcode' );

		// This theme styles the visual editor with editor-style.css to match the theme style
		add_editor_style( 'lib/css/editor-style.css' );

		// Add default posts and comments RSS feed links to <head>
		add_theme_support( 'automatic-feed-links' );

		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		
		// Post formats
		add_theme_support( 'post-formats', array( 'quote', 'video', 'audio', 'gallery', 'link' ) );

		// Title support
		add_theme_support( 'title-tag' );

	}
}
add_action( 'after_setup_theme', 'socialize_theme_setup' );


/////////////////////////////////////// Additional functions ///////////////////////////////////////

// Metaboxes
require_once( socialize . 'lib/metaboxes/config.php' );

// Framework
if ( ! class_exists( 'ReduxFramework' ) && file_exists( socialize . 'lib/ReduxCore/framework.php' ) ) {
    require_once( socialize . 'lib/ReduxCore/framework.php' );
}
if ( ! isset( $socialize ) && file_exists( socialize_inc. 'theme-config.php' ) ) {
    require_once( socialize_inc . 'theme-config.php' );
}

global $socialize;
$socialize = get_option( 'socialize' );

// BuddyPress functions
require_once( socialize_inc . 'bp-functions.php' );

// bbPress functions
require_once( socialize_inc . 'bbpress-functions.php' );

// Page settings
require_once( socialize_inc . 'page-settings.php' );

// Page variables
require_once( socialize_inc . 'page-variables.php' );

// Page headers
require_once( socialize_inc . 'page-headers.php' );

// One click demo installer
require socialize . 'lib/demo-installer/init.php';

// Image resizer
require_once( socialize_inc . 'aq_resizer.php' );

// Custom menu walker
require_once( socialize . 'lib/menus/custom-menu-walker.php' );

// Custom menu fields
require_once( socialize . 'lib/menus/menu-item-custom-fields.php' );

// Shortcodes
if ( function_exists( 'vc_set_as_theme' ) ) {
	require_once( socialize_inc . 'theme-shortcodes.php' );
	require_once( socialize_inc . 'default-vc-templates.php' );
}

// Woocommerce functions
if ( function_exists( 'is_woocommerce' ) ) {
	require_once( socialize_inc . 'wc-functions.php' );
}

// Ajax loop
if ( isset( $socialize['ajax'] ) && $socialize['ajax'] == 'gp-ajax-loop' ) {
	require_once( socialize_inc . 'ajax.php' );
}


/////////////////////////////////////// Enqueue Styles ///////////////////////////////////////

if ( ! function_exists( 'socialize_enqueue_styles' ) ) {

	function socialize_enqueue_styles() { 

		global $socialize;
		
		wp_enqueue_style( 'gp-style', get_stylesheet_uri() );
		
		wp_enqueue_style( 'gp-font-awesome', socialize_uri . 'lib/fonts/font-awesome/css/font-awesome.min.css' );
					
		if ( isset( $socialize['lightbox'] ) && $socialize['lightbox'] != 'disabled' ) {
			wp_enqueue_style( 'gp-prettyphoto', socialize_scripts_uri . 'prettyPhoto/css/prettyPhoto.css' );
		}
		
		if ( ! empty( $socialize['custom_stylesheet'] ) ) {
			wp_enqueue_style( 'gp-custom-style', socialize_uri . $socialize['custom_stylesheet'] );
		}

	}
}

add_action( 'wp_enqueue_scripts', 'socialize_enqueue_styles' );
 
			
/////////////////////////////////////// Enqueue Scripts ///////////////////////////////////////
		
if ( ! function_exists( 'socialize_enqueue_scripts' ) ) {

	function socialize_enqueue_scripts() {

		global $socialize, $post;
		
		wp_enqueue_script( 'gp-modernizr', socialize_scripts_uri . 'modernizr.js', false, '', true );
				
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) { 
			wp_enqueue_script( 'comment-reply' );
		}

		if ( $socialize['smooth_scrolling'] == 'gp-smooth-scrolling' ) { 
			wp_enqueue_script( 'gp-nicescroll', socialize_scripts_uri . 'nicescroll.min.js', false, '', true );
		}
		
		wp_enqueue_script( 'gp-selectivizr', socialize_scripts_uri . 'selectivizr.min.js', false, '', true );

		wp_enqueue_script( 'gp-placeholder', socialize_scripts_uri . 'placeholders.min.js', false, '', true );
									
		if ( isset( $socialize['lightbox'] ) && $socialize['lightbox'] != 'disabled' ) {							
			wp_enqueue_script( 'gp-prettyphoto', socialize_scripts_uri . 'prettyPhoto/js/jquery.prettyPhoto.js', array( 'jquery' ), '', true );
		}

		if ( $socialize['back_to_top'] == 'gp-back-to-top' ) { 
			wp_enqueue_script( 'gp-back-to-top', socialize_scripts_uri . 'jquery.ui.totop.min.js', array( 'jquery' ), '', true );
		}
							
		wp_enqueue_script( 'gp-custom-js', socialize_scripts_uri . 'custom.js', array( 'jquery' ), '', true );

		wp_localize_script( 'gp-custom-js', 'socialize_script', array(
			'lightbox' => $socialize['lightbox'],
		) );	

		wp_register_script( 'gp-flexslider', socialize_scripts_uri . 'jquery.flexslider-min.js', array( 'jquery' ), '', true );
		
		wp_register_script( 'gp-isotope', socialize_scripts_uri . 'isotope.pkgd.min.js', false, '', true );
				
		wp_register_script( 'gp-images-loaded', socialize_scripts_uri . 'imagesLoaded.min.js', false, '', true );

		wp_register_script( 'gp-stellar', socialize_scripts_uri . 'jquery.stellar.min.js', array( 'jquery' ), '', true );

		wp_register_script( 'gp-video-header', socialize_scripts_uri . 'jquery.video-header.js', array( 'jquery' ), '', true );
						
	}
}

add_action( 'wp_enqueue_scripts', 'socialize_enqueue_scripts' );


/////////////////////////////////////// Enqueue Admin Styles ///////////////////////////////////////

if ( ! function_exists( 'socialize_enqueue_admin_styles' ) ) {
	function socialize_enqueue_admin_styles( $gp_hook ) {
			wp_enqueue_style( 'gp-admin ', socialize_css_uri . 'admin.css' );
	}
}
add_action( 'admin_enqueue_scripts', 'socialize_enqueue_admin_styles' );	
	

/////////////////////////////////////// Enqueue Admin Scripts ///////////////////////////////////////

if ( ! function_exists( 'socialize_enqueue_admin_scripts' ) ) {
	function socialize_enqueue_admin_scripts( $gp_hook ) {
		if ( 'post.php' == $gp_hook OR 'post-new.php' == $gp_hook ) {
			wp_enqueue_script( 'gp-admin ', socialize_scripts_uri . 'admin.js', '', '', true );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'socialize_enqueue_admin_scripts' );	


/////////////////////////////////////// WP Header Hooks ///////////////////////////////////////

if ( ! function_exists( 'socialize_wp_header' ) ) {

	function socialize_wp_header() {
	
		global $socialize;

		// Title fallback for versions earlier than WordPress 4.1
		if ( ! function_exists( '_wp_render_title_tag' ) && ! function_exists( 'socialize_render_title' ) ) {
			function socialize_render_title() { ?>
				<title><?php wp_title( '|', true, 'right' ); ?></title>
			<?php }
		}

		// Page settings
		socialize_page_settings();

		// Style settings
		require_once( socialize_inc . 'style-settings.php' );

		echo '<!--[if gte IE 9]><style>.gp-slider-wrapper .gp-slide-caption + .gp-post-thumbnail:before,body:not(.gp-full-page-page-header) .gp-page-header.gp-has-text:before,body:not(.gp-full-page-page-header) .gp-page-header.gp-has-teaser-video.gp-has-text .gp-video-header:before{filter: none;}</style><![endif]-->';

		// Javascript code
		if ( isset( $socialize['js_code'] ) && ( ! ctype_space( $socialize['js_code'] ) && ! empty( $socialize['js_code'] ) ) ) {
			$socialize['js_code'] = str_replace( array( '<script>', '</script>' ), '', $socialize['js_code'] );
			echo '<script>' . $socialize['js_code'] . '</script>';
		}	
		
	}
	
}

add_action( 'wp_head', 'socialize_wp_header' );


/////////////////////////////////////// Navigation Menus ///////////////////////////////////////

if ( ! function_exists( 'socialize_register_menus' ) ) {
	function socialize_register_menus() {
		register_nav_menus(array(
			'gp-primary-main-header-nav' => esc_html__( 'Primary Main Header Navigation', 'socialize' ),
			'gp-secondary-main-header-nav' => esc_html__( 'Secondary Main Header Navigation', 'socialize' ),
			'gp-left-small-header-nav'    => esc_html__( 'Left Small Header Navigation', 'socialize' ),
			'gp-right-small-header-nav' => esc_html__( 'Right Small Header Navigation', 'socialize' ),
			'gp-footer-nav' => esc_html__( 'Footer Navigation', 'socialize' ),
		) );
	}
}
add_action( 'init', 'socialize_register_menus' );


/////////////////////////////////////// Navigation User Meta ///////////////////////////////////////

if ( ! function_exists( 'socialize_nav_user_meta' ) ) {
	function socialize_nav_user_meta( $gp_user_id = NULL ) {

		// These are the metakeys we will need to update
		$GLOBALS['socialize_meta_key']['menus'] = 'metaboxhidden_nav-menus';
		$GLOBALS['socialize_meta_key']['properties'] = 'managenav-menuscolumnshidden';

		// So this can be used without hooking into user_register
		if ( ! $gp_user_id ) {
			$gp_user_id = get_current_user_id(); 
		}
	
		// Set the default hiddens if it has not been set yet
		if ( ! get_user_meta( $gp_user_id, $GLOBALS['socialize_meta_key']['menus'], true ) ) {
			$gp_meta_value = array( 'add-gp_slides', 'add-gp_slide' );
			update_user_meta( $gp_user_id, $GLOBALS['socialize_meta_key']['menus'], $gp_meta_value );
		}

		// Set the default properties if it has not been set yet
		if ( ! get_user_meta( $gp_user_id, $GLOBALS['socialize_meta_key']['properties'], true) ) {
			$gp_meta_value = array( 'link-target', 'xfn', 'description' );
			update_user_meta( $gp_user_id, $GLOBALS['socialize_meta_key']['properties'], $gp_meta_value );
		}
	
	}	
}
add_action( 'admin_init', 'socialize_nav_user_meta' );


/////////////////////////////////////// Sidebars/Widgets ///////////////////////////////////////

// Categories Widget
require_once( socialize_widgets . 'categories.php' );
	
// Recent Comments Widget
require_once( socialize_widgets . 'recent-comments.php' );

// Recent Posts Widget
require_once( socialize_widgets . 'recent-posts.php' );

if ( ! function_exists( 'socialize_widgets_init' ) ) {
	function socialize_widgets_init() {

		// Sidebars
		register_sidebar( array( 
			'name'          => esc_html__( 'Left Sidebar', 'socialize' ),
			'id'            => 'gp-left-sidebar',
			'description'   => esc_html__( 'Displayed on posts, pages and post categories.', 'socialize' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array( 
			'name'          => esc_html__( 'Right Sidebar', 'socialize' ),
			'id'            => 'gp-right-sidebar',
			'description'   => esc_html__( 'Displayed on posts, pages and post categories.', 'socialize' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array( 
			'name'          => esc_html__( 'Homepage Left Sidebar', 'socialize' ),
			'id'            => 'gp-homepage-left-sidebar',
			'description'   => esc_html__( 'Displayed on the homepage.', 'socialize' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array( 
			'name'          => esc_html__( 'Homepage Right Sidebar', 'socialize' ),
			'id'            => 'gp-homepage-right-sidebar',
			'description'   => esc_html__( 'Displayed on the homepage.', 'socialize' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );
				
		register_sidebar( array(
			'name'          => esc_html__( 'Footer 1', 'socialize' ),
			'id'            => 'gp-footer-1',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );        

		register_sidebar( array(
			'name'          => esc_html__( 'Footer 2', 'socialize' ),
			'id'            => 'gp-footer-2',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );        
	
		register_sidebar( array(
			'name'          => esc_html__( 'Footer 3', 'socialize' ),
			'id'            => 'gp-footer-3',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );        
	
		register_sidebar( array(
			'name'          => esc_html__( 'Footer 4', 'socialize' ),
			'id'            => 'gp-footer-4',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );      

		register_sidebar( array(
			'name'          => esc_html__( 'Footer 5', 'socialize' ),
			'id'            => 'gp-footer-5',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );

		// Deprecated since v1.1
		register_sidebar( array( 
			'name'          => esc_html__( 'Standard Sidebar (Deprecated)', 'socialize' ),
			'id'            => 'gp-standard-sidebar',
			'description'   => esc_html__( 'Displayed on posts, pages and post categories.', 'socialize' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>',
		) );
			
	}
}
add_action( 'after_setup_theme', 'socialize_widgets_init' );


/////////////////////////////////////// Add VC elements to sidebar  ///////////////////////////////////////

if ( ! function_exists( 'socialize_pre_set_widgets' ) ) {
	function socialize_pre_set_widgets() {

		global $wp_registered_widgets;
	
		// Get VC element IDs from titles
		$gp_vc_element_1 = get_page_by_title( 'Homepage Left Sidebar', OBJECT, 'vc-element' );
		$gp_vc_element_2 = get_page_by_title( 'Homepage Right Sidebar', OBJECT, 'vc-element' );

		if ( get_option( 'socialize_pre_set_widgets' ) !== '1' && $gp_vc_element_1 && $gp_vc_element_2 ) {

			function socialize_pre_set_widget( $sidebar, $name, $args = array() ) {

				if ( ! $sidebars = get_option( 'sidebars_widgets' ) )
					$sidebars = array();

				// Create the sidebar if it doesn't exist.
				if ( ! isset( $sidebars[ $sidebar ] ) )
					$sidebars[ $sidebar ] = array();

				// Check for existing saved widgets.
				if ( $widget_opts = get_option( "widget_$name" ) ) {
					// Get next insert id.
					ksort( $widget_opts );
					end( $widget_opts );
					$insert_id = key( $widget_opts );
				} else {
					// None existing, start fresh.
					$widget_opts = array( '_multiwidget' => 1 );
					$insert_id = 0;
				}

				// Add our settings to the stack.
				$widget_opts[ ++$insert_id ] = $args;
	
				// Add our widget!
				$sidebars[ $sidebar ][] = "$name-$insert_id";

				update_option( 'sidebars_widgets', $sidebars );
				update_option( "widget_$name", $widget_opts );
			}

			// Add specified VC elemements to left and right homepage sidebars
			socialize_pre_set_widget( 'gp-homepage-left-sidebar', 'visual_composer_addon',
				array(
					'selected_post' => $gp_vc_element_1->ID,
				)
			);
			socialize_pre_set_widget( 'gp-homepage-right-sidebar', 'visual_composer_addon',
				array(
					'selected_post' => $gp_vc_element_2->ID,
				)
			);
		
			update_option( 'socialize_pre_set_widgets', '1' );
		
		}

	}
}
add_action( 'after_setup_theme', 'socialize_pre_set_widgets' );


/////////////////////////////////////// Excerpts ///////////////////////////////////////

// Character Length
if ( ! function_exists( 'socialize_excerpt_length' ) ) {
	function socialize_excerpt_length() {
		if ( function_exists( 'buddyboss_global_search_init' ) && is_search() ) {
			return 50;
		} else {
			return 10000;
		}	
	}
}
add_filter( 'excerpt_length', 'socialize_excerpt_length' );

// Excerpt Output
if ( ! function_exists( 'socialize_excerpt' ) ) {
	function socialize_excerpt( $gp_length ) {
		global $socialize;
		if ( isset( $GLOBALS['socialize_read_more_link'] ) && $GLOBALS['socialize_read_more_link'] == 'enabled' ) {
			$gp_more_text = '...<a href="' . esc_url( get_permalink( get_the_ID() ) ) . '" class="gp-read-more" title="' . the_title_attribute( 'echo=0' ) . '">' . esc_html__( 'Read More', 'socialize' ) . '</a>';
		} else {
			$gp_more_text = '...';
		}	
		$gp_excerpt = get_the_excerpt();					
		$gp_excerpt = strip_tags( $gp_excerpt );
		if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) { 
			if ( mb_strlen( $gp_excerpt ) > $gp_length ) {
				$gp_excerpt = mb_substr( $gp_excerpt, 0, $gp_length ) . $gp_more_text;
			}
		} else {
			if ( strlen( $gp_excerpt ) > $gp_length ) {
				$gp_excerpt = substr( $gp_excerpt, 0, $gp_length ) . $gp_more_text;
			}	
		}
		return $gp_excerpt;
	}
}


/////////////////////////////////////// Add Excerpt Support To Pages ///////////////////////////////////////

if ( ! function_exists( 'socialize_add_excerpts_to_pages' ) ) {
	function socialize_add_excerpts_to_pages() {
		 add_post_type_support( 'page', 'excerpt' );
	}
}
add_action( 'init', 'socialize_add_excerpts_to_pages' );


/////////////////////////////////////// Add post tags to pages ///////////////////////////////////////	

if ( ! function_exists( 'socialize_page_tags_support' ) ) {
	function socialize_page_tags_support() {
		register_taxonomy_for_object_type( 'post_tag', 'page' );
	}
}
add_action( 'init', 'socialize_page_tags_support' );

if ( ! function_exists( 'socialize_page_tags_support_query' ) ) {
	function socialize_page_tags_support_query( $wp_query ) {
		if ( $wp_query->get( 'tag' ) ) {
			$wp_query->set( 'post_type', 'any' );
		}	
	}
}
add_action( 'pre_get_posts', 'socialize_page_tags_support_query' );


/////////////////////////////////////// Change Password Protect Post Text ///////////////////////////////////////	

if ( ! function_exists( 'socialize_password_form' ) ) {
	function socialize_password_form() {
		global $post;
		$gp_label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
		$gp_o = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
		<p>' . esc_html__( 'To view this protected post, enter the password below:', 'socialize' ) . '</p>
		<label for="' . $gp_label . '"><input name="post_password" id="' . $gp_label . '" type="password" size="20" maxlength="20" /></label> <input type="submit" class="pwsubmit" name="Submit" value="' .  esc_attr__( 'Submit', 'socialize' ) . '" />
		</form>
		';
		return $gp_o;
	}
}
add_filter( 'the_password_form', 'socialize_password_form' );


/////////////////////////////////////// Redirect Empty Search To Search Page ///////////////////////////////////////	

if ( ! function_exists( 'socialize_empty_search' ) ) {
	function socialize_empty_search( $gp_query ) {
		global $wp_query;
		if ( isset( $_GET['s'] ) && ( $_GET['s'] == '' ) ) {
			$wp_query->set( 's', ' ' );
			$wp_query->is_search = true;
		}
		return $gp_query;
	}
}
add_action( 'pre_get_posts', 'socialize_empty_search' );


/////////////////////////////////////// Alter category queries ///////////////////////////////////////	

if ( ! function_exists( 'socialize_category_queries' ) ) {
	function socialize_category_queries( $gp_query ) {
		global $socialize;			
		if ( is_admin() OR ! $gp_query->is_main_query() ) { 
			return;
		} else {
			if ( is_post_type_archive( 'gp_portfolio_item' ) OR is_tax( 'gp_portfolios' ) )  {
				$GLOBALS['socialize_orderby'] = $socialize['portfolio_cat_orderby'];
				$GLOBALS['socialize_per_page'] = $socialize['portfolio_cat_per_page'];
				$GLOBALS['socialize_date_posted'] = $socialize['portfolio_cat_date_posted'];
				$GLOBALS['socialize_date_modified'] = $socialize['portfolio_cat_date_modified'];
			} elseif ( is_author() ) {
				$GLOBALS['socialize_orderby'] = $socialize['search_orderby'];
				$GLOBALS['socialize_per_page'] = $socialize['search_per_page'];
				$GLOBALS['socialize_date_posted'] = $socialize['search_date_posted'];
				$GLOBALS['socialize_date_modified'] = $socialize['search_date_modified'];
			} elseif ( is_search() OR is_author() ) {
				$GLOBALS['socialize_orderby'] = $gp['search_orderby'];
				$GLOBALS['socialize_per_page'] = $socialize['search_per_page'];
				$GLOBALS['socialize_date_posted'] = $socialize['search_date_posted'];
				$GLOBALS['socialize_date_modified'] = $socialize['search_date_modified'];				
			} elseif ( is_home() OR is_archive() ) {
				$GLOBALS['socialize_orderby'] = $socialize['cat_orderby'];
				$GLOBALS['socialize_per_page'] = $socialize['cat_per_page'];
				$GLOBALS['socialize_date_posted'] = $socialize['cat_date_posted'];
				$GLOBALS['socialize_date_modified'] = $socialize['cat_date_modified'];
			}
			if ( isset( $GLOBALS['socialize_per_page'] ) ) {
				socialize_query_variables();
				$gp_query->set( 'posts_per_page', $GLOBALS['socialize_per_page'] );
				if ( ! is_search() ) {
					$gp_query->set( 'orderby', $GLOBALS['socialize_orderby_value'] );	
					$gp_query->set( 'order', $GLOBALS['socialize_order'] );
					$gp_query->set( 'meta_key', $GLOBALS['socialize_meta_key'] );
				}
				$gp_query->set( 'date_query', array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ) );
				return;
			}	
		}
	}
}	
add_action( 'pre_get_posts', 'socialize_category_queries', 1 );


/////////////////////////////////////// Pagination ///////////////////////////////////////	

if ( ! function_exists( 'socialize_pagination' ) ) {
	function socialize_pagination( $gp_query ) {
		$gp_big = 999999999;
		if ( get_query_var( 'paged' ) ) {
			$gp_paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$gp_paged = get_query_var( 'page' );
		} else {
			$gp_paged = 1;
		}
		if ( $gp_query >  1 ) {
			return '<div class="gp-pagination gp-pagination-numbers gp-standard-pagination">' . paginate_links( array(
				'base'      => str_replace( $gp_big, '%#%', esc_url( get_pagenum_link( $gp_big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, $gp_paged ),
				'total'     => $gp_query,
				'type'      => 'list',
				'prev_text' => '',
				'next_text' => '',
			) ) . '</div>';
		}
	}
}

if ( ! function_exists( 'socialize_get_previous_posts_page_link' ) ) {
	function socialize_get_previous_posts_page_link() {
		global $paged;
		$gp_nextpage = intval( $paged ) - 1;
		if ( $gp_nextpage < 1 ) {
			$gp_nextpage = 1;
		}	
		if ( $paged > 1 ) {
			return '<a href="#" data-pagelink="' . esc_attr( $gp_nextpage ) . '" class="prev"></a>';
		} else {
			return '<span class="prev gp-disabled"></span>';
		}
	}
}		

if ( ! function_exists( 'socialize_get_next_posts_page_link' ) ) {
	function socialize_get_next_posts_page_link( $gp_max_page = 0 ) {
		global $paged;
		if ( ! $paged ) {
			$paged = 1;
		}	
		$gp_nextpage = intval( $paged ) + 1;
		if ( ! $gp_max_page || $gp_max_page >= $gp_nextpage ) {
			return '<a href="#" data-pagelink="' . esc_attr( $gp_nextpage ) . '" class="next"></a>';
		} else {
			return '<span class="next gp-disabled"></span>';
		}
	}
}


/////////////////////////////////////// Canonical, next and prev rel links on page templates ///////////////////////////////////////	

if ( ! function_exists( 'socialize_rel_prev_next' ) && function_exists( 'wpseo_auto_load' ) ) {
	function socialize_rel_prev_next() {
		if ( is_page_template( 'blog-template.php' ) OR is_page_template( 'portfolio-template.php' ) ) {
		
			global $paged;
		
			// Get template queries
			socialize_query_variables();
			
			if ( is_page_template( 'blog-template.php' ) ) {

				$gp_args = array(
					'post_status' 	      => 'publish',
					'post_type'           => explode( ',', $GLOBALS['socialize_post_types'] ),
					'tax_query'           => array( 'relation' => 'OR', $GLOBALS['socialize_post_cats'], $GLOBALS['socialize_portfolio_cats'], $GLOBALS['socialize_slide_cats'] ),
					'orderby'             => $GLOBALS['socialize_orderby_value'],
					'order'               => $GLOBALS['socialize_order'],
					'meta_key'            => $GLOBALS['socialize_meta_key'],
					'posts_per_page'      => $GLOBALS['socialize_per_page'],
					'paged'               => $GLOBALS['socialize_paged'],
					'date_query'          => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),	
				);

			} else {

				$gp_args = array(
					'post_status'         => 'publish',
					'post_type'           => 'gp_portfolio_item',
					'tax_query'           => array( 'relation' => 'OR', $GLOBALS['socialize_portfolio_cats'] ),
					'posts_per_page'      => $GLOBALS['socialize_per_page'],
					'orderby'             => $GLOBALS['socialize_orderby_value'],
					'order'               => $GLOBALS['socialize_order'],
					'paged'               => $GLOBALS['socialize_paged'],
					'date_query'          => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),	
				);
					
			}	

			// Contains query data
			$gp_query = new wp_query( $gp_args );
			
			// Get maximum pages from query
			$gp_max_page = $gp_query->max_num_pages;
			
			if ( ! $paged ) {
				$paged = 1;
			}
		
			// Prev rel link
			$gp_prevpage = intval( $paged ) - 1;
			if ( $gp_prevpage < 1 ) {
				$gp_prevpage = 1;
			}	
			if ( $paged > 1 ) {
				echo '<link rel="prev" href="' . get_pagenum_link( $gp_prevpage ) . '">';
			}
		
			// Next rel link
			$gp_nextpage = intval( $paged ) + 1;	
			if ( ! $gp_max_page OR $gp_max_page >= $gp_nextpage ) {
				echo '<link rel="next" href="' . get_pagenum_link( $gp_nextpage ) . '">';
			}
		
		}
	}
	add_action( 'wp_head', 'socialize_rel_prev_next' );
}
	
if ( ! function_exists( 'socialize_canonical_link' ) && function_exists( 'wpseo_auto_load' ) ) {	
	function socialize_canonical_link( $gp_canonical ) {
		if ( is_page_template( 'blog-template.php' ) OR is_page_template( 'portfolio-template.php' ) ) {
			global $paged;		
			if ( ! $paged ) {
				$paged = 1;
			}
			return get_pagenum_link( $paged );
		} else {
			return $gp_canonical;
		}
	}
	add_filter( 'wpseo_canonical', 'socialize_canonical_link' );
}


/////////////////////////////////////// Exclude categories ///////////////////////////////////////	

if ( ! function_exists( 'socialize_exclude_cats' ) ) {
	function socialize_exclude_cats( $gp_post_id, $gp_no_link = false ) {
	
		global $socialize;
					
		// Get categories for post
		$gp_cats = wp_get_object_terms( $gp_post_id, 'category', array( 'fields' => 'ids' ) );
		
		// Remove categories that are excluded
		if ( isset( $socialize['cat_exclude_cats'] ) && ! empty( $socialize['cat_exclude_cats'] ) ) { 
			$gp_excluded_cats = array_diff( $gp_cats, $socialize['cat_exclude_cats'] );
		} else {
			$gp_excluded_cats = $gp_cats;
		}
		
		// Construct new categories loop
		if ( ! empty( $gp_excluded_cats ) && ! is_wp_error( $gp_excluded_cats ) ) { 		
			$gp_cat_link = '';
			foreach( $gp_excluded_cats as $gp_excluded_cat ) {
				if ( has_term( $gp_excluded_cat, 'category', $gp_post_id ) ) {
					$term = get_term( $gp_excluded_cat, 'category' );
					$term_link = get_term_link( $term, 'category' );
					if ( ! $term_link OR is_wp_error( $term_link ) ) {
						continue;
					}
					if ( $gp_no_link == true ) {
						$gp_cat_link .= esc_attr( $term->name ) . ' / ';
					} else {
						$gp_cat_link .= '<a href="' . esc_url( $term_link ) . '">' . esc_attr( $term->name ) . '</a> / ';
					}
				}
			}
			return rtrim( $gp_cat_link, ' / ' );
		}

	}
}


/////////////////////////////////////// Post category options ///////////////////////////////////////	

if ( ! function_exists( 'socialize_add_tax_fields' ) ) {
	function socialize_add_tax_fields( $gp_tag ) {

		if ( isset( $gp_tag->term_id ) ) {
			$gp_term_id = $gp_tag->term_id;
			$gp_term_meta = get_option( "taxonomy_$gp_term_id" );
		} else {
			$gp_term_meta = null;
		} ?>

		<div class="form-field">
			<label for="category-page-header"><?php esc_html_e( 'Page Header', 'socialize' ); ?></label>
			<select id="gp_term_meta" name="gp_term_meta[page_header]">
				<option value="default"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
				<option value="gp-standard-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-standard-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Standard', 'socialize' ); ?></option>
				<option value="gp-large-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-large-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Large', 'socialize' ); ?></option>
				<option value="gp-fullwidth-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-fullwidth-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
				<option value="gp-full-page-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-full-page-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Full Page', 'socialize' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'The page header on the page.', 'socialize' ); ?></p>
		</div>
							
		<div class="form-field">
			<label for="category-bg-image"><?php esc_html_e( 'Page Header Background', 'socialize' ); ?></label>
			<input type="text" id="gp_term_meta" name="gp_term_meta[bg_image]" value="<?php echo esc_url( $gp_term_meta['bg_image'] ? $gp_term_meta['bg_image'] : '' ); ?>" />
			<p class="description"><?php wp_kses( _e( 'The background of the page header. <strong>Enter an image URL that must be uploaded to the Media Library.</strong>', 'socialize' ), array( 'strong' => array(), ) ); ?></p>
		</div>

		<div class="form-field">
			<label for="category-layout"><?php esc_html_e( 'Page Layout', 'socialize' ); ?></label>
			<select id="gp_term_meta" name="gp_term_meta[layout]">
				<option value="default"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
				<option value="gp-left-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-left-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Left Sidebar', 'socialize' ); ?></option>
				<option value="gp-right-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-right-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Right Sidebar', 'socialize' ); ?></option>
				<option value="gp-both-sidebars"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-both-sidebars' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Both Sidebars', 'socialize' ); ?></option>
				<option value="gp-no-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-no-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'No Sidebar', 'socialize' ); ?></option>
				<option value="gp-fullwidth"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-fullwidth' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'The layout of the page.', 'socialize' ); ?></p>
		</div>
	
		<div class="form-field">
			<label for="category-left-sidebar"><?php esc_html_e( 'Left Sidebar', 'socialize' ); ?></label>
			<?php $gp_term_meta['left_sidebar'] = isset( $gp_term_meta['left_sidebar'] ) ? $gp_term_meta['left_sidebar'] : ''; ?>
			<select id="gp_term_meta" name="gp_term_meta[left_sidebar]">
				<option value="default"<?php if ( isset( $gp_term_meta['left_sidebar'] ) && $gp_term_meta['left_sidebar'] == 'default' ) { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
				<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $gp_sidebar ) { ?>
					 <option value="<?php echo sanitize_title( $gp_sidebar['id'] ); ?>"<?php if ( isset( $gp_term_meta['left_sidebar'] ) && $gp_term_meta['left_sidebar'] == $gp_sidebar['id'] ) { ?>selected="selected"<?php } ?>>
						<?php echo ucwords( $gp_sidebar['name'] ); ?>
					 </option>
				<?php } ?>
			</select>
			<p class="description"><?php esc_html_e( 'The sidebar to display.', 'socialize' ); ?></p>
		</div>
	
		<div class="form-field">
			<label for="category-right-sidebar"><?php esc_html_e( 'Right Sidebar', 'socialize' ); ?></label>
			<?php $gp_term_meta['right_sidebar'] = isset( $gp_term_meta['right_sidebar'] ) ? $gp_term_meta['right_sidebar'] : ''; ?>
			<select id="gp_term_meta" name="gp_term_meta[right_sidebar]">
				<option value="default"<?php if ( isset( $gp_term_meta['right_sidebar'] ) && $gp_term_meta['right_sidebar'] == 'default' ) { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
				<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $gp_sidebar ) { ?>
					 <option value="<?php echo sanitize_title( $gp_sidebar['id'] ); ?>"<?php if ( isset( $gp_term_meta['right_sidebar'] ) && $gp_term_meta['right_sidebar'] == $gp_sidebar['id'] ) { ?>selected="selected"<?php } ?>>
						<?php echo ucwords( $gp_sidebar['name'] ); ?>
					 </option>
				<?php } ?>
			</select>
			<p class="description"><?php esc_html_e( 'The sidebar to display.', 'socialize' ); ?></p>
		</div>
		
		<div class="form-field">
			<label for="category-format"><?php esc_html_e( 'Format', 'socialize' ); ?></label>
			<select id="gp_term_meta" name="gp_term_meta[format]">
				<option value="default"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>	
				<option value="gp-blog-large"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-large' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Large', 'socialize' ); ?></option>	
				<option value="gp-blog-standard"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-standard' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Standard', 'socialize' ); ?></option>
				<option value="gp-blog-columns-1"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-1' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '1 Column', 'socialize' ); ?></option>
				<option value="gp-blog-columns-2"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-2' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '2 Columns', 'socialize' ); ?></option>
				<option value="gp-blog-columns-3"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-3' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '3 Columns', 'socialize' ); ?></option>			
				<option value="gp-blog-columns-4"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-4' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '4 Columns', 'socialize' ); ?></option>			
				<option value="gp-blog-columns-5"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-5' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '5 Columns', 'socialize' ); ?></option>			
				<option value="gp-blog-columns-6"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-6' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '6 Columns', 'socialize' ); ?></option>			
				<option value="gp-blog-masonry"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-masonry' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Masonry', 'socialize' ); ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'The format to display the items in.', 'socialize' ); ?></p>
		</div>	
		
	<?php }
}
add_action( 'category_add_form_fields', 'socialize_add_tax_fields' );		

if ( ! function_exists( 'socialize_edit_tax_fields' ) ) {
	function socialize_edit_tax_fields( $gp_tag ) {

		$gp_term_id = $gp_tag->term_id;
		$gp_term_meta = get_option( "taxonomy_$gp_term_id" ); ?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="category-page-header"><?php esc_html_e( 'Page Header', 'socialize' ); ?></label>
			</th>
			<td>
				<select id="gp_term_meta" name="gp_term_meta[page_header]">
					<option value="default"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<option value="gp-standard-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-standard-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Standard', 'socialize' ); ?></option>
					<option value="gp-large-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-large-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Large', 'socialize' ); ?></option>
					<option value="gp-fullwidth-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-fullwidth-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
					<option value="gp-full-page-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-full-page-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Full Page', 'socialize' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Choose the page header on the page.', 'socialize' ); ?></p>
			</td>
		</tr>
					
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="category-bg-image"><?php esc_html_e( 'Page Header Background', 'socialize' ); ?></label>
			</th>
			<td>
				<input type="text" id="gp_term_meta" name="gp_term_meta[bg_image]" value="<?php echo esc_url( $gp_term_meta['bg_image'] ? $gp_term_meta['bg_image'] : '' ); ?>" />
				<p class="description"><?php wp_kses( _e( 'The background of the page header. <strong>Enter an image URL that must be uploaded to the Media Library.</strong>', 'socialize' ), array( 'strong' => array(), ) ); ?></p>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="category-layout"><?php esc_html_e( 'Page Layout', 'socialize' ); ?></label>
			</th>
			<td>
				<select id="gp_term_meta" name="gp_term_meta[layout]">
					<option value="default"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<option value="gp-left-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-left-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Left Sidebar', 'socialize' ); ?></option>
					<option value="gp-right-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-right-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Right Sidebar', 'socialize' ); ?></option>
					<option value="gp-both-sidebars"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-both-sidebars' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Both Sidebars', 'socialize' ); ?></option>
					<option value="gp-no-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-no-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'No Sidebar', 'socialize' ); ?></option>
					<option value="gp-fullwidth"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-fullwidth' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'The layout of the page.', 'socialize' ); ?></p>
			</td>
		</tr>
	
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="category-left-sidebar"><?php esc_html_e( 'Left Sidebar', 'socialize' ); ?></label>
			</th>
			<td>
				<?php $gp_term_meta['left_sidebar'] = isset( $gp_term_meta['left_sidebar'] ) ? $gp_term_meta['left_sidebar'] : ''; ?>
				<select id="gp_term_meta" name="gp_term_meta[left_sidebar]">
					<option value="default"<?php if ( isset( $gp_term_meta['left_sidebar'] ) && $gp_term_meta['left_sidebar'] == 'default' ) { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $gp_sidebar ) { ?>
						 <option value="<?php echo sanitize_title( $gp_sidebar['id'] ); ?>"<?php if ( isset( $gp_term_meta['left_sidebar'] ) && $gp_term_meta['left_sidebar'] == $gp_sidebar['id'] ) { ?>selected="selected"<?php } ?>>
							<?php echo ucwords( $gp_sidebar['name'] ); ?>
						 </option>
					<?php } ?>
				</select>
				<p class="description"><?php esc_html_e( 'The sidebar to display.', 'socialize' ); ?></p>
			</td>
		</tr>
	
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="category-right-sidebar"><?php esc_html_e( 'Right Sidebar', 'socialize' ); ?></label>
			</th>
			<td>
				<?php $gp_term_meta['right_sidebar'] = isset( $gp_term_meta['right_sidebar'] ) ? $gp_term_meta['right_sidebar'] : ''; ?>
				<select id="gp_term_meta" name="gp_term_meta[right_sidebar]">
					<option value="default"<?php if ( isset( $gp_term_meta['right_sidebar'] ) && $gp_term_meta['right_sidebar'] == 'default' ) { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $gp_sidebar ) { ?>
						 <option value="<?php echo sanitize_title( $gp_sidebar['id'] ); ?>"<?php if ( isset( $gp_term_meta['right_sidebar'] ) && $gp_term_meta['right_sidebar'] == $gp_sidebar['id'] ) { ?>selected="selected"<?php } ?>>
							<?php echo ucwords( $gp_sidebar['name'] ); ?>
						 </option>
					<?php } ?>
				</select>
				<p class="description"><?php esc_html_e( 'The sidebar to display.', 'socialize' ); ?></p>
			</td>
		</tr>
		
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="category-format"><?php esc_html_e( 'Format', 'socialize' ); ?></label>
			</th>
			<td>
				<select id="gp_term_meta" name="gp_term_meta[format]">
					<option value="default"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<option value="gp-blog-large"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-large' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Large', 'socialize' ); ?></option>
					<option value="gp-blog-standard"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-standard' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Standard', 'socialize' ); ?></option>
					<option value="gp-blog-columns-1"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-1' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '1 Column', 'socialize' ); ?></option>
					<option value="gp-blog-columns-2"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-2' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '2 Columns', 'socialize' ); ?></option>
					<option value="gp-blog-columns-3"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-3' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '3 Columns', 'socialize' ); ?></option>			
					<option value="gp-blog-columns-4"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-4' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '4 Columns', 'socialize' ); ?></option>			
					<option value="gp-blog-columns-5"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-5' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '5 Columns', 'socialize' ); ?></option>			
					<option value="gp-blog-columns-6"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-columns-6' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '6 Columns', 'socialize' ); ?></option>			
					<option value="gp-blog-masonry"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-blog-masonry' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Masonry', 'socialize' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'The format to display the items in.', 'socialize' ); ?></p>
			</td>
		</tr>
			
	<?php }
}
add_action( 'edit_category_form_fields', 'socialize_edit_tax_fields' );	
	
if ( ! function_exists( 'socialize_save_tax_fields' ) ) {	
	function socialize_save_tax_fields( $gp_term_id ) {
		if ( isset( $_POST['gp_term_meta'] ) ) {
			$gp_term_id = $gp_term_id;
			$gp_term_meta = get_option( "taxonomy_$gp_term_id" );
			$gp_cat_keys = array_keys( $_POST['gp_term_meta'] );
				foreach ( $gp_cat_keys as $gp_key ) {
				if ( isset( $_POST['gp_term_meta'][$gp_key] ) ) {
					$gp_term_meta[$gp_key] = $_POST['gp_term_meta'][$gp_key];
				}
			}
			update_option( "taxonomy_$gp_term_id", $gp_term_meta );
		}
	}			
}
add_action( 'created_category', 'socialize_save_tax_fields' );		
add_action( 'edit_category', 'socialize_save_tax_fields' );
		

/////////////////////////////////////// Redirect wp-login.php to login form ///////////////////////////////////////	

if ( ! function_exists( 'socialize_login_redirect' ) ) {
	function socialize_login_redirect() {
		global $socialize, $pagenow;
		if ( 'wp-login.php' == $pagenow && ( isset ( $socialize['popup_box'] ) && $socialize['popup_box'] == 'enabled' ) ) {
			if ( isset( $_POST['wp-submit'] ) OR ( isset( $_GET['action'] ) && $_GET['action'] == 'logout' ) OR ( isset( $_GET['checkemail'] ) && $_GET['checkemail'] == 'confirm' ) OR ( isset( $_GET['checkemail'] ) && $_GET['checkemail'] == 'registered' ) OR ( isset( $_GET['action'] ) && $_GET['action'] == 'postpass' ) OR ( isset( $_GET['reauth'] ) && $_GET['reauth'] == '1' ) OR ( isset( $_GET['action'] ) && $_GET['action'] == 'register' ) OR ( isset( $_GET['action'] ) && $_GET['action'] == 'lostpassword' ) ) return;
			else wp_redirect( esc_url( home_url( '#login/' ) ) );
			exit();
		}
	}
}
add_action( 'init', 'socialize_login_redirect' );


/////////////////////////////////////// Remove hentry tag ///////////////////////////////////////	

if ( ! function_exists( 'socialize_remove_hentry' ) ) {
	function socialize_remove_hentry( $gp_classes ) {
		$gp_classes = array_diff( $gp_classes, array( 'hentry' ) );
		return $gp_classes;
	}
}
add_filter( 'post_class', 'socialize_remove_hentry' );


/////////////////////////////////////// Add user profile fields ///////////////////////////////////////	

if ( ! function_exists( 'socialize_custom_profile_methods' ) ) {
	function socialize_custom_profile_methods( $gp_profile_fields ) {
		$gp_profile_fields['twitter'] = esc_html__( 'Twitter URL', 'socialize' );
		$gp_profile_fields['facebook'] = esc_html__( 'Facebook URL', 'socialize' );
		$gp_profile_fields['googleplus'] = esc_html__( 'Google+ URL', 'socialize' );
		$gp_profile_fields['pinterest'] = esc_html__( 'Pinterest URL', 'socialize' );
		$gp_profile_fields['youtube'] = esc_html__( 'YouTube URL', 'socialize' );
		$gp_profile_fields['vimeo'] = esc_html__( 'Vimeo URL', 'socialize' );
		$gp_profile_fields['flickr'] = esc_html__( 'Flickr URL', 'socialize' );
		$gp_profile_fields['linkedin'] = esc_html__( 'LinkedIn URL', 'socialize' );
		$gp_profile_fields['instagram'] = esc_html__( 'Instagram URL', 'socialize' );
		return $gp_profile_fields;
	}
}
add_filter( 'user_contactmethods', 'socialize_custom_profile_methods' );


/////////////////////////////////////// Add lightbox class to image links ///////////////////////////////////////	

if ( ! function_exists( 'socialize_lightbox_image_link' ) ) {
	function socialize_lightbox_image_link( $gp_content ) {	
		global $socialize, $post;
		if ( isset( $socialize['lightbox'] ) && $socialize['lightbox'] != 'disabled' ) {
			if ( $socialize['lightbox'] == 'group_images' ) {
				$gp_group = '[image-' . $post->ID . ']';
			} else {
				$gp_group = '';
			}
			$gp_pattern = "/<a(.*?)href=('|\")(.*?).(jpg|jpeg|png|gif|bmp|ico)('|\")(.*?)>/i";
			preg_match_all( $gp_pattern, $gp_content, $gp_matches, PREG_SET_ORDER );
			foreach ( $gp_matches as $gp_val ) {
				$gp_pattern = '<a' . $gp_val[1] . 'href=' . $gp_val[2] . $gp_val[3] . '.' . $gp_val[4] . $gp_val[5] . $gp_val[6] . '>';
				$gp_replacement = '<a' . $gp_val[1] . 'href=' . $gp_val[2] . $gp_val[3] . '.' . $gp_val[4] . $gp_val[5] . ' data-rel="prettyPhoto' . $gp_group . '"' . $gp_val[6] . '>';
				$gp_content = str_replace( $gp_pattern, $gp_replacement, $gp_content );			
			}
			return $gp_content;
		} else {
			return $gp_content;
		}
	}	
}
add_filter( 'the_content', 'socialize_lightbox_image_link' );	
add_filter( 'wp_get_attachment_link', 'socialize_lightbox_image_link' );

	
/////////////////////////////////////// Visual Composer ///////////////////////////////////////	

if ( function_exists( 'vc_set_as_theme' ) ) {
	function socialize_vc_functions() {
		vc_set_as_theme( $disable_updater = true ); // Disable auto updates
		vc_set_shortcodes_templates_dir( socialize_vc ); // Set templates directory
	}
	add_action( 'vc_before_init', 'socialize_vc_functions' );
}

					
/////////////////////////////////////// TMG Plugin Activation ///////////////////////////////////////	

if ( version_compare( phpversion(), '5.2.4', '>=' ) ) {
	require_once( socialize_inc . 'class-tgm-plugin-activation.php' );
} else {
	require_once( socialize_inc . 'class-tgm-plugin-activation-2.4.2.php' );
}

if ( ! function_exists( 'socialize_register_required_plugins' ) ) {
	
	function socialize_register_required_plugins() {

		$gp_plugins = array(

			array(
				'name'               => 'Socialize Plugin',
				'slug'               => 'socialize-plugin',
				'source'             => socialize_plugins . 'socialize-plugin.zip',
				'required'           => true,
				'version'            => '1.3',
				'force_activation'   => true,
				'force_deactivation' => false,
			),

			array(
				'name'               => 'WPBakery Visual Composer',
				'slug'               => 'js_composer',
				'source'             => socialize_plugins . 'js_composer.zip',
				'required'           => true,
				'version'            => '4.8.1',
				'force_activation'	 => true,
				'force_deactivation' => false,
			),

			array(
				'name'               => 'Visual Composer Widgets',
				'slug'               => 'vc-widget-addon',
				'source'             => socialize_plugins . 'vc-widget-addon.zip',
				'required'           => true,
				'version'            => '1.0.7',
				'force_activation'	 => true,
				'force_deactivation' => false,
			),
			
			array(
				'name'   		     => 'Theia Sticky Sidebar',
				'slug'   		     => 'theia-sticky-sidebar',
				'source'   		     => socialize_plugins . 'theia-sticky-sidebar.zip',
				'required'   		 => true,
				'version'   		 => '1.3.1',
				'force_activation'	 => true,
				'force_deactivation' => false,
			),

			array(
				'name'      => 'BuddyPress',
				'slug'      => 'buddypress',
				'required' 	=> false,
			),
			
			array(
				'name'      => 'bbPress',
				'slug'      => 'bbpress',
				'required' 	=> false,
			),

			array(
				'name'      => 'BuddyPress Global Search',
				'slug'      => 'buddypress-global-search',
				'required' 	=> false,
			),
						
			array(
				'name'      => 'The Events Calendar',
				'slug'      => 'the-events-calendar	',
				'required' 	=> false,
			),
												
			array(
				'name'      => 'Contact Form 7',
				'slug'      => 'contact-form-7',
				'required' 	=> false,
			),

			array(
				'name'      => 'WordPress Social Login',
				'slug'      => 'wordpress-social-login',
				'required' 	=> false,
			),

			array(
				'name'      => 'Captcha',
				'slug'      => 'captcha',
				'required' 	=> false,
			),
			
			array(
				'name'      => 'Post Views Counters',
				'slug'      => 'post-views-counter',
				'required' 	=> false,
			),
			
			array(
				'name'      => 'Yoast SEO',
				'slug'      => 'wordpress-seo',
				'required' 	=> false,
			),
																							
		);

		$gp_config = array(
			'default_path' => '',                     // Default absolute path to pre-packaged plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                   // Show admin notices or not.
			'dismissable'  => true,                   // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                     // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                  // Automatically activate plugins after installation or not.
			'message'      => '',                     // Message to output right before the plugins table.
		);
 
		tgmpa( $gp_plugins, $gp_config );

	}
	
}

add_action( 'tgmpa_register', 'socialize_register_required_plugins' );

?>