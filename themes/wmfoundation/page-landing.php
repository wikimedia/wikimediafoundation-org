<?php
/**
 * Template Name: Landing Page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

get_header();
while ( have_posts() ) {
	the_post();

	// Page Header.
	$parent_page   = wp_get_post_parent_id( get_the_ID() );
	$template_args = array(
		'h4_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
		'h4_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		'h2_title' => get_the_title(),
		'h1_title' => get_post_meta( get_the_ID(), 'sub_title', true ),
	);

	if ( has_post_thumbnail() ) {
		$template_args['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		wmf_get_template_part( 'template-parts/header/page-image', $template_args );
	} else {
		wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );
	}

	$modules = array(
		'intro',
		'social',
		'framing-copy',
		'cta',
		'facts',
		'connect',
		// Todo: add profile module here.
		'listings',
		'featured-posts',
		'offsite-links',
		'support',
	);

	foreach ( $modules as $module ) {
		get_template_part( 'template-parts/page/page', $module );
	}

	// Connect.
	$template_args = get_post_meta( get_the_ID(), 'connect', true );

	if ( empty( $template_args['hide'] ) ) {
		wmf_get_template_part( 'template-parts/modules/general/connect', $template_args );
	}

	// Listings.
	$template_args = get_post_meta( get_the_ID(), 'listings', true );

	if ( ! empty( $template_args ) ) {
		$listings = empty( $template_args['listings'] ) ? array() : $template_args['listings'];

		if ( ! empty( $listings ) && is_array( $listings ) ) {
			if ( 1 === count( $listings ) ) {
				$template_args = $template_args + $listings[0];
				wmf_get_template_part( 'template-parts/modules/employment/single', $template_args );
			} else {
				wmf_get_template_part( 'template-parts/modules/employment/multiple', $template_args );
			}
		}
	}

	// Featured Posts.
	$template_args = array(
		'context'  => get_the_ID(),
		'subtitle' => get_post_meta( get_the_ID(), 'featured_post_sub_title', true ),
		'class'    => 'bg-black',
	);
	wmf_get_template_part( 'template-parts/modules/featured/posts', $template_args );

	// Off Site Links.
	$template_args = get_post_meta( get_the_ID(), 'off_site_links', true );
	wmf_get_template_part( 'template-parts/modules/links/off-site-links', $template_args );
}
get_footer();
