<?php
/**
 * Template for individual item in list
 *
 * @package wmfoundation
 */

$template_data = wmf_get_template_data();

if ( empty( $template_data ) || empty( $template_data['title'] ) ) {
	return;
}

$title       = $template_data['title'];
$subhead     = ! empty( $template_data['subhead'] ) ? $template_data['subhead'] : '';
$description = ! empty( $template_data['description'] ) ? $template_data['description'] : '';
$link        = ! empty( $template_data['link'] ) ? $template_data['link'] : '';

?>
<li class="mar-bottom_lg">
	<?php if ( ! empty( $title ) ) : ?>
	<h3 class="link-external">

		<?php if ( ! empty( $link ) ) : ?>
		<a href="<?php echo esc_url( $link ); ?>">
		<?php endif; ?>

		<?php echo esc_html( $title ); ?>

		<?php if ( ! empty( $link ) ) : ?>
		<i class="material-icons external-link-icon">open_in_new</i>
		</a>
		<?php endif; ?>
	</h3>
	<?php endif; ?>

	<?php
	if ( ! empty( $subhead ) ) :
		echo wp_kses_post( wpautop( sprintf( '<p class="mar-bottom color-gray">%s</p>', $subhead ) ) );
	endif;
	?>

	<?php if ( ! empty( $description ) ) : ?>
		<div class="mar-bottom">
			<?php echo wp_kses_post( $description ); ?>
		</div>
	<?php endif; ?>
</li>
