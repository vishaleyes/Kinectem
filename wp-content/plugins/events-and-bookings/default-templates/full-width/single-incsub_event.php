<?php

global $blog_id, $wp_query, $booking, $post, $current_user;

$event = new Eab_EventModel($post);

get_header();

?>

<?php

	the_post();
	$EventID = $event->get_ID();
	$start_day = date_i18n('m', strtotime(get_post_meta($post->ID, 'incsub_event_start', true)));
	$event_venue = get_post_meta($EventID, 'incsub_event_venue', true);
	$event_duration = get_post_meta($EventID, 'incsub_event_duration', true);
	$event_notes = get_post_meta($EventID, 'incsub_event_notes', true);
	$event_reoccurance = get_post_meta($EventID, 'incsub_event_reoccurance', true);
	$event_opponent = get_post_meta($EventID, 'incsub_event_opponent', true);

?>
<div class="gp-container" id="gp-content-wrapper">

	<div id="primary">

		<div id="content" role="main">



<div class="event <?php echo Eab_Template::get_status_class($post); ?>" id="wpmudevevents-wrapper">

	<div id="wpmudevents-single" class="singleEventPage">

		<div class="wpmudevevents-header">

			<h2><?php echo $event->get_title(); ?></h2><br />

			<div class="wpmudevevents-contentmeta" style="clear:both">

				<?php echo Eab_Template::get_event_details($event); ?>

			</div>

		</div>

		<?php echo Eab_Template::get_error_notice(); ?>

		<div id="wpmudevevents-left">

			<div id="wpmudevevents-tickets" class="wpmudevevents-box">

				<?php

                    	if ($event->is_premium() && $event->user_is_coming() && !$event->user_paid()) {

                    ?>

					<div id="wpmudevevents-payment">

						<a href="" id="wpmudevevents-notpaid-submit">You haven't paid for this event</a>

					</div>

					<?php echo Eab_Template::get_payment_forms($event); ?>

					<?php } ?>

			</div>

			<div id="wpmudevevents-content" class="wpmudevevents-box">

				<div class="wpmudevevents-boxheader">

					<h3>About this event :</h3>

				</div>

					<div class="wpmudevevents-boxinner">

					<?php

						add_filter('agm_google_maps-options', 'eab_autoshow_map_off', 99);

						the_content();

						remove_filter('agm_google_maps-options', 'eab_autoshow_map_off');

					?>

					</div>


					<div><?php echo Eab_Template::get_inline_rsvps($event); ?></div>

			</div>

			<div id="wpmudevevents-host" class="wpmudevevents-box">
				<div class="wpmudevevents-boxheader">
				<h3><strong>Address: </strong><?php echo $event_venue; ?></h3>
				</div>
				<div class="wpmudevevents-boxheader">
				<h3><strong>Duration: </strong><?php echo $event_duration; ?></h3>
				</div>
				<div class="wpmudevevents-boxheader">
				<h3><strong>Notes: </strong><?php echo $event_notes; ?></h3>
				</div>
				<div class="wpmudevevents-boxheader">
				<h3><strong>Reoccurance: </strong><?php echo $event_reoccurance; ?></h3>
				</div>
<!-- 				<div class="wpmudevevents-boxheader">
				<h3><strong>Opponent: </strong><?php echo $event_opponent; ?></h3>
				</div> -->
			</div>
		</div>

		<div id="wpmudevevents-right">

			<div id="wpmudevevents-attending" class="wpmudevevents-box">

				<?php echo Eab_Template::get_rsvp_form($event); ?>

			</div>

			<?php if ($event->has_venue_map()) { ?>

			<div id="wpmudevevents-googlemap" class="wpmudevevents-box">

				<div class="wpmudevevents-boxheader">

					<h3>Google Map</h3>

				</div>

					<div class="wpmudevevents-boxinner">

					<?php echo $event->get_venue_location(Eab_EventModel::VENUE_AS_MAP, array('width' => '99%')); ?>

					</div>

			</div>

			<?php } ?>

			<div id="wpmudevevents-host" class="wpmudevevents-box">

				<div class="wpmudevevents-boxheader">

				<h3>Your host : <?php the_author_meta('display_name'); ?></h3>

				</div>

					<div class="wpmudevevents-boxinner">

					<p>

						<?php the_author_meta('description'); ?>

					</p>

					</div>

			</div>

		</div>

	</div>

</div>



<div style="clear:both"><?php comments_template( '', true ); ?></div>



		</div>

	</div>

</div>



<?php get_footer('event'); ?>