<?php
/*
Template Name: Homepage
*/
get_header(); global $socialize; ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>		

	<?php if ( $GLOBALS['socialize_content_header'] ) { ?>
		<div id="gp-content-header"<?php if ( $GLOBALS['socialize_content_header_format'] == 'fixed' ) { ?>  class="gp-container"<?php } ?>>
			<?php echo do_shortcode( $GLOBALS['socialize_content_header'] ); ?>
		</div>
	<?php } ?>
		
	<div id="gp-content-wrapper" class="gp-container">

		<div id="gp-left-column">
		
			<div id="gp-content">
		
				<?php if ( $post->post_content ) { ?>
				
					<?php the_content(); ?>
					
				<?php } else { ?>
				
					<div class="widget">
						
						<h3>Hey, where's my homepage?</h3>
						<p>There's been some changes to the homepage template. Now you can completely customize everything about the homepage using the drag and drop Visual Composer. To set up the homepage please watch the video below.</p>
						
						<iframe width="640" height="480" src="https://www.youtube.com/embed/bcW5PQvFkeU?rel=0" frameborder="0" allowfullscreen></iframe>
						
						<h3>The sidebars are no longer sticky?</h3>
						
						<p>Go to <em>Settings -> Theia Sticky Sidebar</em> click the <em>General</em> tab and in the <em>Sidebar CSS Selector box replace the existing text with <code>.gp-sidebar</code>.</p>
							
					</div>
					
				<?php } ?>	
						
			</div>
			
			<?php get_sidebar( 'left' ); ?>
		
		</div>
				
		<?php get_sidebar( 'right' ); ?>
		
	<div class="gp-clear"></div></div>
	
<?php endwhile; endif; ?>
	
<?php get_footer(); ?>