<?php
/**
 * Basic data for list
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

// Whitelist a subset of SVG for use in Transparency Report visualizations.
$allowed_tags  = array_merge(
	wp_kses_allowed_html( 'post' ),
	[
		'svg'  => [
			'viewBox' => true,
			'width'   => true,
			'height'  => true,
		],
		'rect' => [
			'fill'    => true,
			'width'   => true,
			'height'  => true,
			'x'       => true,
			'y'       => true,
		],
	]
);

// error_log( print_r( $allowed_tags, true ) );

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
		<?php echo wp_kses( do_shortcode( wpautop( $list_section['description'] ) ), $allowed_tags ); ?>
	</div>
	<?php endif; ?>

	<ul class="link-list">
	<?php
	if ( isset( $list_section['links'] ) ) :
		foreach ( $list_section['links'] as $link ) :
			wmf_get_template_part( 'template-parts/modules/list/item', $link );
		endforeach;
	endif;
	?>
	</ul>
</div>

	<?php
}
