<?php
/**
 * Block patterns for content editors to quickly create content.
 */

namespace WMF\Editor\Patterns;

/**
 * @var string The slug for the block pattern category to group these into.
 */
const CATEGORY_NAME = 'wikimedia';

/**
 * Hook into WordPress
 */
function bootstrap() {
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_pattern' );
}

function register_pattern() {
	register_block_pattern_category( CATEGORY_NAME, [
		'label' => __( 'Wikimedia', 'shiro' ),
	] );

	register_block_pattern( FactColumns\NAME, [
		'title' => __( 'Numbered fact columns', 'shiro' ),
		'categories' => [ CATEGORY_NAME ],
		'content' => FactColumns\PATTERN,
	] );

	register_block_pattern( TweetColumns\NAME, [
		'title' => __( 'Tweet this columns', 'shiro' ),
		'categories' => [ CATEGORY_NAME ],
		'content' => TweetColumns\PATTERN,
	] );

	register_block_pattern( LinkColumns\NAME, [
		'title' => __( 'External link columns', 'shiro' ),
		'categories' => [ CATEGORY_NAME ],
		'content' => LinkColumns\pattern(),
	] );

	register_block_pattern( CardColumns\NAME, [
		'title' => __( 'Cards' ),
		'categories' => [ CATEGORY_NAME ],
		'content' => CardColumns\PATTERN,
	] );

	register_block_pattern( BlogList\NAME, [
		'title' => __( 'Blog list section', 'shiro' ),
		'categories' => [ CATEGORY_NAME ],
		'content' => BlogList\PATTERN,
	] );

}
