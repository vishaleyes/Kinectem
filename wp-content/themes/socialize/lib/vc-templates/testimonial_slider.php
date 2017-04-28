<?php 

extract( shortcode_atts( array( 
	'effect'  => 'slide',
	'buttons' => 'true',
	'speed'   => '0',
	'classes' => ''
 ), $atts ) );

wp_enqueue_script( 'gp-flexslider' );

// Unique Name
STATIC $gp_i = 0;
$gp_i++;
$gp_name = 'testimonial_slider_' . $gp_i;

ob_start(); ?>

<div id="<?php echo sanitize_html_class( $gp_name ); ?>" class="gp-testimonial-slider gp-slider <?php echo esc_attr( $classes ); ?>">
	<ul class="slides">
		<?php echo do_shortcode( $content ); ?>
	</ul>
</div>

<?php

$gp_output_string = ob_get_contents();
ob_end_clean(); 
echo wp_kses_post( $gp_output_string );

if ( ! is_admin() ) { ?> 
	<script>
	jQuery( document ).ready( function( $ ) {
		'use strict';
		$( '#<?php echo sanitize_html_class( $gp_name ); ?>.gp-slider' ).flexslider( { 
			animation: '<?php echo esc_attr( $effect ); ?>',
			slideshowSpeed: <?php if ( $speed == 0 ) { echo '9999999'; } else { echo absint( $speed ) * 1000; } ?>,
			animationSpeed: 600,
			smoothHeight: true,   
			directionNav: false,			
			controlNav: <?php if ( $buttons == 'true' ) { ?>true<?php } else { ?>false<?php } ?>,				
			pauseOnAction: true, 
			pauseOnHover: false
		});
	});
	</script>
<?php } ?>