<?php
/**
 * Registration and management of the post-meta storage for datasets.
 */

namespace Vegalite_Plugin\Datasets\Metadata;

use Vegalite_Plugin\Datasets;

const META_KEY = 'csv_datasets';

/**
 * Connect namespace functions to actions and hooks.
 */
function bootstrap() : void {
	add_action( 'init', __NAMESPACE__ . '\\register_dataset_meta' );
	add_filter( 'ep_prepare_meta_data', __NAMESPACE__ . '\\do_not_index_dataset_meta' );
}

/**
 * Register our dataset meta field.
 */
function register_dataset_meta() {
	foreach ( Datasets\get_supported_post_types() as $post_type ) {
		register_post_meta(
			$post_type,
			META_KEY,
			[
				'single'       => true,
				'default'      => [],
				'show_in_rest' => false, // Use custom REST routes for management.
			]
		);
	}
}

/**
 * Internal function to fetch the dataset metadata array with some error handling.
 *
 * @param int $post_id Post ID.
 * @return array Datasets array stored in meta.
 */
function get_dataset_meta( int $post_id ) : array {
	$datasets = get_post_meta( $post_id, META_KEY, true );

	if ( empty( $datasets ) || ! is_array( $datasets ) ) {
		return [];
	}

	return $datasets;
}

/**
 * Get array of datasets for a post.
 *
 * @param int $post_id Post ID.
 * @return array Datasets array.
 */
function get_datasets( int $post_id ) : array {
	$datasets = get_dataset_meta( $post_id );

	return array_map(
		function( $filename ) use ( $datasets ) : array {
			return [
				'filename' => $filename,
				'content'  => $datasets[ $filename ],
			];
		},
		array_keys( $datasets )
	);
}

/**
 * Get a single dataset for a post.
 *
 * @param int   $post_id   Post ID.
 * @param string $filename Filename (key) of the dataset to return.
 * @return ?array [ filename, content ] array, or null if no match.
 */
function get_dataset( int $post_id, string $filename ) : ?array {
	$datasets = get_dataset_meta( $post_id );

	$filename = Datasets\sanitize_filename( $filename );
	if ( isset( $datasets[ $filename ] ) && is_string( $datasets[ $filename ] ) ) {
		return [
			'filename' => $filename,
			'content'  => $datasets[ $filename ],
		];
	}

	return null;
}

/**
 * Create or update a dataset on a post.
 *
 * @param int    $post_id  ID of post for which to edit datasets.
 * @param string $filename String filename of the data.
 * @param string $content  CSV string contents of the dataset.
 * @return bool True on successful update, false on failure.
 */
function update_dataset( int $post_id, string $filename, string $content ) : bool {
	$meta_value = get_dataset_meta( $post_id );

	$filename = Datasets\sanitize_filename( $filename );
	if ( isset( $meta_value[ $filename ] ) && trim( $meta_value[ $filename ] ) === trim( $content ) ) {
		// No update needed.
		return true;
	}

	$meta_value[ $filename ] = $content;

	$updated = update_post_meta( $post_id, META_KEY, $meta_value );

	// update_post_meta returns the meta ID when creating a brand new record, which we don't need.
	return is_int( $updated ) ? true : $updated;
}

/**
 * Delete a dataset from a post.
 *
 * @param int    $post_id  ID of post for which to edit meta.
 * @param string $filename String filename of dataset to delete.
 * @return bool True on successful delete, false on failure.
 */
function delete_dataset( int $post_id, string $filename ) : bool {
	$meta_value = get_dataset_meta( $post_id );

	if ( empty( $meta_value ) || ! isset( $meta_value[ $filename ] ) ) {
		// No dataset, cannot delete.
		return false;
	}

	$filename = Datasets\sanitize_filename( $filename );
	unset( $meta_value[ $filename ] );

	if ( empty( $meta_value ) ) {
		// All datasets removed: delete wholesale.
		return delete_post_meta( $post_id, META_KEY );
	}

	return update_post_meta( $post_id, META_KEY, $meta_value );
}

/**
 * Exclude dataset metadata from ElasticPress for indexing and search performance.
 *
 * @param array $meta Metadata to index in EP.
 * @return array Filtered metadata fields.
 */
function do_not_index_dataset_meta( $meta ) {
	foreach ( $meta as $key => $value ) {
		if ( preg_match( '/datasets/', $key ) ) {
			unset( $meta[ $key ] );
		}
	}
	return $meta;
}
