<?php

/**
 * Script to implement the HTML code shared with widgets 
 * in the function pop-up insert shortcodes via GUI
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Creating array to list the fields that must be 
// present in the form before calling wp_parse_args ()

$array = array(
	'title'       => '',  // default value
	'width'       => '',  // default value
	'height'      => '',  // default value
	'width_auto'  => '',  // default value
	'height_auto' => '',  // default value
	'lat'         => '',  // default value
	'lng'         => '',  // default value
	'zoom'        => '',  // default value
	'view'        => '',  // default value
	'layer'       => '',  // default value
	'wheel'       => '',  // default value
	'marker'      => '',  // default value
	'lazyload'    => '',  // default value
	'action'      => 'A', // default value
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

$OBJC = new SZGoogleActionMaps();
extract((array) $OBJC->checkOptions(wp_parse_args($instance,$array),EXTR_OVERWRITE));

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetMaps.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');