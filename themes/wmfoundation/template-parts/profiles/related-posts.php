<?php
/**
 * Get a list of related posts on a single profile.
 *
 * @package wmfoundation
 */

$connected_user = get_post_meta( get_the_ID(), 'connected_user', true );

if ( empty( $connected_user ) ) {
	return;
}

$author_posts = wmf_get_recent_author_posts( $connected_user );
/* translators: %s: Title of Author */
$title = sprintf( __( 'Recent posts by %s', 'wmfoundation' ), get_the_title() );

wmf_get_template_part(
	'template-parts/modules/related/posts', array(
		'description' => $title,
		'posts'       => $author_posts,
	)
);
