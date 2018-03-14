<?php
/**
 * Adds Header navigation
 *
 * @package wmfoundation
 */

$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate Now', 'wmfoundation' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', '#' );
$wmf_menu_button   = get_theme_mod( 'wmf_menu_button_copy', __( 'MENU', 'wmfoundation' ) );

?>

<div class="search-cta-container">
	<?php get_search_form( true ); ?>
	<a class="nav-cta btn <?php echo esc_attr( wmf_get_header_cta_button_class() ); ?>" href="<?php echo esc_url( $wmf_donate_uri ); ?>"><?php echo esc_html( $wmf_donate_button ); ?></a>
</div>

<nav class="main-nav">

	<button class="mobile-nav-toggle bold"><i class="material-icons">menu</i><?php echo esc_html( $wmf_menu_button ); ?></button>

	<?php
	if ( has_nav_menu( 'header' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'header',
				'menu_class'     => 'nav-links list-inline',
				'container'      => '',
			)
		);
	}
	?>
</nav>
