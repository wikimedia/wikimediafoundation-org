<?php
/**
 * The sidebar for list pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'list', true );

if ( empty( $template_args ) ) {
	return;
}
?>

<ul class="toc fixedsticky display-none_small">
	<?php
	foreach ( $template_args as $i => $list_section ) :
		if ( empty( $list_section['title'] ) ) {
			continue;
		}
	?>
		<li class="toc-link-item">
			<a class="toc-link niceScroll" href="#section-<?php echo esc_attr( $i + 1 ); ?>">
				<span class="bold uppercase">
					<?php echo esc_html( $list_section['title'] ); ?>
				</span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
