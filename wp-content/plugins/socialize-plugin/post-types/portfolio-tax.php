<?php

if ( ! class_exists( 'Socialize_Portfolio' ) ) {

	class Socialize_Portfolio {

		public function __construct() {
			add_action( 'init', array( &$this, 'socialize_post_type_portfolio' ), 1 );
			add_action( 'manage_posts_custom_column',  array( &$this, 'socialize_portfolio_custom_columns' ) );
			add_action( 'socialize_portfolios_add_form_fields', array( &$this, 'socialize_add_tax_fields' ) );		
			add_action( 'created_socialize_portfolios', array( &$this, 'socialize_save_tax_fields' ) );		
			add_action( 'socialize_portfolios_edit_form_fields', array( &$this, 'socialize_edit_tax_fields' ) );		
			add_action( 'edited_socialize_portfolios', array( &$this, 'socialize_save_tax_fields' ) );			
		}

		public function socialize_post_type_portfolio() {
		
			global $socialize;
			
			if ( ! isset( $socialize['portfolio_cat_slug'] ) ) {
				$socialize['portfolio_cat_slug'] = 'portfolios';
			}

			if ( ! isset( $socialize['portfolio_item_slug'] ) ) {
				$socialize['portfolio_item_slug'] = 'portfolio';
			}
				
			/*--------------------------------------------------------------
			Portfolio Item Post Type
			--------------------------------------------------------------*/	
	
			register_post_type( 'gp_portfolio_item', array( 
				'labels' => array( 
					'name' => esc_html__( 'Portfolio Items', 'socialize' ),
					'singular_name' => esc_html__( 'Portfolio Item', 'socialize' ),
					'menu_name' => esc_html__( 'Portfolio Items', 'socialize' ),
					'all_items' => esc_html__( 'All Portfolio Items', 'socialize' ),
					'add_new' => _x( 'Add New', 'portfolio', 'socialize' ),
					'add_new_item' => esc_html__( 'Add New Portfolio Item', 'socialize' ),
					'edit_item' => esc_html__( 'Edit Portfolio Item', 'socialize' ),
					'new_item' => esc_html__( 'New Portfolio Item', 'socialize' ),
					'view_item' => esc_html__( 'View Portfolio Item', 'socialize' ),
					'search_items' => esc_html__( 'Search Portfolio Items', 'socialize' ),
					'not_found' => esc_html__( 'No portfolio items found', 'socialize' ),
					'not_found_in_trash' => esc_html__( 'No portfolio items found in Trash', 'socialize' ),
				 ),
				'public' => true,
				'exclude_from_search' => false,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'_builtin' => false,
				'_edit_link' => 'post.php?post=%d',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array( 'slug' => sanitize_title( $socialize['portfolio_item_slug'] ) ),
				'menu_position' => 20,
				'with_front' => true,
				'taxonomies' => array( 'post_tag' ),
				'has_archive' => sanitize_title( $socialize['portfolio_cat_slug'] ),
				'supports' => array( 'title', 'thumbnail', 'editor', 'author', 'comments', 'custom-fields' )
			 ) );
	
	
			/*--------------------------------------------------------------
			Portfolio Categories Taxonomy
			--------------------------------------------------------------*/
			
			register_taxonomy( 'gp_portfolios', 'gp_portfolio_item', array( 
				'labels' => array( 
					'name' => esc_html__( 'Portfolio Categories', 'socialize' ),
					'singular_name' => esc_html__( 'Portfolio Category', 'socialize' ),
					'all_items' => esc_html__( 'All Portfolio Categories', 'socialize' ),
					'add_new' => _x( 'Add New', 'portfolio', 'socialize' ),
					'add_new_item' => esc_html__( 'Add New Portfolio Category', 'socialize' ),
					'edit_item' => esc_html__( 'Edit Portfolio Category', 'socialize' ),
					'new_item' => esc_html__( 'New Portfolio Category', 'socialize' ),
					'view_item' => esc_html__( 'View Portfolio Category', 'socialize' ),
					'search_items' => esc_html__( 'Search Portfolio Categories', 'socialize' ),
					'menu_name' => esc_html__( 'Portfolio Categories', 'socialize' )
				 ),
				'show_in_nav_menus' => true,
				'hierarchical' => true,
				'rewrite' => array( 'slug' => sanitize_title( $socialize['portfolio_cat_slug'] ) )
			 ) );


			register_taxonomy_for_object_type( 'gp_portfolios', 'gp_portfolio_item' );


			/*--------------------------------------------------------------
			Portfolio Item Admin Columns
			--------------------------------------------------------------*/

			function socialize_portfolio_item_edit_columns( $gp_columns ) {
				$gp_columns = array( 
					'cb'                   => '<input type="checkbox" />',
					'title'                => esc_html__( 'Title', 'socialize' ),	
					'portfolio_categories' => esc_html__( 'Categories', 'socialize' ),
					'portfolio_image'      => esc_html__( 'Image', 'socialize' ),				
					'date'                 => esc_html__( 'Date', 'socialize' )
				 );
				return $gp_columns;
			}	
			add_filter( 'manage_edit-socialize_portfolio_item_columns', 'socialize_portfolio_item_edit_columns' );
		
		}

		public function socialize_portfolio_custom_columns( $gp_column ) {
			switch ( $gp_column ) {
				case 'portfolio_categories':
					echo get_the_term_list( get_the_ID(), 'gp_portfolios', '', ', ', '' );
				break;
				case 'portfolio_image':
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( array( 50, 50 ) );
					}
				break;					
			}
		}

		/*--------------------------------------------------------------
		Portfolio Category Options
		--------------------------------------------------------------*/

		public function socialize_add_tax_fields( $gp_tag ) {
			
			if ( isset( $gp_tag->term_id ) ) {
				$gp_term_id = $gp_tag->term_id;	
				$gp_term_meta = get_option( "taxonomy_$gp_term_id" );
			} else {
				$gp_term_meta = null;
			} ?>

			<div class="form-field">
				<label for="category-page-header"><?php esc_html_e( 'Page Header', 'socialize' ); ?></label>
				<select id="gp_term_meta" name="gp_term_meta[page_header]">
					<option value="default"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<option value="gp-standard-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-standard-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Standard', 'socialize' ); ?></option>
					<option value="gp-large-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-large-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Large', 'socialize' ); ?></option>
					<option value="gp-fullwidth-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-fullwidth-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
					<option value="gp-full-page-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-full-page-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Full Page', 'socialize' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'The page header on the page.', 'socialize' ); ?></p>
			</div>
							
			<div class="form-field">
				<label for="category-bg-image"><?php esc_html_e( 'Page Header Background', 'socialize' ); ?></label>
				<input type="text" id="gp_term_meta" name="gp_term_meta[bg_image]" value="<?php echo esc_url( $gp_term_meta['bg_image'] ? $gp_term_meta['bg_image'] : '' ); ?>" />
				<p class="description"><?php esc_html_e( 'The background of the page header. <strong>Enter an image URL that must be uploaded to the Media Library.</strong>', 'socialize' ); ?></p>
			</div>

			<div class="form-field">
				<label for="category-layout"><?php esc_html_e( 'Page Layout', 'socialize' ); ?></label>
				<select id="gp_term_meta" name="gp_term_meta[layout]">
					<option value="default"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<option value="gp-left-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-left-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Left Sidebar', 'socialize' ); ?></option>
					<option value="gp-right-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-right-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Right Sidebar', 'socialize' ); ?></option>
					<option value="gp-no-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-no-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'No Sidebar', 'socialize' ); ?></option>
					<option value="gp-fullwidth"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-fullwidth' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'The layout of the page.', 'socialize' ); ?></p>
			</div>
	
			<div class="form-field">
				<label for="category-sidebar"><?php esc_html_e( 'Sidebar', 'socialize' ); ?></label>
				<?php $gp_term_meta['sidebar'] = isset( $gp_term_meta['sidebar'] ) ? $gp_term_meta['sidebar'] : ''; ?>
				<select id="gp_term_meta" name="gp_term_meta[sidebar]">
					<option value="default"<?php if ( isset( $gp_term_meta['sidebar'] ) && $gp_term_meta['sidebar'] == 'default' ) { ?>selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $gp_sidebar ) { ?>
						 <option value="<?php echo sanitize_title( $gp_sidebar['id'] ); ?>"<?php if ( isset( $gp_term_meta['sidebar'] ) && $gp_term_meta['sidebar'] == $gp_sidebar['id'] ) { ?>selected="selected"<?php } ?>>
							<?php echo ucwords( $gp_sidebar['name'] ); ?>
						 </option>
					<?php } ?>
				</select>
				<p class="description"><?php esc_html_e( 'The sidebar to display.', 'socialize' ); ?></p>
			</div>

			<div class="form-field">
				<label for="category-format"><?php esc_html_e( 'Format', 'socialize' ); ?></label>
				<select id="gp_term_meta" name="gp_term_meta[format]">
					<option value="default"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
					<option value="gp-portfolio-columns-2"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-2' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '2 Columns', 'socialize' ); ?></option>
					<option value="gp-portfolio-columns-3"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-3' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '3 Columns', 'socialize' ); ?></option>			
					<option value="gp-portfolio-columns-4"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-4' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '4 Columns', 'socialize' ); ?></option>			
					<option value="gp-portfolio-columns-5"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-5' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '5 Columns', 'socialize' ); ?></option>			
					<option value="gp-portfolio-columns-6"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-6' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '6 Columns', 'socialize' ); ?></option>			
					<option value="gp-portfolio-masonry"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-masonry' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Masonry', 'socialize' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'The format to display the items in.', 'socialize' ); ?></p>
			</div>	
				
		<?php }

		public function socialize_edit_tax_fields( $gp_tag ) {

			$gp_term_id = $gp_tag->term_id;
			$gp_term_meta = get_option( "taxonomy_$gp_term_id" ); ?>

			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="category-page-header"><?php esc_html_e( 'Page Header', 'socialize' ); ?></label>
				</th>
				<td>
					<select id="gp_term_meta" name="gp_term_meta[page_header]">
						<option value="default"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
						<option value="gp-standard-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-standard-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Standard', 'socialize' ); ?></option>
						<option value="gp-large-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-large-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Large', 'socialize' ); ?></option>
						<option value="gp-fullwidth-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-fullwidth-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
						<option value="gp-full-page-page-header"<?php if ( isset( $gp_term_meta['page_header'] ) && $gp_term_meta['page_header'] == 'gp-full-page-page-header' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Full Page', 'socialize' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Choose the page header on the page.', 'socialize' ); ?></p>
				</td>
			</tr>
					
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="category-bg-image"><?php esc_html_e( 'Page Header Background', 'socialize' ); ?></label>
				</th>
				<td>
					<input type="text" id="gp_term_meta" name="gp_term_meta[bg_image]" value="<?php echo esc_url( $gp_term_meta['bg_image'] ? $gp_term_meta['bg_image'] : '' ); ?>" />
					<p class="description"><?php esc_html_e( 'The background of the page header. <strong>Enter an image URL that must be uploaded to the Media Library.</strong>', 'socialize' ); ?></p>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="category-layout"><?php esc_html_e( 'Page Layout', 'socialize' ); ?></label>
				</th>
				<td>
					<select id="gp_term_meta" name="gp_term_meta[layout]">
						<option value="default"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
						<option value="gp-left-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-left-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Left Sidebar', 'socialize' ); ?></option>
						<option value="gp-right-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-right-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Right Sidebar', 'socialize' ); ?></option>
						<option value="gp-no-sidebar"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-no-sidebar' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'No Sidebar', 'socialize' ); ?></option>
						<option value="gp-fullwidth"<?php if ( isset( $gp_term_meta['layout'] ) && $gp_term_meta['layout'] == 'gp-fullwidth' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Fullwidth', 'socialize' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'The layout of the page.', 'socialize' ); ?></p>
				</td>
			</tr>
	
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="category-sidebar"><?php esc_html_e( 'Sidebar', 'socialize' ); ?></label>
				</th>
				<td>
					<?php $gp_term_meta['sidebar'] = isset( $gp_term_meta['sidebar'] ) ? $gp_term_meta['sidebar'] : ''; ?>
					<select id="gp_term_meta" name="gp_term_meta[sidebar]">
						<option value="default"<?php if ( isset( $gp_term_meta['sidebar'] ) && $gp_term_meta['sidebar'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
						<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $gp_sidebar ) { ?>
							 <option value="<?php echo sanitize_title( $gp_sidebar['id'] ); ?>"<?php if ( isset( $gp_term_meta['sidebar'] ) && $gp_term_meta['sidebar'] == $gp_sidebar['id'] ) { ?> selected="selected"<?php } ?>>
								<?php echo ucwords( $gp_sidebar['name'] ); ?>
							 </option>
						<?php } ?>
					</select>
					<p class="description"><?php esc_html_e( 'The sidebar to display.', 'socialize' ); ?></p>
				</td>
			</tr>

			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="category-format"><?php esc_html_e( 'Format', 'socialize' ); ?></label>
				</th>
				<td>
					<select id="gp_term_meta" name="gp_term_meta[format]">
						<option value="default"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'default' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Default', 'socialize' ); ?></option>
						<option value="gp-portfolio-columns-2"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-2' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '2 Columns', 'socialize' ); ?></option>
						<option value="gp-portfolio-columns-3"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-3' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '3 Columns', 'socialize' ); ?></option>			
						<option value="gp-portfolio-columns-4"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-4' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '4 Columns', 'socialize' ); ?></option>			
						<option value="gp-portfolio-columns-5"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-5' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '5 Columns', 'socialize' ); ?></option>			
						<option value="gp-portfolio-columns-6"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-columns-6' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( '6 Columns', 'socialize' ); ?></option>			
						<option value="gp-portfolio-masonry"<?php if ( isset( $gp_term_meta['format'] ) && $gp_term_meta['format'] == 'gp-portfolio-masonry' ) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Masonry', 'socialize' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'The format to display the items in.', 'socialize' ); ?></p>
				</td>
			</tr>
				
		<?php }
 
		public function socialize_save_tax_fields( $gp_term_id ) {
			if ( isset( $_POST['gp_term_meta'] ) ) {
				$gp_term_id = $gp_term_id;
				$gp_term_meta = get_option( "taxonomy_$gp_term_id" );
				$gp_cat_keys = array_keys( $_POST['gp_term_meta'] );
					foreach ( $gp_cat_keys as $gp_key ) {
					if ( isset( $_POST['gp_term_meta'][$gp_key] ) ) {
						$gp_term_meta[$gp_key] = $_POST['term_meta'][$gp_key];
					}
				}
				update_option( "taxonomy_$gp_term_id", $gp_term_meta );
			}
		}

	}

}

?>