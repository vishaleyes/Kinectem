<?php 

if ( ! function_exists( 'socialize_carousel' ) ) {

	function socialize_carousel( $atts, $content = null ) {	
		
		extract( shortcode_atts( array(
			'widget_title' => '',	
			'cats' => '',
			'page_ids' => '',
			'post_types' => 'post',
			'orderby' => 'newest',
			'date_posted' => 'all',
			'date_modified' => 'all',
			'items_in_view' => '3',
			'per_page' => '12',
			'offset' => '0',
			'image_width' => '350',
			'image_height' => '220',	
			'hard_crop' => true,
			'slider_speed' => '0',
			'animation_speed' => '0.6',
			'buttons' => 'enabled',
			'arrows' => 'enabled',
			'see_all' => 'disabled',
			'see_all_link' => '',
			'see_all_text' => esc_html__( 'See All Items', 'socialize' ),
			'classes' => '',	
			'title_format' => 'gp-standard-title',	
			'title_color' => '#E93100',
			'icon' => '',
		), $atts ) );	

		// Detect shortcode
		$GLOBALS['socialize_shortcode'] = 'carousel';
		
		global $post, $socialize;

		socialize_shortcode_options( $atts );
		socialize_query_variables();
		
		wp_enqueue_script( 'gp-flexslider' );
		
		// Unique Name	
		STATIC $gp_i = 0;
		$gp_i++;
		$gp_name = 'gp_carousel_wrapper_' . $gp_i;

		// Page IDs
		if ( $page_ids ) {
			$page_ids = explode( ',', $page_ids );
		} else {
			$page_ids = '';
		}
		
		$gp_args = array(
			'post_status'    => 'publish',
			'post_type'           => explode( ',', $post_types ),
			'post__in'            => $page_ids,
			'tax_query' 	 => array( 'relation' => 'OR', $GLOBALS['socialize_post_cats'], $GLOBALS['socialize_portfolio_cats'], $GLOBALS['socialize_slide_cats'] ),
			'orderby'        => $GLOBALS['socialize_orderby_value'],
			'order'          => $GLOBALS['socialize_order'],
			'meta_key'       => $GLOBALS['socialize_meta_key'],
			'posts_per_page' => $GLOBALS['socialize_per_page'],		
			'offset' 		 => $GLOBALS['socialize_offset'],
			'paged'			 => 1,
			'date_query' 	 => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),
		);

		ob_start(); $gp_query = new wp_query( $gp_args ); ?>

		<div id="<?php echo sanitize_html_class( $gp_name ); ?>" class="gp-carousel-wrapper gp-vc-element gp-slider gp-blog-standard-size <?php echo esc_attr( $classes ); ?>">
			
			<?php if ( $widget_title ) { ?>
				<h3 class="widgettitle <?php echo $title_format; ?>"<?php if ( $title_color ) { ?> style="background-color: <?php echo esc_attr( $title_color ); ?>; border-color: <?php echo esc_attr( $title_color ); ?>"<?php } ?>>
					<?php if ( $icon ) { ?><i class="fa <?php echo sanitize_html_class( $icon ); ?>"></i><?php } ?>
					<span><?php echo esc_attr( $widget_title ); ?></span>
					<div class="gp-triangle"></div>
					<?php if ( $see_all == 'enabled' ) { ?>
						<span class="gp-see-all-link"><a href="<?php echo esc_url( $see_all_link ); ?>"><?php echo esc_attr( $see_all_text ); ?><i></i></a></span>
					<?php } ?>
				</h3>
			<?php } else { ?>
				<div class="gp-empty-widget-title"></div>
			<?php } ?>

			<?php if ( $gp_query->have_posts() ) : ?>
				
				<ul class="slides">

					<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); ?>
			
						<li>			

							<section <?php post_class( 'gp-post-item' ); ?> itemscope itemtype="http://schema.org/Article">
						
								<?php if ( has_post_thumbnail() ) { ?>
						
									<div class="gp-post-thumbnail gp-loop-featured">
									
									 	<div class="gp-image-above">

											<?php $gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $image_width, $image_height, $hard_crop, false, true ); ?>
											<?php if ( $socialize['retina'] == 'gp-retina' ) {
												$gp_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $image_width * 2, $image_height * 2, $hard_crop, true, true );
											} else {
												$gp_retina = '';
											} ?>

											<a href="<?php if ( get_post_format() == 'link' ) { echo esc_url( get_post_meta( get_the_ID(), 'link', true ) ); } else { the_permalink(); } ?>" title="<?php the_title_attribute(); ?>"<?php if ( get_post_format() == 'link' ) { ?> target="<?php echo get_post_meta( get_the_ID(), 'link_target', true ); ?>"<?php } ?>>
			
												<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); } else { the_title_attribute(); } ?>" class="gp-post-image" itemprop="image" />

											</a>
										
										</div>
														
									</div>
						
								<?php } elseif ( get_post_format() != '0' ) { ?>

									<?php get_template_part( 'lib/sections/loop', get_post_format() ); ?>

								<?php } ?>

								<div class="gp-loop-content">
								
									<h2 class="gp-loop-title" itemprop="headline"><a href="<?php if ( get_post_format() == 'link' ) { echo esc_url( get_post_meta( get_the_ID(), 'link', true ) ); } else { the_permalink(); } ?>" title="<?php the_title_attribute(); ?>"<?php if ( get_post_format() == 'link' ) { ?> target="<?php echo get_post_meta( get_the_ID(), 'link_target', true ); ?>"<?php } ?>><?php the_title(); ?></a></h2>
									
									<div class="gp-loop-meta">
										<time class="gp-post-meta gp-meta-date" itemprop="datePublished" datetime="<?php echo get_the_date( 'c' ); ?>"><?php the_time( get_option( 'date_format' ) ); ?></time>
									</div>	

								</div>
						
							</section>
						
						</li>
					
					<?php endwhile; ?>	

				</ul>

				<?php if ( ! is_admin() ) { ?>	
					<script>
					jQuery( document ).ready( function( $ ) {
						'use strict';
				
						var $window = $(window),
							flexslider = { vars:{} };

						function getGridSize() {
							return ( $window.width() <= 567 ) ? 1 : ( $window.width() <= 1023 ) ? <?php if ( $items_in_view == 1 ) { ?>1<?php } else { ?>2<?php } ?> : <?php echo absint( $items_in_view ); ?>;
						}
  
						$window.load(function() {
							$( '#<?php echo sanitize_html_class( $gp_name ); ?>' ).flexslider({
								animation: 'slide',
								animationLoop: false,
								itemWidth: <?php echo absint( $GLOBALS['socialize_image_width'] ); ?>,
								itemMargin: 30,
								slideshowSpeed: <?php if ( $slider_speed != '0' ) { echo absint( $slider_speed ) * 1000; } else { echo '9999999'; } ?>,
								animationSpeed: <?php echo absint( $animation_speed * 1000 ); ?>,
								directionNav: <?php if ( $arrows == 'enabled' ) { ?>true<?php } else { ?>false<?php } ?>,			
								controlNav: <?php if ( $buttons == 'enabled' ) { ?>true<?php } else { ?>false<?php } ?>,			
								pauseOnAction: true, 
								pauseOnHover: false,
								prevText: '',
								nextText: '',
								minItems: getGridSize(),
								maxItems: getGridSize(),
								start: function(slider){
									flexslider = slider;
								}
							});	
						});
								
						$window.resize( function() {
							var gridSize = getGridSize();
							flexslider.vars.minItems = gridSize;
							flexslider.vars.maxItems = gridSize;
						});			

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

add_shortcode( 'carousel', 'socialize_carousel' );
	
?>