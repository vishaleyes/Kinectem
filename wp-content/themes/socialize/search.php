<?php get_header(); ?>

<?php if ( $GLOBALS['socialize_page_header'] == 'gp-fullwidth-page-header' OR $GLOBALS['socialize_page_header'] == 'gp-full-page-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

<div id="gp-content-wrapper" class="gp-container">

	<?php if ( $GLOBALS['socialize_page_header'] == 'gp-large-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

	<div id="gp-left-column">

		<div id="gp-content">

			<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="gp-breadcrumbs">', '</div>' ); } ?>
				
			<header class="gp-entry-header">		
				<h1 class="gp-entry-title" itemprop="headline">
					<?php if ( isset( $_GET['s'] ) && ( $_GET['s'] != '' ) ) { ?>
						<?php echo absint( $wp_query->found_posts ); ?> <?php esc_html_e( 'search results for', 'socialize' ); ?> "<?php echo esc_attr( $s ); ?>"
					<?php } else { ?>
						<?php esc_html_e( 'Search', 'socialize' ); ?>
					<?php } ?>
				</h1>
			</header>
		
			<div id="gp-new-search">
		
				<?php if ( isset( $_GET['s'] ) && ( $_GET['s'] != '' ) ) { ?>
			
					<p><?php esc_html_e( 'If you didn\'t find what you were looking for try searching again.', 'socialize' ); ?></p>
			
				<?php } else { ?>
			
					<p><?php esc_html_e( 'You left the search box empty, please enter a valid term.', 'socialize' ); ?></p>
		
				<?php } ?>	
		
				<?php get_search_form(); ?>
		
			</div>

			<?php if ( isset( $_GET['s'] ) && ( $_GET['s'] != '' ) ) { ?>
	
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
	
			<?php } ?>			
	
		</div>

		<?php get_sidebar( 'left' ); ?>

	</div>
	
	<?php get_sidebar( 'right' ); ?>

<div class="gp-clear"></div></div>

<?php get_footer(); ?>