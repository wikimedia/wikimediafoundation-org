<?php

function wmf_setup_profile_fields_for_editor() {
	$register = function ( $key, $args ) {
		register_meta(
			'post',
			$key,
			array_merge(
				[
					'single' => true,
					'show_in_rest' => true,
					'object_subtype' => 'profile',
				],
				$args
			)
		);
	};

	$register( 'last_name', [
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => '',
	] );
	$register( 'profile_role', [
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => '',
	] );
	$register( 'profile_featured', [
		'type' => 'boolean',
		'sanitize_callback' => 'rest_sanitize_boolean',
		'default' => false,
	] );
	$register( 'connected_user', [
		'type' => 'integer',
		'sanitize_callback' => function ( $value ) {
			if ( is_numeric( $value ) ) {
				return intval( $value );
			}

			return 0;
		},
		'default' => 0,
	] );
	$register( 'contact_links', [
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
	] );
}

add_action( 'init', 'wmf_setup_profile_fields_for_editor' );
