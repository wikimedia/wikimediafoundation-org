<?php
/**
 * Template for a featured profile
 *
 * @package shiro
 */

$template_data = wmf_get_template_data();

if ( empty( $template_data ) || empty( $template_data['image_id'] ) || empty( $template_data['headline'] ) ) {
	return;
}

$title      = $template_data['headline'];
$image_id   = $template_data['image_id'];
$image      = wp_get_attachment_image_src( $image_id, 'image_16x9_large' );
$teaser     = ! empty( $template_data['teaser'] ) ? $template_data['teaser'] : '';
$link       = ! empty( $template_data['link'] ) ? $template_data['link'] : '';
$link_title = ! empty( $template_data['link_title'] ) ? $template_data['link_title'] : '';

?>

<div class="w-100p cta mod-margin-bottom cta-secondary no-duotone img-left-content-right">
	<div class="mw-1360">
		<div class="card">
			<div class="bg-img-container">
				<div class="bg-img" style="background-image: url(<?php echo esc_url( $image[0] ); ?>);"></div>
			</div>
			<div class="card-content w-45p">
				<?php if ( ! empty( $title ) ) : ?>
				<h2 class="h2">
					<?php echo esc_html( $title ); ?>
				</h2>
				<?php endif; ?>

				<?php if ( ! empty( $teaser ) ) : ?>
				<p>
					<?php echo esc_html( $teaser ); ?>
				</p>
				<?php endif; ?>

				<?php if ( ! empty( $link ) && ! empty( $link_title ) ) : ?>
				<a href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html( $link_title ); ?>
				</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
