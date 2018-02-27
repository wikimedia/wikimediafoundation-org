<?php
/**
 * Profile card
 *
 * @package wmfoundation
 */

$template_data = wmf_get_template_data();

$link = ! empty( $template_data['link'] ) ? $template_data['link'] : '';
$title = ! empty( $template_data['title'] ) ? $template_data
['title'] : '';
$role = ! empty( $template_data['role'] ) ? $template_data
['role'] : '';
$img_id = ! empty( $template_data['img_id'] ) ? $template_data
['img_id'] : '';

?>

<a class="card card-vertical card-person hover-img-zoom w-32p" href="<?php echo esc_url( $link ); ?>">
<div class="card-content top-into-img">

	<?php if ( ! empty( $img_id ) ) : ?>
	<div class="img-container">
		<?php echo wp_get_attachment_image( $img_id, 'img_large_4x5' ); ?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $title ) ) : ?>
	<h5 class="person-name">
		<?php echo esc_html( $title ); ?>
	</h5>
	<?php endif; ?>

	<?php if ( ! empty( $role ) ) : ?>
		<span class="person-title p color-gray"><?php echo esc_html( $role ); ?></span>
	<?php endif; ?>

</div>

</a>