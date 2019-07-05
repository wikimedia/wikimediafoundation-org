<?php
/**
 * Reaktiv Template Data
 *
 * This is a temporary data store for passing data into a template partial.
 *
 * @package shiro
 */

/**
 * Template data store class
 */
class Wmf_Template_Data {

	/**
	 * Instances of the data store.
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * Data.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * This instance's partial path.
	 *
	 * @var string
	 */
	public static $path = '';

	/**
	 * The constructor.
	 *
	 * @param string $path The path to the template partial.
	 */
	protected function __construct( $path ) {
		static::$path = $path;
	}

	/**
	 * Get an instance of the class. Each instance is keyed by the
	 * template partial path.
	 *
	 * @param strin $path The path to the template partial.
	 *
	 * @return Wmf_Template_Data
	 */
	public static function get_instance( $path ) {
		if ( empty( static::$instances[ $path ] ) ) {
			static::$instances[ $path ] = new Wmf_Template_Data( $path );
		}

		return static::$instances[ $path ];
	}

	/**
	 * Set the data.
	 *
	 * @param array $args The data to set.
	 */
	public function set_data( $args ) {
		$this->data = $args;
	}

	/**
	 * Get the data.
	 *
	 * @param string $key Key of a specific data element.
	 *
	 * @return array
	 */
	public function get_data( $key = '' ) {
		if ( '' !== $key ) {
			if ( ! empty( $this->data[ $key ] ) ) {
				return $this->data[ $key ];
			} else {
				return null;
			}
		} else {
			return $this->data;
		}
	}

	/**
	 * Destroy this instance of the data store once it has been used.
	 */
	public function destroy() {
		unset( static::$instances[ static::$path ] );
	}
}

/**
 * Load the template path and pass data through Wmf_Template_Data.
 *
 * @param string $slug The path to the partial.
 * @param array  $args The data to pass to the partial.
 * @param string $name The name extension of the partial.
 */
function wmf_get_template_part( $slug, $args, $name = '' ) {
	// Get the path.
	$path = $slug;
	if ( '' !== $name ) {
		// Add $name to the path if it is not an empty string.
		$path = "{$slug}-{$name}";
	}

	// Save the last path to be reset after the current path is used.
	$last_path = Wmf_Template_Data::$path;

	// Get an instance of the data store with the key $path.
	$temp_data_store = Wmf_Template_Data::get_instance( $path );

	// Set the data in the data store.
	$temp_data_store->set_data( $args );

	// Load the template part.
	get_template_part( $slug, $name );

	// Destroy the data store now that we're done with it.
	$temp_data_store->destroy();

	// Reset the last path in the data store.
	Wmf_Template_Data::$path = $last_path;
}

/**
 * Get data from the template data store.
 *
 * @param array $args Array of arguments for retrieving the data. Could include path, slug, name, or key.
 *                    If slug and name are included, they are combined to form the path. The key is the key
 *                    of the data item to retrieve.
 *
 * @return mixed
 */
function wmf_get_template_data( $args = array() ) {
	// Get the path to use.
	$path = '';
	if ( empty( $args['path'] ) ) {
		if ( ! empty( $args['slug'] ) ) {
			// Combine the slug and name args into a path.
			$path = $args['slug'] . ( empty( $args['name'] ) ? '' : '-' . $args['name'] );
		}

		if ( '' === $path ) {
			// Set the path name to the last active path in Wmf_Template_Data if none is set.
			$path = Wmf_Template_Data::$path;
		}
	}

	// Get an instance of the data store.
	$data_store = Wmf_Template_Data::get_instance( $path );

	// Get the key to use.
	$key = ! empty( $args['key'] ) ? $args['key'] : '';

	// Return the $data array from the data store or a spcific data item if $key is passed in.
	return $data_store->get_data( $key );
}
