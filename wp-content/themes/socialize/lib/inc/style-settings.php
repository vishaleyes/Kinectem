<?php

/*--------------------------------------------------------------
Custom Classes
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_body_classes' ) ) {
	function socialize_body_classes( $gp_classes ) {
		global $socialize, $post;
		$gp_classes[] = 'gp-theme';
		$gp_classes[] = $socialize['theme_layout'];
		$gp_classes[] = $socialize['responsive'];
		$gp_classes[] = $socialize['retina'];
		$gp_classes[] = $socialize['smooth_scrolling'];
		$gp_classes[] = $socialize['back_to_top'];
		$gp_classes[] = $socialize['fixed_header'];
		$gp_classes[] = $socialize['header_layout'];
		$gp_classes[] = $socialize['cart_button'];
		$gp_classes[] = $socialize['search_button'];
		$gp_classes[] = $socialize['profile_button'];
		$gp_classes[] = $socialize['small_header'];
		$gp_classes[] = $GLOBALS['socialize_page_header'];
		$gp_classes[] = $GLOBALS['socialize_layout'];	
		if ( is_page_template( 'homepage-template.php' ) ) {
			$gp_classes[] = 'gp-homepage';
		}	
		return $gp_classes;
	}
}
add_filter( 'body_class', 'socialize_body_classes' );


/*--------------------------------------------------------------
Inline Styling
--------------------------------------------------------------*/

global $socialize;
					
$gp_custom_css = '';

$gp_custom_css .= 

'#gp-main-header {height:' . $socialize['desktop_header_height']['height'] . 'px;}' . 

'.gp-header-standard #gp-logo {padding:' . ( $socialize['desktop_header_height']['height'] - $socialize['desktop_logo_dimensions']['height'] ) / 2 . 'px 0;}' . 

'.gp-header-standard #gp-primary-main-nav .menu > li > a{padding:' . ( $socialize['desktop_header_height']['height'] - ( $socialize['primary_nav_typography']['line-height'] + $socialize['primary_nav_link_border_hover']['border-top'] ) ) / 2 . 'px 0;}
.gp-header-standard #gp-cart-button,.gp-header-standard #gp-search-button,.gp-header-standard #gp-profile-button{padding:' . ( $socialize['desktop_header_height']['height'] - 18 ) / 2 . 'px 0;}' .

'.gp-nav .menu > .gp-standard-menu > .sub-menu > li:hover > a{color:' . $socialize['dropdown_link']['hover'] . '}' .

'.gp-theme li:hover .gp-primary-dropdown-icon{color:' . $socialize['primary_dropdown_icon']['hover'] . '}' .

'.gp-theme .sub-menu li:hover .gp-secondary-dropdown-icon{color:' . $socialize['secondary_dropdown_icon']['hover'] . '}' .

'.gp-header-centered #gp-cart-button,.gp-header-centered #gp-search-button,.gp-header-centered #gp-profile-button{line-height:' . ( $socialize['primary_nav_typography']['line-height'] + 2 ) . 'px;}' .

'.gp-header-standard #gp-secondary-main-nav .menu > li > a{padding:' . ( $socialize['desktop_header_height']['height'] - ( $socialize['secondary_nav_typography']['line-height'] + $socialize['secondary_nav_link_border_hover']['border-top'] ) ) / 2 . 'px 0;}' .

'.gp-header-centered #gp-secondary-main-nav .menu > li > a {line-height:' . $socialize['primary_nav_typography']['line-height'] . ';}' .

'.gp-active{color: ' . $socialize['general_link']['hover'] . ';}' .

'.gp-theme .widget.buddypress div.item-options a.selected:hover{color: ' . $socialize['widget_title_link']['regular'] . '!important;}' .

'@media only screen and (max-width: 1082px) {' .

	'.gp-header-standard #gp-primary-main-nav .menu > li > a {padding:' . ( $socialize['desktop_header_height']['height'] - ( 16 + $socialize['primary_nav_link_border_hover']['border-top'] ) ) / 2 . 'px 0;}' . 
	
	'.gp-header-standard #gp-cart-button,.gp-header-standard #gp-search-button,.gp-header-standard #gp-profile-button{padding:' . ( $socialize['desktop_header_height']['height'] - 18 ) / 2 . 'px 0;}' .
	
	'.gp-header-standard #gp-secondary-main-nav .menu > li > a{padding:' . ( $socialize['desktop_header_height']['height'] - ( 14 + $socialize['secondary_nav_link_border_hover']['border-top'] ) ) / 2 . 'px 0;}' .
	
'}' .

'@media only screen and (max-width: 1023px) {' .
	
	'.gp-responsive #gp-main-header {height:' . $socialize['mobile_header_height']['height'] . 'px;}' .
	
	'.gp-responsive #gp-logo {padding:' . ( $socialize['mobile_header_height']['height'] - $socialize['mobile_logo_dimensions']['height'] ) / 2 . 'px 0;}' .
	
	'.gp-responsive #gp-mobile-nav-button,.gp-responsive #gp-profile-button{padding:' . ( $socialize['mobile_header_height']['height'] - 18 ) / 2 . 'px 0;}' .
	
'}';
	
	
/*--------------------------------------------------------------
Custom CSS
--------------------------------------------------------------*/

if ( isset( $socialize['custom_css'] ) && ! empty( $socialize['custom_css'] ) ) {
	$gp_custom_css .= $socialize['custom_css'];
}

if ( ! empty( $gp_custom_css ) ) {
	echo '<style>' . $gp_custom_css . '</style>';
}

?>