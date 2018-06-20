<?php
/**
 * Partial for Search and Donate button
 *
 * @package wmfoundation
 */

$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate Now', 'wmfoundation' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', '#' );
$container_class   = is_front_page() ? 'subnav-container nav-container' : 'nav-container';

?>

<div class="<?php echo esc_attr( $container_class ); ?>">
	<div class="search-cta-container">
		<?php get_search_form( true ); ?>
		<a class="nav-cta btn <?php echo esc_attr( wmf_get_header_cta_button_class() ); ?>" href="<?php echo esc_url( $wmf_donate_uri ); ?>"><?php echo esc_html( $wmf_donate_button ); ?></a>
	</div>
</div>
