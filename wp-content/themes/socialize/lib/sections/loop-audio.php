<div class="gp-post-format-audio-content gp-image-above">

	<?php 
	
	$gp_mp3 = '';
	$gp_ogg = '';

	if ( get_post_meta( get_the_ID(), 'audio_mp3_url', true ) ) {
		$gp_mp3 = get_post_meta( get_the_ID(), 'audio_mp3_url', true );
		$gp_mp3 = $gp_mp3['url'];
	}

	if ( get_post_meta( get_the_ID(), 'audio_ogg_url', true ) ) {
		$gp_ogg = get_post_meta( get_the_ID(), 'audio_ogg_url', true );
		$gp_ogg = $gp_ogg['url'];
	}
			
	echo do_shortcode( '[audio mp3="' . esc_url( $gp_mp3 ) . '" ogg="' . esc_url( $gp_ogg ) . '"][/audio]' ); ?>
	
</div>