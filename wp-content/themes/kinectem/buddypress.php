<?php get_header();?>
<div class="row">
			<div class="container content">
            <div class="col-md-3 left_side white_bg">
					<ul>
						<li class="li_title"><i class="fa fa-cogs" aria-hidden="true"></i>Activity</li>
                        
                        <?php 
						do_action( 'bp_before_directory_members_page' );
						do_action( 'bp_before_directory_members' ); 
						do_action( 'bp_before_directory_members_tabs' ); ?>
						<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
					<li><i class="fa fa-user" aria-hidden="true"></i><a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/' ); ?>"><?php printf( __( 'Friends %s', 'buddypress' ), '<span class="count_noti">' . bp_get_total_friend_count( bp_loggedin_user_id() ) . '</span>' ); ?></a></li>
				<?php endif; ?>
						<li><i class="fa fa-users" aria-hidden="true"></i><a href="<?php echo bp_loggedin_user_domain(); ?>groups/my-groups/">Teams</a> <span class="count_noti"><?php echo bp_get_total_group_count_for_user( bp_loggedin_user_id() );?></span></li>
						<li><i class="fa fa-envelope" aria-hidden="true"></i><a href="<?php echo bp_loggedin_user_domain(); ?>messages/">Messages</a> <span class="count_noti">
						<?php echo bp_total_unread_messages_count(bp_loggedin_user_id()); ?> </span></li>
						<li><i class="fa fa-calendar-o" aria-hidden="true"></i><a href="<?php echo bp_loggedin_user_domain(); ?>my-events/">Events</a> <span class="count_noti">0</span></li>
					</ul>

					<ul>
                    <?php if ( is_user_logged_in()) :?>
						<li><a href="<?php echo bp_loggedin_user_domain(); ?>profile/"><i class="fa fa-file-text" aria-hidden="true"></i>Profile</a></li>
						<li><a href="<?php echo bp_loggedin_user_domain(); ?>settings/"><i class="fa fa-cog" aria-hidden="true"></i>Settings </a></li>
						
                        <li><a href="<?php echo bp_loggedin_user_domain(); ?>messages"><i class="fa fa-power-off" aria-hidden="true"></i>Log out </a></li>
                
                        <?php endif; ?>
					</ul>

				</div>
                
          
<div class="col-md-6 content_area">
					<div class="col-xs-12 cntnt_hdr">
   <?php
    if ( have_posts() ) :
        // Start the Loop.
        while ( have_posts() ) : the_post();
        ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div><!-- .entry-content -->

            </article><!-- #post-## -->

        <?php
        endwhile;

    endif;
    ?>
  </div>
  </div>
     <div class="col-md-3 right_side white_bg">
					<div class="right_widget">
						<?php if ( is_active_sidebar( 'right-sidebar' ) ) : ?>
	
							<?php dynamic_sidebar( 'right-sidebar' ); ?>
	
						<?php endif; ?>

					</div>
				</div>
				<!-- End Right Side -->
</div>

</div>


<?php get_footer(); ?>