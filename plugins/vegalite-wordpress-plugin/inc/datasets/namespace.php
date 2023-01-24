<?php
/**
 * Handle registration and management of dataset feature support.
 */

namespace Vegalite_Plugin\Datasets;

const FEATURE_NAME = 'datasets';

/**
 * Connect namespace functions to actions and hooks.
 */
function bootstrap() : void {
	add_action( 'init', __NAMESPACE__ . '\\register_dataset_post_type_support' );
}

/**
 * Register the datasets post type supports value on the appropriate post types.
 */
function register_dataset_post_type_support() : void {
	/**
	 * Allow dataset support to be extended to additional post types beyond the defaults.
	 *
	 * @hook datasets/post_types
	 * @param array $post_types Array of names for post types which support datasets.
	 */
	$supported_post_types = apply_filters( 'datasets/post_types', [ 'post', 'page' ] );

	foreach ( $supported_post_types as $post_type ) {
		add_post_type_support( $post_type, FEATURE_NAME );
	}
}

/**
 * Get list of post types which support the datasets feature.
 *
 * @return string[] Supported post type array.
 */
function get_supported_post_types() : array {
	return array_filter(
		get_post_types(),
		function( $post_type ) : bool {
			return post_type_supports( $post_type, FEATURE_NAME );
		}
	);
}

/**
 * Restrict the CSV filename.
 *
 * All filenames are lower-kebab-case and end in csv.
 *
 * @param string $filename Raw filename.
 * @return string Sanitized filename.
 */
function sanitize_filename( string $filename ) : string {
	$filename = preg_replace( '/(\.csv)?$/', '', trim( strtolower( $filename ) ) );
	$filename = preg_replace( '/[^a-z0-9_]+/', '-', $filename );
	$filename = preg_replace( '/(^-|-$)/', '', $filename );
	$filename = "$filename.csv";
	return $filename;
}

/**
 * Convert a CSV dataset to a JSON object.
 *
 * @todo Is there a more robust existing solution to this problem?
 *
 * @param string $csv       String CSV content.
 * @param int    $row_limit Maximum number of rows to return.
 * @return array Array of field details.
 */
function csv_to_json( string $csv, ?int $row_limit = null ) : array {
	/** @var array rows -- fix in-editor type hinting. */
	$rows = array_map(
		'str_getcsv',
		array_slice(
			array_filter( explode( "\n", $csv ) ),
			0,
			is_int( $row_limit ) ? $row_limit + 1 : null
		)
	);
	$headers = $rows[0];
	$data = array_slice( $rows, 1 );
	return array_reduce(
		$data,
		function( $carry, $row ) use ( $headers ) {
			$object = [];
			foreach ( $headers as $idx => $field ) {
				$value = $row[ $idx ];
				$object[ $field ] = is_numeric( $value ) ? floatval( $value ) : $value;
			}
			$carry[] = $object;
			return $carry;
		},
		[]
	);
}

/**
 * Roughly infer the field types of an array of JSON objects given its first row of data.
 *
 * @param array|string $data Data, as CSV string or JSON array.
 * @return array
 */
function infer_field_types( $data ) : array {
	if ( is_string( $data ) ) {
		$data = csv_to_json( $data, 1 );
	}

	$fields = [];
	foreach ( $data[0] ?? [] as $field => $value ) {
		$field = [
			'field' => $field,
		];
		// Roughly infer whether this is NOT a nominal field. (Nominal is the default.)
		// See https://vega.github.io/vega-lite/docs/type.html for more details.
		if ( is_numeric( $value ) || is_numeric( preg_replace( '/%$/', '', $value ) ) ) {
			$field['type'] = 'quantitative';
		} else if ( strtotime( $value ) !== false ) {
			$field['type'] = 'temporal';
		}
		$fields[] = $field;
	}
	return $fields;
}
