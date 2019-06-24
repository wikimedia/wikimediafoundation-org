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
class Header extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$header_section = $this->customize->get_section( 'header_image' );

		$header_image_title = $header_section->title;

		$header_section->title = __( 'Header', 'shiro' );

		$this->customize->add_panel( 'header_image', (array) $header_section );

		$header_section->panel = 'header_image';
		$header_section->title = $header_image_title;

		$section_id = 'wmf_header_content';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Content', 'shiro' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_selected_translation_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Languages', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Languages Translations Copy', 'shiro' ),
				'description' => __( 'This changes the languages label copy found in the translation bar at the top of the page.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_button_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search Button Copy', 'shiro' ),
				'description' => __( 'This changes the search button copy. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_placeholder_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'What are you looking for?', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search Placeholder Copy', 'shiro' ),
				'description' => __( 'This changes the search placeholder copy. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_aria_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search Wikimedia Foundation site', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search input label for assistive technology', 'wmfoundation' ),
				'description' => __( 'This changes the search input label exposed only to assistive technology. This can be set in each translation to localize the label.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_donate_now_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Donate Now', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Donate button Copy', 'shiro' ),
				'description' => __( 'This changes the donate copy. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_donate_now_uri';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '#', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Donate button URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_menu_button_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'MENU', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Menu button copy', 'shiro' ),
				'description' => __( 'This changes the button copy for mobile devices. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang1';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Imagine a world in which every single human being can freely share in the sum of all knowledge.', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision Language 1', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang2';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision Language 2', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang3';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision Language 3', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang4';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision Language 4', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang5';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision Language 5', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);
	}

}
