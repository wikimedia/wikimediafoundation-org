<?php
/**
 * The sidebar for list pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package shiro
 */

// $args is data passed into the template, also we provide fallbacks in case they don't exist.
// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$nested        = $args['nested'] ?? false;
$template_args = is_array( $args['template_args'] ) ? $args['template_args'] : get_post_meta( get_the_ID(), 'list', true );
// phpcs:enable

if ( empty( $template_args ) ) {
	return;
}
?>

<?php if ( ! $nested ) : ?>
<nav
	class="toc-nav"
	data-backdrop="inactive"
	data-dropdown="toc-nav"
	data-dropdown-content=".toc"
	data-dropdown-status="uninitialized"
	data-dropdown-toggle=".toc__button"
	data-sticky="false"
	data-toggleable="yes"
	data-trap="inactive"
	data-visible="false"
>
	<h2 class="toc__title screen-reader-text">
		<?php esc_html_e( 'Table of Contents', 'shiro' ) ?>
	</h2>
	<button
		aria-expanded="false"
		class="toc__button"
		hidden
	>
		<span class="btn-label-a11y">
			<?php esc_html_e( 'Navigate within this page.', 'shiro' ) ?>
		</span>
		<span class="btn-label-active-item">
			<?php
			if ( empty( $template_args[0]['title'] ) ) {
				esc_html_e( 'Toggle menu', 'shiro' );
			} else {
				echo esc_html( $template_args[0]['title'] );
			}
			?>
		</span>
	</button>
	<ul class="table-of-contents toc">
<?php else : ?>
	<ul class="toc__nested">
<?php endif; ?>
	<?php
	foreach ( $template_args as $i => $list_section ) :
		$item_text = $list_section['title'] ?? $list_section['name'];
		$item_link = $list_section['slug'] ? '#' . $list_section['slug'] : '#section-' . ( $i + 1 );
		if ( empty( $item_text ) ) {
			continue;
		}
		?>
		<li class="toc__item">
			<a class="toc__link" href="<?php echo esc_url( $item_link ); ?>">
				<?php echo esc_html( $item_text ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

<?php if ( ! $nested ) : ?>
</nav>
<?php endif; ?>
