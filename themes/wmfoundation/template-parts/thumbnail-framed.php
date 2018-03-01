<?php
/**
 * Template for a an image inside of another image frame.
 *
 * @package wmfoundation
 */

$data = wmf_get_template_data();

if ( empty( $data['container_image'] ) || empty( $data['inner_image'] ) ) {
	return;
}

$container_image = $data['container_image'];
$inner_image     = $data['inner_image'];
$container_class = empty( $data['container_class'] ) ? '' : ' ' . $data['container_class'];
$attachment      = get_post( $inner_image );
$caption         = $attachment->post_excerpt;
$credit          = $attachment->post_content;
$has_caption     = ! empty( $caption ) || ! empty( $credit );

?>

<?php if ( is_singular( 'post' ) ) : ?>
<div class="article-img article-img-main mw-1017">
<?php endif; ?>

<div class="mw-1017 staff-image-container<?php echo esc_attr( $container_class ); ?>">
	<div class="staff-image-bkg duotone-inline-img-container--blue">
		<img src="<?php echo esc_url( $container_image ); ?>" alt="">
	</div>

	<div class="staff-image">
		<?php echo wp_get_attachment_image( $inner_image, 'large' ); ?>
	</div>
</div>

<?php if ( $has_caption ) : ?>
<div class="img-caption mw-900">
	<?php if ( ! empty( $caption ) ) : ?>
		<span class="photo-caption"><?php echo esc_html( $caption ); ?></span>
	<?php endif; ?>

	<?php if ( ! empty( $credit ) ) : ?>
		<span class="photo-credit"><?php echo wp_kses_post( $credit ); ?></span>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php if ( is_singular( 'post' ) ) : ?>
</div>
<?php endif; ?>
