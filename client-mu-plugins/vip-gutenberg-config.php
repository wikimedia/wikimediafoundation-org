<?php

function wmf_use_block_editor_for_post( $can_edit, $post ) {
	$environment = wp_get_environment_type();

	// For now, only run Gutenberg locally.
	if ( 'production' === $environment) {
		return false;
	}

	$post_content = $post->post_content ?? '';

	// This is a good proxy for whether something is a block editor post.
	$existing_block_editor_post = has_blocks( $post_content );
	$new_post                   = '' === $post_content;

	return $can_edit && ( $existing_block_editor_post || $new_post );
}

/* VIP: Disable Gutenberg editor */
add_filter( 'use_block_editor_for_post', 'wmf_use_block_editor_for_post', 20, 2 );
