<?php
/**
 * Register the shiro/blog-list block.
 */

namespace WMF\Editor\Blocks\BlogList;

use WMF\Editor\Blocks\BlogPost;

const BLOCK_NAME = 'shiro/blog-list';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_block' );
}

/**
 * Register the block here.
 */
function register_block() {
	register_block_type(
		BLOCK_NAME,
		[
			'apiVersion'      => 2,
			'render_callback' => __NAMESPACE__ . '\\render_block',
			'attributes' => [
				'postsToShow' => [
					'type' => 'integer',
					'default' => 2,
				],
				'categories' => [
					'type' => 'array',
					'items' => [
						'type' => 'object',
					],
				],
				'order' => [
					'type' => 'string',
					'default' => 'desc',
				],
				'orderBy' => [
					'type' => 'string',
					'default' => 'date',
				],
				'selectedAuthor' => [
					'type' => 'number',
				],
			],
		]
	);
}

/**
 * Callback for server-side rendering for the blog-list block.
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

	if ( count( $recent_posts ) > 0 ) {
		$output = '';

		foreach ( $recent_posts as $recent_post ) {
			$output .= BlogPost\render_block( [ 'post_id' => $recent_post->ID ] );
		}

		return $output;
	}
}
