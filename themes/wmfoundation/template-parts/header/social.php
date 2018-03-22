<?php
/**
 * Setup Social share module
 *
 * @package wmfoundation
 */

$services      = get_post_meta( get_the_ID(), 'share_links', true );
$template_data = array(
	'list_class' => '',
);

if ( ! empty( $services ) ) {
	$template_data['services'] = $services;
}

wmf_get_template_part( 'template-parts/modules/social/share-vertical', $template_data );
