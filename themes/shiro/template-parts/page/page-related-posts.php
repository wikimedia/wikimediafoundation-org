<?php
/**
 * Get related posts using Jetpack
 *
 * @package shiro
 */

// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$related_posts          = wmf_get_related_posts( get_the_ID() );
$title                  = get_theme_mod( 'wmf_related_posts_title', __( 'Related', 'shiro-admin' ) );
$description            = get_theme_mod( 'wmf_related_posts_description', __( 'Read further in the pursuit of knowledge', 'shiro-admin' ) );
// phpcs:enable


get_template_part(
	'template-parts/modules/related/posts',
	null,
	array(
		'title'                  => $title,
		'description'            => $description,
		'posts'                  => $related_posts,
	)
);


