<?php
/**
 * Adds translation roles.
 *
 * @package wmfoundation
 */

namespace WMF\Roles;

/**
 * Adds translation role for Translator.
 */
abstract class Base {

	/**
	 * Role versioning. Uses float versioning instead of semantic.
	 *
	 * @var float
	 */
	public static $version = 1.0;

	/**
	 * The option key for role version control.
	 *
	 * @var string
	 */
	public static $version_option = 'wmf_version_option';
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
	 * The object instance.
	 *
	 * @var Base
	 */
	public static $instance;

	/**
	 * The WP roles object.
	 *
	 * @var \WP_Roles
	 */
	public static $wp_roles;

	/**
	 * Gets the current instance of the object.
	 *
	 * @return Base
	 */
	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Checks the role version against the option and updates roles conditionally.
	 */
	public static function callback() {
		if ( static::$version <= (float) get_option( static::$version_option ) ) {
			return;
		}

		update_option( static::$version_option, static::$version );

		static::get_instance()->update_role();
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
			$wp_roles = new \WP_Roles(); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		}

		static::$wp_roles = $wp_roles;
	}

	/**
	 * Updates the role.
	 */
	public function update_role() {
		if ( ! empty( $this->role_data['new_role'] ) ) {
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
