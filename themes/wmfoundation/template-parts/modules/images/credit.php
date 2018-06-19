<?php
/**
 * Handles simple text CTA module.
 *
 * @package wmfoundation
 */

$image_id = wmf_get_template_data();

if ( empty( $image_id ) ) {
	return;
}

$attachment = get_post( $image_id );

if ( empty( $attachment ) ) {
	return;
}

$title       = $attachment->post_title;
$description = $attachment->post_content;

if ( empty( $image_url ) ) {
	return;
}
?>

<div class="photo-credit-container w-32p p flex flex-all">
	<div class="photo-credit-img_container">
		<?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
	</div>

	<div>
		<p class="credit-desc"><?php echo esc_html( $title ); ?></p>
		<p class="credit"><?php echo wp_kses_post( $description ); ?></p>
	</div>
</div>
