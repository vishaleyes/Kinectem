<?php get_header(); global $socialize; ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>		

	<?php if ( $GLOBALS['socialize_page_header'] == 'gp-fullwidth-page-header' OR $GLOBALS['socialize_page_header'] == 'gp-full-page-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

	<div id="gp-content-wrapper" class="gp-container">

		<?php if ( $GLOBALS['socialize_page_header'] == 'gp-large-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

		<div id="gp-left-column">
		
			<div id="gp-content">	

				<article <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">
		
					<div id="gp-post-navigation">
		
						<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="gp-breadcrumbs">', '</div>' ); } ?>
			
						<?php if ( $socialize['post_meta']['post_nav'] == '1' && $socialize['post_meta']['top_share_icons'] == '1' ) { ?>
			
							<div id="gp-post-links">
								<?php if ( $socialize['post_meta']['post_nav'] == '1' ) { ?>
									<?php previous_post_link( '%link', '', false ); ?>
									<?php next_post_link( '%link', '', false ); ?>
								<?php } ?>
								<?php if ( $socialize['post_meta']['top_share_icons'] == '1' ) { ?>
								<a href="#" class="gp-share-button"></a><?php } ?>
							</div>
			
						<?php } ?>
					
						<?php if ( $socialize['post_meta']['top_share_icons'] == '1' ) { ?>
							<?php get_template_part( 'lib/sections/share', 'icons' ); ?>
						<?php } ?>
			
						<div class="gp-clear"></div>
		
					</div>	
		
					<?php if ( $GLOBALS['socialize_title'] == 'enabled' && get_post_format() != 'video' ) { ?>	

						<header class="gp-entry-header">	
			
							<?php if ( $GLOBALS['socialize_meta_cats'] == '1' ) { ?>		
								<div class="gp-entry-cats">
									<?php echo socialize_exclude_cats( get_the_ID() ); ?>
								</div>
							<?php } ?>	

							<h1 class="gp-entry-title" itemprop="headline">
								<?php if ( ! empty( $GLOBALS['socialize_custom_title'] ) ) { echo esc_attr( $GLOBALS['socialize_custom_title'] ); } else { the_title(); } ?>
							</h1>

							<?php get_template_part( 'lib/sections/entry', 'meta' ); ?>
				
							<?php if ( ! empty( $GLOBALS['socialize_subtitle'] ) ) { ?>
								<h3 class="gp-subtitle"><?php echo esc_attr( $GLOBALS['socialize_subtitle'] ); ?></h3>
							<?php } ?>	
	
						</header>
		
					<?php } ?>

					<?php if ( has_post_thumbnail() && $GLOBALS['socialize_featured_image'] == 'enabled' && get_post_format() == '0' ) { ?>

						<div class="gp-post-thumbnail gp-entry-featured">
			
							<div class="<?php echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); ?>">

								<?php $gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $GLOBALS['socialize_image_width'], $GLOBALS['socialize_image_height'], $GLOBALS['socialize_hard_crop'], false, true ); ?>
								<?php if ( $socialize['retina'] == 'gp-retina' ) {
									$gp_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $GLOBALS['socialize_image_width'] * 2, $GLOBALS['socialize_image_height'] * 2, $GLOBALS['socialize_hard_crop'], true, true );
								} else {
									$gp_retina = '';
								} ?>

								<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); } else { the_title_attribute(); } ?>" class="gp-post-image" itemprop="image" />
				
							</div>
				
						</div>

					<?php } elseif ( get_post_format() != '0' ) { ?>

						<div class="gp-entry-featured">
							<?php get_template_part( 'lib/sections/entry', get_post_format() ); ?>
						</div>

					<?php } ?>
																					
					<div class="gp-entry-content" itemprop="text">
								
						<?php if ( get_post_format() == '0' OR get_post_format() == 'audio' OR get_post_format() == 'gallery' OR get_post_format() == 'video' ) { ?>

							<div class="gp-entry-text <?php echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); ?>"><?php the_content(); ?></div>

						<?php } ?>

						<?php wp_link_pages( 'before=<div class="gp-pagination gp-pagination-numbers gp-standard-pagination gp-entry-pagination"><ul class="page-numbers">&pagelink=<span class="page-numbers">%</span>&after=</ul></div>' ); ?>

					</div>

					<?php if ( $GLOBALS['socialize_meta_tags'] == '1' ) { ?>
						<?php the_tags( '<div class="gp-entry-tags">', ' ', '</div>' ); ?>
					<?php } ?>

					<?php if ( $socialize['post_meta']['bottom_share_icons'] == '1' ) { ?>
						<?php get_template_part( 'lib/sections/share', 'icons' ); ?>
					<?php } ?>
						
					<?php if ( $socialize['post_author_info'] == 'enabled' ) { ?>
						<?php get_template_part( 'lib/sections/author', 'info' ); ?>
					<?php } ?>

					<?php if ( $socialize['post_related_items'] == 'enabled' ) { ?>
						<?php get_template_part( 'lib/sections/related', 'items' ); ?>
					<?php } ?>

					<?php comments_template(); ?>

				</article>

			</div>
		
			<?php get_sidebar( 'left' ); ?>

		</div>

		<?php get_sidebar( 'right' ); ?>

	<div class="gp-clear"></div></div>

<?php endwhile; endif; ?>
	
<?php get_footer(); ?>