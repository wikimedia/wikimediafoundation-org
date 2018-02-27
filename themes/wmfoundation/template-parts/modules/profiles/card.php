<?php
/**
 * Profile card
 *
 * @package wmfoundation
 */

$template_data = wmf_get_template_data();

$link   = ! empty( $template_data['link'] ) ? $template_data['link'] : '';
$title  = ! empty( $template_data['title'] ) ? $template_data
['title'] : '';
$role   = ! empty( $template_data['role'] ) ? $template_data
['role'] : '';
$team   = ! empty( $template_data['team'] ) ? $template_data
['team'] : '';
$img_id = ! empty( $template_data['img_id'] ) ? $template_data
['img_id'] : '';

?>

<a class="card card-vertical card-person hover-img-zoom w-32p" href="<?php echo esc_url( $link ); ?>">

	<?php if ( ! empty( $img_id ) ) : ?>
	<div class="img-container">
		<?php echo wp_get_attachment_image( $img_id, 'image_4x5_large' ); ?>
	</div>
	<?php endif; ?>

	<div class="card-content top-into-img">
		<?php if ( ! empty( $title ) ) : ?>
		<h5 class="person-name">
			<?php echo esc_html( $title ); ?>
		</h5>
		<?php endif; ?>

		<?php if ( ! empty( $role ) || ! empty( $team ) ) : ?>
			<span class="person-title p color-gray">
				<?php printf( esc_html__( '%1$s, %2$s', 'wmfoundation' ), esc_html( $role ), esc_html( $team ) ); ?>
			</span>
		<?php endif; ?>
	</div>

</a>
