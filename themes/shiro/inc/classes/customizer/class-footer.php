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
	 * Get the default text for a field defined for the Footer customizer.
	 *
	 * These are defined here because the Customizer values are not saved, and
	 * are needed in get_theme_mod() calls in templates. This allows for a
	 * centralized location for this values and reduces duplication.
	 *
	 * @param string $setting
	 *
	 * @return string
	 */
	public static function defaults( string $setting = '' ): string {
		$defaults = [
			'wmf_footer_text'                    => __( 'The Wikimedia Foundation, Inc is a nonprofit charitable organization dedicated to encouraging the growth, development and distribution of free, multilingual content, and to providing the full content of these wiki-based projects to the public free of charge.',
				'shiro-admin' ),
			'wmf_projects_menu_label'            => __( 'Projects', 'shiro-admin' ),
			'wmf_movement_affiliates_menu_label' => __( 'Movement Affiliates', 'shiro-admin' ),
			'wmf_other_links_menu_label'         => __( 'Other', 'shiro-admin' ),
			'wmf_footer_copyright'               => __( 'This work is licensed under a <a href="https://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0</a> unported license. Some images under <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC BY-SA</a>.',
				'shiro-admin' ),
		];

		return $defaults[ $setting ] ?? '';
	}

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$section_id = 'wmf_footer';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Footer', 'shiro-admin' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_footer_logo';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Image_Control(
				$this->customize, $control_id, array(
					'label'   => __( 'Footer Logo', 'shiro-admin' ),
					'section' => $section_id,
				)
			)
		);

		$control_id = 'wmf_footer_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => $this::defaults( 'wmf_footer_text' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Footer Text', 'shiro-admin' ),
				'description' => __( 'This changes the large text to the right of the footer logo. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_projects_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => $this::defaults( 'projects_menu_label' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Projects Menu Label', 'shiro-admin' ),
				'description' => __( 'Label above the 3 column projects menu. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_movement_affiliates_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => $this::defaults( 'wmf_movement_affiliates_menu_label' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Movement Affilaites Menu Label', 'shiro-admin' ),
				'description' => __( 'Label above the 1 column movement affiliates menu. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_other_links_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => $this::defaults( 'wmf_other_links_menu_label' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Other Links Menu Label', 'shiro-admin' ),
				'description' => __( 'Label above the 1 column other links menu. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_footer_copyright';
		$this->customize->add_setting(
			$control_id, array(
				'default' => $this::defaults( 'wmf_footer_copyright' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Copyright', 'shiro-admin' ),
				'description' => __( 'The copyright statement at the bottom of the page. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
