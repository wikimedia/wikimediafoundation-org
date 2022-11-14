<?php
/**
 * Template for individual item in list
 *
 * @package shiro
 */

$template_data = $args;

if ( empty( $template_data ) || empty( $template_data['title'] ) ) {
	return;
}

$title       = $template_data['title'];
$image       = ! empty( $template_data['image'] ) ? $template_data['image'] : '';
$subhead     = ! empty( $template_data['subhead'] ) ? $template_data['subhead'] : '';
$description = ! empty( $template_data['description'] ) ? $template_data['description'] : '';
$link        = ! empty( $template_data['link'] ) ? $template_data['link'] : '';


?>
<li class="flex flex-medium">
	<?php if ( ! empty( $image ) ) : ?>
	<div class="mar-right">
		<?php echo wp_get_attachment_image( $image, 'image_square_medium' ); ?>
	</div>
	<?php endif; ?>

	<div>
		<?php if ( ! empty( $title ) ) : ?>
		<h3 class="h2 link-external">

			<?php if ( ! empty( $link ) ) : ?>
			<a href="<?php echo esc_url( $link ); ?>">
			<?php endif; ?>

			<?php echo esc_html( $title ); ?>

			<?php if ( ! empty( $link ) ) : ?>
				<?php
				if ( ! empty( $template_data['offsite'] ) ) {
					wmf_show_icon( 'open', 'external-link-icon' );
				}
				?>
			</a>
			<?php endif; ?>
		</h3>
		<?php endif; ?>

		<?php
		if ( ! empty( $subhead ) ) :
			echo wp_kses_post( wpautop( sprintf( '<p class="mar-bottom bold">%s</p>', $subhead ) ) );
		endif;
		?>

		<?php if ( ! empty( $description ) ) : ?>
			<div class="mar-bottom">
				<?php echo wp_kses_post( $description ); ?>
			</div>
		<?php endif; ?>
	</div>
</li>
