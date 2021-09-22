<?php
/**
 * Front Page Template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 */

/**
 * This loads the blocks template if the front page is using blocks.
 * This is necessary as a conditional so that the "old" home page can exist
 * after the initial deploy of these changes. Once the home page has been moved
 * over to blocks, `front-page.php` can be deleted, and the front page will
 * just use the default page template.
 */
if ( has_blocks() ) {
	get_template_part( 'page-block-editor' );
	exit;
}


get_header();
while ( have_posts() ) {
	the_post();

	// Page Header.
	$parent_page   = wp_get_post_parent_id( get_the_ID() );
	$subtitle      = get_post_meta( get_the_ID(), 'sub_title', true );
	$template_args = array(
		'h4_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
		'h4_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		'h1_title' => get_the_title(),
	);

	if ( has_post_thumbnail() ) {
		$template_args['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		get_template_part( 'template-parts/header/page', 'image', $template_args );
	} else {
		get_template_part( 'template-parts/header/page', 'noimage', $template_args );
	}

	?>
	<?php

	$modules = array(
		'framing-copy',
		'facts',
		'focus-blocks',
		'projects',
		'featured-posts',
		'profiles',
		'support',
		'connect',
	);

	foreach ( $modules as $module ) {
		get_template_part( 'template-parts/page/page', $module );
	}
}
get_footer();
