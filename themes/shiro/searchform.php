<?php
/**
 * The template for displaying search form.
 *
 * @package shiro
 */

$wmf_search_button      = get_theme_mod( 'wmf_search_button_copy', __( 'Search', 'shiro' ) );
$wmf_search_toggle      = get_theme_mod( 'wmf_search_toggle', __( 'Toggle search', 'shiro' ) );
$wmf_search_placeholder = get_theme_mod( 'wmf_search_placeholder_copy', __( 'What are you looking for?', 'shiro' ) );
?>

<button class="search-toggle" aria-label="<?php echo esc_html( $wmf_search_toggle ); ?>">
	<span class="btn-label-a11y"><?php echo esc_html( $wmf_search_toggle ); ?></span>
	<?php wmf_show_icon( 'search', 'material' ); ?>
	<span class="search-label uppercase bold" aria-label="<?php echo esc_html( $wmf_search_toggle ); ?>"><?php echo esc_html( $wmf_search_button ); ?></span>
</button>
