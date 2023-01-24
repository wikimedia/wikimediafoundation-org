<?php
/**
 * Define our custom CSV endpoint.
 */

namespace Vegalite_Plugin\Datasets\Endpoints;

use Vegalite_Plugin\Datasets;

use Vegalite_Plugin\Datasets\Metadata;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Connect namespace functions to actions and hooks.
 */
function bootstrap() : void {
	add_action( 'rest_api_init', __NAMESPACE__ . '\\register_dataset_routes' );
	add_action( 'rest_pre_serve_request', __NAMESPACE__ . '\\deliver_dataset_as_csv', 10, 4 );
}

/**
 * Return the argument schema for a dataset.
 *
 * @return array Dataset schema as array.
 */
function get_dataset_schema() : array {
	return [
		'filename' => [
			'description' => __( 'Filename to use for the dataset.', 'datavis' ),
			'type'        => 'string',
			'default'     => 'data.csv',
			'required'    => true,
		],
		'content' => [
			'description' => __( 'CSV file contents as string.', 'datavis' ),
			'type'        => 'string',
			'required'    => true,
		],
		'url' => [
			'description' => __( 'Public URL endpoint at which this CSV may be downloaded.', 'datavis' ),
			'type'        => 'string',
			'readonly'    => true,
			'required'    => false,
		],
		'rows' => [
			'description' => __( 'Number of rows in the dataset.', 'datavis' ),
			'type'        => 'integer',
			'readonly'    => true,
			'required'    => false,
		],
	];
}

/**
 * Register dataset sub-routes for all post types which support datasets.
 *
 * @return void
 */
function register_dataset_routes() : void {
	foreach ( Datasets\get_supported_post_types() as $post_type ) {
		// REST route lookup logic adapted from rest_get_route_for_post_type_items.
		$post_type = get_post_type_object( $post_type );
		if ( empty( $post_type ) || ! $post_type->show_in_rest ) {
			continue;
		}

		$namespace = ! empty( $post_type->rest_namespace ) ? $post_type->rest_namespace : 'wp/v2';
		$rest_base = ! empty( $post_type->rest_base ) ? $post_type->rest_base : $post_type->name;

		register_rest_route(
			$namespace,
			$rest_base . '/(?P<post_id>[\\d]+)/datasets',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => __NAMESPACE__ . '\\get_datasets',
					'permission_callback' => '__return_true',
				],
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => __NAMESPACE__ . '\\update_dataset_item',
					'permission_callback' => __NAMESPACE__ . '\\edit_post_datasets_permissions_check',
					'args'                => get_dataset_schema(),
				],
			]
		);

		register_rest_route(
			$namespace,
			$rest_base . '/(?P<post_id>[\\d]+)/datasets/(?P<filename>[a-z0-9-_.]+)',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => __NAMESPACE__ . '\\get_dataset_item',
					'permission_callback' => '__return_true',
					'args'                => [
						'format' => [
							'description' => __( 'Format in which to return dataset' ),
							'type'        => 'string',
							'enum'        => [ 'csv', 'json' ],
							'default'     => 'csv',
							'required'    => false,
						],
						'data_limit' => [
							'description' => __( 'Cap the number of rows returned in the JSON data property.' ),
							'type'        => 'integer',
							'minimum'     => 1,
							'required'    => false,
						],
					],
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => __NAMESPACE__ . '\\update_dataset_item',
					'permission_callback' => __NAMESPACE__ . '\\edit_post_datasets_permissions_check',
					'args'                => get_dataset_schema(),
				],
				[
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => __NAMESPACE__ . '\\delete_dataset_item',
					'permission_callback' => __NAMESPACE__ . '\\edit_post_datasets_permissions_check',
				],
			]
		);
	}
}

/**
 * Get the post, if the ID is valid.
 *
 * Adapted from WP_REST_Posts_Controller.
 *
 * @param int $post_id Supplied ID.
 * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
 */
function get_post( $post_id ) {
	$error = new WP_Error(
		'rest_post_invalid_id',
		// Core WP text, no translation domain needed.
		__( 'Invalid post ID.' ),
		[ 'status' => 404 ]
	);

	if ( (int) $post_id <= 0 ) {
		return $error;
	}

	$post = \get_post( (int) $post_id );
	if ( empty( $post ) || empty( $post->ID ) ) {
		return $error;
	}

	return $post;
}



/**
 * Checks if a given request has access to edit a post.
 *
 * We treat all dataset CRUD actions as a post edit, so ability to edit a post
 * grants ability to create, update, and delete datasets on that post.
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
 */
function edit_post_datasets_permissions_check( $request ) {
	$post = get_post( $request['post_id'] );
	if ( is_wp_error( $post ) ) {
		return $post;
	}

	$post_type = $post ? get_post_type_object( $post->post_type ) : null;

	if ( ( $post && ! current_user_can( 'edit_post', $post->ID ) ) || empty( $post_type->show_in_rest ?? null ) ) {
		return new WP_Error(
			'rest_cannot_edit',
			__( 'Sorry, you are not allowed to edit this post.' ),
			[ 'status' => rest_authorization_required_code() ]
		);
	}

	return true;
}

