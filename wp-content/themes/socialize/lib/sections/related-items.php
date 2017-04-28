<?php global $socialize;

// Options
if ( is_singular( 'post' ) ) {
	$gp_post_type = 'post';
	$gp_per_page = $socialize['post_related_items_per_page'];
	$gp_items_in_view = $socialize['post_related_items_in_view'];
	$gp_image_width = $socialize['post_related_items_image']['width'];
	$gp_image_height = $socialize['post_related_items_image']['height'];
} elseif ( is_singular( 'gp_portfolio_item' ) ) {
	$gp_post_type = 'gp_portfolio_item';
	$gp_per_page = $socialize['portfolio_item_related_items_per_page'];
	$gp_items_in_view = $socialize['portfolio_item_related_items_in_view'];
	$gp_image_width = $socialize['portfolio_item_related_items_image']['width'];
	$gp_image_height = $socialize['portfolio_item_related_items_image']['height'];
}

// Check for tags
$gp_tags = wp_get_post_tags( get_the_ID() );

if ( $gp_tags ) {
	$related_type = 'tag__in';
	$gp_related_items = wp_get_post_tags( get_the_ID() );
} else {	
	$related_type = 'category__in';
	$gp_related_items = wp_get_post_terms( get_the_ID(), 'category' );
}	

$gp_temp_query = $wp_query;

if ( $gp_related_items ) {

	$gp_related_ids = array();

	foreach ( $gp_related_items as $gp_related_item ) $gp_related_ids[] = $gp_related_item->term_id;
		
	$gp_args = array(
		'post_type'           => $gp_post_type,
		'orderby'             => 'rand',
		'order'               => 'asc',
		'paged'               => 1,
		'posts_per_page'      => $gp_per_page,
		'offset'              => 0,
		$related_type         => $gp_related_ids,
		'post__not_in'        => array( get_the_ID() ),
		'ignore_sticky_posts' => true,
	); 

	$gp_query = new wp_query( $gp_args ); if ( $gp_query->have_posts() ) : 
				
		wp_enqueue_script( 'gp-flexslider' );

		?>
	
		<div class="gp-related-wrapper gp-carousel-wrapper gp-slider">

			<h3><?php esc_html_e( 'Related Articles', 'socialize' ); ?></h3>
			
			<ul class="slides">
			
				<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); ?>
				
					<li>

						<section <?php post_class( 'gp-post-item' ); ?> itemscope itemtype="http://schema.org/Article">
						
							<?php if ( has_post_thumbnail() ) { ?>
						
								<div class="gp-post-thumbnail gp-loop-featured">
									
									 <div class="gp-image-above">

										<?php $gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $gp_image_width, $gp_image_height, true, false, true ); ?>
										<?php if ( $socialize['retina'] == 'gp-retina' ) {
											$gp_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $gp_image_width * 2, $gp_image_height * 2, true, true, true );
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

								<div class="gp-loop-header">
									<h2 class="gp-loop-title" itemprop="headline"><a href="<?php if ( get_post_format() == 'link' ) { echo esc_url( get_post_meta( get_the_ID(), 'link', true ) ); } else { the_permalink(); } ?>" title="<?php the_title_attribute(); ?>"<?php if ( get_post_format() == 'link' ) { ?> target="<?php echo get_post_meta( get_the_ID(), 'link_target', true ); ?>"<?php } ?>><?php the_title(); ?></a></h2>	
								</div>
																									
								<div class="gp-loop-meta">
									<time class="gp-post-meta gp-meta-date" itemprop="datePublished" datetime="<?php echo get_the_date( 'c' ); ?>"><?php the_time( get_option( 'date_format' ) ); ?></time>
								</div>	

							</div>
						
						</section>
					
					</li>
				
				<?php endwhile; ?>	

			</ul>
				
		</div>

		<?php if ( ! is_admin() ) { ?>	
			<script>
			jQuery( document ).ready( function( $ ) {
				'use strict';
		
				var $window = $( window ),
					flexslider = { vars:{} };

				function getGridSize() {
					return ( $window.width() <= 567 ) ? 1 : ( $window.width() <= 1023 ) ? 2 : <?php echo absint( $gp_items_in_view ); ?>;
				}

				$window.load( function() {
					$( '.gp-related-wrapper' ).flexslider({  
						animation: 'slide',
						animationLoop: false,
						itemWidth: <?php echo absint( $gp_items_in_view ); ?>,
						itemMargin: 30,
						slideshowSpeed: 9999999,
						animationSpeed: 600,
						directionNav: true,			
						controlNav: false,			
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

	<?php endif; wp_reset_postdata(); ?>

<?php } ?>