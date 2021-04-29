<?php
/**
 * Provides a wrapper for the separate parts of the header
 *
 * @package shiro
 */

?>

<div class="top-nav">
	<div class="site-main-nav flex flex-medium flex-align-center mw-980">
		<div class="logo-container logo-container_lg">
			<?php get_template_part( 'template-parts/site-header/logo' ); ?>
		</div>
		<div class="logo-container logo-container_sm">
			<?php get_template_part( 'template-parts/site-header/toggle' ); ?>
			<?php get_template_part( 'template-parts/site-header/logo' ); ?>
		</div>
		<div class="top-nav-buttons">
			<?php get_template_part( 'template-parts/site-header/language-switcher' ); ?>
			<?php get_template_part( 'template-parts/site-header/donate' ); ?>
		</div>
	</div>
</div>
