<?php
/**
 * Version 0.0.3
 *
 * This file is just an example you can copy it to your theme and modify it to fit your own needs.
 * Watch the paths though.
 */
// Exit if accessed directly
//if ( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if ( ! class_exists( 'Radium_Theme_Demo_Data_Importer' ) ) {

	require_once( dirname( __FILE__ ) . '/importer/radium-importer.php' ); //load admin theme data importer

	class Radium_Theme_Demo_Data_Importer extends Radium_Theme_Importer {

		/**
		 * Set framewok
		 *
		 * options that can be used are 'default', 'socialize' or 'optiontree'
		 *
		 * @since 0.0.3
		 *
		 * @var string
		 */
		public $theme_options_framework = 'socialize';

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.1
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Set the key to be used to store theme options
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $theme_option_name       = 'my_theme_options_name'; //set theme options name here (key used to save theme options). Optiontree option name will be set automatically

		/**
		 * Set name of the theme options file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $theme_options_file_name = 'theme_options.txt';

		/**
		 * Set name of the widgets json file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $widgets_file_name       = 'widgets.json';

		/**
		 * Set name of the content file
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $content_demo_file_name  = 'content.xml';

		/**
		 * Holds a copy of the widget settings
		 *
		 * @since 0.0.2
		 *
		 * @var string
		 */
		public $widget_import_results;

		/**
		 * Constructor. Hooks all interactions to initialize the class.
		 *
		 * @since 0.0.1
		 */
		public function __construct() {

			$this->demo_files_path = dirname(__FILE__) . '/demo-files/'; //can

			self::$instance = $this;
			parent::__construct();

		}

		/**
		 * Add menus - the menus listed here largely depend on the ones registered in the theme
		 *
		 * @since 0.0.1
		 */
		public function set_demo_menus(){
								
			// BEGIN MOD				
			$gp_locations = get_theme_mod( 'nav_menu_locations' );
			$gp_menus = wp_get_nav_menus();
			if ( $gp_menus ) {
				foreach( $gp_menus as $gp_menu ) { // assign menus to theme locations
					if ( $gp_menu->name == 'Socialize Primary Main Header Menu' ) {
						$gp_locations['gp-primary-main-header-nav'] = $gp_menu->term_id;	
					} elseif ( $gp_menu->name == 'Socialize Secondary Main Header Menu' ) {
						$gp_locations['gp-secondary-main-header-nav'] = $gp_menu->term_id;					
					} elseif ( $gp_menu->name == 'Socialize Left Small Header Menu' ) {
						$gp_locations['gp-left-small-header-nav'] = $gp_menu->term_id;
						$gp_locations['gp-footer-nav'] = $gp_menu->term_id;	
					} elseif ( $gp_menu->name == 'Socialize Social Icons Menu' ) {
						$gp_locations['gp-right-small-header-nav'] = $gp_menu->term_id;				
					}
				}
			}
			set_theme_mod( 'nav_menu_locations', $gp_locations );				
			// END MOD
		
		}
		
	}

	new Radium_Theme_Demo_Data_Importer;

}