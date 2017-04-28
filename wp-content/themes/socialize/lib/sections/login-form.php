<?php if ( ! is_user_logged_in() ) { global $socialize; ?>

	<div id="login">
		
		<div id="gp-login-modal">
			
			<div class="gp-login-close"></div>
			
			<h3 class="gp-login-title"><?php esc_html_e( 'Login', 'socialize' ); ?></h3>
			<h3 class="gp-lost-password-title"><?php esc_html_e( 'Lost Password', 'socialize' ); ?></h3>
			<h3 class="gp-register-title"><?php esc_html_e( 'Register', 'socialize' ); ?></h3>

			<?php echo do_shortcode( '[login]' ); ?>
		
		</div>
			
	</div>
				
	<script>
	jQuery( document ).ready(function( $ ) {							

		'use strict';	
				
		$( 'a[href="#login"], a[href="#register"]' ).click( function() {
			$( '#login' ).show();
		});
				
		$( document ).mouseup(function(e) {
			var container = $( '#gp-login-modal' );
			if ( ! container.is( e.target ) && container.has( e.target ).length === 0) {
				$( '#login' ).hide();
			}
		});

		$( '.gp-login-close' ).click(function() {
			$( '#login' ).hide();
		});
		
		$( '.gp-login-link a, a[href="#login"]' ).click(function() {
			$( '.gp-login-title' ).show();
			$( '.gp-lost-password-title' ).hide();
			$( '.gp-register-title' ).hide();
		});

		$( '.gp-lost-password-link a' ).click(function() {
			$( '.gp-login-title' ).hide();
			$( '.gp-lost-password-title' ).show();
			$( '.gp-register-title' ).hide();
		});

		$( '.gp-register-link a[href="#gp-register-link"], a[href="#register"]' ).click(function() {
			$( '.gp-login-title' ).hide();
			$( '.gp-lost-password-title' ).hide();
			$( '.gp-register-title' ).show();
		});				
					
	});
	</script>
	
<?php } ?>