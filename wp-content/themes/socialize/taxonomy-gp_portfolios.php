<?php get_header(); 

wp_enqueue_script( 'gp-isotope' );
wp_enqueue_script( 'gp-images-loaded' );
	
?>

<?php if ( $GLOBALS['socialize_page_header'] == 'gp-fullwidth-page-header' OR $GLOBALS['socialize_page_header'] == 'gp-full-page-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

<div id="gp-content-wrapper" class="gp-container">

	<?php if ( $GLOBALS['socialize_page_header'] == 'gp-large-page-header' ) { socialize_page_header( get_the_ID() ); } ?>

	<div id="gp-left-column">

		<div id="gp-content">

			<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="gp-breadcrumbs">', '</div>' ); } ?>
				
			<?php if ( $GLOBALS['socialize_title'] == 'enabled' ) { ?>		
				<header class="gp-entry-header">
					<h1 class="gp-entry-title" itemprop="headline"><?php single_cat_title(); ?></h1>
				</header>
			<?php } ?>
					
			<div id="gp-portfolio" class="gp-portfolio-wrapper <?php echo sanitize_html_class( $GLOBALS['socialize_format'] ); ?>">

				<?php if ( have_posts() ) : ?>

					<?php if ( $GLOBALS['socialize_filter'] == 'enabled' ) { ?>
						<div id="gp-portfolio-filters" class="gp-portfolio-filters">
							<ul>
							   <li><a href="#" data-filter="*" class="gp-active"><?php echo esc_html__( 'All', 'socialize' ); ?></a></li>
								<?php 
								$gp_terms = get_terms( 'gp_portfolios' );
								if ( !empty( $gp_terms ) ) {
									foreach ( $gp_terms as $gp_term ) {
										echo '<li><a href="#" data-filter=".' . sanitize_title( $gp_term->slug ) . '">' . esc_attr( $gp_term->name ) . '</a></li>';
									}
								}
								?>
							</ul>
						</div>
					<?php } ?>
		
					<div class="gp-inner-loop">
			
						<?php while ( have_posts() ) : the_post(); ?>

							<?php get_template_part( 'portfolio', 'loop' ); ?>

						<?php endwhile; ?>
			
					</div>

					<?php echo socialize_pagination( $wp_query->max_num_pages ); ?>

				<?php else : ?>

					<span class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'socialize' ); ?></span>

				<?php endif; ?>
		
			</div>

			<script>		
			jQuery( document ).ready( function( $ ) {

				'use strict';

				var container = $( '#gp-portfolio .gp-inner-loop' );
				var element = container;

				if ( container.find( 'img' ).length == 0 ) {
					element = $( 'body' );
				}	

				imagesLoaded( element, function( instance ) {

					container.isotope({
						itemSelector: '.gp-portfolio-item',
						filter: '*',
						masonry: {
							<?php if ( $GLOBALS['socialize_format'] != 'gp-portfolio-masonry' ) { ?>columnWidth: container.find( '.gp-portfolio-item' )[0],
							gutter: 20<?php } ?>
						}
					});

					container.animate( { 'opacity': 1 }, 1300 );
					$( 'ul.page-numbers' ).animate( { 'opacity': 1 }, 1300 );

				});

				$( '#gp-portfolio-filters ul li a' ).click( function() {

					var selector = $( this ).attr( 'data-filter' );
					container.isotope( { filter: selector } );

					$( '#gp-portfolio-filters ul li a' ).removeClass( 'gp-active' );
					$( this ).addClass( 'gp-active' );

					return false;

				});


				/*--------------------------------------------------------------
				Remove portfolio filters not found on current page
				--------------------------------------------------------------*/

				if ( $( 'div' ).hasClass( 'gp-portfolio-filters' ) ) {

					var isotopeCatArr = [];
					var $portfolioCatCount = 0;
					$( '#gp-portfolio-filters ul li' ).each( function( i ) {
						if ( $( this ).find( 'a' ).length > 0 ) {
							isotopeCatArr[$portfolioCatCount] = $( this ).find( 'a' ).attr( 'data-filter' ).substring( 1 );	
							$portfolioCatCount++;
						}
					});

					isotopeCatArr.shift();

					var itemCats = '';

					$( '#gp-portfolio .gp-inner-loop > .gp-portfolio-item' ).each( function( i ) {
						itemCats += $( this ).attr( 'data-portfolio-cat' );
					});
					itemCats = itemCats.split( ' ' );

					itemCats.pop();
	
					itemCats = $.unique( itemCats );

					var notFoundCats = [];
					$.grep( isotopeCatArr, function( el ) {
						if ( $.inArray(el, itemCats ) == -1 ) {
							notFoundCats.push( el  );
						}
					});

					if(notFoundCats.length != 0 ) {
						$( '#gp-portfolio-filters ul li' ).each( function() {
							if ( $( this ).find( 'a' ).length > 0 ) {
								if( $.inArray( $( this ).find( 'a' ).attr( 'data-filter' ).substring( 1 ), notFoundCats ) != -1 ) {
									$( this ).hide();
								}
							}
				});
					}

				}

			});			
			</script>			

		</div>

		<?php get_sidebar( 'left' ); ?>
	
	</div>
	
	<?php get_sidebar( 'right' ); ?>
		
<div class="gp-clear"></div></div>

<?php get_footer(); ?>