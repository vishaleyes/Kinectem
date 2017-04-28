<?php

/*--------------------------------------------------------------
Statistics
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_statistics' ) ) {

	function socialize_statistics( $atts, $content = null ) {
	
		extract( shortcode_atts( array(
			'widget_title' => '',
			'posts' => '',
			'comments' => '',
			'blogs' => '',
			'activity' => '',
			'members' => '',
			'groups' => '',
			'forums' => '',
			'topics' => '',
			'icon_color' => '#e93100',
			'classes' => '',
			'title_format' => 'gp-standard-title',
			'title_color' => '#E93100',	
			'icon' => '',
		), $atts ) );
		
		global $socialize;
		
		// Unique Name	
		STATIC $gp_i = 0;
		$gp_i++;
		$gp_name = 'gp_statistics_wrapper_' . $gp_i;

		// Get activity count	
		if ( ! function_exists( 'socialize_bp_activity_updates' ) ) {
			function socialize_bp_activity_updates() {
				global $bp, $wpdb;
				if ( ! $gp_count = wp_cache_get( 'gp_bp_activity_updates', 'bp' ) ) {
					$gp_count = $wpdb->get_var( $wpdb->prepare( "SELECT count(a.id) FROM {$bp->activity->table_name} a WHERE type = %s AND a.component = '{$bp->activity->id}'", 'activity_update' ) );
					if ( ! $gp_count ) {
						$gp_count == 0;
					}	
					if ( ! empty( $gp_count ) ) {
						wp_cache_set( 'gp_bp_activity_updates', $gp_count, 'bp' );
					}	
				}
				return $gp_count;
			}
		}	
		
		if ( ! function_exists( 'socialize_bp_activity_updates_delete_clear_cache' ) ) {
			function socialize_bp_activity_updates_delete_clear_cache( $gp_args ) {
				if ( $gp_args['type'] && $gp_args['type'] == 'activity_update' )
					wp_cache_delete( 'gp_bp_activity_updates' );
			}
		}	
		add_action( 'bp_activity_delete', 'socialize_bp_activity_updates_delete_clear_cache' );

		if ( ! function_exists( 'socialize_bp_activity_updates_add_clear_cache' ) ) {
			function socialize_bp_activity_updates_add_clear_cache() {
				wp_cache_delete( 'gp_bp_activity_updates' );
			}
		}
		add_action( 'bp_activity_posted_update', 'socialize_bp_activity_updates_add_clear_cache' );
		
		// Statistics icon background color
		if ( $icon_color ) {
			echo '<style>.gp-statistics-wrapper .gp-stats > div:before{background-color: ' . esc_attr( $icon_color ) . '}</style>';
		}
				
		ob_start(); ?>

		<div id="<?php echo sanitize_html_class( $gp_name ); ?>" class="gp-statistics-wrapper gp-vc-element <?php echo esc_attr( $classes ); ?>">
	
			<?php if ( $widget_title ) { ?>
				<h3 class="widgettitle <?php echo $title_format; ?>"<?php if ( $title_color ) { ?> style="background-color: <?php echo esc_attr( $title_color ); ?>; border-color: <?php echo esc_attr( $title_color ); ?>"<?php } ?>>
					<?php if ( $icon ) { ?><i class="fa <?php echo sanitize_html_class( $icon ); ?>"></i><?php } ?>
					<span><?php echo esc_attr( $widget_title ); ?></span>
					<div class="gp-triangle"></div>
				</h3>
			<?php } ?>

			<div class="gp-stats">
			
				<?php if ( $posts == '1' ) { ?>
					<div class="gp-post-stats">
						<?php $gp_count_posts = wp_count_posts(); ?>
						<span class="gp-stat-details">
							<span class="gp-stat-title"><?php esc_html_e( 'Posts', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( $gp_count_posts->publish ); ?></span>
						</span>	
					</div>	
				<?php } ?>

				<?php if ( $comments == '1' ) { ?>
					<div class="gp-comment-stats">
						<?php $gp_comments_count = wp_count_comments(); ?>
						<span class="gp-stat-details">
							<span class="gp-stat-title"><?php esc_html_e( 'Comments', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( $gp_comments_count->approved ); ?></span>
						</span>		
					</div>	
				<?php } ?>

				<?php if ( is_multisite() && $blogs == '1' ) { ?>
					<div class="gp-blog-stats">
						<span class="gp-stat-details">
							<span class="gp-stat-title"><?php esc_html_e( 'Blogs', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( get_blog_count() ); ?></span>
						</span>		
					</div>	
				<?php } ?>

				<?php if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) && $activity == '1' ) { ?>
					<div class="gp-activity-update-stats">
						<span class="gp-stat-details">
							<span class="gp-stat-title"><?php esc_html_e( 'Activity', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( socialize_bp_activity_updates() ); ?></span>
						</span>		
					</div>	
				<?php } ?>
															
				<?php if ( function_exists( 'bp_is_active' ) && $members == '1' ) { ?>
					<div class="gp-member-stats">
						<span class="gp-stat-details">
							<span class="gp-stat-title"><?php esc_html_e( 'Members', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( bp_get_total_site_member_count() ); ?></span>
						</span>	
					</div>	
				<?php } ?>

				<?php if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) && $groups == '1' ) { ?>
					<div class="gp-group-stats">
						<span class="gp-stat-details">
							<span class="gp-stat-title"><?php esc_html_e( 'Groups', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( groups_get_total_group_count() ); ?></span>
						</span>	
					</div>	
				<?php } ?>

				<?php if ( class_exists( 'bbPress' ) && $forums == '1' ) { ?>
					<div class="gp-forum-stats">
						<span class="gp-stat-details">
							<?php $gp_count_posts = wp_count_posts( 'forum' ); ?>
							<span class="gp-stat-title"><?php esc_html_e( 'Forums', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( $gp_count_posts->publish ); ?></span>
						</span>		
					</div>	
				<?php } ?>

				<?php if ( class_exists( 'bbPress' ) && $topics == '1' ) { ?>
					<div class="gp-topic-stats">
						<span class="gp-stat-details">
							<?php $gp_count_posts = wp_count_posts( 'topic' ); ?>
							<span class="gp-stat-title"><?php esc_html_e( 'Topics', 'socialize' ); ?></span>
							<span class="gp-stat-count"><?php echo absint( $gp_count_posts->publish ); ?></span>
						</span>	
					</div>	
				<?php } ?>

			</div>
															
		</div>
						
		<?php

		$gp_output_string = ob_get_contents();
		ob_end_clean();
		return $gp_output_string;

	}

}
add_shortcode( 'statistics', 'socialize_statistics' );

?>