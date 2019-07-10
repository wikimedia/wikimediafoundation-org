<?php
/**
 * Template for Intro button
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args ) || empty( $template_args['title'] ) || empty( $template_args['link'] ) ) {
	return;
}

?>

<a href="<?php echo esc_url( $template_args['link'] ); ?>" class="btn btn-pink search-btn">
	<?php echo esc_html( $template_args['title'] ); ?>
</a>
