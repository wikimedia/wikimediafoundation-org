<?php
/**
 * Registers additional supports for the core "post" post type.
 *
 * @package shiro
 */


/**
 * Registers the `story` post type.
 */
function wmf_post_init() {
	$post_post_type = get_post_type_object( 'post' );

	$post_post_type->template = [
		[ 'shiro/blog-post-heading' ]
	];
}
add_action( 'init', 'wmf_post_init' );

