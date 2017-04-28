<?php 

if ( ! class_exists( 'Socialize_Slides' ) ) {

	class Socialize_Slides {

		public function __construct() {
			add_action( 'init', array( &$this, 'socialize_post_type_slides' ), 1 );
			add_action( 'manage_posts_custom_column',  array( &$this, 'socialize_slides_custom_columns' ) );
		}

		public function socialize_post_type_slides() {

			/*--------------------------------------------------------------
			Slide Post Type
			--------------------------------------------------------------*/	
	
			register_post_type( 'gp_slide', array( 
				'labels' => array( 
					'name' => esc_html__( 'Slides', 'socialize' ),
					'singular_name' => esc_html__( 'Slide', 'socialize' ),
					'menu_name' => esc_html__( 'Slides', 'socialize' ),
					'all_items' => esc_html__( 'All Slides', 'socialize' ),
					'add_new' => _x( 'Add New', 'portfolio', 'socialize' ),
					'add_new_item' => esc_html__( 'Add New Slide', 'socialize' ),
					'edit_item' => esc_html__( 'Edit Slide', 'socialize' ),
					'new_item' => esc_html__( 'New Slide', 'socialize' ),
					'view_item' => esc_html__( 'View Slide', 'socialize' ),
					'search_items' => esc_html__( 'Search Slides', 'socialize' ),
					'not_found' => esc_html__( 'No slides found', 'socialize' ),
					'not_found_in_trash' => esc_html__( 'No slides found in Trash', 'socialize' ),
				 ),
				'public' => true,
				'exclude_from_search' => false,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'_builtin' => false,
				'_edit_link' => 'post.php?post=%d',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array( 'slug' => 'slide' ),
				'menu_position' => 20,
				'with_front' => true,
				'has_archive' => 'gp_slides',
				'supports' => array( 'title', 'thumbnail' )
			 ) );
	
	
			/*--------------------------------------------------------------
			Slide Categories Taxonomy
			--------------------------------------------------------------*/
			
			register_taxonomy( 'gp_slides', 'gp_slide', array( 
				'labels' => array( 
					'name' => esc_html__( 'Slide Categories', 'socialize' ),
					'singular_name' => esc_html__( 'Slide Category', 'socialize' ),
					'all_items' => esc_html__( 'All Slide Categories', 'socialize' ),
					'add_new' => _x( 'Add New', 'portfolio', 'socialize' ),
					'add_new_item' => esc_html__( 'Add New Slide Category', 'socialize' ),
					'edit_item' => esc_html__( 'Edit Slide Category', 'socialize' ),
					'new_item' => esc_html__( 'New Slide Category', 'socialize' ),
					'view_item' => esc_html__( 'View Slide Category', 'socialize' ),
					'search_items' => esc_html__( 'Search Slide Categories', 'socialize' ),
					'menu_name' => esc_html__( 'Slide Categories', 'socialize' )
				 ),
				'show_in_nav_menus' => true,
				'hierarchical' => true,
				'rewrite' => array( 'slug' => 'slides' )
			 ) );


			register_taxonomy_for_object_type( 'gp_slides', 'gp_slide' );


			/*--------------------------------------------------------------
			Slide Admin Columns
			--------------------------------------------------------------*/

			if ( ! function_exists( 'socialize_slide_edit_columns' ) ) { 
				function socialize_slides_edit_columns( $gp_columns ) {
					$gp_columns = array( 
						'cb'               => '<input type="checkbox" />',
						'title'            => esc_html__( 'Title', 'socialize' ),	
						'slide_categories' => esc_html__( 'Categories', 'socialize' ),
						'slide_image'      => esc_html__( 'Image', 'socialize' ),				
						'date'             => esc_html__( 'Date', 'socialize' )
					 );
					return $gp_columns;
				}	
			}
			add_filter( 'manage_edit-socialize_slide_columns', 'socialize_slides_edit_columns' );
		
		}

		public function socialize_slides_custom_columns( $gp_column ) {
			switch ( $gp_column ) {
				case 'slide_categories':
					echo get_the_term_list( get_the_ID(), 'gp_slides', '', ', ', '' );
				break;
				case 'slide_image':
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( array( 50, 50 ) );
					}
				break;					
			}
		}
		
		
	}

}

?>