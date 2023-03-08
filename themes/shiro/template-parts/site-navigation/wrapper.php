<?php
/**
 * Provides a wrapper for separate parts of the site navigation
 *
 * @package shiro
 */

?>
<nav class="primary-nav">
	<div class="primary-nav__drawer" data-dropdown-content="primary-nav">
		<div class='nav-search nav-search--mobile'>
			<?php get_template_part( 'template-parts/site-navigation/search' ); ?>
		</div>
		<?php get_template_part( 'template-parts/site-navigation/menu' ); ?>
	</div>
</nav>
