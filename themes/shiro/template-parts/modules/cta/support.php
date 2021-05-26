<?php
/**
 * Handles Support Module CTA.
 *
 * @package shiro
 */

$reusable_block_id = get_theme_mod( 'wmf_support_reusable_block' );

if ( is_numeric( $reusable_block_id )
     && $reusable_block_id > 0
     && get_post_type( $reusable_block_id ) === 'wp_block' ) {
	$block = get_post( $reusable_block_id );
	if ( is_a( $block, \WP_Post::class ) ) {
		echo wp_kses_post( apply_filters( 'the_content', $block->post_content ) );
	}
} else {
	// When the updates to how this section works are rolled out, the ability
	// to edit the data originally stored here will be lost. This will use that
	// data if it exists so that it won't suddenly disappear from the frontend,
	// but this code should be removed when the conversion is finished.
	$image_id = get_theme_mod( 'wmf_support_image' );
	$image    = is_numeric( $image_id ) ? wp_get_attachment_image_url( $image_id, 'image_16x9_large' ) : '';

	$template_args = array(
		'class'       => 'cta-primary img-left-content-right bg-img--pink btn-pink cta-support',
		'image'       => $image,
		'heading'     => get_theme_mod( 'wmf_support_heading' ),
		'content'     => get_theme_mod( 'wmf_support_content' ),
		'link_uri'    => get_theme_mod( 'wmf_support_link_uri' ),
		'link_text'   => get_theme_mod( 'wmf_support_link_text' ),
		'support_cta' => true,
	);

	if ( empty( $template_args['link_uri'] ) || empty( $template_args['link_text'] ) ) {
		return; // CTAs need links.
	}

	get_template_part( 'template-parts/modules/cta/page', null, $template_args );
}
