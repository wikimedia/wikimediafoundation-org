<?php
/**
 * Profile card
 *
 * @package shiro
 */

$template_data = $args;

$cardlink  = ! empty( $template_data['link'] ) ? $template_data['link'] : '';
$cardtitle = ! empty( $template_data['title'] ) ? $template_data['title'] : '';
$excerpt   = ! empty( $template_data['excerpt'] ) ? $template_data['excerpt'] : '';
$img_id    = ! empty( $template_data['img_id'] ) ? $template_data['img_id'] : '';

?>

<a class="card card-vertical card-person rounded shadow hover-img-zoom w-32p" href="<?php echo esc_url( $cardlink ); ?>">
	<?php if ( ! empty( $img_id ) ) : ?>
	<div class="img-container" style="background-image:url(<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'image_4x5_large' ) ); ?>)">
	</div>
	<?php endif; ?>

	<div class="card-content top-into-img">
		<?php if ( ! empty( $cardtitle ) ) : ?>
		<h5 class="person-name">
			<?php echo esc_html( $cardtitle ); ?>
		</h5>
		<?php endif; ?>

		<?php if ( ! empty( $excerpt ) ) : ?>
			<span class="person-title p color-gray">
				<?php echo esc_html( $excerpt ); ?>
			</span>
		<?php endif; ?>
	</div>

</a>
