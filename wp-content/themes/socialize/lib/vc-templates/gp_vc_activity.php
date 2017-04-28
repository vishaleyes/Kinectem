<?php

if ( ! function_exists( 'socialize_activity' ) ) {

	function socialize_activity( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'widget_title' => esc_html__( 'Latest Activity', 'socialize' ),
			'post_form' => 'enabled',
			'scope' => '',
			'display_comments' => 'threaded',
			'allow_comments' => 'gp-comments-enabled',		
			'include' => '',
			'order' => 'DESC',
			'per_page' => '5',
			'max' => '',
			'show_hidden' => '',
			'search_terms' => '',
			'user_id' => '',	
			'object' => '',
			'action' => '',
			'primary_id' => '',
			'secondary_id' => '',	
			'see_all' => 'disabled',
			'see_all_link' => '',
			'see_all_text' => esc_html__( 'See All Items', 'socialize' ),	
			'classes' => '',
			'title_format' => 'gp-fancy-title',
			'title_color' => '#E93100',		
			'icon' => '',	
		), $atts ) );
				
		// Add global variable for per page for activity loop function		
		if ( ! update_option( 'activity_per_page', $per_page ) ) {
			add_option( 'activity_per_page', $per_page );
		} else { 
			update_option( 'activity_per_page', $per_page );
		}
		
		global $socialize;
		
		// Unique Name	
		STATIC $gp_i = 0;
		$gp_i++;
		$gp_name = 'gp_activity_wrapper_' . $gp_i;
		
		ob_start(); ?>
	
		<?php if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) ) { ?>
			
			<div id="buddypress">
			
				<div id="<?php echo sanitize_html_class( $gp_name ); ?>" class="gp-activity-wrapper gp-vc-element activity <?php echo esc_attr( $classes ); ?> <?php echo sanitize_html_class( $allow_comments ); ?>">

					<?php if ( $widget_title ) { ?>
						<h3 class="widgettitle <?php echo $title_format; ?>"<?php if ( $title_color ) { ?> style="background-color: <?php echo esc_attr( $title_color ); ?>; border-color: <?php echo esc_attr( $title_color ); ?>"<?php } ?>>		
							<?php if ( $icon ) { ?><i class="fa <?php echo sanitize_html_class( $icon ); ?>"></i><?php } ?>
							<span><?php echo esc_attr( $widget_title ); ?></span>
							<div class="gp-triangle"></div>
							
							<?php if ( $see_all == 'enabled' ) { ?>
								<span class="gp-see-all-link"><a href="<?php echo esc_url( $see_all_link ); ?>"><?php echo esc_attr( $see_all_text ); ?><i></i></a></span>
							<?php } ?>
						</h3>
					<?php } ?>
					
				 	<?php if ( is_user_logged_in() && $post_form == 'enabled' ) { bp_get_template_part( 'activity/post-form' ); } ?>
			
					<?php
			
					do_action( 'bp_before_activity_loop' ); ?>
			
					<?php
					
					if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . "&scope=$scope&display_comments=$display_comments&include=$include&sort=$order&per_page=$per_page&max=$max&show_hidden=$show_hidden&search_terms=$search_terms&user_id=$user_id&object=$object&action=$action&primary_id=$primary_id&secondary_id=$secondary_id&count_total=count_query&page_arg=actsc" ) ) : ?>
			
						<?php if ( empty( $_POST['page'] ) ) : ?>
			
							<ul id="activity-stream" class="gp-inner-loop activity-list item-list">
			
						<?php endif; ?>
			
						<?php while ( bp_activities() ) : bp_the_activity(); ?>

							<?php bp_get_template_part( 'activity/entry' ); ?>

						<?php endwhile; ?>
					
						<?php if ( bp_activity_has_more_items() ) : ?>

							<?php if ( function_exists( 'bp_activity_load_more_link' ) ) { ?>
							
								<li class="load-more">
									<a href="<?php bp_activity_load_more_link(); ?>"><?php esc_html_e( 'Load More', 'socialize' ); ?></a>
								</li>
							
							<?php } ?>
							
						<?php endif; ?>
					
						<?php if ( empty( $_POST['page'] ) ) : ?>
							
							</ul>
						
						<?php endif; ?>
				
					<?php else : ?>
			
						<div id="message" class="info">
							<p><?php esc_html_e( 'Sorry, there was no activity found. Please try a different filter.', 'socialize' ); ?></p>
						</div>
			
					<?php endif; ?>
			
					<?php do_action( 'bp_after_activity_loop' ); ?>
			
					<?php if ( empty( $_POST['page'] ) ) : ?>

						<form name="activity-loop-form" id="activity-loop-form" method="post">

							<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>

						</form>

					<?php endif; ?>
	
				</div>
			
			</div>
		
		<?php } ?>
			
		<?php 

		$gp_output_string = ob_get_contents();
		ob_end_clean();
		return $gp_output_string;

	}
}
add_shortcode( 'activity', 'socialize_activity' );

// Change number of items per page in activity loop
if ( ! function_exists( 'socialize_activity_loop' ) ) {
	function socialize_activity_loop( $query_string, $object ) {
		if ( ! empty( $query_string ) ) {
			$query_string .= '&';
		}
		if ( bp_is_blog_page() ) {
			$query_string .= 'per_page=' . get_option( 'activity_per_page' );
		} else {
			$query_string .= 'per_page=20';	
		}
		return $query_string;	
	}
}
add_filter( 'bp_ajax_querystring', 'socialize_activity_loop', 20, 2 );

?>