<?php
/**
 * Provides a wrapper for the separate parts of the header
 *
 * @package shiro
 */

/**
 * Available translations are calculated here because we need to know it to
 * determine if the inner header should initialize the language switcher
 * dropdown.
 */
$translations = array_filter( wmf_get_translations(), function ( $translation ) {
	return $translation['uri'] !== '';
} );
?>

<div class="site-header">
	<div class="site-header__inner"
		<?php if ( count( $translations ) > 0 ): ?>
			data-dropdown="language-switcher" data-dropdown-toggle=".language-switcher__button"
			data-dropdown-status="uninitialized"
			data-dropdown-content=".language-switcher__content"
			data-visible="no" data-trap="inactive" data-backdrop="inactive" data-toggleable="yes"
		<?php endif; ?>>
		<?php get_template_part( 'template-parts/site-header/toggle' ); ?>
		<?php get_template_part( 'template-parts/site-header/logo' ); ?>
		<?php get_template_part( 'template-parts/site-header/language-switcher',
			null,
			[ 'translations' => $translations ] ); ?>
		<?php get_template_part( 'template-parts/site-header/donate' ); ?>
	</div>
</div>
