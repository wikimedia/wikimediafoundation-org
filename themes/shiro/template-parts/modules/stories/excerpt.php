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
$img_url   = wp_get_attachment_image_url( $img_id, 'image_4x3_large' );
?>

<div class="w-48p wysiwyg">
	<?php if ( ! empty( $img_id ) ) : ?>
		<a href="<?php echo esc_url( $cardlink ); ?>">
			<div class="img-container" style="background-image:url(<?php echo esc_url( $img_url ); ?>)"></div>
		</a>
	<?php endif; ?>

	<div class="card-content">
		<?php if ( ! empty( $cardtitle ) ) : ?>
			<h4 class="story-name"><?php echo esc_html( $cardtitle ); ?></h4>
		<?php endif; ?>

		<?php if ( ! empty( $excerpt ) ) : ?>
			<p class="story-excerpt"><?php echo esc_html( $excerpt ); ?></p>
		<?php endif; ?>

		<p><a href="<?php echo esc_url( $cardlink ); ?>">
			<?php esc_html_e( 'Read more', 'shiro' ); ?> &rarr;</a>
		</p>
	</div>
</div>
