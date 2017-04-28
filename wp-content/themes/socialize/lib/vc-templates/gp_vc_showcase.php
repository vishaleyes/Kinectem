<?php 

if ( ! function_exists( 'socialize_showcase' ) ) {

	function socialize_showcase( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'widget_title' => '',
			'cats' => '', 
			'page_ids' => '',
			'post_types' => 'post',
			'format' => 'gp-blog-horizontal',
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
			'per_page' => '5',
			'offset' => '0',
			'large_featured_image' => 'enabled',
			'small_featured_image' => 'enabled',
			'large_image_width' => '497',
			'large_image_height' => '243',
			'small_image_width' => '100',
			'small_image_height' => '65',
			'hard_crop' => true,
			'large_image_alignment' => 'gp-image-above',
			'small_image_alignment' => 'gp-image-align-left',
			'large_title_position' => 'title-next-to-thumbnail',
			'small_title_position' => 'title-next-to-thumbnail',
			'large_excerpt_length' => '80',
			'small_excerpt_length' => '0',
			'large_meta_author' => '',
			'large_meta_date' => '',
			'large_meta_comment_count' => '',
			'large_meta_views' => '',
			'large_meta_cats' => '',
			'large_meta_tags' => '',		
			'small_meta_author' => '',
			'small_meta_date' => '',
			'small_meta_comment_count' => '',
			'small_meta_views' => '',
			'small_meta_cats' => '',
			'small_meta_tags' => '',					
			'large_read_more_link' => 'disabled',
			'small_read_more_link' => 'disabled',
			'page_arrows' => 'disabled',
			'page_numbers' => 'disabled',
			'see_all' => 'disabled',
			'see_all_link' => '',
			'see_all_text' => esc_html__( 'See All Items', 'socialize' ),
			'classes' => '',
			'title_format' => 'gp-fancy-title',
			'title_color' => '#E93100',	
			'icon' => '',
		), $atts ) );
		
		// Detect shortcode
		$GLOBALS['socialize_shortcode'] = 'showcase';
						
		global $socialize, $post;

		socialize_shortcode_options( $atts );
		socialize_query_variables();
		$GLOBALS['socialize_content_display'] = 'excerpt';

		// Unique Name	
		STATIC $gp_i = 0;
		$gp_i++;
		$GLOBALS['socialize_name'] = 'gp_showcase_wrapper_' . $gp_i;

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
			'tax_query' 	      => array( 'relation' => 'OR', $GLOBALS['socialize_post_cats'], $GLOBALS['socialize_portfolio_cats'], $GLOBALS['socialize_slide_cats'] ),
			'orderby' 		      => $GLOBALS['socialize_orderby_value'],
			'order' 		      => $GLOBALS['socialize_order'],	
			'meta_key' 		      => $GLOBALS['socialize_meta_key'],
			'posts_per_page'      => $GLOBALS['socialize_per_page'],
			'offset' 		      => $GLOBALS['socialize_offset'],	
			'paged'          	  => $GLOBALS['socialize_paged'],
			'date_query' 	 	  => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),
		);
		
		ob_start(); $gp_query = new wp_query( $gp_args ); $gp_counter = 1; ?>		

		<div id="<?php echo sanitize_html_class( $GLOBALS['socialize_name'] ); ?>" class="gp-showcase-wrapper gp-vc-element gp-blog-standard <?php echo $format; ?> <?php echo esc_attr( $classes ); ?>"<?php if ( function_exists( 'socialize_data_properties' ) ) { echo socialize_data_properties( 'showcase' ); } ?> data-largefeaturedimage="<?php echo esc_attr( $large_featured_image ); ?>" data-smallfeaturedimage="<?php echo esc_attr( $small_featured_image ); ?>" data-largeimagewidth="<?php echo esc_attr( $large_image_width ); ?>" data-smallimagewidth="<?php echo esc_attr( $small_image_width ); ?>" data-largeimageheight="<?php echo esc_attr( $large_image_height ); ?>" data-smallimageheight="<?php echo esc_attr( $small_image_height ); ?>" data-largeimagealignment="<?php echo esc_attr( $large_image_alignment ); ?>" data-smallimagealignment="<?php echo esc_attr( $small_image_alignment ); ?>" data-largetitleposition="<?php echo esc_attr( $large_title_position ); ?>" data-smalltitleposition="<?php echo esc_attr( $small_title_position ); ?>" data-largeexcerptlength="<?php echo esc_attr( $large_excerpt_length ); ?>" data-smallexcerptlength="<?php echo esc_attr( $small_excerpt_length ); ?>" data-largemetaauthor="<?php echo esc_attr( $large_meta_author ); ?>" data-smallmetaauthor="<?php echo esc_attr( $small_meta_author ); ?>" data-largemetadate="<?php echo esc_attr( $large_meta_date ); ?>" data-smallmetadate="<?php echo esc_attr( $small_meta_date ); ?>" data-largemetacommentcount="<?php echo esc_attr( $large_meta_comment_count ); ?>" data-smallmetacommentcount="<?php echo esc_attr( $small_meta_comment_count ); ?>" data-largemetaviews="<?php echo esc_attr( $large_meta_views ); ?>" data-smallmetaviews="<?php echo esc_attr( $small_meta_views ); ?>" data-largemetacats="<?php echo esc_attr( $large_meta_cats ); ?>" data-smallmetacats="<?php echo esc_attr( $small_meta_cats ); ?>" data-largemetatags="<?php echo esc_attr( $large_meta_tags ); ?>" data-smallmetatags="<?php echo esc_attr( $small_meta_tags ); ?>" data-largereadmorelink="<?php echo esc_attr( $large_read_more_link ); ?>" data-smallreadmorelink="<?php echo esc_attr( $small_read_more_link ); ?>">

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
		
					<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); 
					
						if ( $gp_counter % $GLOBALS['socialize_per_page'] == 1 ) {
							$GLOBALS['socialize_featured_image'] = $large_featured_image;
							$GLOBALS['socialize_image_width'] = $large_image_width;
							$GLOBALS['socialize_image_height'] = $large_image_height;
							$GLOBALS['socialize_image_alignment'] = $large_image_alignment;
							$GLOBALS['socialize_title_position'] = $large_title_position;
							$GLOBALS['socialize_excerpt_length'] = $large_excerpt_length;
							$GLOBALS['socialize_meta_author'] = $large_meta_author;
							$GLOBALS['socialize_meta_date'] = $large_meta_date;
							$GLOBALS['socialize_meta_comment_count'] = $large_meta_comment_count;
							$GLOBALS['socialize_meta_views'] = $large_meta_views;
							$GLOBALS['socialize_meta_cats'] = $large_meta_cats;
							$GLOBALS['socialize_meta_tags'] = $large_meta_tags;
							$GLOBALS['socialize_read_more_link'] = $large_read_more_link;
						} else {
							$GLOBALS['socialize_featured_image'] = $small_featured_image;
							$GLOBALS['socialize_image_width'] = $small_image_width;
							$GLOBALS['socialize_image_height'] = $small_image_height;
							$GLOBALS['socialize_image_alignment'] = $small_image_alignment;
							$GLOBALS['socialize_title_position'] = $small_title_position;
							$GLOBALS['socialize_excerpt_length'] = $small_excerpt_length;
							$GLOBALS['socialize_meta_author'] = $small_meta_author;
							$GLOBALS['socialize_meta_date'] = $small_meta_date;
							$GLOBALS['socialize_meta_comment_count'] = $small_meta_comment_count;
							$GLOBALS['socialize_meta_views'] = $small_meta_views;
							$GLOBALS['socialize_meta_cats'] = $small_meta_cats;
							$GLOBALS['socialize_meta_tags'] = $small_meta_tags;
							$GLOBALS['socialize_read_more_link'] = $small_read_more_link;	
						}
								
					?>

						<?php if ( ( isset( $gp_counter ) && ( $gp_counter % $GLOBALS['socialize_per_page'] == 2 OR $gp_counter == 2 ) ) && $gp_query->current_post != 0 ) { ?>
							<div class="gp-small-posts">
						<?php } ?>

							<?php get_template_part( 'post', 'loop' ); ?>

						<?php if ( ( isset( $gp_counter ) && $gp_counter % $GLOBALS['socialize_per_page'] == 0 ) OR ( ( ( $gp_query->current_post + 1 ) == $gp_query->post_count ) && $gp_query->current_post != 0 ) ) { ?>
							</div>
						<?php } ?>

					<?php $gp_counter++; endwhile; ?>
		
				</div>

				<?php if ( $page_numbers == 'enabled' ) { ?>
					<?php echo socialize_pagination( $gp_query->max_num_pages ); ?>
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

add_shortcode( 'showcase', 'socialize_showcase' );
	
?>