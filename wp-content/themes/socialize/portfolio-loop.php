<?php global $socialize;

// Portfolio Categories
$gp_terms = get_the_terms( get_the_ID(), 'gp_portfolios' );
if ( isset( $GLOBALS['socialize_cats'] ) ) {
	$gp_cat_array = explode( ',', $GLOBALS['socialize_cats'] );
}
$gp_portfolio_cats = null;
if ( ! empty( $gp_terms ) ) {
	foreach ( $gp_terms as $gp_term ) {
		if ( ! empty( $gp_cat_array[0] ) ) {
			foreach( $gp_cat_array as $gp_cat ) {
				if ( $gp_term->term_id == $gp_cat ) {		
					$gp_portfolio_cats .= sanitize_title( $gp_term->slug ) . ' ';
				}
			}
		} else {
			$gp_portfolio_cats .= sanitize_title( $gp_term->slug ) . ' ';
		}	
	}
} ?>

<section <?php post_class( 'gp-portfolio-item ' . $gp_portfolio_cats . $socialize['portfolio_item_image_size'] ); ?> data-portfolio-cat="<?php echo esc_attr( $gp_portfolio_cats ); ?>" itemscope itemtype="http://schema.org/Article">

	<?php if ( has_post_thumbnail() ) { ?>

		<div class="gp-post-thumbnail gp-featured-content gp-loop-image">
			
			<?php if ( $GLOBALS['socialize_format'] != 'gp-portfolio-masonry' ) {
			
				$gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 700, 500, true, false, true );
			
			} elseif ( $socialize['portfolio_item_image_size'] == 'gp-narrow' ) {
			
				$gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 1000, 500, true, false, true );
				
			} elseif ( $socialize['portfolio_item_image_size'] == 'gp-tall' ) {
			
				$gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 500, 1000, true, false, true );
				
			} else {
			
				$gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 500, 500, true, false, true );						
			
			} ?>
			
			<?php if ( $socialize['retina'] == 'gp-retina' ) {
				$gp_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $gp_image[1] * 2, $gp_image[2] * 2, true, true, true );
			} else {
				$gp_retina = '';
			} ?>
				
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); } else { the_title_attribute(); } ?>" class="gp-post-image" itemprop="image" />			
			</a>
			
		</div>

	<?php } ?>

	<?php if ( $GLOBALS['socialize_format'] != 'gp-portfolio-masonry' ) { ?>

		<div class="gp-loop-header">
			<h2 class="gp-loop-title" itemprop="headline"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
		</div>
	
	<?php } ?>
			
</section>