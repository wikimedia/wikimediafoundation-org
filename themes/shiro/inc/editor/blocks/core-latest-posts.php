<?php
/**
 * Filter the output of the core/latest posts block
 */

namespace WMF\Editor\Blocks\CoreLatestPosts;

use WMF\Editor\Blocks\BlogPost;

const BLOCK_NAME = 'core/latest-posts';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_filter( 'register_block_type_args', __NAMESPACE__ . '\\filter_register_block_type_args', 10, 2 );
}


/**
 * Filter the args registered with the latest-posts block type.
 *
 * @param []|string $args Block registration args.
 * @param string    $name Block name.
 * @return [] Updated block registration arguments.
 */
function filter_register_block_type_args( $args, $name ) {
	if ( $name !== BLOCK_NAME ) {
		return $args;
	}

	// Add a custom render callback.
	$args['render_callback'] = __NAMESPACE__ . '\\render_block';

	return $args;
}

/**
 * Updated render callback for the core/latest-posts block.
 *
 * @param [] $attributes  Parsed block attributes.
 * @return string HTML markup.
 */
function render_block( $attributes ) {

	$args = [
		'posts_per_page'   => $attributes['postsToShow'],
		'post_status'      => 'publish',
		'order'            => $attributes['order'],
		'orderby'          => $attributes['orderBy'],
		'suppress_filters' => false,
	];

	if ( isset( $attributes['categories'] ) ) {
		$args['category__in'] = array_column( $attributes['categories'], 'id' );
	}

	if ( isset( $attributes['selectedAuthor'] ) ) {
		$args['author'] = $attributes['selectedAuthor'];
	}

	$recent_posts = get_posts( $args );

	if ( $recent_posts ) {
		$output = '';

		foreach ( $recent_posts as $recent_post ) {
			$output .= BlogPost\render_block( [ 'post_id' => $recent_post->ID ] );
		}

		return $output;
	}
}
