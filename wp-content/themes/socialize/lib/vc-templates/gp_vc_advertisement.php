<?php

if ( ! function_exists( 'socialize_advertisement' ) ) {

	function socialize_advertisement( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'classes' => '',
		), $atts ) );
		
		// Unique Name	
		STATIC $gp_i = 0;
		$gp_i++;
		$gp_name = 'gp_advertisement_' . $gp_i;
		
		ob_start(); ?>
	
			<div id="<?php echo sanitize_html_class( $gp_name ); ?>" class="gp-advertisement-wrapper widget <?php echo esc_attr( $classes ); ?>">
				<?php echo wp_kses_post( $content ); ?>
			</div>
					
		<?php 

		$gp_output_string = ob_get_contents();
		ob_end_clean();
		return $gp_output_string;

	}
}
add_shortcode( 'advertisement', 'socialize_advertisement' );

?>