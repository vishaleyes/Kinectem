<?php 

extract( shortcode_atts( array(
	'classes' => ''
), $atts ) );	

$gp_columns = explode( '[pricing_column', $content );
$gp_columns_num = count( $gp_columns );
$gp_columns_num = $gp_columns_num - 1;

switch ( $gp_columns_num ) {
	case '2' :
		$gp_column_class = 'gp-pricing-columns-2';
		break;
	case '3' :
		$gp_column_class = 'gp-pricing-columns-3';
		break;
	case '4' :
		$gp_column_class = 'gp-pricing-columns-4';
		break;	
	case '5' :
		$gp_column_class = 'gp-pricing-columns-5';
		break;
}

echo '<div class="gp-pricing-table '. sanitize_html_class( $gp_column_class ) . ' ' . esc_attr( $classes ) . '">' . do_shortcode( $content ) . '</div>';

?>