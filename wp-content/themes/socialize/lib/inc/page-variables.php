<?php

if ( ! function_exists( 'socialize_query_variables' ) ) {
	function socialize_query_variables() {

		if ( ( ( ! isset( $GLOBALS['socialize_shortcode'] ) && ! isset( $_GET['type'] ) ) OR ( isset( $_GET['type'] ) && $_GET['type'] != 'blog' ) ) && ( ! isset( $GLOBALS['socialize_menu'] ) OR $GLOBALS['socialize_menu'] == null ) ) {

			global $socialize, $post;
			$socialize_global = get_option( 'socialize' ); 


			/*--------------------------------------------------------------
			Portfolio Page Template
			--------------------------------------------------------------*/

			if ( is_page_template( 'portfolio-template.php' ) )  {

				$GLOBALS['socialize_cats'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_cats' ) ? implode( ',', redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_cats' ) ) : '';			
				$GLOBALS['socialize_orderby'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_orderby' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_orderby' ) : '';
				$GLOBALS['socialize_date_posted'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_date_posted' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_date_posted' ) : '';
				$GLOBALS['socialize_date_modified'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_date_modified' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_date_modified' ) : '';
				$GLOBALS['socialize_per_page'] = redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_per_page' ) ? redux_post_meta( 'socialize', get_the_ID(), 'portfolio_template_per_page' ) : '';
				$GLOBALS['socialize_page_numbers'] = 'enabled';
			

			/*--------------------------------------------------------------
			Blog Page Template
			--------------------------------------------------------------*/

			} elseif ( is_page_template( 'blog-template.php' ) )  {

				$GLOBALS['socialize_cats'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_cats' ) ? implode( ',', redux_post_meta( 'socialize', get_the_ID(), 'blog_template_cats' ) ) : '';	
				$GLOBALS['socialize_post_types'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_post_types' ) ? implode( ',', redux_post_meta( 'socialize', get_the_ID(), 'blog_template_post_types' ) ) : '';
				$GLOBALS['socialize_format'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_format' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_format' ) : '';
				$GLOBALS['socialize_orderby'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_orderby' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_orderby' ) : '';				
				$GLOBALS['socialize_date_posted'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_date_posted' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_date_posted' ) : '';	
				$GLOBALS['socialize_date_modified'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_date_modified' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_date_modified' ) : '';
				$GLOBALS['socialize_filter'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_filter' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_filter' ) : '';
				$filter_options = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_filter_options' );		
				$GLOBALS['socialize_filter_cats'] = isset( $filter_options['cats'] ) ? $filter_options['cats'] : '';	
				$GLOBALS['socialize_filter_date'] = isset( $filter_options['date'] ) ? $filter_options['date'] : '';	
				$GLOBALS['socialize_filter_title'] = isset( $filter_options['title'] ) ? $filter_options['title'] : '';	
				$GLOBALS['socialize_filter_comment_count'] = isset( $filter_options['comment_count'] ) ? $filter_options['comment_count'] : '';	
				$GLOBALS['socialize_filter_views'] = isset( $filter_options['views'] ) ? $filter_options['views'] : '';
				$GLOBALS['socialize_filter_date_posted'] = isset( $filter_options['date_posted'] ) ? $filter_options['date_posted'] : '';
				$GLOBALS['socialize_filter_date_modified'] = isset( $filter_options['date_modified'] ) ? $filter_options['date_modified'] : '';
				$GLOBALS['socialize_filter_cats_id'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_filter_cats_id' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_filter_cats_id' ) : '';				
				$GLOBALS['socialize_per_page'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_per_page' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_per_page' ) : '';
				$GLOBALS['socialize_featured_image'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_featured_image' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_featured_image' ) : '';
				$gp_image = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_image' );
				$GLOBALS['socialize_image_width'] = isset( $gp_image['width'] ) ? $gp_image['width'] : '';	
				$GLOBALS['socialize_image_height'] = isset( $gp_image['height'] ) ? $gp_image['height'] : '';	
				$GLOBALS['socialize_hard_crop'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_hard_crop' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_hard_crop' ) : '';	
				$GLOBALS['socialize_image_alignment'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_image_alignment' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_image_alignment' ) : '';
				$GLOBALS['socialize_content_display'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_content_display' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_content_display' ) : '';	
				$GLOBALS['socialize_excerpt_length'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_excerpt_length' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_excerpt_length' ) : 0;	
				$meta = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_meta' );
				$GLOBALS['socialize_meta_author'] = isset( $meta['author'] ) ? $meta['author'] : '';	
				$GLOBALS['socialize_meta_date'] = isset( $meta['date'] ) ? $meta['date'] : '';
				$GLOBALS['socialize_meta_comment_count'] = isset( $meta['comment_count'] ) ? $meta['comment_count'] : '';	
				$GLOBALS['socialize_meta_views'] = isset( $meta['views'] ) ? $meta['views'] : '';
				$GLOBALS['socialize_meta_cats'] = isset( $meta['cats'] ) ? $meta['cats'] : '';
				$GLOBALS['socialize_meta_tags'] = isset( $meta['tags'] ) ? $meta['tags'] : '';
				$GLOBALS['socialize_read_more_link'] = redux_post_meta( 'socialize', get_the_ID(), 'blog_template_read_more_link' ) ? redux_post_meta( 'socialize', get_the_ID(), 'blog_template_read_more_link' ) : '';					
				$GLOBALS['socialize_page_numbers'] = 'enabled';	
				
			}	

		}


		/*--------------------------------------------------------------
		Global values for queries
		--------------------------------------------------------------*/
	
		// Pagination
		if ( get_query_var( 'paged' ) ) {
			$GLOBALS['socialize_paged'] = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$GLOBALS['socialize_paged'] = get_query_var( 'page' );
		} else {
			$GLOBALS['socialize_paged'] = 1;
		}
		
		// Categories							
		if ( ! empty( $GLOBALS['socialize_cats'] ) && preg_match( '/[a-zA-Z\-]+/', $GLOBALS['socialize_cats'] ) ) {
			$GLOBALS['socialize_post_cats'] = array( 'taxonomy' => 'category', 'terms' => explode( ',', $GLOBALS['socialize_cats'] ), 'field' => 'slug' );
			$GLOBALS['socialize_slide_cats'] = array( 'taxonomy' => 'gp_slides', 'terms' => explode( ',', $GLOBALS['socialize_cats'] ), 'field' => 'slug' );
			$GLOBALS['socialize_portfolio_cats'] = array( 'taxonomy' => 'gp_portfolios', 'terms' => explode( ',', $GLOBALS['socialize_cats'] ), 'field' => 'slug' );
		} elseif ( ! empty( $GLOBALS['socialize_cats'] ) ) {
			$GLOBALS['socialize_post_cats'] = array( 'taxonomy' => 'category', 'terms' => explode( ',', $GLOBALS['socialize_cats'] ), 'field' => 'id' );
			$GLOBALS['socialize_slide_cats'] = array( 'taxonomy' => 'gp_slides', 'terms' => explode( ',', $GLOBALS['socialize_cats'] ), 'field' => 'id' );
			$GLOBALS['socialize_portfolio_cats'] = array( 'taxonomy' => 'gp_portfolios', 'terms' => explode( ',', $GLOBALS['socialize_cats'] ), 'field' => 'id' );
		} else {
			$GLOBALS['socialize_post_cats'] = null;
			$GLOBALS['socialize_slide_cats'] = null;
			$GLOBALS['socialize_portfolio_cats'] = null;
		}
		
		// Orderby
		if ( isset( $GLOBALS['socialize_orderby'] ) ) {
				
			if ( $GLOBALS['socialize_orderby'] == 'newest' ) {
				$GLOBALS['socialize_orderby_value'] = 'date';
				$GLOBALS['socialize_order'] = 'desc';
				$GLOBALS['socialize_meta_key'] = '';
			} elseif ( $GLOBALS['socialize_orderby'] == 'oldest' ) {
				$GLOBALS['socialize_orderby_value'] = 'date';
				$GLOBALS['socialize_order'] = 'asc';
				$GLOBALS['socialize_meta_key'] = '';				
			} elseif ( $GLOBALS['socialize_orderby']  == 'title_az' ) {
				$GLOBALS['socialize_orderby_value'] = 'title';
				$GLOBALS['socialize_order'] = 'asc';
				$GLOBALS['socialize_meta_key'] = '';	
			} elseif ( $GLOBALS['socialize_orderby']  == 'title_za' ) {
				$GLOBALS['socialize_orderby_value'] = 'title';
				$GLOBALS['socialize_order'] = 'desc';
				$GLOBALS['socialize_meta_key'] = '';									
			} elseif ( $GLOBALS['socialize_orderby'] == 'comment_count' ) {
				$GLOBALS['socialize_orderby_value'] = 'comment_count';
				$GLOBALS['socialize_order'] = 'desc';
				$GLOBALS['socialize_meta_key'] = '';	
			} elseif ( $GLOBALS['socialize_orderby'] == 'views' ) {
				$GLOBALS['socialize_orderby_value'] = 'post_views';
				$GLOBALS['socialize_order'] = 'desc';
				$GLOBALS['socialize_meta_key'] = '';
			} elseif ( $GLOBALS['socialize_orderby'] == 'menu_order' ) {
				$GLOBALS['socialize_orderby_value'] = 'menu_order';
				$GLOBALS['socialize_order'] = 'asc';
				$GLOBALS['socialize_meta_key'] = '';	
			} elseif ( $GLOBALS['socialize_orderby'] == 'rand' ) {
				$GLOBALS['socialize_orderby_value'] = 'rand';
				$GLOBALS['socialize_order'] = 'asc';
				$GLOBALS['socialize_meta_key'] = '';	
			} else {
				$GLOBALS['socialize_orderby_value'] = '';
				$GLOBALS['socialize_order'] = '';
				$GLOBALS['socialize_meta_key'] = '';	
			}
		}	

		// Date posted
		if ( isset( $GLOBALS['socialize_date_posted'] ) ) {			
			if ( $GLOBALS['socialize_date_posted'] == 'day' ) {
				$GLOBALS['socialize_date_posted_value'] = array(
					'column' => 'post_date_gmt',
					'after' => '1 day ago',
				);	
			} elseif ( $GLOBALS['socialize_date_posted'] == 'week' ) {	
				$GLOBALS['socialize_date_posted_value'] = array(	
					'column' => 'post_date_gmt',
					'after' => '1 week ago',
				);
			} elseif ( $GLOBALS['socialize_date_posted'] == 'month' ) {	
				$GLOBALS['socialize_date_posted_value'] = array(	
					'column' => 'post_date_gmt',
					'after' => '1 month ago',
				);
			} elseif ( $GLOBALS['socialize_date_posted'] == 'year' ) {	
				$GLOBALS['socialize_date_posted_value'] = array(	
					'column' => 'post_date_gmt',
					'after' => '1 year ago',
				);
			} elseif ( $GLOBALS['socialize_date_posted'] == 'all' ) {	
				$GLOBALS['socialize_date_posted_value'] = '';
			} else {
				$GLOBALS['socialize_date_posted_value'] = '';
			}
		}	

		// Date modified
		if ( isset( $GLOBALS['socialize_date_modified'] ) ) {			
			if ( $GLOBALS['socialize_date_modified'] == 'day' ) {
				$GLOBALS['socialize_date_modified_value'] = array(
					'column' => 'post_modified_gmt',
					'after' => '1 day ago',
				);	
			} elseif ( $GLOBALS['socialize_date_modified'] == 'week' ) {	
				$GLOBALS['socialize_date_modified_value'] = array(	
					'column' => 'post_modified_gmt',
					'after' => '1 week ago',
				);
			} elseif ( $GLOBALS['socialize_date_modified'] == 'month' ) {	
				$GLOBALS['socialize_date_modified_value'] = array(	
					'column' => 'post_modified_gmt',
					'after' => '1 month ago',
				);
			} elseif ( $GLOBALS['socialize_date_modified'] == 'year' ) {	
				$GLOBALS['socialize_date_modified_value'] = array(	
					'column' => 'post_modified_gmt',
					'after' => '1 year ago',
				);
			} elseif ( $GLOBALS['socialize_date_modified'] == 'all' ) {	
				$GLOBALS['socialize_date_modified_value'] = '';
			} else {
				$GLOBALS['socialize_date_modified_value'] = '';
			}
		}
				
	}	
}

?>