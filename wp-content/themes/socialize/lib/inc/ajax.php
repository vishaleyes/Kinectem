<?php 

if ( ! function_exists( 'socialize_data_properties' ) ) {
	function socialize_data_properties( $gp_type ) {

		// Check to see if options exists
		$GLOBALS['socialize_ajax_cats'] = ! empty( $GLOBALS['socialize_cats'] ) ? $GLOBALS['socialize_cats'] : '';	
		$GLOBALS['socialize_ajax_page_ids'] = ! empty( $GLOBALS['socialize_page_ids'] ) ? $GLOBALS['socialize_page_ids'] : '';		
		$GLOBALS['socialize_ajax_post_types'] = ! empty( $GLOBALS['socialize_post_types'] ) ? $GLOBALS['socialize_post_types'] : '';			
		$GLOBALS['socialize_ajax_format'] = ! empty( $GLOBALS['socialize_format'] ) ? $GLOBALS['socialize_format'] : '';
		$GLOBALS['socialize_ajax_orderby'] = ! empty( $GLOBALS['socialize_orderby'] ) ? $GLOBALS['socialize_orderby'] : '';
		$GLOBALS['socialize_ajax_filter'] = ! empty( $GLOBALS['socialize_filter'] ) ? $GLOBALS['socialize_filter'] : '';
		$GLOBALS['socialize_ajax_date_posted'] = ! empty( $GLOBALS['socialize_date_posted'] ) ? $GLOBALS['socialize_date_posted'] : '';
		$GLOBALS['socialize_ajax_date_modified'] = ! empty( $GLOBALS['socialize_date_modified'] ) ? $GLOBALS['socialize_date_modified'] : '';
		$GLOBALS['socialize_ajax_per_page'] = ! empty( $GLOBALS['socialize_per_page'] ) ? $GLOBALS['socialize_per_page'] : '';
		$GLOBALS['socialize_ajax_menu_per_page'] = ! empty( $GLOBALS['socialize_menu_per_page'] ) ? $GLOBALS['socialize_menu_per_page'] : '';
		$GLOBALS['socialize_ajax_offset'] = ! empty( $GLOBALS['socialize_offset'] ) ? $GLOBALS['socialize_offset'] : 0;
		$GLOBALS['socialize_ajax_featured_image'] = ! empty( $GLOBALS['socialize_featured_image'] ) ? $GLOBALS['socialize_featured_image'] : '';
		$GLOBALS['socialize_ajax_image_width'] = ! empty( $GLOBALS['socialize_image_width'] ) ? $GLOBALS['socialize_image_width'] : '';
		$GLOBALS['socialize_ajax_image_height'] = ! empty( $GLOBALS['socialize_image_height'] ) ? $GLOBALS['socialize_image_height'] : '';
		$GLOBALS['socialize_ajax_hard_crop'] = ! empty( $GLOBALS['socialize_hard_crop'] ) ? $GLOBALS['socialize_hard_crop'] : '';
		$GLOBALS['socialize_ajax_image_alignment'] = ! empty( $GLOBALS['socialize_image_alignment'] ) ? $GLOBALS['socialize_image_alignment'] : '';
		$GLOBALS['socialize_ajax_content_display'] = ! empty( $GLOBALS['socialize_content_display'] ) ? $GLOBALS['socialize_content_display'] : '';
		$GLOBALS['socialize_ajax_excerpt_length'] = ! empty( $GLOBALS['socialize_excerpt_length'] ) ? $GLOBALS['socialize_excerpt_length'] : 0;
		$GLOBALS['socialize_ajax_meta_author'] = ! empty( $GLOBALS['socialize_meta_author'] ) ? $GLOBALS['socialize_meta_author'] : '';
		$GLOBALS['socialize_ajax_meta_date'] = ! empty( $GLOBALS['socialize_meta_date'] ) ? $GLOBALS['socialize_meta_date'] : '';
		$GLOBALS['socialize_ajax_meta_comment_count'] = ! empty( $GLOBALS['socialize_meta_comment_count'] ) ? $GLOBALS['socialize_meta_comment_count'] : '';
		$GLOBALS['socialize_ajax_meta_views'] = ! empty( $GLOBALS['socialize_meta_views'] ) ? $GLOBALS['socialize_meta_views'] : '';
		$GLOBALS['socialize_ajax_meta_cats'] = ! empty( $GLOBALS['socialize_meta_cats'] ) ? $GLOBALS['socialize_meta_cats'] : '';
		$GLOBALS['socialize_ajax_meta_tags'] = ! empty( $GLOBALS['socialize_meta_tags'] ) ? $GLOBALS['socialize_meta_tags'] : '';
		$GLOBALS['socialize_ajax_read_more_link'] = ! empty( $GLOBALS['socialize_read_more_link'] ) ? $GLOBALS['socialize_read_more_link'] : '';
		$GLOBALS['socialize_ajax_page_arrows'] = ! empty( $GLOBALS['socialize_page_arrows'] ) ? $GLOBALS['socialize_page_arrows'] : '';
		$GLOBALS['socialize_ajax_page_numbers'] = ! empty( $GLOBALS['socialize_page_numbers'] ) ? $GLOBALS['socialize_page_numbers'] : '';	
	 
		// Add to blog wrappers to pull query data 
		return ' data-type="' . $gp_type . '" data-cats="' . $GLOBALS['socialize_ajax_cats'] . '" data-posttypes="' . $GLOBALS['socialize_ajax_post_types'] . '" data-pageids="' . $GLOBALS['socialize_ajax_page_ids'] . '" data-format="' . $GLOBALS['socialize_ajax_format'] . '" data-orderby="' . $GLOBALS['socialize_ajax_orderby'] . '" data-filter="' . $GLOBALS['socialize_ajax_filter'] . '" data-perpage="' . $GLOBALS['socialize_ajax_per_page'] . '" data-menuperpage="' . $GLOBALS['socialize_ajax_menu_per_page'] . '" data-offset="' . $GLOBALS['socialize_ajax_offset'] . '"  data-featuredimage="' . $GLOBALS['socialize_ajax_featured_image'] . '" data-imagewidth="' . $GLOBALS['socialize_ajax_image_width'] . '" data-imageheight="' . $GLOBALS['socialize_ajax_image_height'] . '" data-hardcrop="' . $GLOBALS['socialize_ajax_hard_crop'] . '" data-imagealignment="' . $GLOBALS['socialize_ajax_image_alignment'] . '" data-contentdisplay="' . $GLOBALS['socialize_ajax_content_display'] . '" data-excerptlength="' . $GLOBALS['socialize_ajax_excerpt_length'] . '" data-metaauthor="' . $GLOBALS['socialize_ajax_meta_author'] . '" data-metadate="' . $GLOBALS['socialize_ajax_meta_date'] . '" data-metacommentcount="' . $GLOBALS['socialize_ajax_meta_comment_count'] . '" data-metaviews="' . $GLOBALS['socialize_ajax_meta_views'] . '" data-metacats="' . $GLOBALS['socialize_ajax_meta_cats'] . '" data-metatags="' . $GLOBALS['socialize_ajax_meta_tags'] . '" data-readmorelink="' . $GLOBALS['socialize_ajax_read_more_link'] . '" data-pagearrows="' . $GLOBALS['socialize_ajax_page_arrows'] . '" data-pagenumbers="' . $GLOBALS['socialize_ajax_page_numbers'] . '"';

	}
}
 
