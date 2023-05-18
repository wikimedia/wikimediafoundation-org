<?php
/**
 * Theme Customizer.
 *
 * @package shiro
 */

namespace WMF\Customizer;

use WP_Query;

/**
 * Setups the customizer and related settings.
 */
abstract class Base {

	/**
	 * Holds customizer instance
	 *
	 * @var \WP_Customize_Manager $wp_customize Theme Customizer object.
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
	 * Gets the array of choices for the page select.
	 *
	 * @return array the choices.
	 */
	public function page_choices() {
		$choices = array();

		$posts = new WP_Query(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 1000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
			)
		);

		foreach ( $posts->posts as $post_choice ) {
			if ( ! empty( $post_choice->post_title ) ) {
				$page_relative_link = wp_make_link_relative( get_permalink( $post_choice->ID ) );
				$choices[ $post_choice->ID ] = $post_choice->post_title . ' (ID ' . $post_choice->ID . ' - ' . $page_relative_link . ')';
			}
		}

		return $choices;
	}

	/**
	 * Add customizer fields.
	 */
	abstract public function setup_fields();
}

/**
 * Instantiates and loads the various customizer classes.
 */
function load_customizer_classes() {
	// Add customizer class name to list to instantiate.
	$customizers = array(
		'Identity',
		'Connect',
		'General',
		'Header',
		'Footer',
		'Social',
		'Profile',
		'Page',
		'Post',
	);

	foreach ( $customizers as $customizer ) {
		$class          = __NAMESPACE__ . '\\' . $customizer;
		$customizer_obj = new $class();
		$customizer_obj->run();
	}
}
add_action( 'init', '\\' . __NAMESPACE__ . '\load_customizer_classes' );
