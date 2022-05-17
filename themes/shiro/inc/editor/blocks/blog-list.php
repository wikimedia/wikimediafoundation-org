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
				'excludedCategories' => [
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
 * @param array $attributes
 *
 * @return string HTML markup.
 */
function render_block( array $attributes ) : string {

	$args = [
		'posts_per_page'   => $attributes['postsToShow'],
		'post_status'      => 'publish',
		'order'            => $attributes['order'],
		'orderby'          => $attributes['orderBy'],
		'suppress_filters' => false,
	];

	$categories = array_column( $attributes['categories'] ?? [], 'id' );
	$excluded_categories = array_column( $attributes['excludedCategories'] ?? [], 'id' );

	if ( count( $categories ) > 0 ) {
		$args['cat'] = join( ',', $categories );
	}

	if ( count( $excluded_categories ) > 0 ) {
		if ( ! isset( $args['cat'] ) ) {
			$args['cat'] = '';
		}
		$args['cat'] = array_reduce( $excluded_categories, function ( $carry, $item ) {
			return $carry . ",-$item";
		}, $args['cat'] );
	}

	if ( isset( $attributes['selectedAuthor'] ) ) {
		$args['author'] = $attributes['selectedAuthor'];
	}

	if ( wmf_get_current_content_language_term() !== null ) {
		/*
		 * This allows for a special case where translated posts will *not* be
		 * filtered out: If the block has `Translations` selected as one of the
		 * categories to show, then we *don't* filter out non-main languages.
		 * To do so would almost certainly result in no posts being returned.
		 */
		$in_translated = array_reduce( $categories, function( $collected, $cat_id ) {
			if ( $collected === true ) {
				return true;
			}
			$term = get_term( $cat_id, 'category' );

			return $term->slug === 'translations';
		}, false );

		if ( ! $in_translated ) {
			if ( ! isset( $args['tax_query'] ) ) {
				$args['tax_query'] = [];
			}
			$args['tax_query'] = array_merge( $args['tax_query'], [
				[
					'taxonomy' => 'content-language',
					'field' => 'term_id',
					'terms' => [ wmf_get_current_content_language_term()->term_id ],
				]
			] );
		}
	}

	$recent_posts = get_posts( $args );

	if ( count( $recent_posts ) > 0 ) {
		$output = '';

		foreach ( $recent_posts as $recent_post ) {
			$output .= BlogPost\render_block( [ 'post_id' => $recent_post->ID ] );
		}

		return $output;
	}

	return '';
}
