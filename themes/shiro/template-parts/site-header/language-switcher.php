<?php
/**
 * The language switcher in the primary nav.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package shiro
 */

$current_label = get_theme_mod( 'wmf_current_language_label', __( 'Current language:', 'shiro' ) );
$translations  = array_filter( wmf_get_translations(), function ( $translation ) {
	return $translation['uri'] !== '';
} );
$current       = array_reduce( $translations, function ( $carry, $item ) {
	if ( is_string( $carry ) ) {
		return $carry;
	}

	return $item['selected'] ? esc_html( $item['shortname'] ) : null;
}, null );

if ( ! empty( $translations ) ) : ?>
	<div class="language-switcher" data-dropdown="language-switcher" data-dropdown-toggle=".language-switcher__button" data-dropdown-content=".language-switcher__content"
		 data-visible="no" data-trap="inactive" data-backdrop="inactive" data-toggleable="yes">
		<button class="language-switcher__button" aria-expanded="false">
			<span class="btn-label-a11y"><?php echo esc_html( $current_label ); ?> </span>
			<?php wmf_show_icon( 'translate', 'language-switcher__icon' ); ?>
			<span class="language-switcher__label"><?php echo $current; ?></span>
		</button>
		<div class="language-switcher__content">
			<ul>
				<?php foreach ( $translations as $translation ) : ?>
					<li class="language-switcher__language <?php echo $translation['selected'] ? 'language-switcher__language--selected' : '' ?>">
					<span lang="<?php echo esc_attr( $translation['shortname'] ); ?>">
						<a href="<?php echo esc_url( $translation['uri'] ); ?>">
							<span class="language-switcher__language-name">
								<?php echo esc_html( $translation['name'] ); ?>
							</span>
						</a>
					</span>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
<?php endif; ?>
