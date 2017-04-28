<?php
/*
Template Name: Blog
*/
get_header(); global $socialize; ?>

<?php if ( $GLOBALS['socialize_page_header'] == 'gp-fullwidth-page-header' OR $GLOBALS['socialize_page_header'] == 'gp-full-page-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

<div id="gp-content-wrapper" class="gp-container">

	<?php if ( $GLOBALS['socialize_page_header'] == 'gp-large-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

	<div id="gp-left-column">
	
		<div id="gp-content">

			<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="gp-breadcrumbs">', '</div>' ); } ?>
		
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>	

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
					
				<?php the_content(); ?>
		
			<?php endwhile; endif; rewind_posts(); ?>	

			<?php 

			socialize_query_variables();
		
			if ( $GLOBALS['socialize_format'] == 'gp-blog-masonry' ) {
				wp_enqueue_script( 'gp-isotope' );
				wp_enqueue_script( 'gp-images-loaded' );
			}
					
			$gp_args = array(
				'post_status' 	      => 'publish',
				'post_type'           => explode( ',', $GLOBALS['socialize_post_types'] ),
				'tax_query'           => array( 'relation' => 'OR', $GLOBALS['socialize_post_cats'], $GLOBALS['socialize_portfolio_cats'], $GLOBALS['socialize_slide_cats'] ),
				'orderby'             => $GLOBALS['socialize_orderby_value'],
				'order'               => $GLOBALS['socialize_order'],
				'meta_key'            => $GLOBALS['socialize_meta_key'],
				'posts_per_page'      => $GLOBALS['socialize_per_page'],
				'paged'               => $GLOBALS['socialize_paged'],
				'date_query'          => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),	
			);

			$gp_query = new wp_query( $gp_args ); ?>
			
			<div class="gp-blog-wrapper <?php echo sanitize_html_class( $GLOBALS['socialize_format'] ); ?>"<?php if ( function_exists( 'socialize_data_properties' ) ) { echo socialize_data_properties( 'blog-template' ); } ?>>
	
				<?php if ( $gp_query->have_posts() ) : ?>
			
					<?php get_template_part( 'lib/sections/filter' ); ?>
														
					<div class="gp-inner-loop <?php echo sanitize_html_class( $socialize['ajax'] ); ?>">
							
						<?php while ( $gp_query->have_posts() ) : $gp_query->the_post(); ?>

							<?php get_template_part( 'post', 'loop' ); ?>
			
						<?php endwhile; ?>
		
					</div>

					<?php echo socialize_pagination( $gp_query->max_num_pages ); ?>

				<?php else : ?>

					<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'socialize' ); ?></strong>

				<?php endif; wp_reset_postdata(); ?>
			
			</div>

		</div>

		<?php get_sidebar( 'left' ); ?>

	</div>
	
	<?php get_sidebar( 'right' ); ?>

<div class="gp-clear"></div></div>

<?php get_footer(); ?>