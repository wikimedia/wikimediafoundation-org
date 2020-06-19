<?php
/**
 * Placeholder shortcode for the page intro element.
 *
 * @package WMF
 */

namespace WMF\Shortcodes\Page_Intro;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_page_intro', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_page_intro] wrapper shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function shortcode_callback( $atts = [], $content = null ) {
	if ( empty( $content ) ) {
		return '';
	}
	$content = do_shortcode( $content );
	$html = '<div class="page-intro wysiwyg mw-980"><div class="page-intro-text flex flex-medium flex">' . wp_kses_post( $content ) . '</div></div>';

	return wp_kses_post( $html );
}
