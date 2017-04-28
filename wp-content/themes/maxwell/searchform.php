<?php
/**
 * Custom Markup for Search form
 *
 * @package Maxwell
 */

?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'maxwell' ); ?></span>
		<input type="search" class="search-field"
			placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'maxwell' ); ?>"
			value="<?php echo esc_html( get_search_query() ); ?>" name="s"
			title="<?php echo esc_attr_x( 'Search for:', 'label', 'maxwell' ); ?>" />
	</label>
	<button type="submit" class="search-submit">
		<span class="genericon-search"></span>
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'maxwell' ); ?></span>
	</button>
</form>
