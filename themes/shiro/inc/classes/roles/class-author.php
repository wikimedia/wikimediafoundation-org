<?php
/**
 * Edit Author role.
 *
 * @package shiro
 */

namespace WMF\Roles;

/**
 * Adds create_posts cap to author.
 */
class Author extends Base {
	/**
	 * Role ID.
	 *
	 * @var string
	 */
	public $role = 'author';

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
