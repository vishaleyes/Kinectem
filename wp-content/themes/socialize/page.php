<?php get_header(); global $socialize; ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>		

	<?php if ( $GLOBALS['socialize_page_header'] == 'gp-fullwidth-page-header' OR $GLOBALS['socialize_page_header'] == 'gp-full-page-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

	<div id="gp-content-wrapper" class="gp-container">

		<?php if ( $GLOBALS['socialize_page_header'] == 'gp-large-page-header' ) { socialize_page_header( get_the_ID() ); } ?>
		
		<div id="gp-left-column">

			<div id="gp-content">

				<article <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">

					<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="gp-breadcrumbs">', '</div>' ); } ?>
					
					<?php if ( $GLOBALS['socialize_title'] == 'enabled' ) { ?>	
						<header class="gp-entry-header">	
							
							<h1 class="gp-entry-title" itemprop="headline">
								<?php if ( ! empty( $GLOBALS['socialize_custom_title'] ) ) { echo esc_attr( $GLOBALS['socialize_custom_title'] ); } else { the_title(); } ?>
							</h1>
										
							<?php if ( ! empty( $GLOBALS['socialize_subtitle'] ) ) { ?>
								<h3 class="gp-subtitle"><?php echo esc_attr( $GLOBALS['socialize_subtitle'] ); ?></h3>
							<?php } ?>
				
						</header>
					<?php } ?>
					
					<div class="gp-entry-content" itemprop="text">

						<?php if ( has_post_thumbnail() && $GLOBALS['socialize_featured_image'] == 'enabled' ) { ?>

							<div class="gp-post-thumbnail gp-featured-content gp-entry-image <?php echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); ?>">

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

						<?php } ?>

						<div class="gp-entry-text <?php if ( isset( $GLOBALS['socialize_image_alignment'] ) ) { echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); } ?>"><?php the_content(); ?></div>
	
						<?php wp_link_pages( 'before=<div class="gp-pagination gp-pagination-numbers gp-standard-pagination gp-entry-pagination"><ul class="page-numbers">&pagelink=<span class="page-numbers">%</span>&after=</ul></div>' ); ?>	
		
					</div>
		
					<?php if ( $socialize['page_author_info'] == 'enabled' ) { ?>
						<?php get_template_part( 'lib/sections/author', 'info' ); ?>
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