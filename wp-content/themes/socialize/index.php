<?php get_header();

if ( $GLOBALS['socialize_format'] == 'gp-blog-masonry' ) {
	wp_enqueue_script( 'gp-isotope' );
	wp_enqueue_script( 'gp-images-loaded' );
}
		
?>

<?php if ( $GLOBALS['socialize_page_header'] == 'gp-fullwidth-page-header' OR $GLOBALS['socialize_page_header'] == 'gp-full-page-page-header' ) { socialize_page_header( get_the_ID() ); } ?>
	
<div id="gp-content-wrapper" class="gp-container">
	
	<?php if ( $GLOBALS['socialize_page_header'] == 'gp-large-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

	<div id="gp-left-column">
				
		<div id="gp-content">

			<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="gp-breadcrumbs">', '</div>' ); } ?>
		
			<?php if ( $GLOBALS['socialize_page_header'] == 'gp-standard-page-header' ) { ?>				
				<header class="gp-entry-header">
					<h1 class="gp-entry-title" itemprop="headline"><?php if ( is_category() OR is_tag() OR is_tax() ) { ?><?php single_cat_title(); ?><?php } else { ?><?php if ( ! function_exists( '_wp_render_title_tag' ) && ! function_exists( 'socialize_render_title' ) ) { esc_html_e( 'Archives', 'socialize' ); } else { the_archive_title(); } ?><?php } ?></h1>
				</header>
			<?php } ?>	
				
			<?php socialize_query_variables();	 ?>
		
			<div class="gp-blog-wrapper <?php echo sanitize_html_class( $GLOBALS['socialize_format'] ); ?>"<?php if ( function_exists( 'socialize_data_properties' ) ) { echo socialize_data_properties( 'taxonomy' ); } ?>>

				<?php if ( have_posts() ) : ?>
		
					<?php get_template_part( 'lib/sections/filter' ); ?>
							
					<div class="gp-inner-loop <?php echo sanitize_html_class( $socialize['ajax'] ); ?>">

						<?php while ( have_posts() ) : the_post(); ?>

							<?php get_template_part( 'post', 'loop' ); ?>

						<?php endwhile; ?>
	
					</div>

					<?php echo socialize_pagination( $wp_query->max_num_pages ); ?>

				<?php else : ?>

					<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'socialize' ); ?></strong>

				<?php endif; ?>
	
			</div>
			
		</div>
	
		<?php get_sidebar( 'left' ); ?>
	
	</div>

	<?php get_sidebar( 'right' ); ?>

<div class="gp-clear"></div></div>

<?php get_footer(); ?>