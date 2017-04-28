<?php
/**
 * BuddyPress - Groups Cover Image Header.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_group_header' ); ?>
<script type="text/javascript">
var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
jQuery(document).ready(function() {
	jQuery('.follower_team').click(function() { 
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
	jQuery('.unfollower_team').click(function() { 
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
<?php global $bp,$wpdb; ?>
<div id="cover-image-container">
	<a id="header-cover-image" href="<?php bp_group_permalink(); ?>"></a>

	<div id="item-header-cover-image">
		<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
			<div id="item-header-avatar">
				<a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">

					<?php bp_group_avatar(); ?>

				</a>
			</div><!-- #item-header-avatar -->
		<?php endif; ?>

		<div id="item-header-content">

		<?php 

		$current_group = $bp->groups->current_group->id; 
		$table_name = $wpdb->prefix .'bp_groups';
		$sql = "SELECT * FROM $table_name WHERE id = $current_group";
		$result = $wpdb->get_results($sql);
    	foreach( $result as $results ) {
        $location = $results->location_city.' '.$results->location_state;
        $sports=$results->sport.', '; 
        if(empty($results->sport)){
        $sports=""; 
        }

        ?>
        <div class="location-sport">
        	<span><strong> <?php echo $sports.$location ?></strong></span> 
        </div>
    
    		<?php }
		?>
			<div id="item-buttons" class="leave-group"><?php

				/**
				 * Fires in the group header actions section.
				 *
				 * @since 1.2.6
				 */
				do_action( 'bp_group_header_actions' ); ?>

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
				<button class="follower_team" type="button" data-groupid="<?php echo bp_get_group_id(); ?>" data-authorid="<?php echo $authorID; ?>">Follow</button>
				<?php } else { ?>
				<button type="button" class="unfollower_team" data-groupid="<?php echo bp_get_group_id(); ?>" data-authorid="<?php echo $authorID; ?>">Following</button>
				<?php } ?>
				</div><!-- #item-buttons -->



			<?php

			/**
			 * Fires before the display of the group's header meta.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_before_group_header_meta' ); ?>

			<div id="item-meta">

				<?php

				/**
				 * Fires after the group header actions section.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_group_header_meta' ); ?>

				<span class="highlight"><?php bp_group_type(); ?></span>
				<span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>

				<?php bp_group_description(); ?>

			</div>
		</div><!-- #item-header-content -->

		<div id="item-actions">

			<?php if ( bp_group_is_visible() ) : ?>

				<h3><?php _e( 'Group Admins', 'buddypress' ); ?></h3>

				<?php bp_group_list_admins();

				/**
				 * Fires after the display of the group's administrators.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_after_group_menu_admins' );

				if ( bp_group_has_moderators() ) :

					/**
					 * Fires before the display of the group's moderators, if there are any.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_before_group_menu_mods' ); ?>

					<h3><?php _e( 'Group Mods' , 'buddypress' ); ?></h3>

					<?php bp_group_list_mods();

					/**
					 * Fires after the display of the group's moderators, if there are any.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_after_group_menu_mods' );

				endif;

			endif; ?>

		</div><!-- #item-actions -->

	</div><!-- #item-header-cover-image -->
</div><!-- #cover-image-container -->

<?php

/**
 * Fires after the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_group_header' );

/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
do_action( 'template_notices' ); ?>
