<?php

/*--------------------------------------------------------------
BuddyPress Groups
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_bp_groups' ) ) {

	function socialize_bp_groups( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'title' => esc_html__( 'Groups', 'socialize' ),
			'max_groups' => 5,
			'group_default' => 'popular',
			'link_title' => '',			
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',	
		), $atts ) );
		
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
		
			wp_enqueue_script( 'groups_widget_groups_list-js', buddypress()->plugin_url . "bp-groups/js/widget-groups.min.js", array( 'jquery' ), bp_get_version() );
		
			$gp_type = 'BP_Groups_Widget';
		
			// Title color
			if ( $title_color ) {
				$gp_title_color = ' style="border-color: ' . esc_attr( $title_color ) . '"';
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
}
add_shortcode( 'bp_groups', 'socialize_bp_groups' );


/*--------------------------------------------------------------
BuddyPress Members
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_bp_members' ) ) {

	function socialize_bp_members( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'title' => esc_html__( 'Members', 'socialize' ),
			'max_members' => 5,
			'member_default' => 'active',
			'link_title' => '',			
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',	
		), $atts ) );
		
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'members' ) ) {
		
			wp_enqueue_script( 'bp-widget-members' );
		
			$gp_type = 'BP_Core_Members_Widget';
		
			// Title color
			if ( $title_color ) {
				$gp_title_color = ' style="border-color: ' . esc_attr( $title_color ) . '"';
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
}
add_shortcode( 'bp_members', 'socialize_bp_members' );


/*--------------------------------------------------------------
BuddyPress Friends
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_bp_friends' ) ) {

	function socialize_bp_friends( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'title' => esc_html__( 'Friends', 'socialize' ),
			'max_friends' => 5,
			'friend_default' => 'active',
			'link_title' => '',			
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',	
		), $atts ) );
		
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'friends' ) ) {
		
			$gp_type = 'BP_Core_Friends_Widget';
		
			// Title color
			if ( $title_color ) {
				$gp_title_color = ' style="border-color: ' . esc_attr( $title_color ) . '"';
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
}
add_shortcode( 'bp_friends', 'socialize_bp_friends' );


/*--------------------------------------------------------------
BuddyPress Recently Active Members
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_bp_recently_active_members' ) ) {

	function socialize_bp_recently_active_members( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'title' => esc_html__( 'Recently Active Members', 'socialize' ),
			'max_members' => 16,			
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',	
		), $atts ) );
		
		$gp_type = 'BP_Core_Recently_Active_Widget';
		
		// Title color
		if ( $title_color ) {
			$gp_title_color = ' style="border-color: ' . esc_attr( $title_color ) . '"';
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
add_shortcode( 'bp_recently_active_members', 'socialize_bp_recently_active_members' );


/*--------------------------------------------------------------
BuddyPress Who's Online
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_bp_whos_online' ) ) {

	function socialize_bp_whos_online( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'title' => esc_html__( 'Who\'s Online', 'socialize' ),
			'max_members' => 16,			
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',	
		), $atts ) );
		
		$gp_type = 'BP_Core_Whos_Online_Widget';
		
		// Title color
		if ( $title_color ) {
			$gp_title_color = ' style="border-color: ' . esc_attr( $title_color ) . '"';
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
add_shortcode( 'bp_whos_online', 'socialize_bp_whos_online' );

?>