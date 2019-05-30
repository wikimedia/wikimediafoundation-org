<?php
/**
 * Header Customizer.
 *
 * @package shiro
 */

namespace WMF\Customizer;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for the header details
 */
class Footer extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$section_id = 'wmf_footer';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Footer', 'shiro' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_footer_logo';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Image_Control(
				$this->customize, $control_id, array(
					'label'   => __( 'Footer Logo', 'shiro' ),
					'section' => $section_id,
				)
			)
		);

		$control_id = 'wmf_footer_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'The Wikimedia Foundation, Inc is a nonprofit charitable organization dedicated to encouraging the growth, development and distribution of free, multilingual content, and to providing the full content of these wiki-based projects to the public free of charge.', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Footer Text', 'shiro' ),
				'description' => __( 'This changes the large text to the right of the footer logo. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_projects_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Projects', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Projects Menu Label', 'shiro' ),
				'description' => __( 'Label above the 3 column projects menu. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_movement_affiliates_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Movement Affiliates', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Movement Affilaites Menu Label', 'shiro' ),
				'description' => __( 'Label above the 1 column movement affiliates menu. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_other_links_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Other links', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Other Links Menu Label', 'shiro' ),
				'description' => __( 'Label above the 1 column other links menu. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_footer_copyright';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'This work is licensed under a <a href="https://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0</a> unported license. Some images under <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC BY-SA</a>.', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Copyright', 'shiro' ),
				'description' => __( 'The copyright statement at the bottom of the page. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
