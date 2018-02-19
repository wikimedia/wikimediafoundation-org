<?php
/**
 * Theme Customizer.
 *
 * @package wmfoundation
 */

namespace WMF\Customizer;

/**
 * Setups the customizer and related settings.
 */
abstract class Base {

	/**
	 * Holds customizer instance
	 *
	 * @var WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	protected $customize;

	/**
	 * Run hooks related to customizer, called on init
	 */
	public function run() {
		add_action( 'customize_register', array( $this, 'setup_customizer' ) );
	}

	/**
	 * Setup the customizer and store the customizer instance.
	 *
	 * @param object $wp_customize Full WP_Customizer object.
	 */
	public function setup_customizer( $wp_customize ) {
		$this->customize = $wp_customize;
		$this->setup_fields();
	}

	/**
	 * Gets the array of choices for the tag select.
	 *
	 * @param string $taxonomy  Name of taxonomy to select from.
	 * @return array the choices.
	 */
	public function taxonomy_choices( $taxonomy = 'post_tag' ) {
		$choices = array();

		$terms = get_terms( $taxonomy );

		foreach ( $terms as $term ) {
			$choices[ $term->term_id ] = $term->name;
		}

		return $choices;
	}

	/**
	 * Add customizer fields.
	 */
	abstract public function setup_fields();
}

spl_autoload_register( __NAMESPACE__ . '\autoload' );
/**
 * Autoloader callback for customizer classes.
 *
 * @param string $class The class being checked.
 */
function autoload( $class ) {
	if ( false !== strpos( $class, __NAMESPACE__ ) ) {
		$file = strtolower( str_replace( array( __NAMESPACE__ . '\\', '_' ), array( '', '-' ), $class ) );
		$path = sprintf( '%1$sclass-%2$s.php', trailingslashit( dirname( __FILE__ ) ), $file );

		if ( file_exists( $path ) && 0 === validate_file( $path ) ) {
			require $path;
		}
	}
}

/**
 * Instantiates and loads the various customizer classes.
 */
function load_customizer_classes() {
	// Add customizer class name to list to instantiate.
	$customizers = array(
		'Header',
	);

	foreach ( $customizers as $customizer ) {
		$class          = __NAMESPACE__ . '\\' . $customizer;
		$customizer_obj = new $class();
		$customizer_obj->run();
	}
}
add_action( 'init', '\\' . __NAMESPACE__ . '\load_customizer_classes' );
