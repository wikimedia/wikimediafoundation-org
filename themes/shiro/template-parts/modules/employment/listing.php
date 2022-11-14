<?php
/**
 * Handles employment listing output.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['heading'] ) && empty( $template_args['content'] ) && empty( $template_args['link'] ) ) {
	return;
}

$link_href = is_email( $template_args['link'] ) ? sprintf( 'mailto:%s', $template_args['link'] ) : $template_args['link'];
$link_text = ! empty( $template_args['link_text'] ) ? $template_args['link_text'] : $template_args['link'];

?>
<?php if ( ! empty( $template_args['heading'] ) ) : ?>
<h4 class="mar-bottom"><?php echo esc_html( $template_args['heading'] ); ?></h4>
<?php endif; ?>
<?php if ( ! empty( $template_args['content'] ) ) : ?>
<p class="mar-bottom_lg"><?php echo esc_html( $template_args['heading'] ); ?></p>
<?php endif; ?>
<?php if ( ! empty( $link_href ) ) : ?>
<div class="link-list hover-highlight uppercase">
	<!-- Single link -->
	<a href="<?php echo esc_url( $link_href ); ?>" target="_blank"><?php echo esc_html( $link_text ); ?></a>
</div>
<?php endif; ?>
