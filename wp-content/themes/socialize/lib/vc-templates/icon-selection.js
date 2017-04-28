jQuery( document ).ready( function( $ ) {

	'use strict';
			
	var input = $( '#gp-icon-selection-value' );
	var selected = $( '.gp-icon-link.gp-selected' );
	
	// Add icon link as input value	
	$( '.gp-icon-link' ).click( function() {
		$( '.gp-icon-link.gp-selected' ).removeClass( 'gp-selected' );
		$( this ).addClass( 'gp-selected' );     
		var value = $( this ).attr( 'href' );
		input.val( value );
		return false;
	});
	
	// Remember selected icon link	
	$( '.gp-icon-link' ).each( function() {
		var value = $( this ).attr( 'href' );	
		if( input.val() == value ) {
			$( this ).addClass( 'gp-selected' ); 
		}
	});
	
	
});	