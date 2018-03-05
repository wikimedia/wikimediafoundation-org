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
class Translator extends Base {
	/**
	 * Role ID.
	 *
	 * @var string
	 */
	public $role = 'translator';

	/**
	 * Sets the role data property.
	 */
	public function set_role_data() {
		$this->role_data = array(
			'name'        => __( 'Translator', 'wmfoundation' ),
			'clone'       => 'editor',
			'remove_caps' => array(
				'delete_others_pages',
				'delete_others_posts',
				'delete_pages',
				'delete_posts',
				'delete_private_pages',
				'delete_private_posts',
				'delete_published_pages',
				'delete_published_posts',
			),
		);
	}
}
