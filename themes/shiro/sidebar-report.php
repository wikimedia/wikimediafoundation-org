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

<ul class="toc fixedsticky report-nav">
	<?php
	foreach ( $sidebar_items as $report_section ) {
		if ( empty( $report_section['title'] ) ) {
			continue;
		}
		$li_classes = $report_section['active'] ? 'toc-link-item active' : 'toc-link-item';
		?>
		<li class="<?php echo esc_attr( $li_classes ); ?>">
			<a class="toc-link" href="<?php echo esc_url( $report_section['url'] ); ?>">
				<?php echo esc_html( $report_section['title'] ); ?>
			</a>
			<?php
			// Nest page anchor sidebar within nav sidebar.
			if ( $current_page_id === $report_section['id'] ) {
				get_sidebar( 'list' );
			}
			?>
		</li>
		<?php
	}
	?>
</ul>
