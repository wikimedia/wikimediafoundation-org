<?php
/**
 * Edit Editor role.
 *
 * @package shiro
 */

namespace WMF\Roles;

/**
 * Adds create_posts and create_pages caps to editor.
 */
class Editor extends Base {
	/**
	 * Role ID.
	 *
	 * @var string
	 */
	public $role = 'editor';

	/**
	 * Sets the role data property.
	 */
	public function set_role_data() {
		$this->role_data = array(
			'add_caps' => array(
				'create_posts',
				'create_pages',
			),
		);
	}
}
