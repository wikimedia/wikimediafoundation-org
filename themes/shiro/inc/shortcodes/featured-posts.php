<?php
/**
 * Placeholder shortcode for the FM lists field.
 */

namespace WMF\Shortcodes\Featured_Posts;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_featured_posts', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_featured_posts] wrapper shortcode.
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
 * @param int $post_id The post ID.
 *
 * @return string
 */
function shortcode_content( int $post_id ) : string {
	$context = is_front_page() ? 'home' : get_the_ID();

	$template_args = array(
		'context'  => $context,
		'subtitle' => get_post_meta( get_the_ID(), 'featured_post_sub_title', true ),
	);

	ob_start();
	wmf_get_template_part( 'template-parts/modules/featured/posts', $template_args );

	return (string) ob_get_clean();
}
