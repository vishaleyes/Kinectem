<?php 

extract( shortcode_atts( array(
	'title'            => esc_html__( 'Column Title', 'socialize' ), 
	'highlight'        => 'disabled',
	'highlight_text' => '',
	'price'            => '',
	'currency_symbol'  => '',
	'interval'         => '',
	'highlight_color' => '#f84103',
	'title_color' => '#f84103',
	'highlight_title_color' => '#fff',
	'background_color' => '#f7f7f7',	
	'highlight_background_color' => '#fff',
	'text_color' => '#747474',
	'border' => 'enabled',
	'border_color' => '#e7e7e7',
), $atts ) );

$border_class = null;

if ( $border == 'enabled' ) {
	$gp_border_class = 'price-column-border';
} else {
	$gp_border_class = '';
}

if ( $highlight == 'enabled' ) {
	$gp_bg_color = $highlight_background_color; 
} else {	
	$gp_bg_color = $background_color;	
}
	
ob_start(); ?>

<div class="gp-pricing-column <?php echo sanitize_html_class( $gp_border_class ); ?>" style="background-color: <?php echo esc_attr( $gp_bg_color ); ?>; border-color: <?php echo esc_attr( $border_color ); ?>; color: <?php echo esc_attr( $text_color ); ?>;">

	<div class="gp-pricing-column-inner">

		<?php if ( $highlight == 'enabled' ) { ?>
	
			<div class="gp-pricing-column-highlight-text" style="background-color: <?php echo esc_attr( $highlight_color ); ?>; color: <?php echo esc_attr( $highlight_title_color ); ?>;">
				<?php echo esc_attr( $highlight_text ); ?>		
			</div>
	
			<div class="gp-pricing-column-title" style="background-color: <?php echo esc_attr( $highlight_color ); ?>; color: <?php echo esc_attr( $highlight_title_color ); ?>;">
				<?php echo esc_attr( $title ); ?>
			</div>

		<?php } else { ?>

			<div class="gp-pricing-column-title" style="color: <?php echo esc_attr( $title_color ); ?>;">
				<?php echo esc_attr( $title ); ?>
			</div>
	
		<?php } ?>

		<div class="gp-pricing-column-costs" style="border-color: <?php echo esc_attr( $border_color ); ?>;">
			<span class="gp-pricing-column-symbol"><?php echo esc_attr( $currency_symbol ); ?></span>
			<span class="gp-pricing-column-price"><?php echo esc_attr( $price ); ?></span>
			<div class="gp-pricing-column-interval"><?php echo esc_attr( $interval ); ?></div>
		</div>

		<div class="gp-pricing-column-content" style="border-color: <?php echo esc_attr( $border_color ); ?>;"><?php echo do_shortcode( $content ); ?></div>

	</div>

</div>

<div class="gp-pricing-row"></div>

<?php

$gp_output_string = ob_get_contents();
ob_end_clean(); 
echo wp_kses_post( $gp_output_string );

?>