<?php
/**
 * Template for a an image inside of another image frame.
 *
 * @package shiro
 */

$data = $args;

if ( empty( $data['inner_image'] ) ) {
	return;
}

$inner_image     = $data['inner_image'];
$container_class = empty( $data['container_class'] ) ? '' : ' ' . $data['container_class'];
$attachment      = get_post( $inner_image );
$caption         = $attachment->post_excerpt;
$credit          = $attachment->post_content;
$has_caption     = ! empty( $caption ) || ! empty( $credit );

?>

<?php if ( is_singular( 'post' ) || is_singular( 'profile' ) || is_singular( 'story' ) ) : ?>
<div class="article-img article-img-main mw-980">
<?php endif; ?>

<div class="<?php echo esc_attr( $container_class ); ?>">
	<div>
		<?php echo wp_get_attachment_image( $inner_image, 'large' ); ?>
	</div>
	<h1></h1>
</div>

<?php if ( $has_caption ) : ?>
<div class="img-caption mw-980">
	<?php if ( ! empty( $caption ) ) : ?>
		<span class="photo-caption"><?php echo wp_kses_post( $caption ); ?></span>
	<?php endif; ?>

	<?php if ( ! empty( $credit ) ) : ?>
		<span class="photo-credit"><?php echo wp_kses_post( $credit ); ?></span>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php if ( is_singular( 'post' ) || is_singular( 'profile' ) ) : ?>
</div>
<?php endif; ?>
