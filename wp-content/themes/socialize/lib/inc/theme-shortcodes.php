<?php

/*--------------------------------------------------------------
Custom Visual Composer Fields
--------------------------------------------------------------*/

// Icon Selection
function socialize_icon_selection( $gp_settings, $gp_value ) {
   $gp_dependency = vc_generate_dependencies_attributes( $gp_settings );
   $gp_output = '';
	foreach ( $gp_settings['value'] as $gp_val ) {		   
		$gp_output .= '<a href="' . $gp_val . '" class="gp-icon-link"><i class="fa fa-lg ' . $gp_val . '" ' . $gp_dependency . '></i></a>';		
	}
	$gp_output .= '<input name="' . $gp_settings['param_name'] . '" id="gp-icon-selection-value" class="wpb_vc_param_value ' . $gp_settings['param_name'] . ' ' . $gp_settings['type'] . '_field" type="hidden" value="' . $gp_value . '" ' . $gp_dependency . '/>';    
	return $gp_output;
}
vc_add_shortcode_param( 'icon_selection', 'socialize_icon_selection', socialize_scripts_uri . 'icon-selection.js' );	


/*--------------------------------------------------------------
Shortcode Options
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_shortcode_options' ) ) {
	function socialize_shortcode_options( $atts ) {

		$GLOBALS['socialize_cats'] = isset( $atts['cats'] ) ? $atts['cats'] : '';
		$GLOBALS['socialize_page_ids'] = isset( $atts['page_ids'] ) ? $atts['page_ids'] : '';
		$GLOBALS['socialize_post_types'] = isset( $atts['post_types'] ) ? $atts['post_types'] : 'post';
		if ( isset( $GLOBALS['socialize_shortcode'] ) && $GLOBALS['socialize_shortcode'] == 'portfolio' ) {
			$GLOBALS['socialize_format'] = isset( $atts['format'] ) ? $atts['format'] : 'gp-portfolio-columns-2';
		} else {
			$GLOBALS['socialize_format'] = isset( $atts['format'] ) ? $atts['format'] : 'gp-blog-standard';
		}
		$GLOBALS['socialize_orderby'] =  isset( $atts['orderby'] ) ? $atts['orderby'] : 'newest';
		$GLOBALS['socialize_date_posted'] = isset( $atts['date_posted'] ) ? $atts['date_posted'] : 'all';
		$GLOBALS['socialize_date_modified'] = isset( $atts['date_modified'] ) ? $atts['date_modified'] : 'all';
		if ( isset( $GLOBALS['socialize_shortcode'] ) && $GLOBALS['socialize_shortcode'] == 'portfolio' ) {
			$GLOBALS['socialize_filter'] = isset( $atts['filter'] ) ? $atts['filter'] : 'enabled';
		} else {
			$GLOBALS['socialize_filter'] = isset( $atts['filter'] ) ? $atts['filter'] : 'disabled';
		}
		$GLOBALS['socialize_filter_cats'] = isset( $atts['filter_cats'] ) ? $atts['filter_cats'] : '';
		$GLOBALS['socialize_filter_date'] = isset( $atts['filter_date'] ) ? $atts['filter_date'] : '';
		$GLOBALS['socialize_filter_title'] = isset( $atts['filter_title'] ) ? $atts['filter_title'] : '';
		$GLOBALS['socialize_filter_comment_count'] = isset( $atts['filter_comment_count'] ) ? $atts['filter_comment_count'] : '';
		$GLOBALS['socialize_filter_views'] = isset( $atts['filter_views'] ) ? $atts['filter_views'] : '';
		$GLOBALS['socialize_filter_date_posted'] = isset( $atts['filter_date_posted'] ) ? $atts['filter_date_posted'] : '';
		$GLOBALS['socialize_filter_date_modified'] = isset( $atts['filter_date_modified'] ) ? $atts['filter_date_modified'] : '';
		$GLOBALS['socialize_filter_cats_id'] = isset( $atts['filter_cats_id'] ) ? $atts['filter_cats_id'] : '';
		if ( isset( $GLOBALS['socialize_shortcode'] ) && $GLOBALS['socialize_shortcode'] == 'showcase' ) {
			$GLOBALS['socialize_per_page'] = isset( $atts['per_page'] ) ? $atts['per_page'] : '5';
		} else {
			$GLOBALS['socialize_per_page'] = isset( $atts['per_page'] ) ? $atts['per_page'] : '12';
		}
		$GLOBALS['socialize_offset'] = isset( $atts['offset'] ) ? $atts['offset'] : '0';
		$GLOBALS['socialize_featured_image'] = isset( $atts['featured_image'] ) ? $atts['featured_image'] : 'enabled';
		$GLOBALS['socialize_image_width'] = isset( $atts['image_width'] ) ? $atts['image_width'] : '200';
		$GLOBALS['socialize_image_height'] = isset( $atts['image_height'] ) ? $atts['image_height'] : '200';
		$GLOBALS['socialize_hard_crop'] = isset( $atts['hard_crop'] ) ? $atts['hard_crop'] : true;
		$GLOBALS['socialize_image_alignment'] = isset( $atts['image_alignment'] ) ? $atts['image_alignment'] : 'gp-image-align-left';
		$GLOBALS['socialize_title_position'] = isset( $atts['title_position'] ) ? $atts['title_position'] : 'title-next-to-thumbnail';
		$GLOBALS['socialize_content_display'] = isset( $atts['content_display'] ) ? $atts['content_display'] : 'excerpt';
		$GLOBALS['socialize_excerpt_length'] = isset( $atts['excerpt_length'] ) ? $atts['excerpt_length'] : '160';
		$GLOBALS['socialize_meta_author'] = isset( $atts['meta_author'] ) ? $atts['meta_author'] : '';
		$GLOBALS['socialize_meta_date'] = isset( $atts['meta_date'] ) ? $atts['meta_date'] : '';
		$GLOBALS['socialize_meta_comment_count'] = isset( $atts['meta_comment_count'] ) ? $atts['meta_comment_count'] : '';
		$GLOBALS['socialize_meta_views'] = isset( $atts['meta_views'] ) ? $atts['meta_views'] : '';
		$GLOBALS['socialize_meta_cats'] = isset( $atts['meta_cats'] ) ? $atts['meta_cats'] : '';
		$GLOBALS['socialize_meta_tags'] = isset( $atts['meta_tags'] ) ? $atts['meta_tags'] : '';
		$GLOBALS['socialize_read_more_link'] = isset( $atts['read_more_link'] ) ? $atts['read_more_link'] : 'disabled';
		$GLOBALS['socialize_page_arrows'] = isset( $atts['page_arrows'] ) ? $atts['page_arrows'] : 'disabled';
		$GLOBALS['socialize_page_numbers'] = isset( $atts['page_numbers'] ) ? $atts['page_numbers'] : 'disabled';
		$GLOBALS['socialize_caption_title'] = isset( $atts['caption_title'] ) ? $atts['caption_title'] : 'enabled';
		$GLOBALS['socialize_caption_text'] = isset( $atts['caption_text'] ) ? $atts['caption_text'] : 'enabled';

		// Add slug support for filter categories option
		if ( preg_match( '/[a-zA-Z\-]+/', $GLOBALS['socialize_filter_cats_id'] ) ) {
			$gp_taxonomies = get_taxonomies();
			foreach ( $gp_taxonomies as $gp_taxonomy ) {
				$gp_term = term_exists( $GLOBALS['socialize_filter_cats_id'], $gp_taxonomy );
				$gp_tax_name = '';
				if ( $gp_term !== 0 && $gp_term !== null ) {
					$gp_tax_name = $gp_taxonomy;
					break;
				}
			}		
			$gp_filter_cats_slug = get_term_by( 'slug', $GLOBALS['socialize_filter_cats_id'], $gp_tax_name );
			if ( $gp_filter_cats_slug ) {
				$GLOBALS['socialize_filter_cats_id'] = $gp_filter_cats_slug->term_id;
			}
		}
					
	}
}


/*--------------------------------------------------------------
Custom Shortcodes
--------------------------------------------------------------*/

