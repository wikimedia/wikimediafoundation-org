<?php
/**
 * Basic data for list
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args ) || ! is_array( $template_args ) ) {
	return;
}

foreach ( $template_args as $i => $list_section ) {
	?>
<div class="mod-margin-bottom wysiwyg">
	<?php if ( ! empty( $list_section['title'] ) ) : ?>
	<h2 id="section-<?php echo esc_attr( $i + 1 ); ?>">
		<?php echo esc_html( $list_section['title'] ); ?>
	</h2>
	<?php endif; ?>

	<?php if ( ! empty( $list_section['description'] ) ) : ?>
	<div class="mar-bottom">
		<?php echo wp_kses_post( wpautop( $list_section['description'] ) ); ?>
	</div>
	<?php endif; ?>

	<ul class="link-list">
	<?php
	foreach ( $list_section['links'] as $link ) :
		wmf_get_template_part( 'template-parts/modules/list/item', $link );
	endforeach;
	?>
	</ul>
</div>

	<?php
}
