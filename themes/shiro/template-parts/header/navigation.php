<?php
/**
 * Adds Header navigation
 *
 * @package shiro
 */
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
	<div class="nav-search">
		<?php get_search_form( true ); ?>
	</div>

</nav>
