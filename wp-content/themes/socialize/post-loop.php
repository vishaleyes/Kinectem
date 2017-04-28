<?php global $socialize; ?>

<section <?php post_class( 'gp-post-item' ); ?> itemscope itemtype="http://schema.org/Article">

	<?php if ( has_post_thumbnail() && $GLOBALS['socialize_featured_image'] == 'enabled' ) { ?>

		<div class="gp-post-thumbnail gp-loop-featured">
		
			 <div class="<?php echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); ?>">

				<?php $gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $GLOBALS['socialize_image_width'], $GLOBALS['socialize_image_height'], $GLOBALS['socialize_hard_crop'], false, true ); ?>
				<?php if ( $socialize['retina'] == 'gp-retina' ) {
					$gp_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $GLOBALS['socialize_image_width'] * 2, $GLOBALS['socialize_image_height'] * 2, $GLOBALS['socialize_hard_crop'], true, true );
				} else {
					$gp_retina = '';
				} ?>

				<?php $gp_mobile_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 80, 80, $GLOBALS['socialize_hard_crop'], false, true ); ?>
				<?php if ( $socialize['retina'] == 'gp-retina' ) {
					$gp_mobile_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 80 * 2, 80 * 2, $GLOBALS['socialize_hard_crop'], true, true );
				} else {
					$gp_retina = '';
				} ?>
					
				<a href="<?php if ( get_post_format() == 'link' ) { echo esc_url( get_post_meta( get_the_ID(), 'link', true ) ); } else { the_permalink(); } ?>" title="<?php the_title_attribute(); ?>"<?php if ( get_post_format() == 'link' ) { ?> target="<?php echo redux_post_meta( 'socialize', get_the_ID(), 'link_target' ); ?>"<?php } ?>>
					
					<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); } else { the_title_attribute(); } ?>" class="gp-post-image gp-large-image" itemprop="image" />
					
					<img src="<?php echo esc_url( $gp_mobile_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_mobile_retina ); ?>" width="<?php echo absint( $gp_mobile_image[1] ); ?>" height="<?php echo absint( $gp_mobile_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); } else { the_title_attribute(); } ?>" class="gp-post-image gp-mobile-image" itemprop="image" />
	
				</a>
			
			</div>
									
		</div>

	<?php } elseif ( get_post_format() != '0' ) { ?>

		<div class="gp-loop-featured">
			<?php get_template_part( 'lib/sections/loop', get_post_format() ); ?>
		</div>

	<?php } ?>

	<?php if ( get_post_format() != 'quote' OR has_post_thumbnail() && $GLOBALS['socialize_featured_image'] == 'enabled' ) { ?>
	
		<div class="gp-loop-content <?php echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); ?>">
		
			<?php if ( $GLOBALS['socialize_meta_cats'] == '1' ) { ?>		
				<div class="gp-loop-cats">
					<?php echo socialize_exclude_cats( get_the_ID() ); ?>
				</div>
			<?php } ?>	
			
			<h2 class="gp-loop-title" itemprop="headline"><a href="<?php if ( get_post_format() == 'link' ) { echo esc_url( get_post_meta( get_the_ID(), 'link', true ) ); } else { the_permalink(); } ?>" title="<?php the_title_attribute(); ?>"<?php if ( get_post_format() == 'link' ) { ?> target="<?php echo redux_post_meta( 'socialize', get_the_ID(), 'link_target' ); ?>"<?php } ?>><?php the_title(); ?></a></h2>	

			<?php get_template_part( 'lib/sections/loop', 'meta' ); ?>

			<?php if ( $GLOBALS['socialize_content_display'] == 'full_content' ) { ?>

				<div class="gp-loop-text">
					<?php global $more; $more = 0; the_content( '[' . esc_html__( 'Read More', 'socialize' ) . ']' ); ?>
				</div>

			<?php } else { ?>

				<?php if ( $GLOBALS['socialize_excerpt_length'] != '0' ) { ?>
					<div class="gp-loop-text">
						<p><?php echo socialize_excerpt( $GLOBALS['socialize_excerpt_length'] ); ?></p>
					</div>
				<?php } ?>
	
			<?php } ?>
		
			<?php if ( isset( $GLOBALS['socialize_meta_tags'] ) && $GLOBALS['socialize_meta_tags'] == '1' ) { the_tags( '<div class="gp-meta-tags">', ' ', '</div>' ); } ?>

		</div>
	
	<?php } ?>

	<?php if ( $GLOBALS['socialize_format'] == 'gp-blog-large' ) { ?>
		<div class="gp-loop-divider"></div>
	<?php } ?>	
		
</section>