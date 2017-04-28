<div class="gp-post-format-video-content gp-entry-video-wrapper">

	<div class="gp-entry-video">

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
		
	<?php if ( $GLOBALS['socialize_title'] == 'enabled' ) { ?>	

		<header class="gp-entry-header">	

			<?php if ( $GLOBALS['socialize_title'] == 'enabled' ) { ?>	
				<h1 class="gp-entry-title<?php if ( ! empty( $GLOBALS['socialize_subtitle'] ) ) { ?> has-subtitle<?php } ?>" itemprop="headline">
					<?php if ( ! empty( $GLOBALS['socialize_custom_title'] ) ) { echo esc_attr( $GLOBALS['socialize_custom_title'] ); } else { the_title(); } ?>
				</h1>
			<?php } ?>	

			<?php if ( ! empty( $GLOBALS['socialize_subtitle'] ) ) { ?>
				<h3 class="gp-subtitle"><?php echo esc_attr( $GLOBALS['socialize_subtitle'] ); ?></h3>
			<?php } ?>
		
			<?php if ( $GLOBALS['socialize_meta_cats'] == '1' ) { get_template_part( 'lib/sections/entry', 'cats' ); } ?>				
					
			<?php get_template_part( 'lib/sections/entry', 'meta' ); ?>
			
			<?php if ( get_post_meta( get_the_ID(), 'video_description', true ) ) { ?>
				<div class="gp-video-description">
					<?php echo get_post_meta( get_the_ID(), 'video_description', true ); ?>	
				</div>			
			<?php } ?>		

		</header>
	
	<?php } ?>	

</div>