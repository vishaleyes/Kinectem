<?php global $socialize;

if ( $GLOBALS['socialize_layout'] == 'gp-both-sidebars' OR $GLOBALS['socialize_layout'] == 'gp-left-sidebar' ) { ?>

	<aside id="gp-sidebar-left" class="gp-sidebar" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">

		<?php if ( is_active_sidebar( $GLOBALS['socialize_left_sidebar'] ) ) {
			dynamic_sidebar( $GLOBALS['socialize_left_sidebar'] );
		} ?>		

	</aside>

<?php } ?>