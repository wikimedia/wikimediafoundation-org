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


$image_url  = wp_get_attachment_image_url( $image_id, 'thumbnail' );
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

<div class="w-50p p">
	<span class="credit-desc"><strong><a href="#" data-src="<?php echo esc_url( $image_url ); ?>" class="preview" title=""><?php echo esc_html( $title ); ?></a></strong></span>
	<span class="credit"><?php echo wp_kses_post( $description ); ?></span>
</div>
