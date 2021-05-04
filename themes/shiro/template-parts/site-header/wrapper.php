<?php
/**
 * Provides a wrapper for the separate parts of the header
 *
 * @package shiro
 */

?>

<div class="site-header">
	<div class="site-header__inner">
		<?php get_template_part( 'template-parts/site-header/toggle' ); ?>
		<?php get_template_part( 'template-parts/site-header/logo' ); ?>
		<?php get_template_part( 'template-parts/site-header/language-switcher' ); ?>
		<?php get_template_part( 'template-parts/site-header/donate' ); ?>
	</div>
</div>
