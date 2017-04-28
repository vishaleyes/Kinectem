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
	'mode'       => '',  // default value
	'link'       => '',  // default value
	'cover'      => '',  // default value
	'photo'      => '',  // default value
	'biography'  => '',  // default value
	'width'      => '',  // default value
	'width_auto' => '',  // default value
	'action'     => 'A', // default value
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

$OBJC = new SZGoogleActionPlusAuthorBadge();
extract((array) $OBJC->checkOptions(wp_parse_args($instance,$array),EXTR_OVERWRITE));

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetPlusAuthorBadge.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');