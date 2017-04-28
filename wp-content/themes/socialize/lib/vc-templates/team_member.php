<?php

extract( shortcode_atts( array( 
	'image_url' => '',
	'image_width' => '230',
	'image_height' => '230',
	'name' => '',
	'position' => '',
	'link' => '',
	'link_target' => '',
 ), $atts ) );

ob_start(); 

global $socialize, $columns, $socialize_counter;

$socialize_counter = $socialize_counter + 1;
if ( $socialize_counter %$columns == 1 ) {
	$gp_left_column = 'gp-left-column';
} else {
	$gp_left_column = '';
}

$gp_column_width = 100 / $columns;	
	
?>

<div class="gp-team-member <?php echo sanitize_html_class( $gp_left_column ); ?>" style="width: <?php echo floatval( $gp_column_width ); ?>%;">
	
	<?php if ( $image_url ) { ?>
		<?php $gp_image = aq_resize( wp_get_attachment_url( $image_url ), $image_width, $image_height, true, false, true ); ?>
		<?php if ( $socialize['retina'] == 'enabled' ) {
			$gp_retina = aq_resize( wp_get_attachment_url( $image_url ), $image_width * 2, $image_height * 2, true, true, true );
		} else {
			$gp_retina = '';
		} ?>

		<?php if ( $link != '' ) { ?><a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php } ?>
			
			<img src="<?php echo esc_url( $gp_image[0] ); ?>" data-rel="<?php echo esc_url( $gp_retina ); ?>" width="<?php echo absint( $gp_image[1] ); ?>" height="<?php echo absint( $gp_image[2] ); ?>" alt="<?php if ( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ) { echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); } else { the_title_attribute(); } ?>" class="gp-post-image gp-team-image" itemprop="image" />								
		
		<?php if ( $link != '' ) { ?></a><?php } ?>
										
	<?php } ?>
						
	<div class="gp-team-name"><?php echo esc_attr( $name ); ?></div>
	
	<div class="gp-team-position"><?php echo esc_attr( $position ); ?></div>
	
	<div class="gp-team-description"><?php echo wp_kses_post( $content ); ?></div>

</div>

<?php

$gp_output_string = ob_get_contents();
ob_end_clean(); 	
echo wp_kses_post( $gp_output_string );

?>