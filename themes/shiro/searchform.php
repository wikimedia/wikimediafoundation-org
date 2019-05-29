<?php
/**
 * The template for displaying search form.
 *
 * @package shiro
 */

$wmf_search_button      = get_theme_mod( 'wmf_search_button_copy', __( 'Search', 'shiro' ) );
$wmf_search_placeholder = get_theme_mod( 'wmf_search_placeholder_copy', __( 'Enter search terms', 'shiro' ) );
?>

<button class="search-toggle">
	<?php wmf_show_icon( 'search', 'material' ); ?>
	<span class="search-label uppercase bold"><?php echo esc_html( $wmf_search_button ); ?></span>
</button>