if ( ! function_exists( 'socialize_custom_shortcodes' ) ) {
	function socialize_custom_shortcodes() {

		$icons = array( '', 'fa-glass','fa-music','fa-search','fa-envelope-o','fa-heart','fa-star','fa-star-o','fa-user','fa-film','fa-th-large','fa-th','fa-th-list','fa-check','fa-times','fa-search-plus','fa-search-minus','fa-power-off','fa-signal','fa-cog','fa-trash-o','fa-home','fa-file-o','fa-clock-o','fa-road','fa-download','fa-arrow-circle-o-down','fa-arrow-circle-o-up','fa-inbox','fa-play-circle-o','fa-repeat','fa-refresh','fa-list-alt','fa-lock','fa-flag','fa-headphones','fa-volume-off','fa-volume-down','fa-volume-up','fa-qrcode','fa-barcode','fa-tag','fa-tags','fa-book','fa-bookmark','fa-print','fa-camera','fa-font','fa-bold','fa-italic','fa-text-height','fa-text-width','fa-align-left','fa-align-center','fa-align-right','fa-align-justify','fa-list','fa-outdent','fa-indent','fa-video-camera','fa-picture-o','fa-pencil','fa-map-marker','fa-adjust','fa-tint','fa-pencil-square-o','fa-share-square-o','fa-check-square-o','fa-arrows','fa-step-backward','fa-fast-backward','fa-backward','fa-play','fa-pause','fa-stop','fa-forward','fa-fast-forward','fa-step-forward','fa-eject','fa-chevron-left','fa-chevron-right','fa-plus-circle','fa-minus-circle','fa-times-circle','fa-check-circle','fa-question-circle','fa-info-circle','fa-crosshairs','fa-times-circle-o','fa-check-circle-o','fa-ban','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrow-down','fa-share','fa-expand','fa-compress','fa-plus','fa-minus','fa-asterisk','fa-exclamation-circle','fa-gift','fa-leaf','fa-fire','fa-eye','fa-eye-slash','fa-exclamation-triangle','fa-plane','fa-calendar','fa-random','fa-comment','fa-magnet','fa-chevron-up','fa-chevron-down','fa-retweet','fa-shopping-cart','fa-folder','fa-folder-open','fa-arrows-v','fa-arrows-h','fa-bar-chart-o','fa-twitter-square','fa-facebook-square','fa-camera-retro','fa-key','fa-cogs','fa-comments','fa-thumbs-o-up','fa-thumbs-o-down','fa-star-half','fa-heart-o','fa-sign-out','fa-linkedin-square','fa-thumb-tack','fa-external-link','fa-sign-in','fa-trophy','fa-github-square','fa-upload','fa-lemon-o','fa-phone','fa-square-o','fa-bookmark-o','fa-phone-square','fa-twitter','fa-facebook','fa-github','fa-unlock','fa-credit-card','fa-rss','fa-hdd-o','fa-bullhorn','fa-bell','fa-certificate','fa-hand-o-right','fa-hand-o-left','fa-hand-o-up','fa-hand-o-down','fa-arrow-circle-left','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-circle-down','fa-globe','fa-wrench','fa-tasks','fa-filter','fa-briefcase','fa-arrows-alt','fa-users','fa-link','fa-cloud','fa-flask','fa-scissors','fa-files-o','fa-paperclip','fa-floppy-o','fa-square','fa-bars','fa-list-ul','fa-list-ol','fa-strikethrough','fa-underline','fa-table','fa-magic','fa-truck','fa-pinterest','fa-pinterest-square','fa-google-plus-square','fa-google-plus','fa-money','fa-caret-down','fa-caret-up','fa-caret-left','fa-caret-right','fa-columns','fa-sort','fa-sort-asc','fa-sort-desc','fa-envelope','fa-linkedin','fa-undo','fa-gavel','fa-tachometer','fa-comment-o','fa-comments-o','fa-bolt','fa-sitemap','fa-umbrella','fa-clipboard','fa-lightbulb-o','fa-exchange','fa-cloud-download','fa-cloud-upload','fa-user-md','fa-stethoscope','fa-suitcase','fa-bell-o','fa-coffee','fa-cutlery','fa-file-text-o','fa-building-o','fa-hospital-o','fa-ambulance','fa-medkit','fa-fighter-jet','fa-beer','fa-h-square','fa-plus-square','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-double-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-angle-down','fa-desktop','fa-laptop','fa-tablet','fa-mobile','fa-circle-o','fa-quote-left','fa-quote-right','fa-spinner','fa-circle','fa-reply','fa-github-alt','fa-folder-o','fa-folder-open-o','fa-smile-o','fa-frown-o','fa-meh-o','fa-gamepad','fa-keyboard-o','fa-flag-o','fa-flag-checkered','fa-terminal','fa-code','fa-reply-all','fa-mail-reply-all','fa-star-half-o','fa-location-arrow','fa-crop','fa-code-fork','fa-chain-broken','fa-question','fa-info','fa-exclamation','fa-superscript','fa-subscript','fa-eraser','fa-puzzle-piece','fa-microphone','fa-microphone-slash','fa-shield','fa-calendar-o','fa-fire-extinguisher','fa-rocket','fa-maxcdn','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-circle-down','fa-html5','fa-css3','fa-anchor','fa-unlock-alt','fa-bullseye','fa-ellipsis-h','fa-ellipsis-v','fa-rss-square','fa-play-circle','fa-ticket','fa-minus-square','fa-minus-square-o','fa-level-up','fa-level-down','fa-check-square','fa-pencil-square','fa-external-link-square','fa-share-square','fa-compass','fa-caret-square-o-down','fa-caret-square-o-up','fa-caret-square-o-right','fa-eur','fa-gbp','fa-usd','fa-inr','fa-jpy','fa-rub','fa-krw','fa-btc','fa-file','fa-file-text','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-thumbs-up','fa-thumbs-down','fa-youtube-square','fa-youtube','fa-xing','fa-xing-square','fa-youtube-play','fa-dropbox','fa-stack-overflow','fa-instagram','fa-flickr','fa-adn','fa-bitbucket','fa-bitbucket-square','fa-tumblr','fa-tumblr-square','fa-long-arrow-down','fa-long-arrow-up','fa-long-arrow-left','fa-long-arrow-right','fa-apple','fa-windows','fa-android','fa-linux','fa-dribbble','fa-skype','fa-foursquare','fa-trello','fa-female','fa-male','fa-gittip','fa-sun-o','fa-moon-o','fa-archive','fa-bug','fa-vk','fa-weibo','fa-renren','fa-pagelines','fa-stack-exchange','fa-arrow-circle-o-right','fa-arrow-circle-o-left','fa-caret-square-o-left','fa-dot-circle-o','fa-wheelchair','fa-vimeo-square','fa-try','fa-plus-square-o','fa-angellist','fa-area-chart','fa-at','fa-bell-slash','fa-bell-slash-o','fa-bicycle','fa-binoculars','fa-birthday-cake','fa-bus','fa-calculator','fa-cc','fa-cc-amex','fa-cc-discover','fa-cc-mastercard','fa-cc-paypal','fa-cc-stripe','fa-cc-visa','fa-copyright','fa-eyedropper','fa-futbol-o','fa-google-wallet','fa-ils','fa-ioxhost','fa-lastfm','fa-lastfm-square','fa-line-chart','fa-meanpath','fa-newspaper-o','fa-paint-brush','fa-paypal','fa-pie-chart','fa-plug','fa-shekel','fa-sheqel','fa-slideshare','fa-soccer-ball-o','fa-toggle-off','fa-toggle-on','fa-trash','fa-tty','fa-twitch','fa-wifi','fa-yelp' );


		/*--------------------------------------------------------------
		Activity Shortcode
		--------------------------------------------------------------*/

		if ( function_exists( 'bp_is_active' ) ) {
		
			require_once( socialize_vc . 'gp_vc_activity.php' );

			vc_map( array( 
				'name' => esc_html__( 'Activity', 'socialize' ),
				'base' => 'activity',
				'description' => esc_html__( 'Display a BuddyPress activity loop anywhere on your site.', 'socialize' ),
				'class' => 'wpb_vc_activity',
				'controls' => 'full',
				'icon' => 'gp-icon-activity',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(		
					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'widget_title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => '',
					),		
					array( 
					'heading' => esc_html__( 'Post Form', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose whether to add the post form.', 'socialize' ),
					'param_name' => 'post_form',
					'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
					'type' => 'dropdown',
					),
					array( 
					'heading' => esc_html__( 'Scope', 'socialize' ),
					'param_name' => 'scope',
					'type' => 'checkbox',
					'value' => array( esc_html__( 'Single User', 'socialize' ) => 'just-me', esc_html__( 'Friends', 'socialize' ) => 'friends', esc_html__( 'Groups', 'socialize' ) => 'groups', esc_html__( 'Favorites', 'socialize' ) => 'favorites', esc_html__( 'Mentions', 'socialize' ) => 'mentions' ),
					'description' => esc_html__( 'Pre-defined filtering of the activity stream. Show only activity for the scope you pass (based on the logged in user or a user_id you pass).', 'socialize' ),
					),									
					array( 
					'heading' => esc_html__( 'Display Comments', 'socialize' ),
					'description' => esc_html__( 'Whether or not to display comments along with activity items. Threaded will show comments threaded under the activity. Stream will show comments within the actual stream in chronological order along with activity items.', 'socialize' ),
					'param_name' => 'display_comments',
					'value' => array( esc_html__( 'Threaded', 'socialize' ) => 'threaded', esc_html__( 'Stream', 'socialize' ) => 'stream', esc_html__( 'Disable', 'socialize' ) => 'false' ),
					'type' => 'dropdown',
					),	
					array( 
					'heading' => esc_html__( 'Allow Commenting', 'socialize' ),
					'description' => esc_html__( 'Whether or not users can post comments in the activity loop.', 'socialize' ),
					'param_name' => 'allow_comments',
					'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'gp-comments-enabled', esc_html__( 'Disabled', 'socialize' ) => 'gp-comments-disabled' ),
					'type' => 'dropdown',
					),					
					array( 
					'heading' => esc_html__( 'Include', 'socialize' ),
					'description' => esc_html__( 'Pass an activity_id or string of comma separated ids to show only these entries.', 'socialize' ),
					'param_name' => 'include',
					'type' => 'textfield',
					'value' => '',
					),	
					array( 
					'heading' => esc_html__( 'Order', 'socialize' ),
					'description' => esc_html__( 'The criteria which the items are ordered by.', 'socialize' ),
					'param_name' => 'order',
					'value' => array(
						esc_html__( 'Newest', 'socialize' ) => 'DESC',
						esc_html__( 'Oldest', 'socialize' ) => 'ASC',
					),
					'type' => 'dropdown',
					),				
					array( 
					'heading' => esc_html__( 'Items Per Page', 'socialize' ),
					'description' => esc_html__( 'The number of activity items on each page.', 'socialize' ),
					'param_name' => 'per_page',
					'value' => '20',
					'type' => 'textfield',
					),
					array( 
					'heading' => esc_html__( 'Maximum Items', 'socialize' ),
					'description' => esc_html__( 'The maximum number of activity items to show.', 'socialize' ),
					'param_name' => 'max',
					'value' => '',
					'type' => 'textfield',
					),						
					array( 
					'heading' => esc_html__( 'Show Hidden Items', 'socialize' ),
					'description' => esc_html__( 'Show items that have been hidden site wide such as private or hidden group posts.', 'socialize' ),
					'param_name' => 'show_hidden',
					'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
					'type' => 'dropdown',
					),
					array( 
					'heading' => esc_html__( 'Search Terms', 'socialize' ),
					'description' => esc_html__( 'Return only activity items that match these search terms.', 'socialize' ),
					'param_name' => 'search_terms',
					'value' => '',
					'type' => 'textfield',
					),	
					array( 
					'heading' => esc_html__( 'User ID', 'socialize' ),
					'description' => esc_html__( 'Limit activity items to a specific user ID.', 'socialize' ),
					'param_name' => 'user_id',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Object', 'socialize' ),
					'description' => esc_html__( 'The object type to filter by (can be any active component ID as well as custom component IDs) e.g. groups, friends, profile, status, blogs.', 'socialize' ),
					'param_name' => 'object',
					'value' => '',
					'type' => 'textfield',
					),
					array( 
					'heading' => esc_html__( 'Action', 'socialize' ),
					'description' => esc_html__( 'The action type to filter by (can be any active component action as well as custom component actions) e.g. new_forum_post, new_blog_comment new_blog_post, friendship_created, joined_group, created_group, new_forum_topic, activity_update.', 'socialize' ),
					'param_name' => 'action',
					'value' => '',
					'type' => 'textfield',
					),	
					array( 
					'heading' => esc_html__( 'Primary ID', 'socialize' ),
					'description' => esc_html__( 'The ID to filter by for a specific object. For example if you used groups as the object you could pass a group_id as the primary_id and restrict to that group.', 'socialize' ),
					'param_name' => 'primary_id',
					'value' => '',
					'type' => 'textfield',
					),	
					array( 
					'heading' => esc_html__( 'Secondary ID', 'socialize' ),
					'description' => esc_html__( 'The secondary ID to filter by for a specific object. For example if you used blogs as the object you could pass a blog_id as the primary_id and a post_id as the secondary_id then list all comments for that post using new_blog_comment as the action.', 'socialize' ),
					'param_name' => 'secondary_id',
					'value' => '',
					'type' => 'textfield',
					),
					array( 
					'heading' => esc_html__( 'See All', 'socialize' ),
					'description' => esc_html__( 'Add a "See All" link.', 'socialize' ),
					'param_name' => 'see_all',
					'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
					'type' => 'dropdown',
					),
					array( 
					'heading' => esc_html__( 'See All Link', 'socialize' ),
					'description' => esc_html__( 'URL for the "See All" link.', 'socialize' ),
					'param_name' => 'see_all_link',
					'type' => 'textfield',
					'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
					),				 			 
					array( 
					'heading' => esc_html__( 'See All Text', 'socialize' ),
					'description' => esc_html__( 'Custom text for the "See All" link.', 'socialize' ),
					'param_name' => 'see_all_text',
					'type' => 'textfield',
					'value' => esc_html__( 'See All Items', 'socialize' ),
					'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
					),						
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'type' => 'textfield',
					),
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title', esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),																																														
				 )
			) );
		
		}
		

		/*--------------------------------------------------------------
		Advertisement Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_advertisement.php' );

		vc_map( array( 
			'name' => esc_html__( 'Advertisement', 'socialize' ),
			'base' => 'advertisement',
			'description' => esc_html__( 'Insert an advertisement anywhere you can insert this element.', 'socialize' ),
			'class' => 'wpb_vc_advertisement',
			'controls' => 'full',
			'icon' => 'gp-icon-advertisement',
			'category' => esc_html__( 'Content', 'socialize' ),
			'params' => array(
				array( 
				'heading' => esc_html__( 'Advertisement Code', 'socialize' ),
				'description' => esc_html__( 'The advertisement code e.g. Google Adsense JavaScript code.', 'socialize' ),
				'param_name' => 'content',
				'value' => '',
				'type' => 'textarea_html',
				),	
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'type' => 'textfield',
				),
			 )
		) );
			
											
		/*--------------------------------------------------------------
		Blog Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_blog.php' );

		vc_map( array( 
			'name' => esc_html__( 'Blog', 'socialize' ),
			'base' => 'blog',
			'description' => esc_html__( 'Display posts, pages and custom post types in a variety of ways.', 'socialize' ),
			'class' => 'wpb_vc_blog',
			'controls' => 'full',
			'icon' => 'gp-icon-blog',
			'category' => esc_html__( 'Content', 'socialize' ),			
			'admin_enqueue_css' => socialize_css_uri . 'admin.css',
			'front_enqueue_css' => socialize_css_uri . 'admin.css',
			'params' => array(		
				array( 
				'heading' => esc_html__( 'Title', 'socialize' ),
				'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
				'param_name' => 'widget_title',
				'type' => 'textfield',
				'admin_label' => true,
				'value' => '',
				),								
				array( 
				'heading' => esc_html__( 'Categories', 'socialize' ),
				'description' => wp_kses( __( 'Enter the ID numbers or slugs of the categories, separating each ID or slug with a comma e.g. 33,74,25. 	Hover your mouse over the category names on the <a href="' . admin_url( 'edit-tags.php?taxonomy=category' ). '" target="_blank">category page</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'cats',
				'type' => 'textfield',
				),					
				array( 
				'heading' => esc_html__( 'Page IDs', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => wp_kses( __( 'Enter the ID of the pages you want to include, separating each ID with a comma e.g. 33,74,25. Hover your mouse over the page names on the <a href="' . admin_url( 'edit.php?post_type=page' ). '" target="_blank">page list</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'page_ids',
				'type' => 'textfield',
				),			
				array( 
				'heading' => esc_html__( 'Post Types', 'socialize' ),
				'description' => esc_html__( 'The post types to display.', 'socialize' ),
				'param_name' => 'post_types',
				'type' => 'posttypes',
				),	
				array( 
				'heading' => esc_html__( 'Format', 'socialize' ),
				'description' => esc_html__( 'The format to display the items in.', 'socialize' ),
				'param_name' => 'format',
				'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-blog-standard', esc_html__( '1 Column', 'socialize' ) => 'gp-blog-columns-1', esc_html__( '2 Columns', 'socialize' ) => 'gp-blog-columns-2', esc_html__( '3 Columns', 'socialize' ) => 'gp-blog-columns-3', esc_html__( '4 Columns', 'socialize' ) => 'gp-blog-columns-4', esc_html__( '5 Columns', 'socialize' ) => 'gp-blog-columns-5', esc_html__( '6 Columns', 'socialize' ) => 'gp-blog-columns-6', esc_html__( 'Masonry', 'socialize' ) => 'gp-blog-masonry' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Order By', 'socialize' ),
				'description' => esc_html__( 'The criteria which the items are ordered by.', 'socialize' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__( 'Newest', 'socialize' ) => 'newest',
					esc_html__( 'Oldest', 'socialize' ) => 'oldest',
					esc_html__( 'Title (A-Z)', 'socialize' ) => 'title_az',
					esc_html__( 'Title (Z-A)', 'socialize' ) => 'title_za',
					esc_html__( 'Most Comments', 'socialize' ) => 'comment_count',
					esc_html__( 'Most Views', 'socialize' ) => 'views',
					esc_html__( 'Menu Order', 'socialize' ) => 'menu_order',
					esc_html__( 'Random', 'socialize' ) => 'rand',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Date Posted', 'socialize' ),
				'description' => esc_html__( 'The date the items were posted.', 'socialize' ),
				'param_name' => 'date_posted',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Date Modified', 'socialize' ),
				'description' => esc_html__( 'The date the items were modified.', 'socialize' ),
				'param_name' => 'date_modified',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Filter', 'socialize' ),
				'description' => esc_html__( 'Add a dropdown filter menu to the page.', 'socialize' ),
				'param_name' => 'filter',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),	
				array(
				'heading' => esc_html__( 'Filter Options', 'socialize' ),
				'param_name' => 'filter_cats',
				'value' => array( esc_html__( 'Categories', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),	
				array(
				'param_name' => 'filter_date',
				'value' => array( esc_html__( 'Date', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),	
				array(
				'param_name' => 'filter_title',
				'value' => array( esc_html__( 'Title', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),								
				array(
				'param_name' => 'filter_comment_count',
				'value' => array( esc_html__( 'Comment Count', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),
				array(
				'param_name' => 'filter_views',
				'value' => array( esc_html__( 'Views', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),
				array( 
				'param_name' => 'filter_date_posted',
				'value' => array( esc_html__( 'Date Posted', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),				
				array( 
				'description' => esc_html__( 'Choose what options to display in the dropdown filter menu.', 'socialize' ),
				'param_name' => 'filter_date_modified',
				'value' => array( esc_html__( 'Date Modified', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),				
				array( 
				'heading' => esc_html__( 'Filter Category', 'socialize' ),
				'description' => wp_kses( __('Enter the ID number or slug of the category you want to filter by, leave blank to display all categories - the sub categories of this category will also be displayed. Hover your mouse over the category names on the <a href="' . admin_url ( 'edit-tags.php?taxonomy=category'). '" target="_blank">category page</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'filter_cats_id',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),												 
				array( 
				'heading' => esc_html__( 'Items Per Page', 'socialize' ),
				'description' => esc_html__( 'The number of items on each page.', 'socialize' ),
				'param_name' => 'per_page',
				'value' => '12',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Offset', 'socialize' ),
				'description' => esc_html__( 'The number of posts to offset by e.g. set to 3 to exclude the first 3 posts.', 'socialize' ),
				'param_name' => 'offset',
				'value' => '0',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Featured Image', 'socialize' ),
				'description' => esc_html__( 'Display the featured images.', 'socialize' ),
				'param_name' => 'featured_image',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Image Width', 'socialize' ),
				'description' => esc_html__( 'The width of the featured images.', 'socialize' ),
				'param_name' => 'image_width',
				'value' => '200',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),		 
				array( 
				'heading' => esc_html__( 'Image Height', 'socialize' ),
				'description' => esc_html__( 'The height of the featured images.', 'socialize' ),
				'param_name' => 'image_height',
				'value' => '200',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),	
				array( 
				'heading' => esc_html__( 'Hard Crop', 'socialize' ),
				'description' => esc_html__( 'Images are cropped even if it is smaller than the dimensions you want to crop it to.', 'socialize' ),
				'param_name' => 'hard_crop',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),	
				array( 
				'heading' => esc_html__( 'Image Alignment', 'socialize' ),
				'description' => esc_html__( 'Choose how the image aligns with the content.', 'socialize' ),
				'param_name' => 'image_alignment',
				'value' => array( esc_html__( 'Left Align', 'socialize' ) => 'gp-image-align-left', esc_html__( 'Right Align', 'socialize' ) => 'gp-image-align-right', esc_html__( 'Left Wrap', 'socialize' ) => 'gp-image-wrap-left', esc_html__( 'Right Wrap', 'socialize' ) => 'gp-image-wrap-right', esc_html__( 'Above Content', 'socialize' ) => 'gp-image-above' ),
				'type' => 'dropdown',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),
				array( 
				'heading' => esc_html__( 'Content Display', 'socialize' ),
				'description' => esc_html__( 'The amount of content displayed.', 'socialize' ),
				'param_name' => 'content_display',
				'value' => array( esc_html__( 'Excerpt', 'socialize' ) => 'excerpt', esc_html__( 'Full Content', 'socialize' ) => 'full_content' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Excerpt Length', 'socialize' ),
				'description' => esc_html__( 'The number of characters in excerpts.', 'socialize' ),
				'param_name' => 'excerpt_length',
				'value' => '160',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'content_display', 'value' => 'excerpt' ),
				),	
				array(
				'heading' => esc_html__( 'Post Meta', 'socialize' ),
				'param_name' => 'meta_author',
				'value' => array( esc_html__( 'Author Name', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array(
				'param_name' => 'meta_date',
				'value' => array( esc_html__( 'Post Date', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array(
				'param_name' => 'meta_comment_count',
				'value' => array( esc_html__( 'Comment Count', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),
				array(
				'param_name' => 'meta_views',
				'value' => array( esc_html__( 'Views', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array( 
				'param_name' => 'meta_cats',
				'value' => array( esc_html__( 'Post Categories', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),						
				array(
				'description' => esc_html__( 'Select the meta data you want to display.', 'socialize' ),
				'param_name' => 'meta_tags',
				'value' => array( esc_html__( 'Post Tags', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array( 
				'heading' => esc_html__( 'Read More Link', 'socialize' ),
				'description' => esc_html__( 'Add a read more link below the content.', 'socialize' ),
				'param_name' => 'read_more_link',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),		 
				array( 
				'heading' => esc_html__( 'Pagination (Arrows)', 'socialize' ),
				'description' => esc_html__( 'Add pagination arrows.', 'socialize' ),
				'param_name' => 'page_arrows',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Pagination (Numbers)', 'socialize' ),
				'description' => esc_html__( 'Add pagination numbers.', 'socialize' ),
				'param_name' => 'page_numbers',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'See All', 'socialize' ),
				'description' => esc_html__( 'Add a "See All" link.', 'socialize' ),
				'param_name' => 'see_all',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'See All Link', 'socialize' ),
				'description' => esc_html__( 'URL for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_link',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),				 			 
				array( 
				'heading' => esc_html__( 'See All Text', 'socialize' ),
				'description' => esc_html__( 'Custom text for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_text',
				'type' => 'textfield',
				'value' => esc_html__( 'See All Items', 'socialize' ),
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),		 				 		   			 			 
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'Choose the title format.', 'socialize' ),
				'param_name' => 'title_format',
				'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
				'type' => 'dropdown',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),
				array( 
				'heading' => esc_html__( 'Title Color', 'socialize' ),
				'description' => esc_html__( 'The title color.', 'socialize' ),
				'param_name' => 'title_color',
				'value' => '#E93100',
				'type' => 'colorpicker',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),	
				array( 
				'heading' => esc_html__( 'Title Icon', 'socialize' ),
				'param_name' => 'icon',
				'value' => $icons,
				'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
				'type' => 'icon_selection',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),																																											
			 )
		) );


		/*--------------------------------------------------------------
		BuddyPress Shortcodes
		--------------------------------------------------------------*/

		if ( function_exists( 'bp_is_active' ) ) {
			
			require_once( socialize_vc . 'gp_vc_buddypress.php' );

			// BuddyPress Groups
			vc_map( array( 
				'name' => esc_html__( 'Groups', 'socialize' ),
				'base' => 'bp_groups',
				'description' => esc_html__( 'A dynamic list of recently active, popular, and newest groups.', 'socialize' ),
				'class' => 'wpb_vc_bp_groups',
				'controls' => 'full',
				'icon' => 'gp-icon-bp-groups',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(				

					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Groups', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Link Widget Title', 'socialize' ),
					'param_name' => 'link_title',
					'type' => 'checkbox',
					'value' => array( esc_html__( 'Link widget title to Groups directory', 'socialize' ) => 'false' ),
					),	
					array( 
					'heading' => esc_html__( 'Maximum Groups', 'socialize' ),
					'description' => esc_html__( 'Maximum number of groups to show.', 'socialize' ),
					'param_name' => 'max_groups',
					'type' => 'textfield',
					'value' => 5,
					),					
					array( 
					'heading' => esc_html__( 'Default Display', 'socialize' ),
					'description' => esc_html__( 'The group display that is shown by default.', 'socialize' ),
					'param_name' => 'group_default',
					'value' => array(
						esc_html__( 'Popular', 'socialize' ) => 'popular',
						esc_html__( 'Active', 'socialize' ) => 'active',
						esc_html__( 'Newest', 'socialize' ) => 'newest',
					),
					'type' => 'dropdown',
					),						
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );		

			// BuddyPress Members
			vc_map( array( 
				'name' => esc_html__( 'Members', 'socialize' ),
				'base' => 'bp_members',
				'description' => esc_html__( 'A dynamic list of recently active, popular, and newest members.', 'socialize' ),
				'class' => 'wpb_vc_bp_members',
				'controls' => 'full',
				'icon' => 'gp-icon-bp-members',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(				

					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Members', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Link Widget Title', 'socialize' ),
					'param_name' => 'link_title',
					'type' => 'checkbox',
					'value' => array( esc_html__( 'Link widget title to Groups directory', 'socialize' ) => 'false' ),
					),	
					array( 
					'heading' => esc_html__( 'Maximum Members', 'socialize' ),
					'description' => esc_html__( 'Maximum number of members to show.', 'socialize' ),
					'param_name' => 'max_members',
					'type' => 'textfield',
					'value' => 5,
					),					
					array( 
					'heading' => esc_html__( 'Default Display', 'socialize' ),
					'description' => esc_html__( 'The member display that is shown by default.', 'socialize' ),
					'param_name' => 'member_default',
					'value' => array(
						esc_html__( 'Active', 'socialize' ) => 'active',
						esc_html__( 'Newest', 'socialize' ) => 'newest',
						esc_html__( 'Popular', 'socialize' ) => 'popular',
					),
					'type' => 'dropdown',
					),						
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),								
					array( 
					'heading' => esc_html__( 'CSS box', 'socialize' ),
					'param_name' => 'css',
					'type' => 'css_editor',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );							

			// BuddyPress Friends
			vc_map( array( 
				'name' => esc_html__( 'Friends', 'socialize' ),
				'base' => 'bp_friends',
				'description' => esc_html__( 'A dynamic list of recently active, popular, and newest friends.', 'socialize' ),
				'class' => 'wpb_vc_bp_friends',
				'controls' => 'full',
				'icon' => 'gp-icon-bp-friends',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(				

					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Friends', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Link Widget Title', 'socialize' ),
					'param_name' => 'link_title',
					'type' => 'checkbox',
					'value' => array( esc_html__( 'Link widget title to Groups directory', 'socialize' ) => 'false' ),
					),	
					array( 
					'heading' => esc_html__( 'Maximum Friends', 'socialize' ),
					'description' => esc_html__( 'Maximum number of friends to show.', 'socialize' ),
					'param_name' => 'max_friends',
					'type' => 'textfield',
					'value' => 5,
					),					
					array( 
					'heading' => esc_html__( 'Default Display', 'socialize' ),
					'description' => esc_html__( 'The friend display that is shown by default.', 'socialize' ),
					'param_name' => 'friend_default',
					'value' => array(
						esc_html__( 'Active', 'socialize' ) => 'active',
						esc_html__( 'Newest', 'socialize' ) => 'newest',
						esc_html__( 'Popular', 'socialize' ) => 'popular',
					),
					'type' => 'dropdown',
					),						
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );							

			// BuddyPress Recently Active Members
			vc_map( array( 
				'name' => esc_html__( 'Recently Active Members', 'socialize' ),
				'base' => 'bp_recently_active_members',
				'description' => esc_html__( 'Profile photos of recently active members.', 'socialize' ),
				'class' => 'wpb_vc_bp_recently_active_members',
				'controls' => 'full',
				'icon' => 'gp-icon-bp-members',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(				

					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Recently Active Members', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Maximum Members', 'socialize' ),
					'description' => esc_html__( 'Maximum number of members to show.', 'socialize' ),
					'param_name' => 'max_members',
					'type' => 'textfield',
					'value' => 16,
					),	
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );	
				
			// BuddyPress Who's Online
			vc_map( array( 
				'name' => esc_html__( 'Whos Online', 'socialize' ),
				'base' => 'bp_whos_online',
				'description' => esc_html__( 'Profile photos of online users.', 'socialize' ),
				'class' => 'wpb_vc_bp_whos_online',
				'controls' => 'full',
				'icon' => 'gp-icon-bp-members',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(				

					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Who\'s Online', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Maximum Members', 'socialize' ),
					'description' => esc_html__( 'Maximum number of members to show.', 'socialize' ),
					'param_name' => 'max_members',
					'type' => 'textfield',
					'value' => 16,
					),	
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),	
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );
		
		}
		
		/*--------------------------------------------------------------
		bbPress Shortcodes
		--------------------------------------------------------------*/

		if ( class_exists( 'bbPress' ) ) {
		
			require_once( socialize_vc . 'gp_vc_bbpress.php' );
			
			// bbPress Forum Search Form
			vc_map( array( 
				'name' => esc_html__( 'Forums Search Form', 'socialize' ),
				'base' => 'bbp_search',
				'description' => esc_html__( 'The bbPress forum search form.', 'socialize' ),
				'class' => 'wpb_vc_bbp_search',
				'controls' => 'full',
				'icon' => 'gp-icon-bbp-search',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(
					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Search Forums', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),	
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );

			// bbPress Forums List
			vc_map( array( 
				'name' => esc_html__( 'Forums List', 'socialize' ),
				'base' => 'bbp_forums_list',
				'description' => esc_html__( 'A list of forums with an option to set the parent.', 'socialize' ),
				'class' => 'wpb_vc_bbp_forums_list',
				'controls' => 'full',
				'icon' => 'gp-icon-bbp-forums-list',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(			
					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Forums List', 'socialize' ),
					),				
					array( 
					'heading' => esc_html__( 'Parent Forum ID', 'socialize' ),
					'description' => esc_html__( '"0" to show only root - "any" to show all.', 'socialize' ),
					'param_name' => 'parent_forum',
					'type' => 'textfield',
					'value' => '0',
					),
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );
						
			// bbPress Recent Replies
			vc_map( array( 
				'name' => esc_html__( 'Recent Replies', 'socialize' ),
				'base' => 'bbp_recent_replies',
				'description' => esc_html__( 'A list of the most recent replies.', 'socialize' ),
				'class' => 'wpb_vc_bbp_recent_replies',
				'controls' => 'full',
				'icon' => 'gp-icon-bbp-recent-replies',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(			
					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Recent Replies', 'socialize' ),
					),				
					array( 
					'heading' => esc_html__( 'Maximum Replies', 'socialize' ),
					'description' => esc_html__( 'The maximum number of replies to show.', 'socialize' ),
					'param_name' => 'max_shown',
					'type' => 'textfield',
					'value' => 5,
					),
					array(
					'heading' => esc_html__( 'Post Date', 'socialize' ),
					'param_name' => 'show_date',
					'value' => array( esc_html__( 'Show post date.', 'socialize' ) => '1' ),
					'type' => 'checkbox',
					),	
					array(
					'heading' => esc_html__( 'Author', 'socialize' ),
					'param_name' => 'show_user',
					'value' => array( esc_html__( 'Show reply author.', 'socialize' ) => '1' ),
					'type' => 'checkbox',
					),	
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );
						
			// bbPress Recent Topics
			vc_map( array( 
				'name' => esc_html__( 'Recent Topics', 'socialize' ),
				'base' => 'bbp_recent_topics',
				'description' => esc_html__( 'A list of recent topics, sorted by popularity or freshness.', 'socialize' ),
				'class' => 'wpb_vc_bbp_recent_topics',
				'controls' => 'full',
				'icon' => 'gp-icon-bbp-recent-topics',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(			
					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Recent Topics', 'socialize' ),
					),				
					array( 
					'heading' => esc_html__( 'Maximum Topics', 'socialize' ),
					'description' => esc_html__( 'The maximum number of topics to show.', 'socialize' ),
					'param_name' => 'max_shown',
					'type' => 'textfield',
					'value' => 5,
					),			
					array( 
					'heading' => esc_html__( 'Parent Forum ID', 'socialize' ),
					'description' => esc_html__( '"0" to show only root - "any" to show all.', 'socialize' ),
					'param_name' => 'parent_forum',
					'type' => 'textfield',
					'value' => 'any',
					),	
					array(
					'heading' => esc_html__( 'Post Date', 'socialize' ),
					'param_name' => 'show_date',
					'value' => array( esc_html__( 'Show post date.', 'socialize' ) => '1' ),
					'type' => 'checkbox',
					),	
					array(
					'heading' => esc_html__( 'Author', 'socialize' ),
					'param_name' => 'show_user',
					'value' => array( esc_html__( 'Show reply author.', 'socialize' ) => '1' ),
					'type' => 'checkbox',
					),				
					array( 
					'heading' => esc_html__( 'Order By', 'socialize' ),
					'description' => esc_html__( 'The criteria which the topics are ordered by.', 'socialize' ),
					'param_name' => 'order_by',
					'value' => array(
						esc_html__( 'Newest Topics', 'socialize' ) => 'newness',
						esc_html__( 'Popular Topics', 'socialize' ) => 'popular',
						esc_html__( 'Topics With Recent Replies', 'socialize' ) => 'freshness',
					),
					'type' => 'dropdown',
					),
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '#E93100',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );
						
			// bbPress Statistics
			vc_map( array( 
				'name' => esc_html__( 'bbPress Statistics', 'socialize' ),
				'base' => 'bbp_statistics',
				'description' => esc_html__( 'Some statistics from your forum.', 'socialize' ),
				'class' => 'wpb_vc_bbp_statistics',
				'controls' => 'full',
				'icon' => 'gp-icon-bbp-statistics',
				'category' => esc_html__( 'BuddyPress', 'socialize' ),
				'params' => array(			
					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => esc_html__( 'Forum Statistics', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );
						
		}
		
										
		/*--------------------------------------------------------------
		Carousel Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_carousel.php' );
		
		vc_map( array( 
			'name' => esc_html__( 'Carousel', 'socialize' ),
			'base' => 'carousel',
			'description' => esc_html__( 'Display a carousel.', 'socialize' ),
			'class' => 'wpb_vc_carousel',
			'controls' => 'full',
			'icon' => 'gp-icon-carousel',
			'category' => esc_html__( 'Content', 'socialize' ),
			'params' => array(				
				array( 
				'heading' => esc_html__( 'Title', 'socialize' ),
				'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
				'param_name' => 'widget_title',
				'type' => 'textfield',
				'admin_label' => true,
				'value' => '',
				),		
				array( 
				'heading' => esc_html__( 'Categories', 'socialize' ),
				'description' => wp_kses( __( 'Enter the ID numbers or slugs of the categories, separating each ID or slug with a comma e.g. 33,74,25. Hover your mouse over the category names on the <a href="' . admin_url ( 'edit-tags.php?taxonomy=category' ). '" target="_blank">category page</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'cats',
				'type' => 'textfield',
				),						
				array( 
				'heading' => esc_html__( 'Page IDs', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => wp_kses( __( 'Enter the ID of the pages you want to include, separating each ID with a comma e.g. 33,74,25. Hover your mouse over the page names on the <a href="' . admin_url( 'edit.php?post_type=page' ). '" target="_blank">page list</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'page_ids',
				'type' => 'textfield',
				),			
				array( 
				'heading' => esc_html__( 'Post Types', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'The post types to display.', 'socialize' ),
				'param_name' => 'post_types',
				'type' => 'posttypes',
				),			
				array( 
				'heading' => esc_html__( 'Order By', 'socialize' ),
				'description' => esc_html__( 'The criteria which the items are ordered by.', 'socialize' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__( 'Newest', 'socialize' ) => 'newest',
					esc_html__( 'Oldest', 'socialize' ) => 'oldest',
					esc_html__( 'Title (A-Z)', 'socialize' ) => 'title_az',
					esc_html__( 'Title (Z-A)', 'socialize' ) => 'title_za',
					esc_html__( 'Most Comments', 'socialize' ) => 'comment_count',
					esc_html__( 'Most Views', 'socialize' ) => 'views',
					esc_html__( 'Menu Order', 'socialize' ) => 'menu_order',
					esc_html__( 'Random', 'socialize' ) => 'rand',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Date Posted', 'socialize' ),
				'description' => esc_html__( 'The date the items were posted.', 'socialize' ),
				'param_name' => 'date_posted',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Date Modified', 'socialize' ),
				'description' => esc_html__( 'The date the items were modified.', 'socialize' ),
				'param_name' => 'date_modified',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Items In View', 'socialize' ),
				'description' => esc_html__( 'The number of items in view at one time.', 'socialize' ),
				'param_name' => 'items_in_view',
				'value' => '3',
				'type' => 'textfield',
				),								 
				array( 
				'heading' => esc_html__( 'Total Items', 'socialize' ),
				'description' => esc_html__( 'The total number of items.', 'socialize' ),
				'param_name' => 'per_page',
				'value' => '12',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Offset', 'socialize' ),
				'description' => esc_html__( 'The number of posts to offset by e.g. set to 3 to exclude the first 3 posts.', 'socialize' ),
				'param_name' => 'offset',
				'value' => '0',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Image Width', 'socialize' ),
				'description' => esc_html__( 'The width of the featured images.', 'socialize' ),
				'param_name' => 'image_width',
				'value' => '350',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),		 
				array( 
				'heading' => esc_html__( 'Image Height', 'socialize' ),
				'description' => esc_html__( 'The height of the featured images.', 'socialize' ),
				'param_name' => 'image_height',
				'value' => '220',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),	
				array( 
				'heading' => esc_html__( 'Hard Crop', 'socialize' ),
				'description' => esc_html__( 'Images are cropped even if it is smaller than the dimensions you want to crop it to.', 'socialize' ),
				'param_name' => 'hard_crop',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),					
				array( 
				'heading' => esc_html__( 'Carousel Speed', 'socialize' ),
				'description' => esc_html__( 'The number of seconds before the carousel goes to the next set of items.', 'socialize' ),
				'param_name' => 'slider_speed',
				'value' => '0',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Animation Speed', 'socialize' ),
				'description' => esc_html__( 'The speed of the carousel animation in seconds.', 'socialize' ),
				'param_name' => 'animation_speed',
				'value' => '0.6',
				'type' => 'textfield',		
				),	
				array( 
				'heading' => esc_html__( 'Navigation Buttons', 'socialize' ),
				'description' => esc_html__( 'Display the carousel navigation buttons.', 'socialize' ),
				'param_name' => 'buttons',
				'value' => array(
					esc_html__( 'Enabled', 'socialize' ) => 'enabled',
					esc_html__( 'Disabled', 'socialize' ) => 'disabled',
				),
				'type' => 'dropdown',
				),					
				array( 
				'heading' => esc_html__( 'Navigation Arrows', 'socialize' ),
				'description' => esc_html__( 'Display the carousel navigation arrows.', 'socialize' ),
				'param_name' => 'arrows',
				'value' => array(
					esc_html__( 'Enabled', 'socialize' ) => 'enabled',
					esc_html__( 'Disabled', 'socialize' ) => 'disabled',
				),
				'type' => 'dropdown',
				),				
				array( 
				'heading' => esc_html__( 'See All', 'socialize' ),
				'description' => esc_html__( 'Add a "See All" link.', 'socialize' ),
				'param_name' => 'see_all',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'See All Link', 'socialize' ),
				'description' => esc_html__( 'URL for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_link',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),				 			 
				array( 
				'heading' => esc_html__( 'See All Text', 'socialize' ),
				'description' => esc_html__( 'Custom text for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_text',
				'type' => 'textfield',
				'value' => esc_html__( 'See All Items', 'socialize' ),
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),	 			 				 		   			 			 
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'Choose the title format.', 'socialize' ),
				'param_name' => 'title_format',
				'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
				'type' => 'dropdown',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),
				array( 
				'heading' => esc_html__( 'Title Color', 'socialize' ),
				'description' => esc_html__( 'The title color.', 'socialize' ),
				'param_name' => 'title_color',
				'value' => '#E93100',
				'type' => 'colorpicker',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),		
				array( 
				'heading' => esc_html__( 'Title Icon', 'socialize' ),
				'param_name' => 'icon',
				'value' => $icons,
				'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
				'type' => 'icon_selection',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),																																																						
			 )
		) );

					
		/*--------------------------------------------------------------
		Events List Shortcode
		--------------------------------------------------------------*/		

		if ( class_exists( 'Tribe__Events__Main' ) ) {

			require_once( socialize_vc . 'gp_vc_events.php' );

			vc_map( array( 
				'name' => esc_html__( 'Events List', 'socialize' ),
				'base' => 'events_list',
				'description' => esc_html__( 'A widget that displays upcoming events.', 'socialize' ),
				'class' => 'wpb_vc_events_list',
				'controls' => 'full',
				'icon' => 'gp-icon-events-list',
				'category' => esc_html__( 'Content', 'socialize' ),
				'params' => array(				

					array( 
					'heading' => esc_html__( 'Title', 'socialize' ),
					'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
					'param_name' => 'title',
					'type' => 'textfield',
					'admin_label' => true,
					'value' => '',
					),
					array( 
					'heading' => esc_html__( 'Number of Events', 'socialize' ),
					'description' => esc_html__( 'Number of events to show.', 'socialize' ),
					'param_name' => 'limit',
					'type' => 'textfield',
					'value' => '5',
					),
					array( 
					'heading' => esc_html__( 'Show', 'socialize' ),
					'param_name' => 'no_upcoming_events',
					'type' => 'checkbox',
					'value' => array( esc_html__( 'Show widget only if there are upcoming events', 'socialize' ) => '1' ),
					),
					array( 
					'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
					'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
					'param_name' => 'classes',
					'value' => '',
					'type' => 'textfield',
					),		
					array( 
					'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
					'description' => esc_html__( 'Choose the title format.', 'socialize' ),
					'param_name' => 'title_format',
					'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
					'type' => 'dropdown',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
					array( 
					'heading' => esc_html__( 'Title Color', 'socialize' ),
					'description' => esc_html__( 'The title color.', 'socialize' ),
					'param_name' => 'title_color',
					'value' => '',
					'type' => 'colorpicker',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),	
					array( 
					'heading' => esc_html__( 'Title Icon', 'socialize' ),
					'param_name' => 'icon',
					'value' => $icons,
					'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
					'type' => 'icon_selection',
					'group' => esc_html__( 'Design options', 'socialize' ),
					),
				 )
			) );	
		
		}
							
		/*--------------------------------------------------------------
		Login/Register Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_login.php' );
		
		vc_map( array( 
			'name' => esc_html__( 'Login/Register Form', 'socialize' ),
			'base' => 'login',
			'description' => esc_html__( 'Add a login and register form.', 'socialize' ),
			'class' => 'wpb_vc_login',
			'controls' => 'full',
			'icon' => 'gp-icon-login',
			'category' => esc_html__( 'Content', 'socialize' ),
			'params' => array(				
				array( 
				'heading' => esc_html__( 'Title', 'socialize' ),
				'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
				'param_name' => 'widget_title',
				'type' => 'textfield',
				'admin_label' => true,
				'value' => '',
				),				
				array( 
				'heading' => esc_html__( 'Default View', 'socialize' ),
				'description' => esc_html__( 'Choose whether the login or register form is shown by default.', 'socialize' ),
				'param_name' => 'default_view',
				'value' => array( esc_html__( 'Login Form', 'socialize' ) => 'gp-default-view-login', esc_html__( 'Registration Form', 'socialize' ) => 'gp-default-view-register' ),
				'type' => 'dropdown',
				),																																										
			 )
		) );
		
										
		/*--------------------------------------------------------------
		Portfolio Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_portfolio.php' );
		
		vc_map( array( 
			'name' => esc_html__( 'Portfolio', 'socialize' ),
			'base' => 'portfolio',
			'description' => esc_html__( 'Display your portfolio items in a variety of ways.', 'socialize' ),
			'class' => 'wpb_vc_portfolio',
			'controls' => 'full',
			'icon' => 'gp-icon-portfolio',
			'category' => esc_html__( 'Content', 'socialize' ),
			'params' => array( 		
				array( 
				'heading' => esc_html__( 'Format', 'socialize' ),
				'description' => esc_html__( 'The format to display the items in.', 'socialize' ),
				'param_name' => 'format',
				'value' => array( esc_html__( '2 Columns', 'socialize' ) => 'gp-portfolio-columns-2', esc_html__( '3 Columns', 'socialize' ) => 'gp-portfolio-columns-3', esc_html__( '4 Columns', 'socialize' ) => 'gp-portfolio-columns-4', esc_html__( '5 Columns', 'socialize' ) => 'gp-portfolio-columns-5', esc_html__( '6 Columns', 'socialize' ) => 'gp-portfolio-columns-6', esc_html__( 'Masonry', 'socialize' ) => 'gp-portfolio-masonry' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Categories', 'socialize' ),
				'description' => wp_kses( __('Enter the ID numbers or slugs of the categories, separating each ID or slug with a comma e.g. 33,74,25. Hover your mouse over the category names on the <a href="' . admin_url( 'edit-tags.php?taxonomy=gp_portfolios&post_type=gp_portfolio_item' ) . '" target="_blank">category page</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'cats',
				'value' => '',
				'type' => 'textfield',
				),		 
				array( 
				'heading' => esc_html__( 'Order By', 'socialize' ),
				'description' => esc_html__( 'The criteria which the items are ordered by.', 'socialize' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__( 'Newest', 'socialize' ) => 'newest',
					esc_html__( 'Oldest', 'socialize' ) => 'oldest',
					esc_html__( 'Title (A-Z)', 'socialize' ) => 'title_az',
					esc_html__( 'Title (Z-A)', 'socialize' ) => 'title_za',
					esc_html__( 'Most Comments', 'socialize' ) => 'comment_count',
					esc_html__( 'Most Views', 'socialize' ) => 'views',
					esc_html__( 'Menu Order', 'socialize' ) => 'menu_order',
					esc_html__( 'Random', 'socialize' ) => 'rand',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Date Posted', 'socialize' ),
				'description' => esc_html__( 'The date the items were posted.', 'socialize' ),
				'param_name' => 'date_posted',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Date Modified', 'socialize' ),
				'description' => esc_html__( 'The date the items were modified.', 'socialize' ),
				'param_name' => 'date_modified',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),			
				array( 
				'heading' => esc_html__( 'Filter', 'socialize' ),
				'description' => esc_html__( 'Add category filter links to the page.', 'socialize' ),
				'param_name' => 'filter',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),			 
				array( 
				'heading' => esc_html__( 'Items Per Page', 'socialize' ),
				'description' => esc_html__( 'The number of items on each page.', 'socialize' ),
				'param_name' => 'per_page',
				'value' => '12',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Offset', 'socialize' ),
				'description' => esc_html__( 'The number of posts to offset by e.g. set to 3 to exclude the first 3 posts.', 'socialize' ),
				'param_name' => 'offset',
				'value' => '0',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Pagination', 'socialize' ),
				'description' => esc_html__( 'Add pagination.', 'socialize' ),
				'param_name' => 'page_numbers',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),		 		 				 		   			 			 
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'type' => 'textfield',
				),	
				array( 
				'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'Choose the title format.', 'socialize' ),
				'param_name' => 'title_format',
				'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
				'type' => 'dropdown',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),						
				array( 
				'heading' => esc_html__( 'Title Color', 'socialize' ),
				'description' => esc_html__( 'The title color.', 'socialize' ),
				'param_name' => 'title_color',
				'value' => '#E93100',
				'type' => 'colorpicker',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),
				array( 
				'heading' => esc_html__( 'Title Icon', 'socialize' ),
				'param_name' => 'icon',
				'value' => $icons,
				'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
				'type' => 'icon_selection',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),																																							
			 )
		) );


		/*--------------------------------------------------------------
		Pricing Table Shortcode
		--------------------------------------------------------------*/

		// Pricing Table
		vc_map( array( 
			'name' => esc_html__( 'Pricing Table', 'socialize' ),
			'base' => 'pricing_table',
			'description' => esc_html__( 'A table to compare the prices of different items.', 'socialize' ),
			'as_parent' => array( 'only' => 'pricing_column' ),
			'controls' => 'full',
			'icon' => 'gp-icon-pricing-table',
			'category' => esc_html__( 'Content', 'socialize' ),
			'js_view' => 'VcColumnView',
			'params' => array( 
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'type' => 'textfield',
				),	
			),
			'js_view' => 'VcColumnView'
		 ) );


		// Pricing Column
		vc_map( array( 
			'name' => esc_html__( 'Pricing Column', 'socialize' ),
			'base' => 'pricing_column',
			'content_element' => true,
			'as_child' => array( 'only' => 'pricing_table' ),
			'icon' => 'gp-icon-pricing-table',
			'params' => array( 	
				array( 
				'heading' => esc_html__( 'Column Title', 'socialize' ),
				'description' => esc_html__( 'The title for the column.', 'socialize' ),
				'param_name' => 'title',
				'value' => '',
				'type' => 'textfield'
				),
				array( 
				'heading' => esc_html__( 'Price', 'socialize' ),
				'description' => esc_html__( 'The price for the column.', 'socialize' ),
				'param_name' => 'price',
				'value' => '',
				'type' => 'textfield'
				),
				array( 
				'heading' => esc_html__( 'Currency Symbol', 'socialize' ),
				'description' => esc_html__( 'The currency symbol.', 'socialize' ),
				'param_name' => 'currency_symbol',
				'value' => '',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Interval', 'socialize' ),
				'description' => esc_html__( 'The interval for the column e.g. per week, per month.', 'socialize' ),
				'param_name' => 'interval',
				'value' => '',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Highlight Column', 'socialize' ),
				'description' => esc_html__( 'Make this column stand out.', 'socialize' ),
				'param_name' => 'highlight',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown'
				),	
				array( 
				'heading' => esc_html__( 'Highlight Text', 'socialize' ),
				'description' => esc_html__( 'Add highlight text above the column title.', 'socialize' ),
				'param_name' => 'highlight_text',
				'value' => '',
				'dependency' => array( 'element' => 'highlight', 'value' => 'enabled' ),
				'type' => 'textfield',
				),	
				array( 
				'heading' => esc_html__( 'Content', 'socialize' ),
				'description' => esc_html__( 'Use the Unordered List button to create the points in your pricing column. You can also add shortcodes such as the [button link="#"] shortcode seen in the site demo.', 'socialize' ),
				'param_name' => 'content',
				'type' => 'textarea_html',
				),
				array( 
				'heading' => esc_html__( 'Highlight Color', 'socialize' ),
				'description' => esc_html__( 'The highlight color.', 'socialize' ),
				'param_name' => 'highlight_color',
				'value' => '#f84103',
				'dependency' => array( 'element' => 'highlight', 'value' => 'enabled' ),
				'type' => 'colorpicker',
				),		
				array( 
				'heading' => esc_html__( 'Title Color', 'socialize' ),
				'description' => esc_html__( 'The title color.', 'socialize' ),
				'param_name' => 'title_color',
				'value' => '#f84103',
				'dependency' => array( 'element' => 'highlight', 'value' => 'disabled' ),
				'type' => 'colorpicker',
				),	
				array( 
				'heading' => esc_html__( 'Highlight Title Color', 'socialize' ),
				'description' => esc_html__( 'The highlight title color.', 'socialize' ),
				'param_name' => 'highlight_title_color',
				'value' => '#fff',
				'dependency' => array( 'element' => 'highlight', 'value' => 'enabled' ),
				'type' => 'colorpicker',
				),	
				array( 
				'heading' => esc_html__( 'Background Color', 'socialize' ),
				'description' => esc_html__( 'The background color.', 'socialize' ),
				'param_name' => 'background_color',
				'value' => '#f7f7f7',
				'dependency' => array( 'element' => 'highlight', 'value' => 'disabled' ),
				'type' => 'colorpicker',
				),		 
				array( 
				'heading' => esc_html__( 'Highlight Background Color', 'socialize' ),
				'description' => esc_html__( 'The highlight background color.', 'socialize' ),
				'param_name' => 'highlight_background_color',
				'value' => '#fff',
				'dependency' => array( 'element' => 'highlight', 'value' => 'enabled' ),
				'type' => 'colorpicker',
				),		 		 		 
				array( 
				'heading' => esc_html__( 'Text Color', 'socialize' ),
				'description' => esc_html__( 'The text color.', 'socialize' ),
				'param_name' => 'text_color',
				'value' => '#747474',
				'type' => 'colorpicker',
				),	
				array( 
				'heading' => esc_html__( 'Border', 'socialize' ),
				'description' => esc_html__( 'Add a border around the columns.', 'socialize' ),
				'param_name' => 'border',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),			 
				array( 
				'heading' => esc_html__( 'Border Color', 'socialize' ),
				'description' => esc_html__( 'The border color.', 'socialize' ),
				'param_name' => 'border_color',
				'value' => '#e7e7e7',
				'dependency' => array( 'element' => 'border', 'value' => 'enabled' ),
				'type' => 'colorpicker',
				),	 		 																																							
			 )
		 ) );

		class WPBakeryShortCode_Pricing_Table extends WPBakeryShortCodesContainer {}
		class WPBakeryShortCode_Pricing_Column extends WPBakeryShortCode {}	


		/*--------------------------------------------------------------
		Showcase Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_showcase.php' );

		vc_map( array( 
			'name' => esc_html__( 'Showcase', 'socialize' ),
			'base' => 'showcase',
			'description' => esc_html__( 'Display your content in horizontal and vertical formats.', 'socialize' ),
			'class' => 'wpb_vc_showcase',
			'controls' => 'full',
			'icon' => 'gp-icon-showcase',
			'category' => esc_html__( 'Content', 'socialize' ),			
			'admin_enqueue_css' => socialize_css_uri . 'admin.css',
			'front_enqueue_css' => socialize_css_uri . 'admin.css',
			'params' => array(		
				array( 
				'heading' => esc_html__( 'Title', 'socialize' ),
				'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
				'param_name' => 'widget_title',
				'type' => 'textfield',
				'admin_label' => true,
				'value' => '',
				),		 									
				array( 
				'heading' => esc_html__( 'Categories', 'socialize' ),
				'description' => wp_kses( __( 'Enter the ID numbers or slugs of the categories, separating each ID or slug with a comma e.g. 33,74,25. Hover your mouse over the category names on the <a href="' . admin_url ( 'edit-tags.php?taxonomy=category'). '" target="_blank">category page</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'cats',
				'type' => 'textfield',
				),					
				array( 
				'heading' => esc_html__( 'Page IDs', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => wp_kses( __( 'Enter the ID of the pages you want to include, separating each ID with a comma e.g. 33,74,25. Hover your mouse over the page names on the <a href="' . admin_url( 'edit.php?post_type=page' ). '" target="_blank">page list</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'page_ids',
				'type' => 'textfield',
				),			
				array( 
				'heading' => esc_html__( 'Post Types', 'socialize' ),
				'description' => esc_html__( 'The post types to display.', 'socialize' ),
				'param_name' => 'post_types',
				'type' => 'posttypes',
				),			
				array( 
				'heading' => esc_html__( 'Format', 'socialize' ),
				'description' => esc_html__( 'The format to display the items in.', 'socialize' ),
				'param_name' => 'format',
				'value' => array( esc_html__( 'Horizontal Showcase', 'socialize' ) => 'gp-blog-horizontal', esc_html__( 'Vertical Showcase', 'socialize' ) => 'gp-blog-vertical' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Order By', 'socialize' ),
				'description' => esc_html__( 'The criteria which the items are ordered by.', 'socialize' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__( 'Newest', 'socialize' ) => 'newest',
					esc_html__( 'Oldest', 'socialize' ) => 'oldest',
					esc_html__( 'Title (A-Z)', 'socialize' ) => 'title_az',
					esc_html__( 'Title (Z-A)', 'socialize' ) => 'title_za',
					esc_html__( 'Most Comments', 'socialize' ) => 'comment_count',
					esc_html__( 'Most Views', 'socialize' ) => 'views',
					esc_html__( 'Menu Order', 'socialize' ) => 'menu_order',
					esc_html__( 'Random', 'socialize' ) => 'rand',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Date Posted', 'socialize' ),
				'description' => esc_html__( 'The date the items were posted.', 'socialize' ),
				'param_name' => 'date_posted',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Date Modified', 'socialize' ),
				'description' => esc_html__( 'The date the items were modified.', 'socialize' ),
				'param_name' => 'date_modified',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Filter', 'socialize' ),
				'description' => esc_html__( 'Add a dropdown filter menu to the page.', 'socialize' ),
				'param_name' => 'filter',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),	
				array(
				'heading' => esc_html__( 'Filter Options', 'socialize' ),
				'param_name' => 'filter_cats',
				'value' => array( esc_html__( 'Categories', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),	
				array(
				'param_name' => 'filter_date',
				'value' => array( esc_html__( 'Date', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),	
				array(
				'param_name' => 'filter_title',
				'value' => array( esc_html__( 'Title', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),								
				array(
				'param_name' => 'filter_comment_count',
				'value' => array( esc_html__( 'Comment Count', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),
				array(
				'param_name' => 'filter_views',
				'value' => array( esc_html__( 'Views', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),	
				array( 
				'param_name' => 'filter_date_posted',
				'value' => array( esc_html__( 'Date Posted', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),				
				array( 
				'description' => esc_html__( 'Choose what options to display in the dropdown filter menu.', 'socialize' ),
				'param_name' => 'filter_date_modified',
				'value' => array( esc_html__( 'Date Modified', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),				
				array( 
				'heading' => esc_html__( 'Filter Category', 'socialize' ),
				'description' => wp_kses( __('Enter the ID number or slug of the category you want to filter by, leave blank to display all categories - the sub categories of this category will also be displayed. Hover your mouse over the category names on the <a href="' . admin_url ( 'edit-tags.php?taxonomy=category'). '" target="_blank">category page</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'filter_cats_id',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'filter', 'value' => 'enabled' ),
				),																	 
				array( 
				'heading' => esc_html__( 'Items Per Page', 'socialize' ),
				'description' => esc_html__( 'The number of items on each page.', 'socialize' ),
				'param_name' => 'per_page',
				'value' => '5',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Offset', 'socialize' ),
				'description' => esc_html__( 'The number of posts to offset by e.g. set to 3 to exclude the first 3 posts.', 'socialize' ),
				'param_name' => 'offset',
				'value' => '0',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Large Featured Image', 'socialize' ),
				'description' => esc_html__( 'Display the large featured image.', 'socialize' ),
				'param_name' => 'large_featured_image',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),					
				array( 
				'heading' => esc_html__( 'Large Image Width', 'socialize' ),
				'description' => esc_html__( 'The width of the large featured image.', 'socialize' ),
				'param_name' => 'large_image_width',
				'value' => '350',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'large_featured_image', 'value' => 'enabled' ),
				),		 
				array( 
				'heading' => esc_html__( 'Large Image Height', 'socialize' ),
				'description' => esc_html__( 'The height of the large featured image.', 'socialize' ),
				'param_name' => 'large_image_height',
				'value' => '220',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'large_featured_image', 'value' => 'enabled' ),
				),
				array( 
				'heading' => esc_html__( 'Small Featured Image', 'socialize' ),
				'description' => esc_html__( 'Display the small featured image.', 'socialize' ),
				'param_name' => 'small_featured_image',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),				
				array( 
				'heading' => esc_html__( 'Small Image Width', 'socialize' ),
				'description' => esc_html__( 'The width of the small featured images.', 'socialize' ),
				'param_name' => 'small_image_width',
				'value' => '100',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'small_featured_image', 'value' => 'enabled' ),
				),		 
				array( 
				'heading' => esc_html__( 'Small Image Height', 'socialize' ),
				'description' => esc_html__( 'The height of the small featured images.', 'socialize' ),
				'param_name' => 'small_image_height',
				'value' => '65',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'small_featured_image', 'value' => 'enabled' ),
				),					
				array( 
				'heading' => esc_html__( 'Hard Crop', 'socialize' ),
				'description' => esc_html__( 'Images are cropped even if it is smaller than the dimensions you want to crop it to.', 'socialize' ),
				'param_name' => 'hard_crop',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Large Image Alignment', 'socialize' ),
				'description' => esc_html__( 'Choose how the large image aligns with the content.', 'socialize' ),
				'param_name' => 'large_image_alignment',
				'value' => array( esc_html__( 'Above Content', 'socialize' ) => 'image-above', esc_html__( 'Left Wrap', 'socialize' ) => 'gp-image-wrap-left', esc_html__( 'Right Wrap', 'socialize' ) => 'gp-image-wrap-right', esc_html__( 'Left Align', 'socialize' ) => 'gp-image-align-left', esc_html__( 'Right Align', 'socialize' ) => 'gp-image-align-right' ),
				'type' => 'dropdown',
				'dependency' => array( 'element' => 'large_featured_image', 'value' => 'enabled' ),
				),
				array( 
				'heading' => esc_html__( 'Small Image Alignment', 'socialize' ),
				'description' => esc_html__( 'Choose how the small image aligns with the content.', 'socialize' ),
				'param_name' => 'small_image_alignment',
				'value' => array( esc_html__( 'Left Align', 'socialize' ) => 'gp-image-align-left', esc_html__( 'Left Wrap', 'socialize' ) => 'gp-image-wrap-left', esc_html__( 'Right Wrap', 'socialize' ) => 'gp-image-wrap-right', esc_html__( 'Above Content', 'socialize' ) => 'image-above', esc_html__( 'Right Align', 'socialize' ) => 'gp-image-align-right' ),
				'type' => 'dropdown',
				'dependency' => array( 'element' => 'small_featured_image', 'value' => 'enabled' ),
				),				
				array( 
				'heading' => esc_html__( 'Large Excerpt Length', 'socialize' ),
				'description' => esc_html__( 'The number of characters in large excerpts.', 'socialize' ),
				'param_name' => 'large_excerpt_length',
				'value' => '80',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Small Excerpt Length', 'socialize' ),
				'description' => esc_html__( 'The number of characters in small excerpts.', 'socialize' ),
				'param_name' => 'small_excerpt_length',
				'value' => '0',
				'type' => 'textfield',
				),					
				array(
				'heading' => esc_html__( 'Large Post Meta', 'socialize' ),
				'param_name' => 'large_meta_author',
				'value' => array( esc_html__( 'Author Name', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array(
				'param_name' => 'large_meta_date',
				'value' => array( esc_html__( 'Post Date', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),		
				array(
				'param_name' => 'large_meta_comment_count',
				'value' => array( esc_html__( 'Comment Count', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array(
				'param_name' => 'large_meta_views',
				'value' => array( esc_html__( 'Views', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),
				array( 
				'param_name' => 'large_meta_cats',
				'value' => array( esc_html__( 'Post Categories', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array(
				'description' => esc_html__( 'Select the large meta data you want to display.', 'socialize' ),
				'param_name' => 'large_meta_tags',
				'value' => array( esc_html__( 'Post Tags', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),
				array(
				'heading' => esc_html__( 'Small Post Meta', 'socialize' ),
				'param_name' => 'small_meta_author',
				'value' => array( esc_html__( 'Author Name', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array(
				'param_name' => 'small_meta_date',
				'value' => array( esc_html__( 'Post Date', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),		
				array(
				'param_name' => 'small_meta_comment_count',
				'value' => array( esc_html__( 'Comment Count', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array(
				'param_name' => 'small_meta_views',
				'value' => array( esc_html__( 'Views', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),
				array( 
				'param_name' => 'small_meta_cats',
				'value' => array( esc_html__( 'Post Categories', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),
				array(
				'description' => esc_html__( 'Select the small meta data you want to display.', 'socialize' ),
				'param_name' => 'small_meta_tags',
				'value' => array( esc_html__( 'Post Tags', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),	
				array( 
				'heading' => esc_html__( 'Large Read More Link', 'socialize' ),
				'description' => esc_html__( 'Add a read more link below the large content.', 'socialize' ),
				'param_name' => 'large_read_more_link',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),												
				array( 
				'heading' => esc_html__( 'Small Read More Link', 'socialize' ),
				'description' => esc_html__( 'Add a read more link below the small content.', 'socialize' ),
				'param_name' => 'small_read_more_link',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Pagination (Arrows)', 'socialize' ),
				'description' => esc_html__( 'Add pagination arrows.', 'socialize' ),
				'param_name' => 'page_arrows',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Pagination (Numbers)', 'socialize' ),
				'description' => esc_html__( 'Add pagination numbers.', 'socialize' ),
				'param_name' => 'page_numbers',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),						
				array( 
				'heading' => esc_html__( 'See All', 'socialize' ),
				'description' => esc_html__( 'Add a "See All" link.', 'socialize' ),
				'param_name' => 'see_all',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'See All Link', 'socialize' ),
				'description' => esc_html__( 'URL for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_link',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),				 			 
				array( 
				'heading' => esc_html__( 'See All Text', 'socialize' ),
				'description' => esc_html__( 'Custom text for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_text',
				'type' => 'textfield',
				'value' => esc_html__( 'See All Items', 'socialize' ),
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),	 			 				 		   			 			 
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'Choose the title format.', 'socialize' ),
				'param_name' => 'title_format',
				'value' => array( esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title', esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title' ),
				'type' => 'dropdown',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),
				array( 
				'heading' => esc_html__( 'Title Color', 'socialize' ),
				'description' => esc_html__( 'The title color.', 'socialize' ),
				'param_name' => 'title_color',
				'value' => '#E93100',
				'type' => 'colorpicker',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),	
				array( 
				'heading' => esc_html__( 'Title Icon', 'socialize' ),
				'param_name' => 'icon',
				'value' => $icons,
				'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
				'type' => 'icon_selection',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),																																											
			 )
		) );
		

		/*--------------------------------------------------------------
		Slider Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_slider.php' );
		
		vc_map( array( 
			'name' => esc_html__( 'Slider', 'socialize' ),
			'base' => 'slider',
			'description' => esc_html__( 'Display a slider.', 'socialize' ),
			'class' => 'wpb_vc_slider',
			'controls' => 'full',
			'icon' => 'gp-icon-slider',
			'category' => esc_html__( 'Content', 'socialize' ),
			'params' => array(					
				array( 
				'heading' => esc_html__( 'Title', 'socialize' ),
				'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
				'param_name' => 'widget_title',
				'type' => 'textfield',
				'admin_label' => true,
				'value' => '',
				),				
				array( 
				'heading' => esc_html__( 'Categories', 'socialize' ),
				'description' => wp_kses( __( 'Enter the ID numbers or slugs of the categories, separating each ID or slug with a comma e.g. 33,74,25. Hover your mouse over the category names on the <a href="' . admin_url ( 'edit-tags.php?taxonomy=category' ). '" target="_blank">category page</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'cats',
				'type' => 'textfield',
				),					
				array( 
				'heading' => esc_html__( 'Page IDs', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => wp_kses( __( 'Enter the ID of the pages you want to include, separating each ID with a comma e.g. 33,74,25. Hover your mouse over the page names on the <a href="' . admin_url( 'edit.php?post_type=page' ). '" target="_blank">page list</a> to reveal the URL which contains the ID numbers.', 'socialize' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'param_name' => 'page_ids',
				'type' => 'textfield',
				),			
				array( 
				'heading' => esc_html__( 'Post Types', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'The post types to display.', 'socialize' ),
				'param_name' => 'post_types',
				'type' => 'posttypes',
				),
				array( 
				'heading' => esc_html__( 'Format', 'socialize' ),
				'description' => esc_html__( 'The format of the slider.', 'socialize' ),
				'param_name' => 'format',
				'value' => array(
					esc_html__( 'Two Columns', 'socialize' ) => 'gp-slider-two-cols',
					esc_html__( 'One Column', 'socialize' ) => 'gp-slider-one-col',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Large Image Width', 'socialize' ),
				'description' => esc_html__( 'The width of the large slider image.', 'socialize' ),
				'param_name' => 'large_image_width',
				'value' => '',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Large Image Height', 'socialize' ),
				'description' => esc_html__( 'The height of the large slider image.', 'socialize' ),
				'param_name' => 'large_image_height',
				'value' => '',
				'type' => 'textfield',
				),	
				array( 
				'heading' => esc_html__( 'Small Image Width', 'socialize' ),
				'dependency' => array( 'element' => 'format', 'value' => array( 'three-cols', 'gp-slider-two-cols' ) ),
				'description' => esc_html__( 'The width of the small slider image.', 'socialize' ),
				'param_name' => 'small_image_width',
				'value' => '',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Small Image Height', 'socialize' ),
				'dependency' => array( 'element' => 'format', 'value' => array( 'three-cols', 'gp-slider-two-cols' ) ),
				'description' => esc_html__( 'The height of the small slider image.', 'socialize' ),
				'param_name' => 'small_image_height',
				'value' => '',
				'type' => 'textfield',
				),			
				array( 
				'heading' => esc_html__( 'Hard Crop', 'socialize' ),
				'description' => esc_html__( 'Images are cropped even if it is smaller than the dimensions you want to crop it to.', 'socialize' ),
				'param_name' => 'hard_crop',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				'dependency' => array( 'element' => 'featured_image', 'value' => 'enabled' ),
				),							
				array( 
				'heading' => esc_html__( 'Caption Title', 'socialize' ),
				'description' => esc_html__( 'Display the caption titles.', 'socialize' ),
				'param_name' => 'caption_title',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Caption Text', 'socialize' ),
				'description' => esc_html__( 'Display the caption text (only displays in the main slide).', 'socialize' ),
				'param_name' => 'caption_text',
				'value' => array( esc_html__( 'Enabled', 'socialize' ) => 'enabled', esc_html__( 'Disabled', 'socialize' ) => 'disabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Animation', 'socialize' ),
				'description' => esc_html__( 'The slider animation.', 'socialize' ),
				'param_name' => 'animation',
				'value' => array(
					esc_html__( 'Slide', 'socialize' ) => 'slide',
					esc_html__( 'Fade', 'socialize' ) => 'fade',
				),
				'type' => 'dropdown',
				),								
				array( 
				'heading' => esc_html__( 'Slider Speed', 'socialize' ),
				'description' => esc_html__( 'The number of seconds before the slider goes to the next slide.', 'socialize' ),
				'param_name' => 'slider_speed',
				'value' => '6',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Animation Speed', 'socialize' ),
				'description' => esc_html__( 'The speed of the slider animation in seconds.', 'socialize' ),
				'param_name' => 'animation_speed',
				'value' => '0.6',
				'type' => 'textfield',		
				),	
				array( 
				'heading' => esc_html__( 'Navigation Buttons', 'socialize' ),
				'description' => esc_html__( 'Display the slider navigation buttons.', 'socialize' ),
				'param_name' => 'buttons',
				'value' => array(
					esc_html__( 'Enabled', 'socialize' ) => 'enabled',
					esc_html__( 'Disabled', 'socialize' ) => 'disabled',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Order By', 'socialize' ),
				'description' => esc_html__( 'The criteria which the items are ordered by.', 'socialize' ),
				'param_name' => 'orderby',
				'value' => array(
					esc_html__( 'Newest', 'socialize' ) => 'newest',
					esc_html__( 'Oldest', 'socialize' ) => 'oldest',
					esc_html__( 'Title (A-Z)', 'socialize' ) => 'title_az',
					esc_html__( 'Title (Z-A)', 'socialize' ) => 'title_za',
					esc_html__( 'Most Comments', 'socialize' ) => 'comment_count',
					esc_html__( 'Most Views', 'socialize' ) => 'views',
					esc_html__( 'Menu Order', 'socialize' ) => 'menu_order',
					esc_html__( 'Random', 'socialize' ) => 'rand',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Date Posted', 'socialize' ),
				'description' => esc_html__( 'The date the items were posted.', 'socialize' ),
				'param_name' => 'date_posted',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'Date Modified', 'socialize' ),
				'description' => esc_html__( 'The date the items were modified.', 'socialize' ),
				'param_name' => 'date_modified',
				'value' => array(
					esc_html__( 'Any date', 'socialize' ) => 'all',
					esc_html__( 'In the last year', 'socialize' ) => 'year',
					esc_html__( 'In the last month', 'socialize' ) => 'month',
					esc_html__( 'In the last week', 'socialize' ) => 'week',
					esc_html__( 'In the last day', 'socialize' ) => 'day',
				),
				'type' => 'dropdown',
				),	
				array( 
				'heading' => esc_html__( 'Number Of Slides', 'socialize' ),
				'description' => esc_html__( 'The number of slides.', 'socialize' ),
				'param_name' => 'per_page',
				'value' => '9',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Offset', 'socialize' ),
				'description' => esc_html__( 'The number of posts to offset by e.g. set to 3 to exclude the first 3 posts.', 'socialize' ),
				'param_name' => 'offset',
				'value' => '0',
				'type' => 'textfield',
				),	
				array( 
				'heading' => esc_html__( 'See All', 'socialize' ),
				'description' => esc_html__( 'Add a "See All" link.', 'socialize' ),
				'param_name' => 'see_all',
				'value' => array( esc_html__( 'Disabled', 'socialize' ) => 'disabled', esc_html__( 'Enabled', 'socialize' ) => 'enabled' ),
				'type' => 'dropdown',
				),
				array( 
				'heading' => esc_html__( 'See All Link', 'socialize' ),
				'description' => esc_html__( 'URL for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_link',
				'type' => 'textfield',
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),				 			 
				array( 
				'heading' => esc_html__( 'See All Text', 'socialize' ),
				'description' => esc_html__( 'Custom text for the "See All" link.', 'socialize' ),
				'param_name' => 'see_all_text',
				'type' => 'textfield',
				'value' => esc_html__( 'See All Items', 'socialize' ),
				'dependency' => array( 'element' => 'see_all', 'value' => 'enabled' ),
				),																	 						 		   			 			 
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'type' => 'textfield',
				'param_name' => 'classes',
				'value' => '',
				),		
				array( 
				'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'Choose the title format.', 'socialize' ),
				'param_name' => 'title_format',
				'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
				'type' => 'dropdown',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),
				array( 
				'heading' => esc_html__( 'Title Color', 'socialize' ),
				'description' => esc_html__( 'The title color.', 'socialize' ),
				'param_name' => 'title_color',
				'value' => '#E93100',
				'type' => 'colorpicker',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),					
				array( 
				'heading' => esc_html__( 'Title Icon', 'socialize' ),
				'param_name' => 'icon',
				'value' => $icons,
				'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
				'type' => 'icon_selection',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),																																							
			 )
		) );


		/*--------------------------------------------------------------
		Statistics Shortcode
		--------------------------------------------------------------*/

		require_once( socialize_vc . 'gp_vc_statistics.php' );
		
		vc_map( array( 
			'name' => esc_html__( 'Statistics', 'socialize' ),
			'base' => 'statistics',
			'description' => esc_html__( 'Display a list of site statistics.', 'socialize' ),
			'class' => 'wpb_vc_statistics',
			'controls' => 'full',
			'icon' => 'gp-icon-statistics',
			'category' => esc_html__( 'Content', 'socialize' ),
			'params' => array(				
				array( 
				'heading' => esc_html__( 'Title', 'socialize' ),
				'description' => esc_html__( 'The title at the top of the element.', 'socialize' ),
				'param_name' => 'widget_title',
				'type' => 'textfield',
				'admin_label' => true,
				'value' => '',
				),				
				array( 
				'heading' => esc_html__( 'Statistics', 'socialize' ),
				'param_name' => 'posts',
				'value' => array( esc_html__( 'Posts', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),			
				array( 
				'param_name' => 'comments',
				'value' => array( esc_html__( 'Comments', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),			
				array( 
				'param_name' => 'blogs',
				'value' => array( esc_html__( 'Blogs', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),				
				array( 
				'param_name' => 'activity',
				'value' => array( esc_html__( 'BuddyPress Activity Updates', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),				
				array( 
				'param_name' => 'members',
				'value' => array( esc_html__( 'BuddyPress Members', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),				
				array( 
				'param_name' => 'groups',
				'value' => array( esc_html__( 'BuddyPress Groups', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),				
				array( 
				'param_name' => 'forums',
				'value' => array( esc_html__( 'bbPress Forums', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				),				
				array( 
				'param_name' => 'topics',
				'value' => array( esc_html__( 'bbPress Forum Topics', 'socialize' ) => '1' ),
				'type' => 'checkbox',
				'description' => esc_html__( 'Choose what statistics to show.', 'socialize' ),
				),							
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'type' => 'textfield',
				),	
				array( 
				'heading' => esc_html__( 'Title Format', 'socialize' ) . ' <span class="gp-new-option">New</span>',
				'description' => esc_html__( 'Choose the title format.', 'socialize' ),
				'param_name' => 'title_format',
				'value' => array( esc_html__( 'Standard', 'socialize' ) => 'gp-standard-title', esc_html__( 'Fancy', 'socialize' ) => 'gp-fancy-title' ),
				'type' => 'dropdown',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),
				array( 
				'heading' => esc_html__( 'Title Color', 'socialize' ),
				'description' => esc_html__( 'The title color.', 'socialize' ),
				'param_name' => 'title_color',
				'value' => '#E93100',
				'type' => 'colorpicker',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),				
				array( 
				'heading' => esc_html__( 'Statistics Icon Background Color', 'socialize' ),
				'description' => esc_html__( 'The statistics icon background color.', 'socialize' ),
				'param_name' => 'icon_color',
				'value' => '#e93100',
				'type' => 'colorpicker',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),	
				array( 
				'heading' => esc_html__( 'Title Icon', 'socialize' ),
				'param_name' => 'icon',
				'value' => $icons,
				'description' => esc_html__( 'The icon you want to display next to the title.', 'socialize' ),
				'type' => 'icon_selection',
				'group' => esc_html__( 'Design options', 'socialize' ),
				),																																								
			 )
		) );
		
		
		/*--------------------------------------------------------------
		Team Shortcode
		--------------------------------------------------------------*/

		// Team Wrapper
		vc_map( array( 
			'name' => esc_html__( 'Team', 'socialize' ),
			'base' => 'team',
			'description' => esc_html__( 'Display your team members.', 'socialize' ),
			'as_parent' => array( 'only' => 'team_member' ), 
			'class' => 'wpb_vc_team',
			'controls' => 'full',
			'icon' => 'gp-icon-team',
			'category' => esc_html__( 'Content', 'socialize' ),
			'js_view' => 'VcColumnView',
			'params' => array( 	
				array( 
				'heading' => esc_html__( 'Columns', 'socialize' ),
				'param_name' => 'columns',
				'value' => '3',
				'description' => esc_html__( 'The number of columns.', 'socialize' ),
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'type' => 'textfield',
				),																																								
			 ),
			'js_view' => 'VcColumnView',
		) );

		// Team Member
		vc_map( array( 
			'name' => esc_html__( 'Team Member', 'socialize' ),
			'base' => 'team_member',
			'icon' => 'gp-icon-team',
			'content_element' => true,
			'as_child' => array( 'only' => 'team' ),
			'params' => array( 	
				array( 
				'heading' => esc_html__( 'Image', 'socialize' ),
				'description' => esc_html__( 'The team member image.', 'socialize' ),
				'param_name' => 'image_url',
				'value' => '',
				'type' => 'attach_image'
				),
				array( 
				'heading' => esc_html__( 'Image Width', 'socialize' ),
				'description' => esc_html__( 'The width of the team member image.', 'socialize' ),
				'param_name' => 'image_width',
				'value' => '230',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Image Height', 'socialize' ),
				'description' => esc_html__( 'The height of the team member image.', 'socialize' ),
				'param_name' => 'image_height',
				'value' => '230',
				'type' => 'textfield',
				),			
				array( 
				'heading' => esc_html__( 'Name', 'socialize' ),
				'description' => esc_html__( 'The name of the team member.', 'socialize' ),
				'param_name' => 'name',
				'admin_label' => true,
				'value' => '',
				'type' => 'textfield'
				),	
				array( 
				'heading' => esc_html__( 'Position', 'socialize' ),
				'description' => esc_html__( 'The position of the team member e.g. CEO', 'socialize' ),
				'param_name' => 'position',
				'value' => '',
				'type' => 'textfield',
				),
				array( 
				'heading' => esc_html__( 'Link', 'socialize' ),
				'description' => esc_html__( 'Add a link for the team member image.', 'socialize' ),
				'param_name' => 'link',
				'value' => '',
				'type' => 'textfield',
				),	
				array( 
				'heading' => esc_html__( 'Link Target', 'socialize' ),
				'description' => esc_html__( 'The link target for the team member image.', 'socialize' ),
				'param_name' => 'link_target',
				'value' => array( esc_html__( 'Same Window', 'socialize' ) => '_self', esc_html__( 'New Window', 'socialize' ) => '_blank' ),
				'type' => 'dropdown',
				'dependency' => array( 'element' => 'link', 'not_empty' => true ),
				),				
				array( 
				'heading' => esc_html__( 'Description', 'socialize' ),
				'description' => esc_html__( 'The description of the team member.', 'socialize' ),
				'param_name' => 'content',
				'value' => '',
				'type' => 'textarea_html',
				),																																								
			 )
		 ) );

		class WPBakeryShortCode_Team extends WPBakeryShortCodesContainer {}
		class WPBakeryShortCode_Team_Member extends WPBakeryShortCode {}											


		/*--------------------------------------------------------------
		Testimonials Shortcode
		--------------------------------------------------------------*/

		// Testimonial Slider
		vc_map( array( 
			'name' => esc_html__( 'Testimonial Slider', 'socialize' ),
			'base' => 'testimonial_slider',
			'description' => esc_html__( 'Show your testimonials in a slider.', 'socialize' ),
			'as_parent' => array( 'only' => 'testimonial' ), 
			'class' => 'wpb_vc_testimonial',
			'controls' => 'full',
			'icon' => 'gp-icon-testimonial-slider',
			'category' => esc_html__( 'Content', 'socialize' ),
			'js_view' => 'VcColumnView',
			'params' => array( 	
				array( 
				'heading' => esc_html__( 'Effect', 'socialize' ),
				'param_name' => 'effect',
				'value' => array( esc_html__( 'Slide', 'socialize' ) => 'slide', esc_html__( 'Fade', 'socialize' ) => 'fade' ),
				'description' => esc_html__( 'The slider effect.', 'socialize' ),
				'type' => 'dropdown'
				),
				array( 
				'heading' => esc_html__( 'Slider Speed', 'socialize' ),
				'param_name' => 'speed',
				'value' => '0',
				'description' => esc_html__( 'The number of seconds between slide transitions, set to 0 to disable the autoplay.', 'socialize' ),
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Buttons', 'socialize' ),
				'param_name' => 'buttons',
				'value' => array( esc_html__( 'Show', 'socialize' ) => 'true', esc_html__( 'Hide', 'socialize' ) => 'false' ),
				'description' => esc_html__( 'The slider buttons.', 'socialize' ),
				'type' => 'dropdown',
				),				
				array( 
				'heading' => esc_html__( 'Extra Class Names', 'socialize' ),
				'description' => esc_html__( 'If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'socialize' ),
				'param_name' => 'classes',
				'value' => '',
				'type' => 'textfield',
				),																																								
			 ),
			'js_view' => 'VcColumnView'
		 ) );


		// Testimonial Slide
		vc_map( array( 
			'name' => esc_html__( 'Testimonial', 'socialize' ),
			'base' => 'testimonial',
			'content_element' => true,
			'as_child' => array( 'only' => 'testimonial_slider' ),
			'icon' => 'gp-icon-testimonial-slider',
			'params' => array( 	
				array( 
				'heading' => esc_html__( 'Image', 'socialize' ),
				'description' => esc_html__( 'The testimonial slide image.', 'socialize' ),
				'param_name' => 'image_url',
				'value' => '',
				'type' => 'attach_image'
				),
				array( 
				'heading' => esc_html__( 'Image Width', 'socialize' ),
				'description' => esc_html__( 'The width the testimonial slide image.', 'socialize' ),
				'param_name' => 'image_width',
				'value' => '120',
				'description' => '',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Image Height', 'socialize' ),
				'description' => esc_html__( 'The height of the testimonial slide images.', 'socialize' ),
				'param_name' => 'image_height',
				'value' => '120',
				'type' => 'textfield',
				),		
				array( 
				'heading' => esc_html__( 'Quote', 'socialize' ),
				'description' => esc_html__( 'The testimonial quote.', 'socialize' ),
				'param_name' => 'content',
				'value' => '',
				'type' => 'textarea',
				),		
				array( 
				'heading' => esc_html__( 'Name', 'socialize' ),
				'description' => esc_html__( 'The name of the person who gave the testimonial.', 'socialize' ),
				'param_name' => 'name',
				'value' => '',
				'type' => 'textfield',
				),																																								
			 )
		 ) );

		class WPBakeryShortCode_Testimonial_Slider extends WPBakeryShortCodesContainer {}
		class WPBakeryShortCode_Testimonial extends WPBakeryShortCode {}																																

	}
	
}

add_action( 'init', 'socialize_custom_shortcodes' );

?>