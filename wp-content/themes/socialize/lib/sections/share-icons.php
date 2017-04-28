<?php global $socialize; ?>

<div id="gp-share-icons">

	<h3><?php esc_html_e( 'Share This Post', 'socialize' ); ?></h3>

	<div class="gp-share-icons">
	
		<a href="http://twitter.com/share?text=<?php urlencode( the_title() ); ?>&url=<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Tweet This Post', 'socialize' ); ?>" class="gp-share-twitter" onclick="window.open(this.href, 'mywin', 'left=50,top=50,width=600,height=350,toolbar=0'); return false;"></a>	
	
		<a href="http://www.facebook.com/sharer.php?u=<?php urlencode( the_permalink() ); ?>&t=<?php the_title(); ?>" title="<?php esc_attr_e( 'Share on Facebook', 'socialize' ); ?>" class="gp-share-facebook" onclick="window.open(this.href, 'mywin',
	'left=50,top=50,width=600,height=350,toolbar=0'); return false;"></a>
	
		<a href="https://plusone.google.com/_/+1/confirm?hl=en-US&url=<?php urlencode( the_permalink() ) ?>" title="<?php esc_attr_e( 'Share on Google+', 'socialize' ); ?>" class="gp-share-google-plus" onclick="window.open(this.href, 'mywin',
	'left=50,top=50,width=600,height=350,toolbar=0'); return false;"></a>

		<?php if ( isset( $GLOBALS['socialize_page_header_bg_css'] ) OR ( has_post_thumbnail() && ( isset( $GLOBALS['socialize_featured_image'] ) && $GLOBALS['socialize_featured_image'] == 'enabled' ) )  ) {	
			if ( isset( $GLOBALS['socialize_page_header_bg_css'] ) && $GLOBALS['socialize_page_header_bg_css'] ) {
				preg_match_all( '~\bbackground(-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $GLOBALS['socialize_page_header_bg_css'], $matches );
				$gp_image = $matches['image'];
				$pinterest_image = $gp_image[0];
			} else {
				$pinterest_image = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
			} ?>
			<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->ID)); ?>&media=<?php echo esc_url( $pinterest_image ); ?>&description=<?php the_title(); ?>" count-layout="vertical" class="gp-share-pinterest" target="_blank"></a>	
		<?php } ?>
	
	</div>
	
</div>