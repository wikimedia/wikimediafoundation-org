<?php
/**
 * Adds Header navigation
 *
 * @package shiro
 */
?>

<nav class="primary-nav">

	<div class="primary-nav__drawer">
		<div class="nav-search">
			<?php get_search_form( true ); ?>
		</div>
	<?php
	if ( has_nav_menu( 'header' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'header',
				'menu_class'     => 'primary-nav__items',
				'container'      => '',
				// Remove this when implementing multi-level navigation.
				// For now it's here because the existing menu implementation
				// doesn't support more than one level and looks very odd if
				// you try.
				'depth' => 1,
			)
		);
	}
	?>
	</div>

</nav>
