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
	<?php
	if ( ! empty( $list_section['title'] ) ) :
		// Stories injected as list items have headings with link targets.
		if ( ! empty( $list_section['link'] ) ) :
			?>
			<h2 id="section-<?php echo esc_attr( $i + 1 ); ?>" class="story-link">
				<a href="<?php echo esc_url( $list_section['link'] ); ?>">
					<?php echo esc_html( $list_section['title'] ); ?>
				</a>
			</h2>
			<?php
		else :
			?>
			<h2 id="section-<?php echo esc_attr( $i + 1 ); ?>">
				<?php echo esc_html( $list_section['title'] ); ?>
			</h2>
			<?php
		endif;
	endif;
	?>

	<?php if ( ! empty( $list_section['description'] ) ) : ?>
	<div class="mar-bottom">
		<?php echo wp_kses_post( do_shortcode( wpautop( $list_section['description'] ) ) ); ?>
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
