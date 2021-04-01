<?php
/**
 * Block patterns for content editors to quickly create content.
 */

namespace WMF\Editor\Patterns;

/**
 * Hook into WordPress
 */
function bootstrap() {
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_pattern' );
}

function register_pattern() {
	register_block_pattern_category( 'wikimedia-columns', [
		'label' => __( 'Wikimedia columns', 'shiro' ),
	] );

	register_block_pattern( FactColumns\NAME, [
		'title' => __( 'Numbered fact columns' ),
		'categories' => [ 'wikimedia-columns' ],
		'content' => FactColumns\PATTERN,
	] );

	register_block_pattern( TweetColumns\NAME, [
		'title' => __( 'Tweet this columns', 'shiro' ),
		'categories' => [ 'wikimedia-columns' ],
		'content' => TweetColumns\PATTERN,

	] );

	register_block_pattern( CardColumns\NAME, [
		'title' => __( 'Cards' ),
		'categories' => [ 'wikimedia-columns' ],
		'content' => CardColumns\PATTERN,
	] );

	register_block_pattern( BlogList\NAME, [
		'title' => __( 'Blog list section', 'shiro' ),
		'categories' => [ 'wikimedia-columns' ],
		'content' => BlogList\PATTERN
	] );

}
