<?php
/**
 * Handles employment listing wrapper for a single listing.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['listings'][0] ) ) {
	return;
}

?>

<div class="mw-784 article-main mod-margin-bottom wysiwyg center">
	<?php if ( ! empty( $template_args['heading'] ) ) : ?>
	<h3 class="mar-bottom_lg"><?php echo esc_html( $template_args['heading'] ); ?></h3>
	<?php endif; ?>
	<?php get_template_part( 'template-parts/modules/employment/listing', null, $template_args['listings'][0] ); ?>
</div>
