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

	$template_args = array(
		'eyebrow_title' => get_the_title(),
		'eyebrow_class' => 'h1',
		'mar_bottom'    => get_post_meta( get_the_ID(), 'sub_title', true ),
	);

	if ( has_post_thumbnail() ) {
		$template_args['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		wmf_get_template_part( 'template-parts/header/page-image', $template_args );
	} else {
		wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );
	}

	$template_args = array(
		'intro' => get_post_meta( get_the_ID(), 'page_intro', true ),
	);

	wmf_get_template_part( 'template-parts/modules/intro/page', $template_args );

}
get_footer();