/**
 * Compute generated properties for datasets before fulfilling the request.
 *
 * @param array           $dataset Dataset array
 * @param WP_REST_Request $request Full details about the request.
 * @return array Array of computed dataset properties (includes original filename).
 */
function get_computed_dataset_properties( array $dataset, WP_REST_Request $request ) : array {
	$rest_route_url = untrailingslashit( $request->get_route() );
	// Handle requests to both /datasets/ and /datasets/:filename.
	if ( substr( $rest_route_url, -9 ) === '/datasets' ) {
		$rest_route_url = $rest_route_url . '/' . $dataset['filename'];
	}
	return [
		'filename' => $dataset['filename'],
		'url'      => get_rest_url( null, $rest_route_url ),
		'rows'     => count( explode( "\n", $dataset['content'] ) ) - 1,
		'fields'   => Datasets\infer_field_types( $dataset['content'] ),
	];
}

/**
 * Serve a list of available datasets.
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
 */
function get_datasets( WP_REST_Request $request ) {
	$post_id = $request['post_id'];

	$valid_check = get_post( $post_id );
	if ( is_wp_error( $valid_check ) ) {
		return $valid_check;
	}

	$datasets = Metadata\get_datasets( $post_id );

	if ( empty( $datasets ) ) {
		return rest_ensure_response( [] );
	}

	return rest_ensure_response(
		array_map(
			function( $dataset ) use ( $request ) : array {
				// Return a no-content version of the dataset.
				return get_computed_dataset_properties( $dataset, $request );
			},
			$datasets
		)
	);
}

/**
 * Create or update a dataset item.
 *
 * @param WP_REST_Request $request POST or PUT request.
 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
 */
function update_dataset_item( WP_REST_Request $request ) {
	$error = new WP_Error(
		'rest_dataset_update_error',
		__( 'Could not write CSV.', 'datavis' ),
		[ 'status' => 400 ]
	);

	$post_id = $request['post_id'];

	$valid_check = get_post( $post_id );
	if ( is_wp_error( $valid_check ) ) {
		return $valid_check;
	}

	$updated = Metadata\update_dataset( $post_id, $request['filename'], $request['content'] );

	if ( ! $updated ) {
		return $error;
	}

	// Respond with the object as it was saved, plus computed properties.
	$saved_dataset = Metadata\get_dataset( $post_id, $request['filename'] );
	$saved_dataset = array_merge( get_computed_dataset_properties( $saved_dataset, $request ), $saved_dataset );
	return rest_ensure_response( $saved_dataset );
}

/**
 * Retrieve a single dataset item.
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
 */
function get_dataset_item( WP_REST_Request $request ) {
	$error = new WP_Error(
		'rest_dataset_invalid_id',
		__( 'Invalid dataset filename.', 'datavis' ),
		[ 'status' => 404 ]
	);

	$post_id = $request['post_id'];

	$valid_check = get_post( $post_id );
	if ( is_wp_error( $valid_check ) ) {
		return $valid_check;
	}

	$dataset = Metadata\get_dataset( $post_id, $request['filename'] );

	if ( $request['filename'] !== $dataset['filename'] || empty( $dataset ) ) {
		return $error;
	}

	$dataset = array_merge( get_computed_dataset_properties( $dataset, $request ), $dataset );

	if ( $request['format'] === 'json' ) {
		$dataset['data'] = Datasets\csv_to_json( $dataset['content'], $request['data_limit'] );
	}

	return rest_ensure_response( $dataset );
}

/**
 * Delete a single dataset item.
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
 */
function delete_dataset_item( WP_REST_Request $request ) {
	$post_id = $request['post_id'];

	$valid_check = get_post( $post_id );
	if ( is_wp_error( $valid_check ) ) {
		return $valid_check;
	}

	$deleted = Metadata\delete_dataset( $post_id, $request['filename'] );

	if ( ! $deleted ) {
		return new WP_Error(
			'rest_dataset_deletion_failed',
			__( 'Could not delete dataset.', 'datavis' ),
			[ 'status' => 409 ]
		);
	}

	return rest_ensure_request( null );
}

/**
 * Take over delivery of REST response to serve datasets as CSV content.
 *
 * @param bool             $served  Whether the request has already been served.
 * @param WP_HTTP_Response $result  Result to send to the client. Usually a `WP_REST_Response`.
 * @param WP_REST_Request  $request Request used to generate the response.
 * @param WP_REST_Server   $server  Server instance.
 * @return true
 */
function deliver_dataset_as_csv( $served, $result, $request, $server ) {
	if ( $request['format'] !== 'csv' || strpos( $request->get_route(), '/datasets/' ) === false || $request->get_method() !== 'GET' ) {
		return $served;
	}

	$csv_data = $result->get_data();

	if ( empty( $csv_data['filename'] ) || ! isset( $csv_data['content'] ) ) {
		// This may not be a CSV metadata object response. For safety, do nothing.
		// Note that empty "content" is acceptable, but the property must exist.
		return $served;
	}

	if ( ! headers_sent() ) {
		$server->send_header( 'Content-Type', 'text/csv; charset=' . get_option( 'blog_charset' ) );
	}

	echo $csv_data['content'];

	return true;
}
