<?php include_once( socialize_inc . 'login-settings.php' ); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<?php global $socialize; ?>
<?php if ( $socialize['responsive'] == 'gp-responsive' ) { ?><meta name="viewport" content="width=device-width, initial-scale=1"><?php } ?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php esc_url( bloginfo( 'pingback_url' ) ); ?>" />
<?php wp_head(); ?>
<script type="text/javascript">
	
jQuery(document).ready(function() {
	 if(jQuery('#eab-events-fpe-categories').val() == 5) {
	 	jQuery('.opp-display').show(); 
	 }else {
	 	jQuery('.opp-display').hide(); 
	 }
    
    jQuery('#eab-events-fpe-categories').change(function(){
        if(jQuery('#eab-events-fpe-categories').val() == 5) {
            jQuery('.opp-display').show(); 
        } else {
            jQuery('.opp-display').hide(); 
        } 
    });
});
</script>

</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

<?php if ( ! is_page_template( 'blank-page-template.php' ) ) { ?>
	
	<div id="gp-site-wrapper">
			
		<?php if ( has_nav_menu( 'gp-primary-main-header-nav' ) OR has_nav_menu( 'gp-secondary-main-header-nav' ) ) { ?>		
			<nav id="gp-mobile-nav" itemscope itemtype="http://schema.org/SiteNavigationElement">
				<div id="gp-mobile-nav-close-button"></div>
				<?php wp_nav_menu( array( 'theme_location' => 'gp-primary-main-header-nav', 'sort_column' => 'menu_order', 'container' => '', 'items_wrap' => '<ul class="menu">%3$s</ul>', 'fallback_cb' => 'null', 'walker' => new socialize_custom_menu ) ); ?>
				<?php wp_nav_menu( array( 'theme_location' => 'gp-secondary-main-header-nav', 'sort_column' => 'menu_order', 'container' => 'ul', 'fallback_cb' => 'null', 'walker' => new socialize_custom_menu ) ); ?>
			</nav>
		<?php } ?>
			
		<div id="gp-page-wrapper">

			<header id="gp-main-header" itemscope itemtype="http://schema.org/WPHeader">

				<div class="gp-container">
				
					<div id="gp-logo">
						<?php if ( $socialize['desktop_logo']['url'] OR $socialize['mobile_logo']['url'] ) { ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>">
								<img src="<?php echo esc_url( $socialize['desktop_logo']['url'] ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="<?php echo absint( $socialize['desktop_logo_dimensions']['width'] ); ?>" height="<?php echo absint( $socialize['desktop_logo_dimensions']['height'] ); ?>" class="gp-desktop-logo" />
								<img src="<?php echo esc_url( $socialize['mobile_logo']['url'] ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="<?php echo absint( $socialize['mobile_logo_dimensions']['width'] ); ?>" height="<?php echo absint( $socialize['mobile_logo_dimensions']['height'] ); ?>" class="gp-mobile-logo" />
							</a>
						<?php } ?>
						<div class="gp-triangle"></div>
					</div>

					<a id="gp-mobile-nav-button"></a>
				
					<?php if ( has_nav_menu( 'gp-primary-main-header-nav' ) OR has_nav_menu( 'gp-secondary-main-header-nav' ) OR $socialize['search_button'] != 'gp-search-disabled' ) { ?>

						<nav id="gp-main-nav" class="gp-nav" itemscope itemtype="http://schema.org/SiteNavigationElement">
						
							<div id="gp-primary-main-nav">
								<?php wp_nav_menu( array( 'theme_location' => 'gp-primary-main-header-nav', 'sort_column' => 'menu_order', 'container' => 'ul', 'fallback_cb' => 'null', 'walker' => new socialize_custom_menu ) ); ?>
							</div>
						
							<?php if ( function_exists( 'is_woocommerce' ) && $socialize['cart_button'] != 'gp-cart-disabled' ) { echo socialize_dropdown_cart(); } ?>
						
							<?php if ( $socialize['search_button'] != 'gp-search-disabled' ) { ?>
								<div id="gp-search">
									<div id="gp-search-button"></div>
									<div id="gp-search-box"><?php get_search_form(); ?></div>
								</div>
							<?php } ?>
							
							<?php if ( $socialize['profile_button'] != 'gp-profile-disabled' ) { ?>
								<a href="<?php if ( function_exists( 'bp_is_active' ) ) { global $bp; echo $bp->loggedin_user->domain; } else { global $current_user; get_currentuserinfo(); echo get_author_posts_url( $current_user->ID ); } ?>" id="gp-profile-button"></a>
							<?php } ?>	
																		
							<div id="gp-secondary-main-nav">
								<?php wp_nav_menu( array( 'theme_location' => 'gp-secondary-main-header-nav', 'sort_column' => 'menu_order', 'container' => 'ul', 'fallback_cb' => 'null', 'walker' => new socialize_custom_menu ) ); ?>
							</div>
												
						</nav>
													
					<?php } ?>
						
				</div>
			
			</header>

			<?php if ( $socialize['small_header'] != 'gp-no-small-header' ) { ?>
	
				<header id="gp-small-header" itemscope itemtype="http://schema.org/WPHeader">
	
					<div class="gp-container">

						<div class="gp-left-triangle"></div>
						<div class="gp-right-triangle"></div>
					
						<nav id="gp-top-nav" class="gp-nav" itemscope itemtype="http://schema.org/SiteNavigationElement">		
							
							<div id="gp-left-top-nav">	
								<?php wp_nav_menu( array( 'theme_location' => 'gp-left-small-header-nav', 'sort_column' => 'menu_order', 'container' => 'ul', 'fallback_cb' => 'null', 'walker' => new socialize_custom_menu ) ); ?>
							</div>	
						
							<div id="gp-right-top-nav">	
								<?php wp_nav_menu( array( 'theme_location' => 'gp-right-small-header-nav', 'sort_column' => 'menu_order', 'container' => 'ul', 'fallback_cb' => 'null', 'walker' => new socialize_custom_menu ) ); ?>
							</div>	
										
						</nav>
					
					</div>
		
				</header>
	
			<?php } ?>
		
			<div id="gp-fixed-padding"></div>
		
			<?php if ( $socialize['header_ad'] ) { ?>
				<div id="gp-header-area">
					<div class="gp-container">
						<?php echo do_shortcode( $socialize['header_ad'] ); ?>
					</div>
				</div>
			<?php } ?>
				
<?php } ?>