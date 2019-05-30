<?php
/**
 * Edit Contributor role.
 *
 * @package shiro
 */

namespace WMF\Roles;

/**
 * Adds create_posts cap to contributor.
 */
class Contributor extends Base {
	/**
	 * Role ID.
	 *
	 * @var string
	 */
	public $role = 'contributor';

	/**
	 * Sets the role data property.
	 */
	public function set_role_data() {
		$this->role_data = array(
			'add_caps' => array(
				'create_posts',
			),
		);
	}
}
