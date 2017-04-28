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
	'title'       => '', // valore predefinito
	'url'         => '', // valore predefinito
	'width'       => '', // valore predefinito
	'width_auto'  => '', // valore predefinito
	'height'      => '', // valore predefinito
	'height_auto' => '', // valore predefinito
	'pre'         => '', // valore predefinito
);

// Creating arrays to list of fields to be retrieved FORM 
// and loading the file with the HTML template to display

extract(wp_parse_args($instance,$array),EXTR_OVERWRITE);

// Read the options to control the default values ​​to be 
// assigned to the widget when it is added to the sidebar

if ($object = SZGoogleModule::getObject('SZGoogleModuleDrive'))
{
	$options = (object) $object->getOptions();

	if (!ctype_digit($width)  and $width  != 'auto') $width  = $options->drive_viewer_s_width;
	if (!ctype_digit($height) and $height != 'auto') $height = $options->drive_viewer_s_height;

	// Checking if the string contains a value consistent with the
	// selection of the parameter as a numeric value that is character

	$YESNO = array('1','0','n','y');

	if (!in_array($pre,$YESNO)) $pre = $options->drive_viewer_w_wrap_pre;
}

// Setting any of the default parameters for fields 
// that contain invalid values ​​or inconsistent

$DEFAULT = include(dirname(SZ_PLUGIN_GOOGLE_MAIN)."/options/sz_google_options_drive.php");

if (!ctype_digit($width)  or $width  == 0) { $width  = $DEFAULT['drive_viewer_s_width']['value'];  $width_auto  = '1'; }
if (!ctype_digit($height) or $height == 0) { $height = $DEFAULT['drive_viewer_s_height']['value']; $height_auto = '1'; }

// If the values are taken from the default options, you can
// create the problems caused to the difference in the storage state

$pre = str_replace(array('0','1'),array('n','y'),$pre);

// Loading ADMIN template for composition using
// shortcodes in many cases the same code Widget

@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseHeader.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/widgets/SZGoogleWidgetDriveViewer.php');
@include(dirname(SZ_PLUGIN_GOOGLE_MAIN).'/admin/mce/shortcodes/SZGoogleBaseFooter.php');