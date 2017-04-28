<?php

/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>
<script type="text/javascript">
var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
jQuery(document).ready(function() {
	jQuery('.follower').click(function() { 
        var groupid = jQuery(this).data('groupid');
        var authorid = jQuery(this).data('authorid');
        jQuery.ajax({
			    data: {action: 'follower_save', authorid:authorid,groupid:groupid},
				type: "POST",
				url: ajaxurl,
				cache: false,
				success: function(data) { 
				 window.location.href=window.location.href;
				}
				});
    });   
	jQuery('.unfollower').click(function() { 
        var groupid = jQuery(this).data('groupid');
        var authorid = jQuery(this).data('authorid');
        jQuery.ajax({
			    data: {action: 'follower_delete', authorid:authorid,groupid:groupid},
				type: "POST",
				url: ajaxurl,
				cache: false,
				success: function(data) { 					
				 window.location.href=window.location.href;
				}
				});
    });
  });
</script>
<?php

/**
 * Fires before the display of groups from the groups loop.
 *
 * @since BuddyPress (1.2.0)
 */
do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="group-dir-count-top">

			<?php bp_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-top">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the listing of the groups list.
	 *
	 * @since BuddyPress (1.1.0)
	 */
	do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li <?php bp_group_class(); ?>>
		
			<div class="gp-group-box">
			
				<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
					<div class="item-avatar">				
						<div class="action">

							<?php

							/**
							 * Fires inside the action section of an individual group listing item.
							 *
							 * @since BuddyPress (1.1.0)
							 */
							do_action( 'bp_directory_groups_actions' ); ?>

						</div>
						<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=full&width=210&height=210' ); ?></a>
					</div>
				<?php endif; ?>

				<div class="item">
					<div class="item-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></div>
					<div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span></div>


					<!-- <div class="meta"><?php bp_group_type(); ?> / <?php bp_group_member_count(); ?></div> -->
				
					<!--<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>-->

					<?php

					/**
					 * Fires inside the listing of an individual group listing item.
					 *
					 * @since BuddyPress (1.1.0)
					 */
					do_action( 'bp_directory_groups_item' ); ?>

				</div>
				<?php 
				global $bp, $wpdb;
				$table_check=$wpdb->prefix."bp_team_follow";
				$groupid = bp_get_group_id();
				$authorID = bp_loggedin_user_id();
				$sqlqry_folllow = "SELECT * FROM $table_check WHERE group_id ='".$groupid."'";
				$total_result = $wpdb->get_results($sqlqry_folllow);
				$total_count = sizeof($total_result);
				$sqlqry = "SELECT * FROM $table_check WHERE author_id = '".$authorID."' AND group_id ='".$groupid."' ";
				$result = $wpdb->get_results($sqlqry);
				if(empty($result)){
				?>
				<button class="follower" type="button" data-groupid="<?php echo bp_get_group_id(); ?>" data-authorid="<?php echo $authorID; ?>">Follow</button>
				<?php } else { ?>
				<button type="button" class="unfollower" data-groupid="<?php echo bp_get_group_id(); ?>" data-authorid="<?php echo $authorID; ?>">Following</button>
				<?php } ?>
				<!-- (<?php echo $total_count; ?>) -->
			</div>
			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the listing of the groups list.
	 *
	 * @since BuddyPress (1.1.0)
	 */
	do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-dir-count-bottom">

			<?php bp_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-bottom">

			<?php bp_groups_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the display of groups from the groups loop.
 *
 * @since BuddyPress (1.2.0)
 */
do_action( 'bp_after_groups_loop' ); ?>