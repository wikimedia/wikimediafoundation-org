<?php
/**
 * Server-side registration for the shiro/blog-post block.
 */

namespace WMF\Editor\Blocks\BlogPost;

const BLOCK_NAME = 'shiro/blog-post';

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
				'post_id' => [
					'type' => 'integer',
				],
				'is_featured' => [
					'type' => 'boolean',
					'default' => false,
				],
			],
		]
	);
}

/**
 * Render this block, given it's attributes.
 *
 * @param [] $attributes Block attributes.
 * @return string HTML markup.
 */
function render_block( $attributes ) {
	if ( empty( $attributes['post_id'] ) ) {
		return '';
	}

	$post = get_post( $attributes['post_id'] );
	if ( ! $post || is_wp_error( $post ) ) {
		return '';
	}

	setup_postdata( $post );
	ob_start();

	get_template_part(
		'template-parts/modules/cards/card',
		'horizontal',
		[
			'link'       => get_the_permalink(),
			'image_id'   => get_post_thumbnail_id(),
			'title'      => get_the_title(),
			'authors'    => wmf_byline(),
			'date'       => get_the_date(),
			'excerpt'    => get_the_excerpt(),
			'categories' => get_the_category(),
			'class'      => 'blog-post' . ( $attributes['is_featured'] ? ' blog-post--featured' : '' ),
		]
	);

	wp_reset_postdata();
	return ob_get_clean();
}
