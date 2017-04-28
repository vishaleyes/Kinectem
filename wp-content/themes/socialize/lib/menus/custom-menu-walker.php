<?php

if ( ! class_exists( 'socialize_custom_menu' ) ) {

	class socialize_custom_menu extends Walker_Nav_Menu {

		// Start level (add classes to ul sub-menus)
		function start_lvl( &$output, $depth = 0, $gp_args = array() ) {
		
			// Depth dependent classes
			$indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
			$display_depth = ( $depth + 1 ); // because it counts the first submenu as 0
			$classes = array(
				'sub-menu',
				( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
				( $display_depth >=2 ? 'sub-sub-menu' : '' ),
				'menu-depth-' . $display_depth
				);
			$class_names = implode( ' ', $classes );

			// Build html
			$output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
			
		}
  
		// Start element (add main/sub classes to li's and links)
		function start_el( &$output, $item, $depth = 0, $gp_args = array(), $id = 0 ) {
			global $wp_query, $socialize;
	
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

			// Depth dependent classes
			$depth_classes = array(
				( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
				( $depth >=2 ? 'sub-sub-menu-item' : '' ),
				( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
				'menu-item-depth-' . $depth
			);
			$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

			// Depth dependent classes
			$display_depth = ( $depth + 1); // because it counts the first submenu as 0
			$sub_menu_classes = array(
				'sub-menu',
				( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
				( $display_depth >=2 ? 'sub-sub-menu' : '' ),
				'menu-depth-' . $display_depth
				);
			$submenu_depth_class_names = implode( ' ', $sub_menu_classes );
			
			// Parsed classes
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

			// Build html
			
			$gp_menu_type = get_post_meta( $item->ID, 'menu-item-gp-menu-type', true ) ? get_post_meta( $item->ID, 'menu-item-gp-menu-type', true ) : 'gp-standard-menu';
			
			// Profile class
			if ( $gp_menu_type == 'gp-profile-link' ) {
				$gp_profile_class = 'gp-standard-menu';
			} else {
				$gp_profile_class = '';
			}	
			
			if ( ( is_user_logged_in() && get_post_meta( $item->ID, 'menu-item-gp-user-display', true ) != 'gp-show-logged-out' ) OR ( ! is_user_logged_in() && get_post_meta( $item->ID, 'menu-item-gp-user-display', true ) != 'gp-show-logged-in' ) ) {
			
				if ( ( is_user_logged_in() && ( $gp_menu_type == 'gp-login-link' OR $gp_menu_type == 'gp-register-link' ) ) ) {
				
					$output .= '';
				
				} elseif ( ( ! is_user_logged_in() && ( $gp_menu_type == 'gp-logout-link' OR $gp_menu_type == 'gp-profile-link' ) ) ) {
				
					$output .= '';

				} else {
						
					$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $gp_menu_type . ' ' . $gp_profile_class . ' ' . get_post_meta( $item->ID, 'menu-item-gp-columns', true ) . ' ' . get_post_meta( $item->ID, 'menu-item-gp-content', true ) . ' ' . get_post_meta( $item->ID, 'menu-item-gp-display', true ) . ' ' . $depth_class_names . ' ' . get_post_meta( $item->ID, 'menu-item-gp-hide-nav-label', true ) . ' ' . $class_names . '">';

					// Link attributes
					$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
					$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
					$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
				
					// Menu type
					if ( $gp_menu_type == 'gp-login-link' ) {				
						$gp_item_link = '#login';
					} elseif ( $gp_menu_type == 'gp-register-link' ) {
						if ( function_exists( 'bp_is_active' ) ) {
							$gp_item_link = bp_get_signup_page( false );
						} else {
							$gp_item_link = '#register';
						}	
					} elseif ( $gp_menu_type == 'gp-logout-link' ) {	
						$gp_item_link = wp_logout_url( get_permalink() );	
					} elseif ( $gp_menu_type == 'gp-profile-link' ) {	
						if ( function_exists( 'bp_is_active' ) ) {
							global $bp;
							$gp_item_link = $bp->loggedin_user->domain; 
						} else {
							global $current_user; get_currentuserinfo();	
							$gp_item_link = get_author_posts_url( $current_user->ID );
						}								
					} else {
						$gp_item_link = $item->url;
					}
				
					$attributes .= ! empty( $gp_item_link ) ? ' href="' . esc_attr( $gp_item_link ) .'"' : '';
				
					$attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';
			
					// Tab content menu
					if ( $gp_menu_type == 'gp-tab-content-menu' OR $gp_menu_type == 'gp-content-menu' ) {

						// Default variables
						$GLOBALS['socialize_menu'] = true;
						$GLOBALS['socialize_cats'] = $item->object_id;
						$GLOBALS['socialize_orderby'] = 'date';
					
						socialize_query_variables();

						// Posts per page depending on menu type
						if ( $gp_menu_type == 'gp-content-menu' ) {
							$GLOBALS['socialize_menu_per_page'] = 5;
						} else {
							$GLOBALS['socialize_menu_per_page'] = 4;
						}
								
						$query_args = array(
							'post_status' 	      => 'publish',
							'post_type'           => array( 'post', 'page' ),
							'tax_query'           => array( 'relation' => 'OR', $GLOBALS['socialize_post_cats'], $GLOBALS['socialize_portfolio_cats'], $GLOBALS['socialize_slide_cats'] ),
							'orderby'             => 'date',
							'order'           	  => 'desc',
							'posts_per_page'      => $GLOBALS['socialize_menu_per_page'],
							'paged'               => 1,
						);

						$gp_query = new wp_query( $query_args ); 

						if ( function_exists( 'socialize_data_properties' ) ) {
							$socialize_data_properties = socialize_data_properties( 'menu' ); 
						} else {
							$socialize_data_properties = '';
						}
				
						$gp_dropdown = '<ul class="sub-menu ' . $submenu_depth_class_names . '">
						<li id="nav-menu-item-'. $item->ID . '" class="' . $class_names . '"' . $socialize_data_properties . '>';
					
							if ( $gp_query->have_posts() ) :
				
								if ( $gp_menu_type == 'gp-tab-content-menu' ) {

									$taxonomies = get_taxonomies(); // Go through all taxonomies
									foreach ( $taxonomies as $taxonomy ) {
										$term_args = array(
											'parent'  => $item->object_id, // Get child categories
										);
										$terms = get_terms( $taxonomy, $term_args );
										if ( ! empty( $terms ) ) {
											$gp_dropdown .= '<ul class="gp-menu-tabs">
												<li id="' . $item->object_id . '" class="gp-selected">' . esc_html__( 'All', 'socialize' ) . '</li>';		
												foreach( $terms as $term ) {
													if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
														$gp_dropdown .= '<li id="' . $term->term_id . '">' . $term->name . '</li>';
													}
												}
											$gp_dropdown .= '</ul>';
										} 
									}
													
								}

								$gp_dropdown .= '<div class="gp-inner-loop ' . $socialize['ajax'] . '" >';
						
								while ( $gp_query->have_posts() ) : $gp_query->the_post();
															
									// Post link
									if ( get_post_format() == 'link' ) { 
										$gp_link = esc_url( get_post_meta( get_the_ID(), 'link', true ) );
										$gp_target = 'target="' . get_post_meta( get_the_ID(), 'link_target', true ) . '"';
									} else {
										$gp_link = get_permalink();
										$gp_target = '';
									}
														
									$gp_dropdown .= '<section class="' . implode( ' ' , get_post_class( 'gp-post-item' ) ) . '">';

										if ( has_post_thumbnail() ) {
																				
											$gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 270, 140, true, false, true );
											if ( $socialize['retina'] == 'gp-retina' ) {
												$gp_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 270 * 2, 140 * 2, true, true, true );
											} else {
												$gp_retina = '';
											}
									
											$gp_dropdown .= '<div class="gp-post-thumbnail"> <div class="gp-image-above">
												<a href="' . $gp_link . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '"' . $gp_target . '>
													<img src="' . $gp_image[0] . '" data-rel="' . $gp_retina . '" width="' . $gp_image[1] . '" height="' . $gp_image[2] . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" class="gp-post-image" />
												</a>
											</div></div>';
							
										}
								
										$gp_dropdown .= '<h2 class="gp-loop-title"><a href="' . $gp_link . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '"'. $gp_target. '>' . get_the_title() . '</a></h2>
										
										<div class="gp-loop-meta"><time class="gp-post-meta gp-meta-date" datetime="' . get_the_date( 'c' ) . '">' . get_the_time( get_option( 'date_format' ) ) . '</time></div>
						
									</section>';
						
								endwhile; 
					
								$gp_dropdown .= '</div><div class="gp-pagination gp-standard-pagination gp-pagination-arrows">' . socialize_get_previous_posts_page_link( $gp_query->max_num_pages ) . socialize_get_next_posts_page_link( $gp_query->max_num_pages ) . '</div>';
											
							endif; wp_reset_postdata();
							$GLOBALS['socialize_menu'] = null;
							$GLOBALS['socialize_cats'] = null;
							$GLOBALS['socialize_orderby'] = null;

						$gp_dropdown .= '</li></ul>';

					} else {
					
						$gp_dropdown = '';
					
					}	
					
					// Navigation label
					if ( $gp_menu_type == 'gp-profile-link' ) {
						global $current_user; get_currentuserinfo();	
						if ( function_exists( 'bp_notifications_get_notifications_for_user' ) ) { 
							$gp_notifications = bp_notifications_get_notifications_for_user( bp_loggedin_user_id(), 'object' );
							$gp_count = ! empty( $gp_notifications ) ? count( $gp_notifications ) : 0;
							$gp_count = '<a href="' . $bp->loggedin_user->domain . '/notifications" class="gp-notification-counter">' . $gp_count . '</a>';
						} else {
							$gp_count = '';
						}    					
						$gp_nav_label = $current_user->display_name;
						$gp_after = $gp_args->after . $gp_count;
					} elseif ( get_post_meta( $item->ID, 'menu-item-gp-hide-nav-label', true ) == 'gp-hide-nav-label' ) {
						$gp_nav_label = '';
						$gp_after = $gp_args->after;
					} else {
						$gp_nav_label = $item->title;
						$gp_after = $gp_args->after;
					}

					$item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s%7$s',
						$gp_args->before,
						$attributes,
						$gp_args->link_before,
						apply_filters( 'the_title', $gp_nav_label, $item->ID ),
						$gp_args->link_after,
						$gp_after,
						$gp_dropdown
					);
			
					// Build html
					$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $gp_args );
				}
				
			}
							
		}
		
		// End element (add closing li's)
		function end_el( &$output, $item, $depth = 0, $gp_args = array(), $id = 0 ) {
			
			global $socialize;

			$gp_menu_type = get_post_meta( $item->ID, 'menu-item-gp-menu-type', true ) ? get_post_meta( $item->ID, 'menu-item-gp-menu-type', true ) : 'gp-standard-menu';

			if ( ( is_user_logged_in() && get_post_meta( $item->ID, 'menu-item-gp-user-display', true ) != 'gp-show-logged-out' ) OR ( ! is_user_logged_in() && get_post_meta( $item->ID, 'menu-item-gp-user-display', true ) != 'gp-show-logged-in' ) ) {
			
				if ( ( is_user_logged_in() && ( $gp_menu_type == 'gp-login-link' OR $gp_menu_type == 'gp-register-link' ) ) ) {
				
					$output .= '';
				
				} elseif ( ( ! is_user_logged_in() && ( $gp_menu_type == 'gp-logout-link' OR $gp_menu_type == 'gp-profile-link' ) ) ) {
				
					$output .= '';
				
				} else {
				
					$output .= '</li>';

				}
			
			}
								
		}

	}
} 

?>