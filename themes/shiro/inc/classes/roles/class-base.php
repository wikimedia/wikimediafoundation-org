<?php
/**
 * Adds translation roles.
 *
 * @package shiro
 */

namespace WMF\Roles;

/**
 * Adds translation role for Translator.
 */
class Base {

	/**
	 * Role versioning. Uses float versioning instead of semantic.
	 *
	 * @var float
	 */
	private $version = 0.4;

	/**
	 * The option key for role version control.
	 *
	 * @var string
	 */
	private $version_option = 'wmf_version_option';

	/**
	 * Role ID.
	 *
	 * @var string
	 */
	public $role = '';

	/**
	 * Details about the role.
	 *
	 * @var array
	 */
	public $role_data = array();

	/**
	 * The WP roles object.
	 *
	 * @var \WP_Roles
	 */
	public static $wp_roles;

	/**
	 * Callback function that starts the engine.
	 */
	public static function callback() {
		$base = new static();

		$base->maybe_update_roles();
	}

	/**
	 * Set the static::$wp_roles property.
	 */
	public function __construct() {
		if ( ! empty( static::$wp_roles ) ) {
			return;
		}

		global $wp_roles;

		if ( empty( $wp_roles ) ) {
			$wp_roles = new \WP_Roles(); // WPCS: override ok.
		}

		static::$wp_roles = $wp_roles;
	}

	/**
	 * Adds custom caps to the pages post type.
	 *
	 * @param array  $args Post type args.
	 * @param string $name Post type name.
	 *
	 * @return array
	 */
	public static function post_type_args_filter( $args, $name ) {
		if ( empty( $args['capabilities']['create_posts'] ) || false !== strpos( $args['capabilities']['create_posts'], 'edit' ) ) {
			switch ( $name ) {
				case 'page':
					$cap = 'create_pages';
					break;
				case 'guest-author':
					return $args;
				default:
					$cap = 'create_posts';
					break;
			}

			$args['capabilities'] = array(
				'create_posts' => $cap,
			);
		}

		return $args;
	}

	/**
	 * Sets the role data property.
	 */
	public function set_role_data() {}

	/**
	 * Checks the role version against the option and updates roles conditionally.
	 */
	public function maybe_update_roles() {
		if ( $this->version <= (float) get_option( $this->version_option ) ) {
			return;
		}

		update_option( $this->version_option, $this->version );

		foreach ( glob( dirname( __FILE__ ) . '/*.php' ) as $file ) {
			if ( __FILE__ === $file ) {
				continue; // we don't need this file.
			}

			$class      = ucfirst( str_replace( array( 'class-', '.php' ), '', basename( $file ) ) );
			$class_name = __NAMESPACE__ . '\\' . $class;

			if ( class_exists( $class_name ) && is_a( $class_name, __NAMESPACE__ . '\BASE', true ) ) {
				/**
				 * Object will be type of Base.
				 *
				 * @var Base $object
				 */
				$object = new $class_name();
				$object->set_role_data();
				$object->update_role();
			}
		}
	}

	/**
	 * Updates the role.
	 */
	public function update_role() {
		if ( ! empty( $this->role_data['name'] ) ) {
			$this->add_role();
		}

		$this->add_caps();
		$this->remove_caps();
	}

	/**
	 * Adds new role.
	 */
	public function add_role() {
		$clone = empty( $this->role_data['clone'] ) ? false : get_role( $this->role_data['clone'] );
		$caps  = empty( $this->role_data['caps'] ) ? array() : $this->role_data['caps'];

		if ( false !== $clone && is_a( $clone, 'WP_Role' ) ) {
			$caps = $clone->capabilities;
		}

		wpcom_vip_add_role( $this->role, $this->role_data['name'], $caps );
	}

	/**
	 * Adds new caps to a role.
	 */
	public function add_caps() {
		if ( empty( $this->role_data['add_caps'] ) ) {
			return;
		}

		foreach ( $this->role_data['add_caps'] as $cap ) {
			static::$wp_roles->add_cap( $this->role, $cap );
		}
	}

	/**
	 * Removes caps from a role.
	 */
	public function remove_caps() {
		if ( empty( $this->role_data['remove_caps'] ) ) {
			return;
		}

		foreach ( $this->role_data['remove_caps'] as $cap ) {
			static::$wp_roles->remove_cap( $this->role, $cap );
		}
	}
}
