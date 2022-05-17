<?php

function wmf_setup_profile_fields_for_editor() {
	$register = function( $key, $args ) {
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

	$register( 'last_name', [ 'type' => 'string', ] );
	$register( 'profile_role', [ 'type' => 'string', ] );
	$register( 'profile_featured', [ 'type' => 'boolean', ] );
	$register( 'connected_user', [ 'type' => 'integer' ] );
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
					]
				]
			]
		]
	] );
}

add_action( 'init', 'wmf_setup_profile_fields_for_editor' );
