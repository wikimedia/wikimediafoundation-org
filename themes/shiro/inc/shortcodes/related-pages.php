<?php
/**
 * Placeholder shortcode for the FM Related_Pages field.
 */

namespace WMF\Shortcodes\Related_Pages;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_related_pages', __NAMESPACE__ . '\\shortcode_callback' );
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
	$template_args = (array) get_post_meta( $post_id, 'related_pages', true );
	if ( empty( $template_args ) ) {
		return '';
	}

	$template_args['preheading'] = get_theme_mod( 'wmf_related_pages_pre_heading', __( 'Related', 'shiro' ) );

	ob_start();
	wmf_get_template_part( 'template-parts/modules/related/pages', $template_args );

	return (string) ob_get_clean();
}
