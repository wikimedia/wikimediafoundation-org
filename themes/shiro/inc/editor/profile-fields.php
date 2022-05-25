<?php

/**
 * Register a single profile field.
 *
 * @param array $registry Array containing key and arguments.
 *
 * @return void
 */
function wmf_register_profile_field( array $registry ) {
	[ 'key' => $key, 'args' => $args ] = $registry;
	$default_register_meta_settings = [
		'single' => true,
		'show_in_rest' => true,
		'object_subtype' => 'profile',
	];
	$compiled_args = array_merge( $default_register_meta_settings, $args );
	register_post_meta(
		'profile',
		$key,
		apply_filters( 'profile_field_args', $compiled_args )
	);
}

/**
 * Set up all profile fields.
 *
 * @return void
 */
function wmf_setup_profile_fields_for_editor() {
	$profile_fields = [
		[
			'key' => 'last_name',
			'args' => [
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default' => '',
			],
		],
		[
			'key' => 'profile_role',
			'args' => [
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default' => '',
			],
		],
		[
			'key' => 'profile_featured',
			'args' => [
				'type' => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default' => false,
			],
		],
		[
			'key' => 'connected_user',
			'args' => [
				'type' => 'integer',
				'sanitize_callback' => function ( $value ) {
					if ( is_numeric( $value ) ) {
						return intval( $value );
					}

					return 0;
				},
				'default' => 0,
			],
		],
		[
			'key' => 'contact_links',
			'args' => [
				'type' => 'array',
				'show_in_rest' => [
					'schema' => [
						'items' => [
							'type' => 'object',
							'properties' => [
								'title' => [
									'type' => 'string',
								],
								'link' => [
									'type' => 'string',
								],
							],
						],
					],
				],
				'sanitize_callback' => function ( $value ) {
					if ( ! is_array( $value ) ) {
						return [];
					}

					return array_filter( $value, function ( $row ) {
						// Both items must be set to a non-empty value.
						if ( isset( $row['title'] ) && isset( $row['link'] ) &&
						     ! empty( $row['title'] ) && ! empty( $row['link'] ) ) {
							return true;
						}

						return false;
					} );
				},
			],
		],
	];

	array_map( 'wmf_register_profile_field', $profile_fields );
}

/**
 * If the query is a search for guest-authors, add title support to guest authors.
 *
 * This is necessary because otherwise REST API doesn't return a title (because
 * guest-author removes title support from itself).
 *
 * @param \WP_Query $query
 *
 * @return void
 */
function wmf_enable_guest_author_titles_when_searching( WP_Query $query ) {
	if ( ! $query->is_search() ) {
		return;
	}
	if ( ! in_array( 'guest-author', $query->get( 'post_type', [] ), true ) ) {
		return;
	}
	add_post_type_support( 'guest-author', [ 'title' ] );
}

/**
 * Because Guest Authors disable `title`, we need to add that value to what REST queries return.
 *
 * @param \WP_REST_Response $response
 * @param \WP_Post $post
 *
 * @return \WP_REST_Response
 */
function wmf_add_author_name_to_guest_author_rest_response( WP_REST_Response $response, WP_Post $post ) {
	$data = $response->get_data();
	$data['author_name'] = $post->post_title;
	$response->set_data( $data );

	return $response;
}

add_action( 'rest_prepare_guest-author', 'wmf_add_author_name_to_guest_author_rest_response', 10, 2 );
add_action( 'pre_get_posts', 'wmf_enable_guest_author_titles_when_searching', 100 );
add_action( 'init', 'wmf_setup_profile_fields_for_editor' );
