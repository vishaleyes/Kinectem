// Defining the main variable to contain the 
// functions that will be called from the popup

var SZGoogleDialog = 
{
	local_ed:'ed',

	// Init function for the initial operations of 
	// the component to be executed in this file

	init: function(ed) {
		SZGoogleDialog.local_ed = ed;
		tinyMCEPopup.resizeToInnerSize();
	},

	// Function associated with the cancel button at 
	// the end of the screen in each popup shortcode

	cancel: function(ed) {
		tinyMCEPopup.close();
	},

	// Insert function for creating the code 
	// shortcode with all the preset options

	insert: function(ed) {

		var SZGoogleEditor = tinyMCE.get("content");

		// Execution command after calculating the variable 
		// editor currently displayed and stored in SZGoogleEditor

		SZGoogleEditor.execCommand('mceRemoveNode',false,null);

		// Calculating the values ​​of variables directly 
		// from the form fields without submission standards

		var output  = '';

		var template    = jQuery('#ID_template'   ).val();
		var user        = jQuery('#ID_user'       ).val();
		var group       = jQuery('#ID_group'      ).val();
		var tag         = jQuery('#ID_tag'        ).val();
		var set         = jQuery('#ID_set'        ).val();
		var width       = jQuery('#ID_width'      ).val();
		var height      = jQuery('#ID_height'     ).val();
		var columns     = jQuery('#ID_columns'    ).val();
		var rows        = jQuery('#ID_rows'       ).val();
		var orientation = jQuery('#ID_orientation').val();
		var position    = jQuery('#ID_position'   ).val();

		if (jQuery('#ID_width_auto' ).is(':checked')) width  = 'auto';
		if (jQuery('#ID_height_auto').is(':checked')) height = 'auto';

		// Composition shortcode selected with list
		// of available options and associated value

		output = '[sz-panoramio ';

		if (template    != '') output += 'template="'    + template    + '" ';
		if (user        != '') output += 'user="'        + user        + '" ';
		if (group       != '') output += 'group="'       + group       + '" ';
		if (tag         != '') output += 'tag="'         + tag         + '" ';
		if (set         != '') output += 'set="'         + set         + '" ';
		if (width       != '') output += 'width="'       + width       + '" ';
		if (height      != '') output += 'height="'      + height      + '" ';
		if (columns     != '') output += 'columns="'     + columns     + '" ';
		if (rows        != '') output += 'rows="'        + rows        + '" ';
		if (orientation != '') output += 'orientation="' + orientation + '" ';
		if (position    != '') output += 'position="'    + position    + '" ';

		output += '/]';

		// Once the composition of the command shortcode 
		// recall methods for inclusion in TinyMCE editor

		SZGoogleEditor.execCommand('mceReplaceContent',false,output);
		tinyMCEPopup.close();
	}
};

// Initialize the dialog and TinyMCE also call 
// the init routine for the initial operations

tinyMCEPopup.onInit.add(SZGoogleDialog.init,SZGoogleDialog);