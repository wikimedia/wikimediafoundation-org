<?php
/**
 * The sidebar for list pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package shiro
 */

$current_page_id = get_the_ID();
$sidebar_items   = wmf_get_report_sidebar_data();

if ( empty( $sidebar_items ) ) {
	return;
}
?>

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
			<?php esc_html_e( 'Navigate within this section.', 'shiro' ) ?>
		</span>
		<span class="btn-label-active-item">
			<?php
			if ( empty( $sidebar_items[0]['title'] ) ) {
				esc_html_e( 'Toggle menu', 'shiro' );
			} else {
				echo wp_kses_post( $sidebar_items[0]['title'] );
			}
			?>
		</span>
	</button>
	<ul class="table-of-contents toc">
		<?php
		foreach ( $sidebar_items as $report_section ) {
			if ( empty( $report_section['title'] ) ) {
				continue;
			}
			$link_classes = $report_section['active'] ? 'toc__link toc__link--active-page' : 'toc__link';
			?>
			<li class="toc__item">
				<a class="<?php echo esc_attr( $link_classes ); ?>" href="<?php echo esc_url( $report_section['url'] ); ?>">
					<?php echo wp_kses_post( $report_section['title'] ); ?>
				</a>
				<?php
				// Nest page anchor sidebar within nav sidebar.
				if ( $current_page_id === $report_section['id'] ) {
					get_sidebar( 'list', [ 'nested'=> true ] );
				}
				?>
			</li>
			<?php
		}
		?>
	</ul>
</nav>
