<?php global $socialize; $gp_footer_widget_class = ''; ?>

<?php if ( ! is_page_template( 'blank-page-template.php' ) ) { ?>

			<?php if ( ! empty( $socialize['footer_ad'] ) ) { ?>
				<div id="gp-footer-area">
					<div class="gp-container">
						<?php echo do_shortcode( $socialize['footer_ad'] ); ?>
					</div>
				</div>			
			<?php } ?>
		
			<?php if ( ! empty( $socialize['footer_image']['url'] ) ) { ?>
				<div id="footer-image">
					<div class="gp-container">
						<img src="<?php echo esc_url( $socialize['footer_image']['url'] ); ?>" width="<?php echo absint( $socialize['footer_image_dimensions']['width'] ); ?>" height="<?php echo absint( $socialize['footer_image_dimensions']['height'] ); ?>" alt="" />
					</div>	
				</div>
			<?php } ?>
				
			<footer id="gp-footer" itemscope itemtype="http://schema.org/WPFooter">
		
				<?php if ( is_active_sidebar( 'gp-footer-1' ) OR is_active_sidebar( 'gp-footer-2' ) OR is_active_sidebar( 'gp-footer-3' ) OR is_active_sidebar( 'gp-footer-4' ) OR is_active_sidebar( 'gp-footer-5' ) ) { ?>

					<div id="gp-footer-widgets">
						
						<div class="gp-container">
					
							<?php
							if ( is_active_sidebar( 'gp-footer-1' ) && is_active_sidebar( 'gp-footer-2' ) && is_active_sidebar( 'gp-footer-3' ) && is_active_sidebar( 'gp-footer-4' ) && is_active_sidebar( 'gp-footer-5' ) ) {
								$gp_footer_widget_class = 'gp-footer-fifth';
							} elseif ( is_active_sidebar( 'gp-footer-1' ) && is_active_sidebar( 'gp-footer-2' ) && is_active_sidebar( 'gp-footer-3' ) && is_active_sidebar( 'gp-footer-4' ) ) { 			
								$gp_footer_widget_class = 'gp-footer-fourth';
							} elseif ( is_active_sidebar( 'gp-footer-1' ) && is_active_sidebar( 'gp-footer-2' ) && is_active_sidebar( 'gp-footer-3' ) ) {
								$gp_footer_widget_class = 'gp-footer-third';
							} elseif ( is_active_sidebar( 'gp-footer-1' ) && is_active_sidebar( 'gp-footer-2' ) ) {
								$gp_footer_widget_class = 'gp-footer-half';
							} elseif ( is_active_sidebar( 'gp-footer-1' ) ) {
								$gp_footer_widget_class = 'gp-footer-whole';
							} ?>

							<?php if ( is_active_sidebar( 'gp-footer-1' ) ) { ?>
								<div class="gp-footer-widget gp-footer-1 <?php echo sanitize_html_class( $gp_footer_widget_class ); ?>">
									<?php dynamic_sidebar( 'gp-footer-1' ); ?>
								</div>
							<?php } ?>

							<?php if ( is_active_sidebar( 'gp-footer-2' ) ) { ?>
								<div class="gp-footer-widget gp-footer-2 <?php echo sanitize_html_class( $gp_footer_widget_class ); ?>">
									<?php dynamic_sidebar( 'gp-footer-2' ); ?>
								</div>
							<?php } ?>

							<?php if ( is_active_sidebar( 'gp-footer-3' ) ) { ?>
								<div class="gp-footer-widget gp-footer-3 <?php echo sanitize_html_class( $gp_footer_widget_class ); ?>">
									<?php dynamic_sidebar( 'gp-footer-3' ); ?>
								</div>
							<?php } ?>

							<?php if ( is_active_sidebar( 'gp-footer-4' )  ) { ?>
								<div class="gp-footer-widget gp-footer-4 <?php echo sanitize_html_class( $gp_footer_widget_class ); ?>">
									<?php dynamic_sidebar( 'gp-footer-4' ); ?>
								</div>
							<?php } ?>

							<?php if ( is_active_sidebar( 'gp-footer-5' ) ) { ?>
								<div class="gp-footer-widget gp-footer-5 <?php echo sanitize_html_class( $gp_footer_widget_class ); ?>">
									<?php dynamic_sidebar( 'gp-footer-5' ); ?>
								</div>
							<?php } ?>
						</div>
				
					</div>

				<?php } ?>
			
				<div id="gp-copyright"<?php if ( $gp_footer_widget_class != '' ) { ?> class="gp-has-footer-widgets"<?php } ?>>
	
					<div class="gp-container">

						<div id="gp-copyright-text">
							<?php if ( ! empty( $socialize['copyright_text'] ) ) { ?>
								<?php echo wp_kses_post( $socialize['copyright_text'] ); ?>
							<?php } else { ?>
								<?php esc_html_e( 'Copyright &copy;', 'socialize' ); ?> <?php echo date( 'Y' ); ?> <a href="http://themeforest.net/user/GhostPool/portfolio?ref=GhostPool"><?php esc_html_e( 'GhostPool.com', 'socialize' ); ?></a>. <?php esc_html_e( 'All rights reserved.', 'socialize' ); ?>
							<?php } ?>
						</div>
										
						<?php if ( has_nav_menu( 'gp-footer-nav' ) ) { ?>
							<nav id="gp-footer-nav" class="gp-nav" itemscope itemtype="http://schema.org/SiteNavigationElement">
								<?php wp_nav_menu( array( 'theme_location' => 'gp-footer-nav', 'sort_column' => 'menu_order', 'container' => 'ul', 'fallback_cb' => 'null' ) ); ?>			
							</nav>
						<?php } ?>
				
					</div>
					
				</div>

			</footer>
		
			<div class="gp-clear"></div>
		
		</div>
	
		<?php if ( ( isset( $socialize['popup_box'] ) && $socialize['popup_box'] == 'enabled' ) ) { get_template_part( 'lib/sections/login', 'form' ); } ?>
	
	</div>
	
<?php } ?>

<?php wp_footer(); ?>
<?php if ( is_user_logged_in() ) {
	} else { 
	$redurl = home_url().'/register/';
		?>
	<script type="text/javascript">
	url="<?php echo $redurl; ?>";
	jQuery('#nav-menu-item-49 a').attr("href",url);
	jQuery('#nav-menu-item-58 a').attr("href",url);
	jQuery('#nav-menu-item-102 a').attr("href",url);
	jQuery('#nav-menu-item-83 a').attr("href",url);
	jQuery('#nav-menu-item-46 a').attr("href",url);	
	</script>
<?php	} ?>
</body>
</html>