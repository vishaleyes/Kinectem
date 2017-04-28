<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>		

	<?php if ( $GLOBALS['socialize_page_header'] == 'gp-fullwidth-page-header' OR $GLOBALS['socialize_page_header'] == 'gp-full-page-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

	<div id="gp-content-wrapper" class="gp-container">

		<?php if ( $GLOBALS['socialize_page_header'] == 'gp-large-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

		<div id="gp-left-column">
		
			<div id="gp-content">

				<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="gp-breadcrumbs">', '</div>' ); } ?>
					
				<header class="gp-entry-header">
					<h1 class="gp-entry-title" itemprop="headline">
						<?php the_title(); ?>
					</h1>
				</header>

				<?php the_content(); ?>

			</div>

			<?php get_sidebar( 'left' ); ?>
			
		</div>
		
		<?php get_sidebar( 'right' ); ?>
		
	<div class="gp-clear"></div></div>
	
<?php endwhile; endif; ?>
	
<?php get_footer(); ?>