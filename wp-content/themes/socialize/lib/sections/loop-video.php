<div class="gp-post-format-video-content <?php echo sanitize_html_class( $GLOBALS['socialize_image_alignment'] ); ?>">

	<?php if ( get_post_meta( get_the_ID(), 'video_embed_url', true ) ) { ?>

		<?php global $wp_embed; ?>
		<?php echo $wp_embed->run_shortcode( '[embed width="' . absint( $GLOBALS['socialize_image_width'] ) . '" height="' . absint( $GLOBALS['socialize_image_height'] ) . '"]' . esc_url( get_post_meta( get_the_ID(), 'video_embed_url', true ) ) . '[/embed]' ); ?>

	<?php } else { 

		$gp_mp4 = '';
		$gp_m4v = '';
		$gp_webm = '';
		$gp_ogv = '';
		
		if ( get_post_meta( get_the_ID(), 'video_mp4_url', true ) ) {	
			$gp_mp4 = get_post_meta( get_the_ID(), 'video_mp4_url', true );
			$gp_mp4 = $gp_mp4['url'];
		}
	
		if ( get_post_meta( get_the_ID(), 'video_m4v_url', true ) ) {		
			$gp_m4v = get_post_meta( get_the_ID(), 'video_m4v_url', true );
			$gp_m4v = $gp_m4v['url'];
		}
	
		if ( get_post_meta( get_the_ID(), 'video_webm_url', true ) ) {	
			$gp_webm = get_post_meta( get_the_ID(), 'video_webm_url', true );
			$gp_webm = $gp_webm['url'];
		}
	
		if ( get_post_meta( get_the_ID(), 'video_ogv_url', true ) ) {	
			$gp_ogv = get_post_meta( get_the_ID(), 'video_ogv_url', true );
			$gp_ogv = $gp_ogv['url'];
		}
	
		?>

		<?php echo do_shortcode( '[video mp4="' . esc_url( $gp_mp4 ) . '" m4v="' . esc_url( $gp_m4v ) . '" webm="' . esc_url( $gp_webm ). '" ogv="' . esc_url( $gp_ogv ) . '"][/video]' ); ?>

	<?php } ?>

</div>