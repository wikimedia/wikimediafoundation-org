<?php
/**
 * Individual related pages
 *
 * @package wmfoundation
 */

$template_data = wmf_get_template_data();

if ( empty( $template_data ) || empty( $template_data['title'] ) || empty( $template_data['id'] ) ) {
	return;
}

$title = $template_data['title'];
$link  = ! empty( $template_data['link'] ) ? $template_data['link'] : '';
$image_id = ! empty( $template_data['img_id'] ) ? $template_data['img_id'] : false;
$image = '';

if ( false === $image_id ) {
	$page_header = get_post_meta( $template_data['id'], 'page_header_background', true );
	$image_id = ! empty( $page_header['image'] ) ? $page_header['image'] : false;
}

if ( ! empty( $image_id ) ) {
	$image = wp_get_attachment_image_src( $image_id, 'image_4x5_large' );
}

?>
<a class="card card-vertical hover-img-zoom bg-img--blue w-32p" href="<?php echo esc_url( $link ); ?>">

<div class="img-container">
	<div class="bg-img-container">
		<?php if ( ! empty( $image ) ) : ?>
			<div class="bg-img" style="background-image: url(<?php echo esc_url( $image[0] ); ?>)"></div>
		<?php endif; ?>
	</div>
</div>

<div class="card-content">
	<h3 class="card-heading color-white">
		<?php echo esc_html( $title ); ?>
	</h3>
</div>

</a>