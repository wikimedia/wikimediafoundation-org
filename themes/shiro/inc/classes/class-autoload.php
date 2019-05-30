<?php
/**
 * Autoload the classes.
 *
 * @package shiro
 */

namespace WMF;

/**
 * Handles autoloading for the WMF namespace.
 */
class Autoload {

	/**
	 * Path starting from this file.
	 *
	 * @var string
	 */
	public $path_base;

	/**
	 * The class being autoloaded.
	 *
	 * @var string
	 */
	public $class;

	/**
	 * The constructed path to the class file.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Autoload constructor.
	 *
	 * Sets the $path_base variable.
	 */
	public function __construct() {
		$this->path_base = dirname( __FILE__ );
	}

	/**
	 * Callback for the spl_autoload_register function.
	 *
	 * @param string $class The class being checked.
	 */
	public function callback( $class ) {
		if ( false === strpos( $class, __NAMESPACE__ ) ) {
			return; // It's not in our namespace so ignore it.
		}
		$this->class = trim( str_replace( __NAMESPACE__, '', $class ), '\\' );

		if ( ! $this->verify_path() ) {
			return; // The path cannot be verified.
		}

		$this->require_file();
	}

	/**
	 * Verifies the file exists and is a valid file path.
	 *
	 * @return bool
	 */
	public function verify_path() {
		$this->set_path();

		if ( file_exists( $this->path ) && 0 === validate_file( $this->path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Builds the path from the namespace parts.
	 */
	public function set_path() {
		$path_parts = explode( '\\', $this->class );

		$this->path = trailingslashit( $this->path_base );

		$parts_count = count( $path_parts );

		foreach ( $path_parts as $part ) {
			$parts_count--;

			$part = strtolower( str_replace( '_', '-', $part ) );

			$this->path .= 0 === $parts_count ? sprintf( 'class-%s.php', $part ) : sprintf( '%s/', $part );
		}
	}

	/**
	 * Requires the path.
	 */
	public function require_file() {
		require $this->path;
	}
}

$wmf_autoload = new Autoload();

spl_autoload_register( array( $wmf_autoload, 'callback' ) );
