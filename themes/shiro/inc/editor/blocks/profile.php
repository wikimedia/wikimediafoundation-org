<?php
/**
 * Server-side registration for the shiro/profile block.
 */

namespace WMF\Editor\Blocks\Profile;

const BLOCK_NAME = 'shiro/profile';

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
			'attributes'      => [
				'profile_id' => [
					'type' => 'integer',
				],
			],
		]
	);
}

/**
 * Render this block, given its attributes.
 *
 * @param [] $attributes Block attributes.
 *
 * @return string HTML markup.
 */
function render_block( $attributes ) {
	if ( empty( $attributes['profile_id'] ) ) {
		return '';
	}

	$profile_id = $attributes['profile_id'];

	$team_name = '';
	$team      = get_the_terms( $profile_id, 'role' );
	if ( ! empty( $team ) && ! is_wp_error( $team ) ) {
		$team_name = $team[0]->name;
	}

	ob_start();

	get_template_part(
		'template-parts/modules/profiles/card',
		null,
		array(
			'title'  => get_the_title( $profile_id ),
			'img_id' => get_post_thumbnail_id( $profile_id ),
			'link'   => get_permalink( $profile_id ),
			'role'   => get_post_meta( $profile_id, 'profile_role', true ),
			'team'   => $team_name,
		)
	);

	return ob_get_clean();
}
