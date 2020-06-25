<?php
/**
 * Placeholder shortcode for the FM page CTA field.
 */

namespace WMF\Shortcodes\Page_CTA;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_page_cta', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_page_cta] wrapper shortcode.
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
 * Render the shortcode HTML.
 *
 * @param int $post_id The post ID.
 *
 * @return string
 */
function shortcode_content( int $post_id ) : string {
	$template_args = (array) get_post_meta( $post_id, 'page_cta', true );
	if ( empty( $template_args ) ) {
		return '';
	}
	ob_start();
	wmf_get_template_part( 'template-parts/modules/cta/page', $template_args );

	return (string) ob_get_clean();
}
