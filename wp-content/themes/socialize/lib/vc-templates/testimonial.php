<?php

extract( shortcode_atts( array( 
	'image_url'    => '',
	'image_width'  => '120',
	'image_height' => '120',
	'name'         => '',
 ), $atts ) );
 
ob_start(); global $socialize; ?>

	<li class="gp-testimonial-slide">
	
		<?php if ( $image_url ) { ?>
		
			<?php $gp_image = aq_resize( wp_get_attachment_url( $image_url ), $image_width, $image_height, true, false, true ); ?>
			<?php if ( $socialize['retina'] == 'enabled' ) {
				$gp_retina = aq_resize( wp_get_attachment_url( $image_url ), $image_width * 2, $image_height * 2, true, true, true );
			} else {
				$gp_retina = '';
			} ?>
			
			<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); } else { the_title_attribute(); } ?>" class="gp-post-image gp-testimonial-image" itemprop="image" />
											
		<?php } ?>
							
		<div class="gp-testimonial-quote"<?php if ( $image_url ) { ?> style="margin-left: <?php echo absint( $gp_image[1] ) + 38; ?>px;"<?php } ?>>
			<?php if ( $content ) { ?><h5><?php echo wp_kses_post( $content ); ?></h5><?php } ?>
			<?php if ( $name ) { ?><span class="gp-testimonial-name"><?php echo esc_attr( $name ); ?></span><?php } ?>
		</div>

	</li>

<?php

$gp_output_string = ob_get_contents();
ob_end_clean(); 	
echo wp_kses_post( $gp_output_string );

?>