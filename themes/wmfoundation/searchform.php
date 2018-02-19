<?php
/**
 * The template for displaying search form.
 *
 * @package wmfoundation
 */

$wmf_search_button      = get_theme_mod( 'wmf_search_button_copy', __( 'Search', 'wmfoundation' ) );
$wmf_search_placeholder = get_theme_mod( 'wmf_search_placeholder_copy', __( 'Enter search terms', 'wmfoundation' ) );
?>

<div class="search-container">
	<button class="search-toggle">
		<i class="material-icons">search</i>
		<span class="search-label uppercase bold"><?php echo esc_html( $wmf_search_button ); ?></span>
	</button><div class="search-bar-container">
		<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" placeholder="<?php echo esc_html( $wmf_search_placeholder ); ?> value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
			<i class="material-icons">search</i>
			<button type="submit"><?php esc_html( $wmf_search_button ); ?></button>
		</form>
	</div>

</div>
