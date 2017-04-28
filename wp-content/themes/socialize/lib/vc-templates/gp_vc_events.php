<?php

/*--------------------------------------------------------------
Events List
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_events_list' ) ) {

	function socialize_events_list( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'title' => '',
			'limit' => '5',			
			'no_upcoming_events' => '1',
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',	
		), $atts ) );
		
		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			$gp_type = 'Tribe__Events__Pro__Advanced_List_Widget';
		} else {
			$gp_type = 'Tribe__Events__List_Widget';
		}
				
		// Title color
		if ( $title_color ) {
			$gp_title_color = ' style="background-color: ' . esc_attr( $title_color ) . '; border-color: ' . esc_attr( $title_color ) . '"';
		} else {
			$gp_title_color = '';
		}
		
		// Add icon
		if ( $icon ) {
			$gp_title_icon = '<i class="fa ' . sanitize_html_class( $icon ) . '"></i>';
		} else {
			$gp_title_icon = '';
		}
				
		$gp_args = array(
			'before_title' => '<h3 class="widgettitle ' . $title_format . '"' . $gp_title_color . '>' . wp_kses_post( $gp_title_icon ) . '<span>',
			'after_title' => '</span><div class="gp-triangle"></div></h3>',
		);

		ob_start();

		the_widget( $gp_type, $atts, $gp_args );
		$gp_output_string = ob_get_clean();
		return $gp_output_string;
	}
}
add_shortcode( 'events_list', 'socialize_events_list' );

?>