<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

get_header();
while ( have_posts() ) {
	the_post();
	$parent_page = wp_get_post_parent_id( get_the_ID() );

	$template_args = array(
		'eyebrow_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
		'eyebrow_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		'eyebrow_class' => 'h4 uppercase',
		'mar_bottom'    => get_the_title(),
	);

	if ( has_post_thumbnail() ) {
		$template_args['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		wmf_get_template_part( 'template-parts/header/page-image', $template_args );
	} else {
		wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );
	}

	get_template_part( 'template-parts/content', 'page' );
}
get_footer();
