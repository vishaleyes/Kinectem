<?php

if ( function_exists( 'bp_is_active' ) ) {

	// Load custom BuddyPress stylesheet
	if ( ! function_exists( 'socialize_bp_enqueue_styles' ) ) {	
		function socialize_bp_enqueue_styles() {
			wp_enqueue_style( 'gp-bp', socialize_css_uri . 'bp.css' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'socialize_bp_enqueue_styles' );

	// Default avatar dimensions	
	define( 'BP_AVATAR_THUMB_WIDTH', 58 );
	define( 'BP_AVATAR_THUMB_HEIGHT', 58 );
	define( 'BP_AVATAR_FULL_WIDTH', 210 );
	define( 'BP_AVATAR_FULL_HEIGHT', 210 );

	// Change default groups avatar	
	if ( ! function_exists( 'socialize_change_bp_group_avatar' ) ) {	
		function socialize_change_bp_group_avatar( $avatar ) {
			global $groups_template;
			if ( strpos( $avatar, 'group-avatars' ) ) {
				return $avatar;
			} else {
				return '<img src="'. socialize_images . 'default-group-avatar.png" class="avatar" alt="' . esc_attr( $groups_template->group->name ) . '" itemprop="image" />';
			}	
		}
	}
	add_filter( 'bp_get_group_avatar', 'socialize_change_bp_group_avatar', 1, 1 );

}

?>