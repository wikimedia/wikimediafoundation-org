<?php
/**
 * Adds Header navigation
 *
 * @package shiro
 */


$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate Now', 'shiro' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', '#' );
$wmf_menu_button = get_theme_mod( 'wmf_menu_button_copy', __( 'MENU', 'shiro' ) );

$wmf_translation_selected = get_theme_mod( 'wmf_selected_translation_copy', __( 'Languages', 'shiro' ) );
$wmf_translations = wmf_get_translations();
?>


<nav class="main-nav flex flex-medium flex-align-center">

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
	<div class="search-cta-container">
		<?php get_search_form( true ); ?>
	</div>

</nav>
