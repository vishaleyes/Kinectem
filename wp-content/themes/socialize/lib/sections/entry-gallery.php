<?php global $socialize;

// Get image IDs
$gp_image_ids = array_filter( explode( ',', get_post_meta( get_the_ID(), 'gallery_slider', true ) ) );	

if ( $gp_image_ids ) {

	wp_enqueue_script( 'gp-flexslider' );

	?>

	<div class="gp-post-format-gallery-slider-content gp-slider <?php echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); ?>" style="width: <?php echo absint( $GLOBALS['socialize_image_width'] ); ?>px;"> 
						
		 <ul class="slides">
			<?php foreach ( $gp_image_ids as $gp_image_id ) { ?>
				<li>
					<?php $gp_image = aq_resize( wp_get_attachment_url( $gp_image_id ), $GLOBALS['socialize_image_width'], $GLOBALS['socialize_image_height'], $GLOBALS['socialize_hard_crop'], false, true ); ?>
					<?php if ( $socialize['retina'] == 'gp-retina' ) {
						$gp_retina = aq_resize( wp_get_attachment_url( $gp_image_id ), $GLOBALS['socialize_image_width'] * 2, $GLOBALS['socialize_image_height'] * 2, $GLOBALS['socialize_hard_crop'], true, true );
					} else {
						$gp_retina = '';
					} ?>
					<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true) ) { echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true); } else { the_title_attribute(); } ?>" class="gp-post-image" itemprop="image" />			
				</li>
			<?php } ?>
		</ul>
	 </div>

	<?php if ( ! is_admin() OR ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) { ?>
	
		<script>
		jQuery( window ).load( function() {
			'use strict';
			jQuery( '.gp-post-format-gallery-slider-content' ).flexslider( { 
				animation: 'fade',
				slideshowSpeed: 9999999,
				animationSpeed: 600,
				directionNav: true,			
				controlNav: false,			
				pauseOnAction: true, 
				pauseOnHover: false,
				prevText: '',
				nextText: '',
				smoothHeight: true
			});
		});
		</script>

	<?php } ?>
	
<?php } ?>