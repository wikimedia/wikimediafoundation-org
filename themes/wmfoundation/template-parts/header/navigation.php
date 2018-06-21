<?php
/**
 * Adds Header navigation
 *
 * @package wmfoundation
 */

$wmf_menu_button = get_theme_mod( 'wmf_menu_button_copy', __( 'MENU', 'wmfoundation' ) );

?>


<nav class="main-nav">

	<button class="mobile-nav-toggle bold"><?php wmf_show_icon( 'menu', 'icon-white material' ); ?><?php echo esc_html( $wmf_menu_button ); ?></button>

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
