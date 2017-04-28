<?php

if ( ! function_exists( 'socialize_page_settings' ) ) {
	function socialize_page_settings() {

		global $socialize, $post;
		$socialize_global = get_option( 'socialize' ); 


		/*--------------------------------------------------------------
		WooCommerce Shop Page
		--------------------------------------------------------------*/

		if ( function_exists( 'is_woocommerce' ) && ( is_shop() OR is_product_category() OR is_product_tag() ) ) {

			$gp_post_id = get_option( 'woocommerce_shop_page_id' ); // Get WooCommerce shop page ID	

			$GLOBALS['socialize_page_header'] = get_post_meta( $gp_post_id, 'page_page_header', true ) ? get_post_meta( $gp_post_id, 'page_page_header', true ) : $socialize_global['shop_page_header'];
			
			$GLOBALS['socialize_page_header_bg'] = get_post_meta( $gp_post_id, 'page_page_header_bg', true );
			
			$GLOBALS['socialize_page_header_text'] = get_post_meta( $gp_post_id, 'page_page_header', true ) ? get_post_meta( 
			$gp_post_id, 'page_page_header', true ) : $socialize_global['shop_page_header_text'];
			
			$GLOBALS['socialize_teaser_video_bg'] = get_post_meta( $gp_post_id, 'page_page_header_teaser_video_bg', true );
			
			$GLOBALS['socialize_full_video_bg'] = get_post_meta( $gp_post_id, 'page_page_header_full_video_bg', true );		
			
			$GLOBALS['socialize_title'] = get_post_meta( $gp_post_id, 'page_title', true );
			
			$GLOBALS['socialize_custom_title'] = get_post_meta( $gp_post_id, 'page_custom_title', true );
			
			$GLOBALS['socialize_subtitle'] = get_post_meta( $gp_post_id, 'page_subtitle', true );
			
			$GLOBALS['socialize_layout'] = get_post_meta( $gp_post_id, 'page_layout', true ) ? get_post_meta( $gp_post_id, 'page_layout', true ) : $socialize_global['shop_layout'];
			
			$GLOBALS['socialize_left_sidebar'] = get_post_meta( $gp_post_id, 'page_left_sidebar', true ) ? get_post_meta( $gp_post_id, 'page_left_sidebar', true ) : $socialize_global['shop_left_sidebar'];
			
			$GLOBALS['socialize_right_sidebar'] = get_post_meta( $gp_post_id, 'page_right_sidebar', true ) ? get_post_meta( $gp_post_id, 'page_right_sidebar', true ) : $socialize_global['shop_right_sidebar'];
	
		
		/*--------------------------------------------------------------
		WooCommerce Products
		--------------------------------------------------------------*/

		} elseif ( function_exists( 'is_woocommerce' ) && is_singular( 'product' ) ) {

			$GLOBALS['socialize_page_header'] = 'gp-standard-page-header';
			
			$GLOBALS['socialize_title'] = 'enabled';	
			
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'product_layout' ) == 'default' ? 
			$socialize_global['product_layout'] : redux_post_meta( 'socialize', get_the_ID(), 'product_layout' );
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'product_left_sidebar' ) == '' ? 
			$socialize_global['product_left_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'product_left_sidebar' );
			
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'product_right_sidebar' ) == '' ? 
			$socialize_global['product_right_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'product_right_sidebar' );
	

		/*--------------------------------------------------------------
		BuddyPress
		--------------------------------------------------------------*/

		} elseif ( function_exists( 'bp_is_active' ) && ! bp_is_blog_page() ) {

			$bp_pages = get_option( 'bp-pages' );

			if ( bp_is_activity_component() ) {
				$gp_page_id = $bp_pages['activity'];
			} elseif ( bp_is_groups_component() ) {
				$gp_page_id = $bp_pages['groups'];
			} elseif ( bp_is_members_component() ) {
				$gp_page_id = $bp_pages['members'];
			} else {
				$gp_page_id = null;
			}
			
			$GLOBALS['socialize_page_header'] = get_post_meta( $gp_page_id, 'page_page_header', true ) ? get_post_meta( $gp_page_id, 'page_page_header', true ) : $socialize_global['bp_page_header'];
			
			$GLOBALS['socialize_page_header_text'] = get_post_meta( $gp_page_id, 'page_page_header_text', true ) ? get_post_meta( $gp_page_id, 'page_page_header_text', true ) : $socialize_global['bp_page_header_text'];
			
			$GLOBALS['socialize_layout'] = get_post_meta( $gp_page_id, 'page_layout', true ) ? get_post_meta( $gp_page_id, 'page_layout', true ) : $socialize_global['bp_layout'];
			
			$GLOBALS['socialize_left_sidebar'] = get_post_meta( $gp_page_id, 'page_left_sidebar', true ) ? get_post_meta( $gp_page_id, 'page_left_sidebar', true ) : $socialize_global['bp_left_sidebar'];
			
			$GLOBALS['socialize_right_sidebar'] = get_post_meta( $gp_page_id, 'page_right_sidebar', true ) ? get_post_meta( $gp_page_id, 'page_right_sidebar', true ) : $socialize_global['bp_right_sidebar'];
			

		/*--------------------------------------------------------------
		bbPress
		--------------------------------------------------------------*/

		} elseif ( function_exists( 'is_bbpress' ) && is_bbpress() ) {

			if ( bbp_is_single_topic() OR bbp_is_single_reply() ) {
				$gp_forum_id = bbp_get_topic_forum_id();
			} else {
				$gp_forum_id = get_the_ID();
			}
				
			if ( bbp_is_topic_tag() OR bbp_is_topic_tag_edit() ) {
				
				$GLOBALS['socialize_page_header'] = $socialize_global['bbpress_page_header'];
				
				$GLOBALS['socialize_page_header_bg'] = $socialize_global['bbpress_page_header_bg'];
				
				$GLOBALS['socialize_page_header_text'] = $socialize_global['bbpress_page_header_text'];
				
				$GLOBALS['socialize_layout'] = $socialize_global['bbpress_layout'];
				
				$GLOBALS['socialize_left_sidebar'] = $socialize_global['bbpress_left_sidebar'];
				
				$GLOBALS['socialize_right_sidebar'] = $socialize_global['bbpress_right_sidebar'];
			
			} else {
				
				$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_page_header' ) == '' ? 
				$socialize_global['bbpress_page_header'] : redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_page_header' );
				
				$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_page_header_bg' ) == '' ? 
				$socialize_global['bbpress_page_header_bg'] : redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_page_header_bg' );
				
				$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_page_header_text' ) == '' ? $socialize_global['bbpress_page_header_text'] : redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_page_header_text' );
								
				$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_layout' ) == 'default' ? $socialize_global['bbpress_layout'] : redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_layout' );
				
				$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_left_sidebar' ) == '' ? $socialize_global['bbpress_left_sidebar'] : redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_left_sidebar' );	
				
				$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_right_sidebar' ) == '' ? $socialize_global['bbpress_right_sidebar'] : redux_post_meta( 'socialize', $gp_forum_id, 'bbpress_right_sidebar' );			
			
			}		


		/*--------------------------------------------------------------
		Events Calendar
		--------------------------------------------------------------*/

		} elseif ( class_exists( 'Tribe__Events__Main' ) && is_post_type_archive( 'tribe_events' ) ) {

			$GLOBALS['socialize_page_header'] = $socialize_global['events_page_header'];		
			
			$GLOBALS['socialize_page_header_bg'] = $socialize_global['events_page_header_bg'];		
			
			$GLOBALS['socialize_page_header_text'] = $socialize_global['events_page_header_text'];
			
			$GLOBALS['socialize_title'] = 'disabled';
			
			$GLOBALS['socialize_layout'] = $socialize_global['events_layout'];
			
			$GLOBALS['socialize_left_sidebar'] = $socialize_global['events_left_sidebar'];	
			
			$GLOBALS['socialize_right_sidebar'] = $socialize_global['events_right_sidebar'];			


		/*--------------------------------------------------------------
		Events Posts
		--------------------------------------------------------------*/

		} elseif ( class_exists( 'Tribe__Events__Main' ) && is_singular( 'tribe_events' ) ) {

			$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', get_the_ID(), 'events_post_page_header' ) == 'default' ? $socialize_global['events_post_page_header'] : redux_post_meta( 'socialize', get_the_ID(), 'events_post_page_header' );

			$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'events_post_page_header_bg' ) ? $socialize_global['events_page_header_bg'] : redux_post_meta( 'socialize', get_the_ID(), 'events_post_page_header_bg' );
									
			$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', get_the_ID(), 'events_post_page_header_text' ) == '' ? $socialize_global['events_post_page_header_text'] : redux_post_meta( 'socialize', get_the_ID(), 'events_post_page_header_text' );
			
			$GLOBALS['socialize_title'] = 'disabled';
			
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'events_post_layout' ) == 'default' ? $socialize_global['events_post_layout'] : redux_post_meta( 'socialize', get_the_ID(), 'events_post_layout' );
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'events_post_left_sidebar' ) == '' ? $socialize_global['events_post_left_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'events_post_left_sidebar' );
			
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'events_post_right_sidebar' ) == '' ? $socialize_global['events_post_right_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'events_post_right_sidebar' );
			

		/*--------------------------------------------------------------
		Portfolio Categories
		--------------------------------------------------------------*/

		} elseif ( is_post_type_archive( 'gp_portfolio_item' ) OR is_tax( 'gp_portfolios' ) )  {

			// Get category option
			$term_id = get_queried_object()->term_id;
			$gp_term_data = get_option( "taxonomy_$term_id" );
	
			$GLOBALS['socialize_page_header'] = $socialize_global['portfolio_cat_page_header'] ? $socialize_global['portfolio_cat_page_header'] : '';
			
			$GLOBALS['socialize_page_header_bg'] = $gp_term_data['bg_image'];
			
			$GLOBALS['socialize_page_header_text'] =  $socialize_global['portfolio_cat_page_header_text'];
						
			$GLOBALS['socialize_title'] = 'enabled';	
			
			$GLOBALS['socialize_layout'] = $socialize_global['portfolio_cat_layout'] ? $socialize_global['portfolio_cat_layout'] : '';
			
			$GLOBALS['socialize_left_sidebar'] = ! isset( $gp_term_data['left_sidebar'] ) || $gp_term_data['left_sidebar'] == 'default' ? 
			$socialize_global['portfolio_cat_left_sidebar'] : $gp_term_data['left_sidebar']; 
			
			$GLOBALS['socialize_right_sidebar'] = ! isset( $gp_term_data['right_sidebar'] ) || $gp_term_data['right_sidebar'] == 'default' ? 
			$socialize_global['portfolio_cat_right_sidebar'] : $gp_term_data['right_sidebar'];
			
			$GLOBALS['socialize_format'] = $socialize_global['portfolio_cat_format'] ? $socialize_global['portfolio_cat_format'] : '';
			
			$GLOBALS['socialize_orderby'] = $socialize_global['portfolio_cat_orderby'] ? $socialize_global['portfolio_cat_orderby'] : '';
			
			$GLOBALS['socialize_date_posted'] = $socialize_global['portfolio_cat_date_posted'] ? $socialize_global['portfolio_cat_date_posted'] : '';
			
			$GLOBALS['socialize_date_modified'] = $socialize_global['portfolio_cat_date_modified'] ? $socialize_global['portfolio_cat_date_modified'] : '';
			
			$GLOBALS['socialize_filter'] = $socialize_global['portfolio_cat_filter'] ? $socialize_global['portfolio_cat_filter'] : '';
			
			$GLOBALS['socialize_per_page'] = $socialize_global['portfolio_cat_per_page'] ? $socialize_global['portfolio_cat_per_page'] : '';
			
			$GLOBALS['socialize_page_numbers'] = 'enabled';


		/*--------------------------------------------------------------
		Portfolio Page Template
		--------------------------------------------------------------*/

		} elseif ( is_page_template( 'portfolio-template.php' ) )  {

			$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header' ) == 'default' ? $socialize_global['portfolio_template_page_header'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header' );

			$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_bg' ) : '';

			$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_text' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_text' ) : '';			
						
			$GLOBALS['socialize_teaser_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_teaser_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_teaser_video_bg' ) : '';
			
			$GLOBALS['socialize_full_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_full_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_page_header_full_video_bg' ) : '';
				
			$GLOBALS['socialize_title'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_title' ) : '';	
		
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_layout' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_layout' ) : '';
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_left_sidebar' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_left_sidebar' ) : '';
			
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_right_sidebar' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_right_sidebar' ) : '';
			
			$GLOBALS['socialize_format'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_format' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_format' ) : '';
			
			$GLOBALS['socialize_filter'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_filter' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_filter' ) : '';
					
		
		/*--------------------------------------------------------------
		Portfolio Items
		--------------------------------------------------------------*/

		} elseif ( is_singular( 'gp_portfolio_item' ) ) {

			$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header' ) == 'default' ? $socialize_global['portfolio_item_page_header'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header' );

			$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_bg' ) : '';	
		
			$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_text' ) == '' ? $socialize_global['portfolio_item_page_header_text'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_text' );
			
			$GLOBALS['socialize_teaser_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_teaser_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_teaser_video_bg' ) : '';
			
			$GLOBALS['socialize_full_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_full_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_page_header_full_video_bg' ) : '';
					
			$GLOBALS['socialize_title'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_title' ) : '';	
			
			$GLOBALS['socialize_custom_title'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_custom_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_custom_title' ) : '';
			
			$GLOBALS['socialize_subtitle'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_subtitle' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_subtitle' ) : '';
			
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_layout' ) == 'default' ? $socialize_global['portfolio_item_layout'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_layout' );
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_left_sidebar' ) == '' ? $socialize_global['portfolio_item_left_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_left_sidebar' );	
			
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_right_sidebar' ) == '' ? $socialize_global['portfolio_item_right_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_right_sidebar' );	
					
			$gp_image = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_image' );
			
			$GLOBALS['socialize_image_width'] = ! empty( $gp_image['width'] ) ? $gp_image['width'] : $socialize_global['portfolio_item_image']['width'];
			
			$GLOBALS['socialize_image_height'] = ! empty( $gp_image['height'] ) ? $gp_image['height'] : $socialize_global['portfolio_item_image']['height'];
			
			$GLOBALS['socialize_hard_crop'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_hard_crop' ) == 'default' ? $socialize_global['portfolio_item_hard_crop'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_hard_crop' );			
			
			$GLOBALS['socialize_type'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_type' ) == 'default' ? $socialize_global['portfolio_item_type'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_type' );
			
			$GLOBALS['socialize_image_size'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_image_size' ) == 'default' ? $socialize_global['portfolio_item_image_size'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_image_size' );
			
			$GLOBALS['socialize_link_target'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_link_target' ) == 'default' ? $socialize_global['portfolio_item_link_target'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_link_target' );
			
			$GLOBALS['socialize_link_text'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_link_text' ) == '' ? $socialize_global['portfolio_item_link_text'] : redux_post_meta( 'socialize', get_the_ID(), 'portfolio_item_link_text' );
			
			$GLOBALS['socialize_meta_author'] = $socialize_global['portfolio_item_meta']['author'];
			
			$GLOBALS['socialize_meta_date'] = $socialize_global['portfolio_item_meta']['date'];
			
			$GLOBALS['socialize_meta_comment_count'] = $socialize_global['portfolio_item_meta']['comment_count'];
			
			$GLOBALS['socialize_meta_views'] = $socialize_global['portfolio_item_meta']['views'];
			
			$GLOBALS['socialize_meta_cats'] = $socialize_global['portfolio_item_meta']['cats'];
			
			$GLOBALS['socialize_meta_tags'] = $socialize_global['portfolio_item_meta']['tags'];	


		/*--------------------------------------------------------------
		Search/Author Results
		--------------------------------------------------------------*/

		} elseif ( is_search() or is_author() ) {
			
			$GLOBALS['socialize_page_header'] = $socialize_global['search_page_header'];
			
			$GLOBALS['socialize_page_header_bg'] = $socialize_global['search_page_header_bg'];

			$GLOBALS['socialize_page_header_text'] = $socialize_global['search_page_header_text'];	
									
			$GLOBALS['socialize_layout'] = $socialize_global['search_layout'];
			
			$GLOBALS['socialize_left_sidebar'] = $socialize_global['search_left_sidebar']; 
			
			$GLOBALS['socialize_right_sidebar'] = $socialize_global['search_right_sidebar']; 
			
			$GLOBALS['socialize_format'] = $socialize_global['search_format'];
			
			$GLOBALS['socialize_orderby'] = $socialize_global['search_orderby'];
			
			$GLOBALS['socialize_date_posted'] = $socialize_global['search_date_posted'];
			
			$GLOBALS['socialize_date_modified'] = $socialize_global['search_date_modified'];
			
			$GLOBALS['socialize_filter'] = $socialize_global['search_filter'];

			$GLOBALS['socialize_filter_date'] = $socialize_global['search_filter_options']['date'];
			
			$GLOBALS['socialize_filter_page_header'] = $socialize_global['search_filter_options']['title'];
			
			$GLOBALS['socialize_filter_comment_count'] = $socialize_global['search_filter_options']['comment_count'];
			
			$GLOBALS['socialize_filter_views'] = $socialize_global['search_filter_options']['views'];
			
			$GLOBALS['socialize_filter_date_posted'] = $socialize_global['search_filter_options']['date_posted'];
			
			$GLOBALS['socialize_filter_date_modified'] = $socialize_global['search_filter_options']['date_modified'];
			
			$GLOBALS['socialize_per_page'] = $socialize_global['search_per_page'];
			
			$GLOBALS['socialize_featured_image'] = $socialize_global['search_featured_image'];
			
			$GLOBALS['socialize_image_width'] = $socialize_global['search_image']['width'];
			
			$GLOBALS['socialize_image_height'] = $socialize_global['search_image']['height'];
			
			$GLOBALS['socialize_hard_crop'] = $socialize_global['search_hard_crop'];
			
			$GLOBALS['socialize_image_alignment'] = $socialize_global['search_image_alignment'];
			
			$GLOBALS['socialize_content_display'] = $socialize_global['search_content_display'];
			
			$GLOBALS['socialize_excerpt_length'] = $socialize_global['search_excerpt_length'];
			
			$GLOBALS['socialize_meta_author'] = $socialize_global['search_meta']['author'];
			
			$GLOBALS['socialize_meta_date'] = $socialize_global['search_meta']['date'];
			
			$GLOBALS['socialize_meta_comment_count'] = $socialize_global['search_meta']['comment_count'];
			
			$GLOBALS['socialize_meta_views'] = $socialize_global['search_meta']['views'];
			
			$GLOBALS['socialize_meta_cats'] = $socialize_global['search_meta']['cats'];
			
			$GLOBALS['socialize_meta_tags'] = $socialize_global['search_meta']['tags'];
			
			$GLOBALS['socialize_read_more_link'] = $socialize_global['search_read_more_link'];
			
			$GLOBALS['socialize_page_numbers'] = 'enabled';
	
	
		/*--------------------------------------------------------------
		Blog Page Template
		--------------------------------------------------------------*/

		} elseif ( is_page_template( 'blog-template.php' ) )  {

			$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header' ) == 'default' ? $socialize_global['blog_template_page_header'] : redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header' );
			
			$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_bg' ) ?  redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_bg' ) : '';
			
			$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_text' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_text' ) : '';
				
			$GLOBALS['socialize_teaser_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_teaser_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_teaser_video_bg' ) : '';				
			
			$GLOBALS['socialize_full_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_full_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header_full_video_bg' ) : '';
				
			$GLOBALS['socialize_title'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_title' ) : '';
			
			$GLOBALS['socialize_custom_title'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_custom_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_custom_title' ) : '';
			
			$GLOBALS['socialize_subtitle'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_subtitle' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_subtitle' ) : '';
		
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_layout' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_layout' ) : '';
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_left_sidebar' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_left_sidebar' ) : '';	
						
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_right_sidebar' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_right_sidebar' ) : '';	


		/*--------------------------------------------------------------
		Homepage Template
		--------------------------------------------------------------*/

		} elseif ( is_page_template( 'homepage-template.php' ) )  {
				
			$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_page_header' ) == 'default' ? $socialize_global['homepage_page_header'] : redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header' );
			
			$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_bg' ) ?  redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_bg' ) : '';
			
			$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_text' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_text' ) : '';
				
			$GLOBALS['socialize_teaser_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_teaser_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_teaser_video_bg' ) : '';				
			
			$GLOBALS['socialize_full_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_full_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_page_header_full_video_bg' ) : '';
				
			$GLOBALS['socialize_title'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_title' ) : '';
			
			$GLOBALS['socialize_custom_title'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_custom_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_custom_title' ) : '';
			
			$GLOBALS['socialize_subtitle'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_subtitle' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_subtitle' ) : '';
		
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_layout' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_layout' ) : '';
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_left_sidebar' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_left_sidebar' ) : '';	
						
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_right_sidebar' ) ? redux_post_meta( 'socialize', get_the_ID(), 'homepage_right_sidebar' ) : '';
						
			$GLOBALS['socialize_content_header'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_content_header' );
			
			$GLOBALS['socialize_content_header_format'] = redux_post_meta( 'socialize', get_the_ID(), 'homepage_content_header_format' );		
						
						
		/*--------------------------------------------------------------
		Other Templates
		--------------------------------------------------------------*/

		} elseif ( is_attachment() OR is_404() OR is_page_template( 'blank-page-template.php' )  ) {

			$GLOBALS['socialize_page_header'] = 'gp-standard-page-header';
			
			$GLOBALS['socialize_layout'] = 'gp-fullwidth';
	
								
		/*--------------------------------------------------------------
		Post Categories, Archives & Tags
		--------------------------------------------------------------*/

		} elseif ( is_home() OR is_archive() ) {

			// Get category option
			if ( is_category() ) {
				$gp_term = get_category( get_query_var( 'cat' ) );
				$gp_term_id = $gp_term->cat_ID;
				$gp_term_data = get_option( "taxonomy_$gp_term_id");		
			} else {
				$gp_term_data = null;
			}
			
			$GLOBALS['socialize_page_header'] = ! isset( $gp_term_data['page_header'] ) || $gp_term_data['page_header'] == 'default' ? $socialize_global['cat_page_header'] : $gp_term_data['page_header'];

			$GLOBALS['socialize_page_header_bg'] = $gp_term_data['bg_image'];
		
			$GLOBALS['socialize_page_header_text'] = $socialize_global['cat_page_header_text'];
			
			$GLOBALS['socialize_layout'] = ! isset( $gp_term_data['layout'] ) || $gp_term_data['layout'] == 'default' ? $socialize_global['cat_layout'] : $gp_term_data['layout'];
			
			$GLOBALS['socialize_left_sidebar'] = ! isset( $gp_term_data['left_sidebar'] ) || $gp_term_data['left_sidebar'] == 'default' ? $socialize_global['cat_left_sidebar'] : $gp_term_data['left_sidebar']; 
						
			$GLOBALS['socialize_right_sidebar'] = ! isset( $gp_term_data['right_sidebar'] ) || $gp_term_data['right_sidebar'] == 'default' ? $socialize_global['cat_right_sidebar'] : $gp_term_data['right_sidebar'];
			
			$GLOBALS['socialize_format'] = ! isset( $gp_term_data['format'] ) || $gp_term_data['format'] == 'default' ? $socialize_global['cat_format'] : $gp_term_data['format'];
			
			$GLOBALS['socialize_filter'] = $socialize_global['cat_filter'];
			
			$GLOBALS['socialize_orderby'] = $socialize_global['cat_orderby'];
			
			$GLOBALS['socialize_date_posted'] = $socialize_global['cat_date_posted'];
			
			$GLOBALS['socialize_date_modified'] = $socialize_global['cat_date_modified'];
			
			$GLOBALS['socialize_filter_date'] = $socialize_global['cat_filter_options']['date'];
			
			$GLOBALS['socialize_filter_page_header'] = $socialize_global['cat_filter_options']['title'];
			
			$GLOBALS['socialize_filter_comment_count'] = $socialize_global['cat_filter_options']['comment_count'];
			
			$GLOBALS['socialize_filter_views'] = $socialize_global['cat_filter_options']['views'];
			
			$GLOBALS['socialize_filter_date_posted'] = $socialize_global['cat_filter_options']['date_posted'];
			
			$GLOBALS['socialize_filter_date_modified'] = $socialize_global['cat_filter_options']['date_modified'];
			
			$GLOBALS['socialize_per_page'] = $socialize_global['cat_per_page'];

			$GLOBALS['socialize_featured_image'] = $socialize_global['cat_featured_image'];
			
			$GLOBALS['socialize_image_width'] = $socialize_global['cat_image']['width'];
			
			$GLOBALS['socialize_image_height'] = $socialize_global['cat_image']['height'];
			
			$GLOBALS['socialize_hard_crop'] = $socialize_global['cat_hard_crop'];
			
			$GLOBALS['socialize_image_alignment'] = $socialize_global['cat_image_alignment'];
			
			$GLOBALS['socialize_content_display'] = $socialize_global['cat_content_display'];
			
			$GLOBALS['socialize_excerpt_length'] = $socialize_global['cat_excerpt_length'];
			
			$GLOBALS['socialize_meta_author'] = $socialize_global['cat_meta']['author'];
			
			$GLOBALS['socialize_meta_date'] = $socialize_global['cat_meta']['date'];
			
			$GLOBALS['socialize_meta_comment_count'] = $socialize_global['cat_meta']['comment_count'];
			
			$GLOBALS['socialize_meta_views'] = $socialize_global['cat_meta']['views'];
			
			$GLOBALS['socialize_meta_cats'] = $socialize_global['cat_meta']['cats'];
			
			$GLOBALS['socialize_meta_tags'] = $socialize_global['cat_meta']['tags'];
			
			$GLOBALS['socialize_read_more_link'] = $socialize_global['cat_read_more_link'];
			
			$GLOBALS['socialize_page_numbers'] = 'enabled';


		/*--------------------------------------------------------------
		Posts
		--------------------------------------------------------------*/

		} elseif ( is_singular( 'post' ) ) {
	
			$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', get_the_ID(), 'post_page_header' ) == 'default' ? $socialize_global['post_page_header'] : redux_post_meta( 'socialize', get_the_ID(), 'post_page_header' );
						
			$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_bg' ) : '';
			
			$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_text' ) == '' ? $socialize_global['post_page_header_text'] : redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_text' );
					
			$GLOBALS['socialize_teaser_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_teaser_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_teaser_video_bg' ) : '';
			
			$GLOBALS['socialize_full_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_full_video_bg' ) ? redux_post_meta( 'socialize', get_the_ID(), 'post_page_header_full_video_bg' ) : '';
			
			$GLOBALS['socialize_title'] = redux_post_meta( 'socialize', get_the_ID(), 'post_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'post_title' ) : '';
			
			$GLOBALS['socialize_custom_title'] = redux_post_meta( 'socialize', get_the_ID(), 'post_custom_title' ) ? redux_post_meta( 'socialize', get_the_ID(), 'post_custom_title' ) : '';
			
			$GLOBALS['socialize_subtitle'] = redux_post_meta( 'socialize', get_the_ID(), 'post_subtitle' ) ? redux_post_meta( 'socialize', get_the_ID(), 'post_subtitle' ) : '';
			
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'post_layout' ) == 'default' ? $socialize_global['post_layout'] : redux_post_meta( 'socialize', get_the_ID(), 'post_layout' );
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'post_left_sidebar' ) == '' ? $socialize_global['post_left_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'post_left_sidebar' );
			
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'post_right_sidebar' ) == '' ? $socialize_global['post_right_sidebar'] : redux_post_meta( 'socialize', get_the_ID(), 'post_right_sidebar' );
			
			$GLOBALS['socialize_featured_image'] = redux_post_meta( 'socialize', get_the_ID(), 'post_featured_image' ) == 'default' ? $socialize_global['post_featured_image'] : redux_post_meta( 'socialize', get_the_ID(), 'post_featured_image' );
			$gp_image = redux_post_meta( 'socialize', get_the_ID(), 'post_image' );
			
			$GLOBALS['socialize_image_width'] = ! empty( $gp_image['width'] ) ? $gp_image['width'] : $socialize_global['post_image']['width'];
			
			$GLOBALS['socialize_image_height'] = ! empty( $gp_image['height'] ) ? $gp_image['height'] : $socialize_global['post_image']['height'];
			
			$GLOBALS['socialize_hard_crop'] = redux_post_meta( 'socialize', get_the_ID(), 'post_hard_crop' ) == 'default' ? $socialize_global['post_hard_crop'] : redux_post_meta( 'socialize', get_the_ID(), 'post_hard_crop' );
			
			$GLOBALS['socialize_image_alignment'] = redux_post_meta( 'socialize', get_the_ID(), 'post_image_alignment' ) == 'default' ? $socialize_global['post_image_alignment'] : redux_post_meta( 'socialize', get_the_ID(), 'post_image_alignment' );
			
			$GLOBALS['socialize_meta_author'] = $socialize_global['post_meta']['author'];
			
			$GLOBALS['socialize_meta_date'] = $socialize_global['post_meta']['date'];
			
			$GLOBALS['socialize_meta_comment_count'] = $socialize_global['post_meta']['comment_count'];
			
			$GLOBALS['socialize_meta_views'] = $socialize_global['post_meta']['views'];
			
			$GLOBALS['socialize_meta_cats'] = $socialize_global['post_meta']['cats'];
			
			$GLOBALS['socialize_meta_tags'] = $socialize_global['post_meta']['tags'];


		/*--------------------------------------------------------------
		Slides
		--------------------------------------------------------------*/

		} elseif ( is_singular( 'gp_slide' ) ) {

			$GLOBALS['socialize_page_header'] = 'gp-standard-page-header';
			
			$GLOBALS['socialize_layout'] = 'gp-no-sidebar';
	
	
		/*--------------------------------------------------------------
		Pages
		--------------------------------------------------------------*/

		} elseif ( is_page() ) {

			$GLOBALS['socialize_page_header'] = redux_post_meta( 'socialize', get_the_ID(), 'page_page_header' ) == 'default' ? $socialize_global['page_page_header'] : redux_post_meta( 'socialize', get_the_ID(), 'page_page_header' );
			
			$GLOBALS['socialize_page_header_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'page_page_header_bg' );
						
			$GLOBALS['socialize_page_header_text'] = redux_post_meta( 'socialize', get_the_ID(), 'page_page_header_text' ) == '' ? $socialize_global['page_page_header_text'] : redux_post_meta( 'socialize', get_the_ID(), 'page_page_header_text' );

			$GLOBALS['socialize_teaser_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'page_page_header_teaser_video_bg' );
			
			$GLOBALS['socialize_full_video_bg'] = redux_post_meta( 'socialize', get_the_ID(), 'page_page_header_full_video_bg' );		
			
			$GLOBALS['socialize_title'] = redux_post_meta( 'socialize', get_the_ID(), 'page_title' );
			
			$GLOBALS['socialize_custom_title'] = redux_post_meta( 'socialize', get_the_ID(), 'page_custom_title' );
			
			$GLOBALS['socialize_subtitle'] = redux_post_meta( 'socialize', get_the_ID(), 'page_subtitle' );
			
			$GLOBALS['socialize_layout'] = redux_post_meta( 'socialize', get_the_ID(), 'page_layout' ) == 'default' ? 
			$socialize_global['page_layout'] : redux_post_meta( 'socialize', get_the_ID(), 'page_layout' );
			
			$GLOBALS['socialize_left_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'page_left_sidebar' ) == '' ? $socialize_global['page_left_sidebar'] : 
			redux_post_meta( 'socialize', get_the_ID(), 'page_left_sidebar' );	
					
			$GLOBALS['socialize_right_sidebar'] = redux_post_meta( 'socialize', get_the_ID(), 'page_right_sidebar' ) == '' ? $socialize_global['page_right_sidebar'] : 
			redux_post_meta( 'socialize', get_the_ID(), 'page_right_sidebar' );
						
			$GLOBALS['socialize_featured_image'] = redux_post_meta( 'socialize', get_the_ID(), 'page_featured_image' ) == 'default' ? 
			$socialize_global['page_featured_image'] : redux_post_meta( 'socialize', get_the_ID(), 'page_featured_image' );
			
			$gp_image = redux_post_meta( 'socialize', get_the_ID(), 'page_image' );
			
			$GLOBALS['socialize_image_width'] = ! empty( $gp_image['width'] ) ? $gp_image['width'] : $socialize_global['page_image']['width'];
			
			$GLOBALS['socialize_image_height'] = ! empty( $gp_image['height'] ) ? $gp_image['height'] : 
			$socialize_global['page_image']['height'];
			
			$GLOBALS['socialize_hard_crop'] = redux_post_meta( 'socialize', get_the_ID(), 'page_hard_crop' ) == 'default' ? 
			$socialize_global['page_hard_crop'] : redux_post_meta( 'socialize', get_the_ID(), 'page_hard_crop' );
			
			$GLOBALS['socialize_image_alignment'] = redux_post_meta( 'socialize', get_the_ID(), 'page_image_alignment' ) == 'default' ? $socialize_global['page_image_alignment'] : redux_post_meta( 'socialize', get_the_ID(), 'page_image_alignment' );


		/*--------------------------------------------------------------
		Custom Post Types
		--------------------------------------------------------------*/

		} else {

			$GLOBALS['socialize_page_header'] = 'gp-standard-page-header';
			
			$GLOBALS['socialize_title'] = 'enabled';
			
			$GLOBALS['socialize_layout'] = 'gp-no-sidebar';

		}

	}
}

?>