<?php

/*--------------------------------------------------------------
Set WooCommerce defaults
--------------------------------------------------------------*/

if ( is_admin() &&get_option( 'socialize_wc_defaults' ) !== '1' ) {
	function socialize_woocommerce_defaults() {		
		update_option( 'shop_catalog_image_size', array( 'width' => 454, 'height' => 550, 'crop' => 1 ) );
		update_option( 'shop_thumbnail_image_size', array( 'width' => 180, 'height' => 180, 'crop' => 1 ) ); 
		update_option( 'shop_single_image_size', array( 'width' => 500, 'height' => 700, 'crop' => 1 ) );
	}	
	add_action( 'init', 'socialize_woocommerce_defaults', 1 );	
	update_option( 'socialize_wc_defaults', '1' );												
}
	

/*--------------------------------------------------------------
Pagination
--------------------------------------------------------------*/

remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );
if ( ! function_exists( 'woocommerce_pagination' ) ) {
	function woocommerce_pagination() {
		global $wp_query;
		echo socialize_pagination( $wp_query->max_num_pages );
	}
}	
add_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );


/*--------------------------------------------------------------
Dropdown Cart
--------------------------------------------------------------*/

// Plugin Check
function socialize_dropdown_plugin_installed() {
    
	if ( function_exists( 'dropdowncart_scripts' ) ) { ?>
	
		<div class="error"><p><?php _e( 'Woocommerce Dropdown Cart Widget is already built into this theme, please deactivate this plugin.', 'socialize' ); ?></p></div>
		
	<?php }
}
add_action( 'admin_notices', 'socialize_dropdown_plugin_installed' );

// Dropdown Cart Loop
if ( ! function_exists( 'socialize_dropdown_loop' ) ) {

	function socialize_dropdown_loop() {
	
		global $woocommerce;

		if ( sizeof( $woocommerce->cart->cart_contents ) > 0 ) {
	
			$i = 0;				
		
			foreach ( $woocommerce->cart->cart_contents as $cart_item_key => $cart_item ) {
	
				$i++;
			
				if ( $i == 1 ) {				
					$rowclass = ' class="cart-oddrow"';			
				} else {
					$rowclass = ' class="cart-evenrow"';
					$i = 0;
				}

				$_product = $cart_item['data'];
	
				if ( $_product->exists() && $cart_item['quantity'] > 0 ) {
		
					echo '<li' . $rowclass . '>';
		
						echo '<a href="' . get_permalink( $cart_item['product_id'] ) . '">';				
				
				
							if ( has_post_thumbnail( $cart_item['product_id'] ) ) {					
								echo get_the_post_thumbnail( $cart_item['product_id'], 'shop_thumbnail' ); 
							} else {			
								$placeholder = wc_get_image_size( 'shop_thumbnail' );
								echo '<img src="' . $woocommerce->plugin_url() . '/assets/images/placeholder.png" alt="Placeholder" width="' . $placeholder['width'] . '" height="' . $placeholder['height'] . '" />'; 			
							}
										
							echo '<span class="gp-dropdowncart-product">';
						
								echo '<span class="gp-product-title">' . apply_filters( 'woocommerce_cart_widget_product_title', $_product->get_title(), $_product ) . '</span>';		
										
								if ( $_product instanceof woocommerce_product_variation && is_array( $cart_item['variation'] ) ) {
									echo woocommerce_get_formatted_variation( $cart_item['variation'] );
								}
			
								echo '<span class="gp-quantity">' . $cart_item['quantity'] . ' &times; ' . woocommerce_price( $_product->get_price() ) . '</span>';

							echo '</span>';
					
						echo '</a>';
		
					echo '</li>';
			
				}
			
			}
		
		} else {
	 
			echo '<li class="gp-empty">' . __( 'No products in the cart.', 'socialize' ) . '</li>'; 
	
		} ?>
					
		<?php if ( sizeof( $woocommerce->cart->cart_contents ) > 0 ) {
	
			echo '<li class="gp-total">';
		
				if ( get_option( 'js_prices_include_tax' ) == 'yes' ) {
					_e( 'Total', 'socialize' );
				} else {
					_e( 'Subtotal', 'socialize' );
				}

				echo ': ' . $woocommerce->cart->get_cart_total();
		
			echo '</li>';
		
			do_action( 'woocommerce_widget_shopping_cart_before_buttons' );
		
			echo '<li class="gp-dropdowncart-buttons">
				  <a href="' . $woocommerce->cart->get_cart_url() . '"><span class="button ">' . __( 'View Cart', 'socialize' ) . '</span></a> <a href="' . $woocommerce->cart->get_checkout_url() . '"><span class="button">' . __( 'Checkout', 'socialize' ) . '</span></a>
				  </li>';
		}
			
	}
	
}

// Normal Drop Down Cart
if ( ! function_exists( 'socialize_dropdown_cart' ) ) {														
	function socialize_dropdown_cart() {
		global $woocommerce;
		if ( ! is_cart() ) { ?>	
			<div id="gp-dropdowncart" class="gp-nav">
				<ul class="menu">
					<li class="gp-standard-menu gp-dropdowncart-menu">
						<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" id="gp-cart-button" title="<?php _e( 'View your shopping cart', 'socialize' ); ?>">
							<span id="gp-cart-counter"><?php echo sprintf( _n( '%d', '%d', $woocommerce->cart->cart_contents_count, 'socialize' ), $woocommerce->cart->cart_contents_count ); ?></span>
						</a>
						<ul class="sub-menu">
							<?php echo socialize_dropdown_loop(); ?>			
						</ul>
					</li>
				</ul>		
			</div>
	<?php }
	}
}


// Ajaxify Cart Button
if ( ! function_exists( 'socialize_woocommerce_add_to_cart_fragment' ) ) {
	function socialize_woocommerce_add_to_cart_fragment( $fragments ) {
		global $woocommerce; ob_start(); ?>
			<span id="gp-cart-counter"><?php echo sprintf( _n( '%d', '%d', $woocommerce->cart->cart_contents_count, 'socialize' ), $woocommerce->cart->cart_contents_count ); ?></span>
		<?php $fragments['#cart-button .cart-count'] = ob_get_clean();
		return $fragments;

	}
}
add_filter( 'add_to_cart_fragments', 'socialize_woocommerce_add_to_cart_fragment' );

// Ajaxify Dropdwon Cart
if ( ! function_exists( 'socialize_woocommerce_dropdown_fragment' ) ) {
	function socialize_woocommerce_dropdown_fragment( $fragments ) {
		global $woocommerce; ob_start(); ?>
			<ul class="sub-menu">
				<?php echo socialize_dropdown_loop(); ?>			
			</ul>
		<?php $fragments['.dropdowncart .sub-menu'] = ob_get_clean();
		return $fragments;
	}
}
add_filter( 'add_to_cart_fragments', 'socialize_woocommerce_dropdown_fragment' );

?>