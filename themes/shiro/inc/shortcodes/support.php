<?php
/**
 * Placeholder shortcode for the FM support field.
 */

namespace WMF\Shortcodes\Support;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_support', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_support] wrapper shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function shortcode_callback( $atts = [] ) {
	$html = shortcode_content( get_the_ID() );
	return wp_kses_post( $html );
}

/**
 * Render shortcode HTML.
 *
 * @param int $post_id The post ID.
 */
function shortcode_content( int $post_id ) {
	$image_id = get_theme_mod( 'wmf_support_image' );
	$image    = is_numeric( $image_id ) ? wp_get_attachment_image_url( $image_id, 'image_16x9_large' ) : '';

	$template_args = array(
		'class'     => 'cta-primary img-left-content-right bg-img--pink btn-pink cta-support',
		'image'     => $image,
		'heading'   => get_theme_mod( 'wmf_support_heading' ),
		'content'   => get_theme_mod( 'wmf_support_content' ),
		'link_uri'  => get_theme_mod( 'wmf_support_link_uri' ),
		'link_text' => get_theme_mod( 'wmf_support_link_text' ),
	);

	if ( empty( $template_args['link_uri'] ) || empty( $template_args['link_text'] ) ) {
		return ''; // CTAs need links.
	}

	ob_start();
	wmf_get_template_part( 'template-parts/modules/cta/page', $template_args );
	return (string) ob_get_clean();
}
