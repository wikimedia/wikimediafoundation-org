<?php
/**
 * Page Customizer.
 *
 * @package shiro
 */

namespace WMF\Customizer;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for general page settings
 */
class Page extends Base {

	/**
	 * Add Customizer fields for general pages.
	 */
	public function setup_fields() {
		$section_id = 'wmf_page_content';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Page Settings', 'shiro-admin' ),
				'priority' => 60,
			)
		);

		$control_id = 'wmf_pagination_newer';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Newer', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Newer Posts Copy', 'shiro-admin' ),
				'description' => __( 'This displays in pagination sections in a link for newer posts.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_pagination_older';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Older', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Older Posts Copy', 'shiro-admin' ),
				'description' => __( 'This displays in pagination sections in a link for older posts.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_downloads_header';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Downloads', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Downloads Section Header', 'shiro-admin' ),
				'description' => __( 'This displays in the sidebar before the downloads list.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
