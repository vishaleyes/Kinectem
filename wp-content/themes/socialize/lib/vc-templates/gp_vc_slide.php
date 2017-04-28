<?php global $socialize; ?>

<?php if ( ! empty( $socialize['slide_link'] ) OR $post->post_type == 'post' OR $post->post_type == 'page' ) { ?><a href="<?php if ( $post->post_type == 'gp_slide' ) { echo esc_url( $socialize['slide_link'] ); } elseif ( get_post_format() == 'link' ) { echo esc_url( get_post_meta( get_the_ID(), 'link', true ) ); } else { the_permalink(); } ?>" title="<?php the_title_attribute(); ?>"<?php if ( get_post_format() == 'link' OR $post->post_type == 'gp_slide' ) { ?> target="<?php if ( $post->post_type == 'gp_slide' ) { echo esc_attr( $socialize['slide_link_target'] ); } else { echo get_post_meta( get_the_ID(), 'link_target', true ); } ?>"<?php } ?>><?php } ?>

	<?php if ( $GLOBALS['socialize_caption_title'] == 'enabled' OR ( $GLOBALS['socialize_caption_text'] == 'enabled' && $GLOBALS['socialize_slide_number'] == '1' ) ) { ?>
	
		<div class="gp-slide-caption">
				
			<div class="gp-entry-cats">
				<?php echo socialize_exclude_cats( get_the_ID(), true ); ?>
			</div>
				
			<?php if ( $GLOBALS['socialize_caption_title'] == 'enabled' ) { ?>
				<h2 class="gp-slide-caption-title">
					<?php if ( $post->post_type == 'gp_slide' && ! empty( $socialize['slide_caption_title'] ) ) { echo esc_attr( $socialize['slide_caption_title'] ); } else { the_title(); } ?>
				</h2>
			<?php } ?>

			<?php if ( $GLOBALS['socialize_caption_text'] == 'enabled' && $GLOBALS['socialize_slide_number'] == '1' ) { ?>
				<p class="gp-slide-caption-text">
					<?php if ( $post->post_type == 'gp_slide' ) { echo esc_attr( $socialize['slide_caption_text'] ); } else { echo socialize_excerpt( 200 ); } ?>
				</p>
			<?php } ?>

		</div>
		
	<?php } ?>
		
	<div class="gp-post-thumbnail">
											
		<?php if ( has_post_thumbnail() ) { 
				
			// Image URL
			$gp_image = aq_resize( $GLOBALS['socialize_image_url'], $GLOBALS['socialize_slide_width'], $GLOBALS['socialize_slide_height'], $GLOBALS['socialize_hard_crop'], false, true );
			if ( $socialize['retina'] == 'gp-retina' ) {
				$gp_retina = aq_resize( $GLOBALS['socialize_image_url'], $GLOBALS['socialize_slide_width'] * 2, $GLOBALS['socialize_slide_height'] * 2, $GLOBALS['socialize_hard_crop'], true, true );
			} else {
				$gp_retina = '';
			}

			$gp_mobile_image = aq_resize( $GLOBALS['socialize_image_url'], 492, 303, $GLOBALS['socialize_hard_crop'], false, true );
			if ( $socialize['retina'] == 'gp-retina' ) {
				$gp_mobile_retina = aq_resize( $GLOBALS['socialize_image_url'], 492 * 2, 303 * 2, $GLOBALS['socialize_hard_crop'], true, true );
			} else {
				$gp_mobile_retina = '';
			}
			
		?>
			<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); } else { the_title_attribute(); } ?>" class="gp-post-image gp-large-image" itemprop="image" />

			<img src="<?php echo esc_url( $gp_mobile_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_mobile_retina ); ?>" width="<?php echo absint( $gp_mobile_image[1] ); ?>" height="<?php echo absint( $gp_mobile_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); } else { the_title_attribute(); } ?>" class="gp-post-image gp-mobile-image" itemprop="image" />
						
		<?php } ?>	

	</div>
						
<?php if ( ! empty( $socialize['slide_link'] ) OR $post->post_type == 'post' OR $post->post_type == 'page' ) { ?></a><?php } ?>