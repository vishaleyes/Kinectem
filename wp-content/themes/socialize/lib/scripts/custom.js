function vc_prettyPhoto() { }  // Disable Visual Composer prettyPhoto override

jQuery( document ).ready( function( $ ) {

	'use strict';
	
	/*--------------------------------------------------------------
	Retina images
	--------------------------------------------------------------*/

	if ( $( 'body' ).hasClass( 'gp-retina' ) ) {
		window.devicePixelRatio >= 2 && $( '.gp-post-thumbnail img' ).each( function() {
			$( this ).attr( { src: $( this ).attr( 'data-rel' ) } );
		});
	}
	
			
	/*--------------------------------------------------------------
	Parallax effect
	--------------------------------------------------------------*/

	if( $( 'div' ).hasClass( 'gp-parallax' ) || $( 'header' ).hasClass( 'gp-parallax' ) ) {
		//$( '.gp-parallax' ).css( 'opacity', 0 );		
		$( window ).load( function() {
			$.stellar({
				responsive: true,
				horizontalScrolling: false
			});
			//$( '.gp-parallax' ).css( 'opacity', 1 );
		});
	}
	

	/*--------------------------------------------------------------
	Remove "|" from BuddyPress item options
	--------------------------------------------------------------*/

	$( '.item-options' ).contents().filter( function() {
		return this.nodeType == 3;
	}).remove();


	/*--------------------------------------------------------------
	Hide BuddyPress item options if width too small
	--------------------------------------------------------------*/

	var optionsWidth = $( '.widget.buddypress .item-options' ).width();	

	function gpBPWidgetOptions() {
		
		$( '.widget.buddypress' ).each( function() {
			
			var widget = $( this );
			
			var widgettitle = widget.find( '.widgettitle' ).html();
			var widgettitleSpan = '<span>' + widgettitle + '</span>';
			widget.find( '.widgettitle' ).html( widgettitleSpan );
			var textWidth = widget.find( 'span:first' ).width();
			widget.find( '.widgettitle' ).html( widgettitle );

			var containerWidth = widget.find( '.widgettitle' ).width();
		
			if ( ( containerWidth - optionsWidth ) > textWidth ) {
				widget.find( '.item-options' ).removeClass( 'gp-small-item-options' );
				widget.find( '.gp-item-options-button' ).remove();
			} else {	
				widget.find( '.item-options' ).addClass( 'gp-small-item-options' );
				widget.find( '.item-options' ).append( '<div class="gp-item-options-button"></div>' );
			}
			
			widget.find( '.gp-item-options-button' ).toggle( function() {
				widget.find( '.gp-small-item-options' ).addClass( 'gp-active' );
			}, function() {
				widget.find( '.gp-small-item-options' ).removeClass( 'gp-active' );
			});		
						
		});
		
	}
	
	gpBPWidgetOptions();
	$( window ).resize( gpBPWidgetOptions );

	
	/*--------------------------------------------------------------
	BuddyPress tabs for mobile
	--------------------------------------------------------------*/			
						
	$( '.item-list-tabs:not(#subnav)' ).prepend( '<div id="gp-bp-tabs-button"></div>' );
	var bptabs = $( '.item-list-tabs:not(#subnav) > ul' );
	
	function gpBPTabs() {

		if ( $( '.item-list-tabs:not(#subnav)' ).find( 'ul' ).length > 0 ) {	

			if ( $( window ).width() <= 567 && $( 'body' ).hasClass( 'gp-responsive' ) ) {
	
				$( bptabs ).hide();

				$( '#gp-bp-tabs-button' ).toggle( function() {
					$( bptabs ).stop().slideDown();
					$( this ).addClass( 'gp-active' );
				}, function() {
					$( bptabs ).stop().slideUp();
					$( this ).removeClass( 'gp-active' );
				});
		
			} else {
		
				$( bptabs ).css( 'height', 'auto' ).show();
		
			}
		
		}
						
	}
	
	gpBPTabs();
	$( window ).resize( gpBPTabs );
				
				
	/*--------------------------------------------------------------
	Masonry blog
	--------------------------------------------------------------*/

	if ( $( 'body' ).hasClass( 'page-template-blog-template' ) && $( '.gp-blog-wrapper' ).hasClass( 'gp-blog-masonry' ) ) {
	
		var container = $( '.gp-blog-masonry .gp-inner-loop' ),
			element = container;

		if ( container.find( 'img' ).length == 0 ) {
			element = $( '<img />' );
		}	

		imagesLoaded( element, function( instance ) {

			container.isotope({
				itemSelector: '.gp-post-item',
				masonry: {
					columnWidth: container.find( '.gp-post-item' )[0],
					gutter: 20
				}
			});

			container.animate( { 'opacity': 1 }, 1300 );
			$( '.gp-pagination' ).animate( { 'opacity': 1 }, 1300 );

		});
				
	}
	
	
	/*--------------------------------------------------------------
	Switch navigation position if near edge
	--------------------------------------------------------------*/

	function gpSwitchNavPosition() {
		$( '#gp-main-nav .menu > li.gp-standard-menu' ).each( function() {
			$( this ).on( 'mouseenter mouseleave', function(e) {
				if ( $( this ).find( 'ul' ).length > 0 ) {
					var menuElement = $( 'ul:first', this ),
						pageWrapper = $( '#gp-main-header .gp-container' ),
						pageWrapperOffset = pageWrapper.offset(),
						menuOffset = menuElement.offset(),
						menuLeftOffset = menuOffset.left - pageWrapperOffset.left,
						pageWrapperWidth = pageWrapper.width();
						if ( $( this ).hasClass( 'gp-dropdowncart-menu' ) ) {							
							var menuWidth = menuElement.width();
						} else {
							var menuWidth = menuElement.width() + 200;
						}
						var isEntirelyVisible = ( menuLeftOffset + menuWidth <= pageWrapperWidth );	
					if ( ! isEntirelyVisible ) {
						$( this ).addClass( 'gp-nav-edge' );
					} else {
						$( this ).removeClass( 'gp-nav-edge' );
					}
				}   
			});
		});	
	}

	gpSwitchNavPosition();
	$( window ).resize( gpSwitchNavPosition );
		
	    
	/*--------------------------------------------------------------
	Mega menus text/image support
	--------------------------------------------------------------*/
		
	if ( $( '.gp-megamenu' ).length > 0 ) {
		
		$( '.gp-nav .gp-megamenu > .sub-menu > li > a, .gp-menu-text > a' ).contents().unwrap().wrap( '<span></span>' );
			
		$( '.gp-nav .gp-megamenu .sub-menu .sub-menu li.gp-menu-image' ).each( function() {
			if ( $( this ).find( 'a' ).length > 0 ) {	
				var src = $( this ).find( 'a' ).attr( 'href' );
				$( '<img class="gp-menu-image" alt="">' ).insertAfter( $( this ).children( ':first' ) );
				$( this ).find( '.gp-menu-image' ).attr( 'src', src );
				$( this ).find( 'a' ).remove();				
			}			
		});
	
		$( '#gp-mobile-nav .gp-menu-image' ).hide();
	
	}
	

	/*--------------------------------------------------------------
	FontAwesome menu icons
	--------------------------------------------------------------*/
		
	$( '.menu li.fa' ).each( function() {	
		var all = $( this ).attr( 'class' ).split(' ');
		for ( var i = 0; i < all.length; ++i ) {
			var cls = all[i];
			if ( cls.indexOf( 'fa' ) == 0 ) {
				$( this ).find( '> a:first-child' ).addClass( cls );
				$( this ).removeClass( cls );
			}
		}
	});
	
					
	/*--------------------------------------------------------------
	Dropdown menu icons
	--------------------------------------------------------------*/
		
	$( '#gp-main-nav .menu > li' ).each( function() {
		if ( $( this ).find( 'ul' ).length > 0 ) {	
			$( '<i class="gp-dropdown-icon gp-primary-dropdown-icon fa fa-angle-down" />' ).appendTo( $( this ).children( ':first' ) );		
		}		
	});
	
	$( '#gp-main-nav .menu > li.gp-standard-menu ul > li' ).each( function() {
		if ( $( this ).find( 'ul' ).length > 0 ) {	
			$( '<i class="gp-dropdown-icon gp-secondary-dropdown-icon fa" />' ).appendTo( $( this ).children( ':first' ) );
		}					
	});
	
							
	/*--------------------------------------------------------------
	Slide up/down header mobile navigation
	--------------------------------------------------------------*/

	function gpHeaderMobileNav() {
		$( '#gp-mobile-nav-button' ).click( function() {
			$( 'body' ).addClass( 'gp-mobile-nav-active' );
		});
		
		$( '#gp-mobile-nav-close-button' ).click( function() {
			$( 'body' ).removeClass( 'gp-mobile-nav-active' );
		});		
	}
	
	gpHeaderMobileNav();
	

	/*--------------------------------------------------------------
	Slide up/down header mobile dropdown menus
	--------------------------------------------------------------*/

	$( '#gp-mobile-nav .menu li' ).each( function() {
		if ( $( this ).find( 'ul' ).length > 0 ) {
			$( '<i class="gp-mobile-dropdown-icon" />' ).insertAfter( $( this ).children( ':first' ) );		
		}		
	});
	
	function gpHeaderMobileTopNav() {

		$( '#gp-mobile-nav ul > li' ).each( function() {
			
			var navItem = $( this );
			
			if ( $( navItem ).find( 'ul' ).length > 0 ) {	
		
				$( navItem ).children( '.gp-mobile-dropdown-icon' ).toggle( function() {
					$( navItem ).addClass( 'gp-active' );
					$( navItem ).children( '.sub-menu' ).stop().slideDown()
					$( '#gp-mobile-nav' ).addClass( 'gp-auto-height' );
				}, function() {
					$( navItem ).removeClass( 'gp-active' );
					$( navItem ).children( '.sub-menu' ).stop().slideUp();
				});
		
			}
					
		});
	
	}
	
	gpHeaderMobileTopNav();


	/*--------------------------------------------------------------
	Search box
	--------------------------------------------------------------*/

	$( document ).mouseup(function(e) {		
		var container = $( '#gp-search' );
		if ( ! container.is( e.target ) && container.has( e.target ).length === 0) {
			$( '#gp-search-box' ).hide();
			$( '#gp-search-button' ).removeClass( 'gp-active' );
		}
	});		
	
	$( document ).on( 'click', '#gp-search-button:not(gp-active)', function() {
		$( this ).addClass( 'gp-active' );
		$( '#gp-search-box' ).show();
	});
	
	$( document).on( 'click', '#gp-search-button.gp-active', function() {
		$( this ).removeClass( 'gp-active' );
		$( '#gp-search-box' ).hide();
	});

				
	/*--------------------------------------------------------------
	Smooth scroll
	--------------------------------------------------------------*/

	if ( $( 'body' ).hasClass( 'gp-smooth-scrolling' ) && $( window ).width() > 767 && $( 'body' ).outerHeight( true ) > $( window ).height() ) {
		$( 'html' ).niceScroll({
			cursorcolor: '#424242',
			scrollspeed: 100,
			mousescrollstep: 40,
			cursorwidth: 10,
			cursorborder: '0',
			zindex: 10000,
			cursoropacitymin: 0.3,
			cursoropacitymax: 0.6
		});
	}

	
	/*--------------------------------------------------------------
	Back to top button
	--------------------------------------------------------------*/

	if ( $( 'body' ).hasClass( 'gp-back-to-top' ) ) {
		$().UItoTop({ 
			containerID: 'gp-to-top',
			containerHoverID: 'gp-to-top-hover',
			text: '<i class="fa fa-chevron-up"></i>',
			scrollSpeed: 600
		});
	}
		

	/*--------------------------------------------------------------
	prettyPhoto lightbox
	--------------------------------------------------------------*/

	if ( socialize_script.lightbox != 'disabled' ) {
		$( 'a.prettyphoto, a[data-rel^="prettyPhoto"]' ).prettyPhoto({
			hook: 'data-rel',
			theme: 'pp_default',
			deeplinking: false,
			social_tools: '',
			default_width: '768'
		});
	}
	

	/*--------------------------------------------------------------
	Share icons panel
	--------------------------------------------------------------*/
		
	$( '.gp-share-button' ).toggle( function() {
		$( '#gp-post-navigation #gp-share-icons' ).stop().slideDown();
		$( this ).addClass( 'gp-active' );
	}, function() {
		$( '#gp-post-navigation #gp-share-icons' ).stop().slideUp();
		$( this ).removeClass( 'gp-active' );
	});
	
	
	/*--------------------------------------------------------------
	Title header video
	--------------------------------------------------------------*/
	
	if ( $( '.gp-page-header' ).hasClass( 'gp-has-video' ) ) {
		headerVideo.init({
			mainContainer: $( '.gp-page-header' ),
			videoContainer: $( '.gp-video-header' ),
			header: $( '.gp-video-media' ),
			videoTrigger: $( '.gp-play-video-button' ),
			closeButton: $( '.gp-close-video-button' ),
			autoPlayVideo: false
		});
	}


	/*--------------------------------------------------------------
	Resize header upon scrolling
	--------------------------------------------------------------*/

	function gpResizeHeader() {

		var topHeaderHeight = $( '#gp-top-header' ).height(),
			mainHeaderHeight = $( '#gp-main-header' ).height(),
			headerHeight = ( topHeaderHeight + mainHeaderHeight );
		
		$( '#gp-fixed-padding' ).css( 'height', headerHeight );

		$( window ).scroll( function() {
		
			if ( $( window ).width() > 1082 && $( 'body' ).hasClass( 'gp-fixed-header' ) ) {

				if ( $( document ).scrollTop() > ( headerHeight + 50 ) ) {
				
					$( 'body' ).addClass( 'gp-scrolling' );
					$( '#gp-main-header' ).fadeIn( 'slow' );
					$( '#gp-fixed-padding' ).css( 'position', 'relative' );

				} else {
				
					$( 'body' ).removeClass( 'gp-scrolling' );
					$( '#gp-main-header' ).css( 'display', '' );
					$( '#gp-fixed-padding' ).css( 'position', 'absolute' );
				
				}
			
			} else {
			
				$( 'body' ).removeClass( 'gp-scrolling' );
				$( '#gp-fixed-padding' ).css( 'position', 'absolute' );
			
			}

		});				

	}

	gpResizeHeader();
	$( window ).resize( gpResizeHeader );


	/*--------------------------------------------------------------
	Demo switcher
	--------------------------------------------------------------*/

	$( '#gp-demo-switcher-button' ).toggle( function() {
		$( '#gp-demo-switcher' ).animate({ 'left': '0' });
	}, function() {
		$( '#gp-demo-switcher' ).animate({ 'left': '-280px' });	
	});


});