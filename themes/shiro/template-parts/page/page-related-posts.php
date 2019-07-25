<?php
/**
 * Get related posts using Jetpack
 *
 * @package shiro
 */

$related_posts          = wmf_get_related_posts( get_the_ID() );
$title                  = get_theme_mod( 'wmf_related_posts_title', __( 'Related', 'shiro' ) );
$description            = get_theme_mod( 'wmf_related_posts_description', __( 'Read further in the pursuit of knowledge', 'shiro' ) );


wmf_get_template_part(
	'template-parts/modules/related/posts', array(
		'title'                  => $title,
		'description'            => $description,
		'posts'                  => $related_posts,
	)
);


