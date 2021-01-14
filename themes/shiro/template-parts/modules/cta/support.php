<?php
/**
 * Handles Support Module CTA.
 *
 * @package shiro
 */

$image_id = get_theme_mod( 'wmf_support_image' );
$support_link = get_theme_mod( 'wmf_support_link_uri' );
$image    = is_numeric( $image_id ) ? wp_get_attachment_image_url( $image_id, 'image_16x9_large' ) : '';
$page_id = get_queried_object_id();

$template_args = array(
	'class'     => 'cta-primary img-left-content-right bg-img--pink btn-pink cta-support',
	'image'     => $image,
	'heading'   => get_theme_mod( 'wmf_support_heading' ),
	'content'   => get_theme_mod( 'wmf_support_content' ),
	'link_uri'  => $support_link'&utm_source='$page_id,
	'link_text' => get_theme_mod( 'wmf_support_link_text' ),
);

if ( empty( $template_args['link_uri'] ) || empty( $template_args['link_text'] ) ) {
	return; // CTAs need links.
}

wmf_get_template_part( 'template-parts/modules/cta/page', $template_args );
