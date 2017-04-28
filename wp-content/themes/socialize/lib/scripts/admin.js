jQuery( document ).ready( function( $ ) {

	'use strict';
	

	/*--------------------------------------------------------------
	Setup homepage
	--------------------------------------------------------------*/
	
	$( '.socialize_vc_homepage_1_template' ).click( function() {

    	$( '#page_template' ).val( 'homepage-template.php' );
		
		$( '#wp-homepage_content_header-editor-container .wp-editor-area' ).val( '[slider cats="main-slider" format="gp-slider-two-cols" per_page="3" slider_speed="0"]' );
		
		$( '#redux-socialize-metabox-page-options' ).hide();
		
		$( '#redux-socialize-metabox-homepage-options' ).show();	
		
	});
	

});