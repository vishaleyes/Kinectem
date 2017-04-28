<?php 

if ( ! function_exists( 'socialize_blog' ) ) {

	function socialize_blog( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'widget_title' => '',			
			'cats' => '',
			'page_ids' => '',
			'post_types' => 'post',
			'format' => 'gp-blog-standard',
			'orderby' => 'newest',
			'date_posted' => 'all',
			'date_modified' => 'all',
			'filter' => 'disabled',
			'filter_cats' => '',
			'filter_date' => '',
			'filter_title' => '',					
			'filter_comment_count' => '',
			'filter_views' => '',
			'filter_date_posted' => '',
			'filter_date_modified' => '',
			'filter_cats_id' => '',
			'per_page' => '12',
			'offset' => '0',
			'featured_image' => 'enabled',
			'image_width' => '200',
			'image_height' => '200',
			'hard_crop' => true,
			'image_alignment' => 'gp-image-align-left',
			'title_position' => 'title-next-to-thumbnail',
			'content_display' => 'excerpt',
			'excerpt_length' => '160',
			'meta_author' => '',
			'meta_date' => '',
			'meta_views' => '',
			'meta_comment_count' => '',
			'meta_cats' => '',
			'meta_tags' => '',	
			'read_more_link' => 'disabled',
			'page_arrows' => 'disabled',
			'page_numbers' => 'disabled',
			'see_all' => 'disabled',
			'see_all_link' => '',
			'see_all_text' => esc_html__( 'See All Items', 'socialize' ),
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',		
		), $atts ) );
							
		global $socialize;

		// Detect shortcode
		$GLOBALS['socialize_shortcode'] = 'blog';
		
		socialize_shortcode_options( $atts );
		socialize_query_variables();

		if ( $GLOBALS['socialize_format'] == 'gp-blog-masonry' ) {
			wp_enqueue_script( 'gp-isotope' );
			wp_enqueue_script( 'gp-images-loaded' );
		}
						
		// Unique Name	
		STATIC $gp_i = 0;
		$gp_i++;
		$gp_name = 'gp_blog_wrapper_' . $gp_i;

		// Page IDs
		if ( $page_ids ) {
			$page_ids = explode( ',', $page_ids );
		} else {
			$page_ids = '';
		}
											
		$gp_args = array(
			'post_status'         => 'publish',
			'post_type'           => explode( ',', $post_types ),
			'post__in'            => $page_ids,
			'tax_query'           => array( 'relation' => 'OR', $GLOBALS['socialize_post_cats'], $GLOBALS['socialize_portfolio_cats'], $GLOBALS['socialize_slide_cats'] ),
			'orderby' 		      => $GLOBALS['socialize_orderby_value'],
			'order' 		      => $GLOBALS['socialize_order'],	
			'meta_key' 		      => $GLOBALS['socialize_meta_key'],
			'posts_per_page'      => $GLOBALS['socialize_per_page'],
			'offset' 		      => $GLOBALS['socialize_offset'],	
			'paged'          	  => $GLOBALS['socialize_paged'],
			'date_query' 	      => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),
		);
		
		ob_start(); $gp_query = new WP_Query( $gp_args ); ?>		

		<div id="<?php echo sanitize_html_class( $gp_name ); ?>" class="gp-blog-wrapper gp-vc-element <?php echo sanitize_html_class( $GLOBALS['socialize_format'] ); ?> <?php echo esc_attr( $classes ); ?>"<?php if ( function_exists( 'socialize_data_properties' ) ) { echo socialize_data_properties( 'blog' ); } ?>>

			<?php if ( $widget_title ) { ?>
				<h3 class="widgettitle <?php echo $title_format; ?>"<?php if ( $title_color ) { ?> style="background-color: <?php echo esc_attr( $title_color ); ?>; border-color: <?php echo esc_attr( $title_color ); ?>"<?php } ?>>
					<?php if ( $icon ) { ?><i class="fa <?php echo sanitize_html_class( $icon ); ?>"></i><?php } ?>
					<span><?php echo esc_attr( $widget_title ); ?></span>
					<div class="gp-triangle"></div>
					<?php if ( $see_all == 'enabled' ) { ?>
						<span class="gp-see-all-link"><a href="<?php echo esc_url( $see_all_link ); ?>"><?php echo esc_attr( $see_all_text ); ?><i></i></a></span>
					<?php } ?>
				</h3>
			<?php } elseif ( $filter == 'disabled' ) { ?>
				<div class="gp-empty-widget-title"></div>
			<?php } ?>
			
			<?php if ( $gp_query->have_posts() ) : ?>
			
				<?php if ( $page_arrows == 'enabled' ) { ?>
					<div class="gp-pagination gp-standard-pagination gp-pagination-arrows">
						<?php echo socialize_get_previous_posts_page_link( $gp_query->max_num_pages ); ?>
						<?php echo socialize_get_next_posts_page_link( $gp_query->max_num_pages ); ?>	
					</div>
				<?php } ?>
											
				<?php get_template_part( 'lib/sections/filter' ); ?>
				
				<div class="gp-inner-loop <?php echo sanitize_html_class( $socialize['ajax'] ); ?>">
		
					<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); ?>

						<?php get_template_part( 'post', 'loop' ); ?>

					<?php endwhile; ?>
		
				</div>

				<?php if ( $page_numbers == 'enabled' ) { ?>
					<?php echo socialize_pagination( $gp_query->max_num_pages ); ?>
				<?php } ?>

				<?php if ( $GLOBALS['socialize_format'] == 'gp-blog-masonry' && ( ! is_admin() OR ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ) { ?>
		
					<script>
					jQuery( document ).ready( function( $ ) {

						'use strict';

						if ( $( '#<?php echo sanitize_html_class( $gp_name ); ?>' ).hasClass( 'gp-blog-masonry' ) ) {
	
							var container = $( '#<?php echo sanitize_html_class( $gp_name ); ?> .gp-inner-loop' );
							var element = container;

							if ( container.find( 'img' ).length == 0 ) {
								element = $( '<img />' );
							}	

							imagesLoaded( element, function( instance ) {

								container.isotope({
									itemSelector: '.gp-post-item',
									masonry: {
										columnWidth: container.find( '.gp-post-item' )[0],
										gutter: 20
									}
								});

								container.animate( { 'opacity': 1 }, 1300 );
								$( '.gp-pagination' ).animate( { 'opacity': 1 }, 1300 );

							});
				
						}
	
					});	
					</script>	
		
				<?php } ?>
		
			<?php else : ?>

				<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'socialize' ); ?></strong>

			<?php endif; wp_reset_postdata(); ?>
							
		</div>
					
		<?php

		$gp_output_string = ob_get_contents();
		ob_end_clean();  
		$GLOBALS['socialize_shortcode'] = null;
		return $gp_output_string;

	}

}

add_shortcode( 'blog', 'socialize_blog' );
	
?>