<?php
/**
 * Header Customizer.
 *
 * @package wmfoundation
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
				'title'    => __( 'Footer', 'wmfoundation' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_footer_logo';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Image_Control(
				$this->customize, $control_id, array(
					'label'   => __( 'Footer Logo', 'wmfoundation' ),
					'section' => $section_id,
				)
			)
		);

		$control_id = 'wmf_footer_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'The Wikimedia Foundation, Inc is a nonprofit charitable organization dedicated to encouraging the growth, development and distribution of free, multilingual content, and to providing the full content of these wiki-based projects to the public free of charge.', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Footer Text', 'wmfoundation' ),
				'description' => __( 'This changes the large text to the right of the footer logo. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_projects_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Projects', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Projects Menu Label', 'wmfoundation' ),
				'description' => __( 'Label above the 3 column projects menu. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_movement_affiliates_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Movement Affiliates', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Movement Affilaites Menu Label', 'wmfoundation' ),
				'description' => __( 'Label above the 1 column movement affiliates menu. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_footer_copyright';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'This work is licensed under a <a href="#">Creative Commons Attribution 3.0</a> unported license. Some images under <a href="#">CC BY-SA</a>.', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Copyright', 'wmfoundation' ),
				'description' => __( 'The copyright statement at the bottom of the page. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