if ( ! function_exists( 'socialize_register_ajax' ) ) {
	function socialize_register_ajax() {
	
		global $query_string, $post;
		
		// Determine http or https for admin-ajax.php URL
		if ( is_ssl() ) { $gp_scheme = 'https'; } else { $gp_scheme = 'http'; }

		if ( is_archive() OR is_page_template( 'blog-template.php' ) OR ( isset( $GLOBALS['socialize_shortcode'] ) && ( $GLOBALS['socialize_shortcode'] == 'blog' OR $GLOBALS['socialize_shortcode'] == 'showcase' OR $GLOBALS['socialize_shortcode'] == 'carousel' ) ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
		
		wp_enqueue_script( 'ajax-loop', socialize_scripts_uri . 'ajax-loop.js', array( 'jquery' ) );
		wp_localize_script( 'ajax-loop', 'gpAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php', $gp_scheme ),
			'ajaxnonce' => wp_create_nonce( 'gp-ajax-nonce' ),
			'querystring' => $query_string,
		) ); 
		
	}
}
add_action( 'wp_enqueue_scripts', 'socialize_register_ajax' );

if ( ! function_exists( 'socialize_ajax' ) ) {
	function socialize_ajax() {
	
		global $socialize;
	
		if ( ! wp_verify_nonce( $_GET['ajaxnonce'], 'gp-ajax-nonce' ) )
			die();
	
		// Pagination
		$socialize_pagination = ( isset( $_GET['pagenumber'] )  ) ? $_GET['pagenumber'] : 0;
				
		// Get theme options from ajax values
		$GLOBALS['socialize_cats'] = isset( $_GET['cats'] ) ? $_GET['cats'] : '';		
		$GLOBALS['socialize_post_types'] = isset( $_GET['posttypes'] ) ? explode( ',', $_GET['posttypes'] ) : '';
		$GLOBALS['socialize_page_ids'] = isset( $_GET['pageids'] ) ? $_GET['pageids'] : '';
		$GLOBALS['socialize_format'] = isset( $_GET['format'] ) ? $_GET['format'] : '';
		$GLOBALS['socialize_orderby'] = isset( $_GET['orderby'] ) ? $_GET['orderby'] : '';
		$GLOBALS['socialize_filter'] = isset( $_GET['filter'] ) ? $_GET['filter'] : '';
		$GLOBALS['socialize_date_posted'] = isset( $_GET['dateposted'] ) ? $_GET['dateposted'] : '';
		$GLOBALS['socialize_date_modified'] = isset( $_GET['datemodified'] ) ? $_GET['datemodified'] : '';
		$GLOBALS['socialize_per_page'] = isset( $_GET['perpage'] ) ? $_GET['perpage'] : '';
		$GLOBALS['socialize_menu_per_page'] = isset( $_GET['menuperpage'] ) ? $_GET['menuperpage'] : '';
		$GLOBALS['socialize_offset'] = isset( $_GET['offset'] ) ? $_GET['offset'] : '';
		$GLOBALS['socialize_featured_image'] = isset( $_GET['featuredimage'] ) ? $_GET['featuredimage'] : '';
		$GLOBALS['socialize_image_width'] = isset( $_GET['imagewidth'] ) ? $_GET['imagewidth'] : '';
		$GLOBALS['socialize_image_height'] = isset( $_GET['imageheight'] ) ? $_GET['imageheight'] : '';
		$GLOBALS['socialize_hard_crop'] = isset( $_GET['hardcrop'] ) ? $_GET['hardcrop'] : '';
		$GLOBALS['socialize_image_alignment'] = isset( $_GET['imagealignment'] ) ? $_GET['imagealignment'] : '';
		$GLOBALS['socialize_content_display'] = isset( $_GET['contentdisplay'] ) ? $_GET['contentdisplay'] : '';
		$GLOBALS['socialize_excerpt_length'] = isset( $_GET['excerptlength'] ) ? $_GET['excerptlength'] : '0';
		$GLOBALS['socialize_meta_author'] = isset( $_GET['metaauthor'] ) ? $_GET['metaauthor'] : '';
		$GLOBALS['socialize_meta_date'] = isset( $_GET['metadate'] ) ? $_GET['metadate'] : '';
		$GLOBALS['socialize_meta_comment_count'] = isset( $_GET['metacommentcount'] ) ? $_GET['metacommentcount'] : '';
		$GLOBALS['socialize_meta_views'] = isset( $_GET['metaviews'] ) ? $_GET['metaviews'] : '';
		$GLOBALS['socialize_meta_cats'] = isset( $_GET['metacats'] ) ? $_GET['metacats'] : '';
		$GLOBALS['socialize_meta_tags'] = isset( $_GET['metatags'] ) ? $_GET['metatags'] : '';
		$GLOBALS['socialize_read_more_link'] = isset( $_GET['readmorelink'] ) ? $_GET['readmorelink'] : '';
		$GLOBALS['socialize_page_arrows'] = isset( $_GET['pagearrows'] ) ? $_GET['pagearrows'] : '';
		$GLOBALS['socialize_page_numbers'] = isset( $_GET['pagenumbers'] ) ? $_GET['pagenumbers'] : '';
						
		// Use filtered category is selected
		if ( isset( $_GET['cats_new'] ) && $_GET['cats_new'] != '0' ) {
			$GLOBALS['socialize_cats'] = $_GET['cats_new'];
		}

		// Use filtered menu category is selected
		if ( isset( $_GET['menu_cats_new'] ) && $_GET['menu_cats_new'] != '0' ) {
			$GLOBALS['socialize_cats'] = $_GET['menu_cats_new'];
		}
		
		// Use filtered orderby if selected
		if ( isset( $_GET['orderby_new'] ) && $_GET['orderby_new'] != '0' ) {
			$GLOBALS['socialize_orderby'] = $_GET['orderby_new'];
		}		

		// Use filtered date posted if selected
		if ( isset( $_GET['date_posted_new'] ) && $_GET['date_posted_new'] != '0' ) {
			$GLOBALS['socialize_date_posted'] = $_GET['date_posted_new'];
		}	
			
		// Use filtered date modified if selected
		if ( isset( $_GET['date_modified_new'] ) && $_GET['date_modified_new'] != '0' ) {
			$GLOBALS['socialize_date_modified'] = $_GET['date_modified_new'];
		}	
							
		socialize_query_variables();
		
		$GLOBALS['socialize_meta_query'] = '';
			
		// Tax query
		if ( $_GET['type'] == 'blog' OR $_GET['type'] == 'showcase' OR $_GET['type'] == 'blog-template' OR $_GET['type'] == 'menu' ) {
			$gp_tax_query = array( 'relation' => 'OR', $GLOBALS['socialize_post_cats'], $GLOBALS['socialize_slide_cats'], $GLOBALS['socialize_portfolio_cats'] );
		} else {
			$gp_tax_query = '';
		}

		// Page IDs
		if ( $GLOBALS['socialize_page_ids'] ) {
			$GLOBALS['socialize_page_ids'] = explode( ',', $GLOBALS['socialize_page_ids'] );
		} else {
			$GLOBALS['socialize_page_ids'] = '';
		}

		// Query														
		if ( $_GET['type'] == 'taxonomy' ) {
			$gp_defaults = array(
				'date_query' => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),
			);
			$gp_args = $_GET['querystring'] . "&post_status=publish&orderby=" . $GLOBALS['socialize_orderby_value'] . "&order=" . $GLOBALS['socialize_order'] . "&meta_key=" . $GLOBALS['socialize_meta_key'] . "&posts_per_page=" . $GLOBALS['socialize_per_page'] . "&paged=$socialize_pagination";		
			$gp_args = wp_parse_args( $gp_args, $gp_defaults );
		} elseif ( $_GET['type'] == 'menu' ) {	
			$gp_args = array(
				'post_status' 	  => 'publish',
				'post_type'       => array( 'post', 'page' ),
				'tax_query'       => $gp_tax_query,
				'orderby'         => 'date',
				'order'           => 'desc',
				'posts_per_page'  => $GLOBALS['socialize_menu_per_page'],
				'paged'           => $socialize_pagination,		
			);				
		} else {
			$gp_args = array(
				'post_status' 	 => 'publish',
				'post_type' 	 => 'post',
				'post__in'       => $GLOBALS['socialize_page_ids'],
				'tax_query' 	 => $gp_tax_query,
				'orderby' 		 => $GLOBALS['socialize_orderby_value'],
				'order' 		 => $GLOBALS['socialize_order'],
				'meta_query' 	 => $GLOBALS['socialize_meta_query'],
				'meta_key' 		 => $GLOBALS['socialize_meta_key'],
				'posts_per_page' => $GLOBALS['socialize_per_page'],
				'offset' 		 => $GLOBALS['socialize_offset'],
				'paged'          => $socialize_pagination,
				'date_query' => array( $GLOBALS['socialize_date_posted_value'], $GLOBALS['socialize_date_modified_value'] ),
			);
		}
		
		//print_r($gp_args);
		
		$gp_query = new wp_query( $gp_args );
		
		$GLOBALS['socialize_counter'] = 1;
						
		if ( $gp_query->have_posts() ) :
		
			$gp_total_pages = $gp_query->max_num_pages;

			// Pagination (Arrows)
			if ( $GLOBALS['socialize_page_arrows'] == 'enabled' OR $_GET['type'] == 'menu' ) { 
				echo '<div class="gp-pagination-arrows gp-ajax-pagination">';
					if ( $socialize_pagination > 1 ) {
						echo '<a href="#" data-pagelink="' . ( $socialize_pagination - 1 ) . '" class="prev"></a>';
					} else {
						echo '<span class="prev gp-disabled"></span>';
					}
					if ( $socialize_pagination < $gp_total_pages ) {
						echo '<a href="#" data-pagelink="' . ( $socialize_pagination + 1 ) . '" class="next"></a>';
					} else {
						echo '<span class="next gp-disabled"></span>';
					}
				echo '</div>'; 
			}
			
			while ( $gp_query->have_posts() ) : $gp_query->the_post(); 	
		
				// Large and small options for showcase element
				if ( $_GET['type'] == 'showcase' ) {
					if ( $GLOBALS['socialize_counter'] % $GLOBALS['socialize_per_page'] == 1 ) {
						$GLOBALS['socialize_featured_image'] = isset( $_GET['largefeaturedimage'] ) ? $_GET['largefeaturedimage'] : '';
						$GLOBALS['socialize_image_width'] = isset( $_GET['largeimagewidth'] ) ? $_GET['largeimagewidth'] : '';
						$GLOBALS['socialize_image_height'] = isset( $_GET['largeimageheight'] ) ? $_GET['largeimageheight'] : '';
						$GLOBALS['socialize_image_alignment'] = isset( $_GET['largeimagealignment'] ) ? $_GET['largeimagealignment'] : '';
						$GLOBALS['socialize_excerpt_length'] = isset( $_GET['largeexcerptlength'] ) ? $_GET['largeexcerptlength'] : '0';
						$GLOBALS['socialize_meta_author'] = isset( $_GET['largemetaauthor'] ) ? $_GET['largemetaauthor'] : '';
						$GLOBALS['socialize_meta_date'] = isset( $_GET['largemetadate'] ) ? $_GET['largemetadate'] : '';
						$GLOBALS['socialize_meta_comment_count'] = isset( $_GET['largemetacommentcount'] ) ? $_GET['largemetacommentcount'] : '';
						$GLOBALS['socialize_meta_views'] = isset( $_GET['largemetaviews'] ) ? $_GET['largemetaviews'] : '';
						$GLOBALS['socialize_meta_cats'] = isset( $_GET['largemetacats'] ) ? $_GET['largemetacats'] : '';
						$GLOBALS['socialize_meta_tags'] = isset( $_GET['largemetatags'] ) ? $_GET['largemetatags'] : '';
						$GLOBALS['socialize_read_more_link'] = isset( $_GET['largereadmorelink'] ) ? $_GET['largereadmorelink'] : '';
					} else {
						$GLOBALS['socialize_featured_image'] = isset( $_GET['smallfeaturedimage'] ) ? $_GET['smallfeaturedimage'] : '';
						$GLOBALS['socialize_image_width'] = isset( $_GET['smallimagewidth'] ) ? $_GET['smallimagewidth'] : '';
						$GLOBALS['socialize_image_height'] = isset( $_GET['smallimageheight'] ) ? $_GET['smallimageheight'] : '';
						$GLOBALS['socialize_image_alignment'] = isset( $_GET['smallimagealignment'] ) ? $_GET['smallimagealignment'] : '';
						$GLOBALS['socialize_excerpt_length'] = isset( $_GET['smallexcerptlength'] ) ? $_GET['smallexcerptlength'] : '0';
						$GLOBALS['socialize_meta_author'] = isset( $_GET['smallmetaauthor'] ) ? $_GET['smallmetaauthor'] : '';
						$GLOBALS['socialize_meta_date'] = isset( $_GET['smallmetadate'] ) ? $_GET['smallmetadate'] : '';
						$GLOBALS['socialize_meta_comment_count'] = isset( $_GET['smallmetacommentcount'] ) ? $_GET['smallmetacommentcount'] : '';
						$GLOBALS['socialize_meta_views'] = isset( $_GET['smallmetaviews'] ) ? $_GET['smallmetaviews'] : '';
						$GLOBALS['socialize_meta_cats'] = isset( $_GET['smallmetacats'] ) ? $_GET['smallmetacats'] : '';
						$GLOBALS['socialize_meta_tags'] = isset( $_GET['smallmetatags'] ) ? $_GET['smallmetatags'] : '';
						$GLOBALS['socialize_read_more_link'] = isset( $_GET['smallreadmorelink'] ) ? $_GET['smallreadmorelink'] : '';
					}
				}
											
			?>
	
				<?php if ( $_GET['type'] == 'showcase' && ( ( isset( $GLOBALS['socialize_counter'] ) && $GLOBALS['socialize_counter'] % $GLOBALS['socialize_per_page'] == 2 OR $GLOBALS['socialize_counter'] == 2 ) && $gp_query->current_post != 0 ) ) { ?>
					<div class="gp-small-posts">
				<?php } ?>

					<?php if ( $_GET['type'] == 'menu' ) {
															
						// Post link
						if ( get_post_format() == 'link' ) { 
							$gp_link = esc_url( get_post_meta( get_the_ID(), 'link', true ) );
						} else {
							$gp_link = get_permalink();
						}
						
						echo '<section class="' . implode( ' ' , get_post_class( 'gp-post-item' ) ) . '" itemscope itemtype="http://schema.org/Article">';
						
							if ( has_post_thumbnail() ) {
						
								$gp_image = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 270, 140, true, false, true );
								if ( $socialize['retina'] == 'gp-retina' ) {
									$gp_retina = aq_resize( wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), 270 * 2, 140 * 2, true, true, true );
								} else {
									$gp_retina = '';
								}
									
								echo '<div class="gp-post-thumbnail"><div class="gp-image-above">
									<a href="' . $gp_link . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" target="' . get_post_meta( get_the_ID(), 'link_target', true ) . '">
										<img src="' . $gp_image[0] . '" data-rel="' . $gp_retina . '" width="' . $gp_image[1] . '" height="' . $gp_image[2] . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" class="gp-post-image" />
									</a>
								</div></div>';
				
							}
									
							echo '<h2 class="gp-loop-title"><a href="' . $gp_link . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" target="' . get_post_meta( get_the_ID(), 'link_target', true ) . '">' . get_the_title() . '</a></h2>		
							
							<div class="gp-loop-meta"><time class="gp-post-meta gp-meta-date" itemprop="datePublished" datetime="' . get_the_date( 'c' ) . '">' . get_the_time( get_option( 'date_format' ) ) . '</time></div>
										
						</section>';						
						
					} else {
				
						get_template_part( 'post', 'loop' );
					
					} ?>

				<?php if ( $_GET['type'] == 'showcase' && ( isset( $GLOBALS['socialize_counter'] ) && $GLOBALS['socialize_counter'] % $GLOBALS['socialize_per_page'] == 0 ) OR ( ( $gp_query->current_post + 1 ) == $gp_query->post_count && $gp_query->current_post != 0 ) ) { ?>
					</div>
				<?php } ?>	

			<?php $GLOBALS['socialize_counter']++; endwhile; ?>

			<?php 
			
			// Pagination (Numbers)		
			if ( $gp_total_pages > 1 && $_GET['type'] != 'menu' && $GLOBALS['socialize_page_numbers'] == 'enabled' ) { 
				  echo '<div class="gp-pagination gp-pagination-numbers gp-ajax-pagination">';
				  echo paginate_links( array(  
					'base'     => '%_%',  
					'format'   => '/page/%#%',
					'current'  => $socialize_pagination,  
					'total'    => $gp_total_pages,  
					'type'      => 'list',
					'prev_text' => '',
					'next_text' => '',        
				  ));
				  echo '</div>'; 
			}
			?>
		
		<?php else : ?>

			<strong class="gp-no-items-found"><?php esc_html_e( 'No items found.', 'socialize' ); ?></strong>
	
		<?php endif; wp_reset_postdata();

		die();
	}	
}
add_action( 'wp_ajax_gp_ajax', 'socialize_ajax' );
add_action( 'wp_ajax_nopriv_gp_ajax', 'socialize_ajax' );

?>