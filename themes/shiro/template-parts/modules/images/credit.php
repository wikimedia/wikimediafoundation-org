<?php
/**
 * Handles simple text CTA module.
 *
 * @package shiro
 */

$image_id = $args['image_id'];

if ( empty( $image_id ) ) {
	return;
}

$attachment = get_post( $image_id );

if ( empty( $attachment ) ) {
	return;
}

$title       = $attachment->post_title;
$description = $attachment->post_content;
$credit_info = get_post_meta( $image_id, 'credit_info', true );
$author      = ! empty( $credit_info['author'] ) ? $credit_info['author'] : '';
$license     = ! empty( $credit_info['license'] ) ? $credit_info['license'] : '';
$url         = ! empty( $credit_info['url'] ) ? $credit_info['url'] : '';
if ( is_int(stripos($license,'Public domain') ) ) {
$license_url = 'https://en.wikipedia.org/wiki/Public_domain';
} elseif ( is_int(stripos($license,'GFDL') ) && is_int(stripos($license,'1.2') )  ) {
    $license_url = 'https://commons.wikimedia.org/wiki/Commons:GNU_Free_Documentation_License,_version_1.2';
} elseif ( is_int(stripos($license,'CC0') ) ) {
    $license_url = 'https://creativecommons.org/publicdomain/zero/1.0/';
} elseif ( is_int(stripos($license,'CC') ) && is_int(stripos($license,'BY') ) && is_int(stripos($license,'SA') ) && is_int(stripos($license,'4.0') )  ) {
    $license_url = 'https://creativecommons.org/licenses/by-sa/4.0/';
} elseif ( is_int(stripos($license,'CC') ) && is_int(stripos($license,'BY') ) && is_int(stripos($license,'SA') ) && is_int(stripos($license,'3.0') )  ) {
    $license_url = 'https://creativecommons.org/licenses/by-sa/3.0/';
} elseif ( is_int(stripos($license,'CC') ) && is_int(stripos($license,'BY') ) && is_int(stripos($license,'SA') ) && is_int(stripos($license,'2.0') )  ) {
    $license_url = 'https://creativecommons.org/licenses/by-sa/2.0/';
} elseif ( is_int(stripos($license,'CC') ) && is_int(stripos($license,'BY') ) && ! is_int(stripos($license,'SA') ) && is_int(stripos($license,'4.0') )  ) {
    $license_url = 'https://creativecommons.org/licenses/by/4.0/';
} elseif ( is_int(stripos($license,'CC') ) && is_int(stripos($license,'BY') ) && ! is_int(stripos($license,'SA') ) && is_int(stripos($license,'3.0') )  ) {
    $license_url = 'https://creativecommons.org/licenses/by/3.0/';
} elseif ( is_int(stripos($license,'CC') ) && is_int(stripos($license,'BY') ) && ! is_int(stripos($license,'SA') ) && is_int(stripos($license,'2.5') )  ) {
    $license_url = 'https://creativecommons.org/licenses/by/2.5/';
} elseif ( is_int(stripos($license,'CC') ) && is_int(stripos($license,'BY') ) && ! is_int(stripos($license,'SA') ) && is_int(stripos($license,'2.0') )  ) {
    $license_url = 'https://creativecommons.org/licenses/by/2.0/';
} else {
$license_url = ! empty( $credit_info['license_url'] ) ? $credit_info['license_url'] : '';
}
?>

<div class="attribution-item">
	<div class="attribution-item__image">
		<?php echo wp_get_attachment_image( $image_id, [63, 63] ); ?>
	</div>

	<div class="attribution-item__content">
		<p>
			<?php wmf_shiro_echo_wrap_with_link( $title, $url ); ?>
		</p>

		<?php if ( empty( $author ) && empty( $license ) ) : ?>
			<?php if ( ! empty( $description ) ) : ?>
				<p><?php echo wp_kses_post( $description ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<?php if ( ! empty( $author ) ) : ?>
				<p><?php echo esc_html( $author ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $license ) ) : ?>
				<p>
					<?php wmf_shiro_echo_wrap_with_link( $license, $license_url ); ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
