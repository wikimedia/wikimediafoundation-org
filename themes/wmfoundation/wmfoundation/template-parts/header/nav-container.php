<?php
/**
 * Partial for Search and Donate button
 *
 * @package wmfoundation
 */

$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate Now', 'wmfoundation' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', '#' );

?>

<div class="nav-container">
	<div class="logo-container logo-container_lg">
		<?php get_template_part( 'template-parts/header/logo' ); ?>
	</div>
	<div class="search-cta-container">
		<?php get_search_form( true ); ?>
		<a class="nav-cta btn <?php echo esc_attr( wmf_get_header_cta_button_class() ); ?>" href="<?php echo esc_url( $wmf_donate_uri ); ?>"><?php echo esc_html( $wmf_donate_button ); ?></a>
	</div>
</div>
