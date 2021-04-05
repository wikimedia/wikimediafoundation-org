<?php
/**
 * Adds Header navigation
 *
 * @package shiro
 */
?>

<nav class="primary-nav">

	<div class="primary-nav__drawer">
	<?php
	if ( has_nav_menu( 'header' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'header',
				'menu_class'     => 'primary-nav__items',
				'container'      => '',
			)
		);
	}
	?>
	<div class="search-cta-container">
		<?php get_search_form( true ); ?>
	</div>
	</div>

</nav>
