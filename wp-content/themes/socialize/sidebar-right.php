<?php global $socialize;

if ( $GLOBALS['socialize_layout'] == 'gp-both-sidebars' OR $GLOBALS['socialize_layout'] == 'gp-right-sidebar' ) { 

	if ( is_user_logged_in() ){
	?>
		
	<aside id="gp-sidebar-right" class="gp-sidebar" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">

		<?php if ( is_active_sidebar( $GLOBALS['socialize_right_sidebar'] ) ) {
			dynamic_sidebar( $GLOBALS['socialize_right_sidebar'] );
		} ?>		

	</aside>

<?php } }?>