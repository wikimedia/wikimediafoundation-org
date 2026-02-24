<?php
/**
 * The template for displaying search form.
 *
 * @package wmfoundation
 */

$wmf_search_button      = get_theme_mod( 'wmf_search_button_copy', __( 'Search', 'wmfoundation' ) );
$wmf_search_placeholder = get_theme_mod( 'wmf_search_placeholder_copy', __( 'Enter search terms', 'wmfoundation' ) );
$wmf_search_aria_label  = get_theme_mod( 'wmf_search_aria_label', __( 'Search Wikimedia Foundation site', 'wmfoundation' ) );
?>

<div class="search-bar-container">
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="search" placeholder="<?php echo esc_attr( $wmf_search_placeholder ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" aria-label="<?php echo esc_attr( $wmf_search_aria_label ); ?>">
		<?php wmf_show_icon( 'search', 'material' ); ?>
		<button class="search-submit" type="submit"><?php echo esc_html( $wmf_search_button ); ?></button>
	</form>
</div>
<button class="search-toggle" aria-hidden="true">
	<?php wmf_show_icon( 'search', 'material' ); ?>
	<span class="search-label uppercase bold"><?php echo esc_html( $wmf_search_button ); ?></span>
</button>
