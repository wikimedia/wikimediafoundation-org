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

		$section_id = 'wmf_header_general';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'General', 'shiro' ),
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
        
		$section_id = 'wmf_header_donate';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Donate', 'shiro' ),
				'priority' => 70,
				'panel'    => 'header_image',
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
				'label'       => __( 'Navigation donate button Copy', 'shiro' ),
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
				'label'   => __( 'Navigation donate button URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_homedonate_button';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Donate now', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Homepage donate button copy', 'shiro' ),
				'description' => __( 'This changes the homepage donate button copy. This can be set in each translation to localize the button.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_homedonate_uri';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '#', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Homepage bonate button URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_homedonate_intro';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Protect and sustain Wikipedia', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Homepage bonate button intro', 'shiro' ),
				'description' => __( 'This changes the homepage donate button intro copy.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
        
        $control_id = 'wmf_homedonate_secure';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'SECURE DONATIONS', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Homepage bonate button secure copy', 'shiro' ),
				'description' => __( 'This changes the homepage donate button secure notice copy.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
        
		$section_id = 'wmf_header_search';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Search', 'shiro' ),
				'priority' => 70,
				'panel'    => 'header_image',
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

		$control_id = 'wmf_search_esc_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'esc', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Label for escape button in search popup', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$section_id = 'wmf_header_vision';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Vision', 'shiro' ),
				'priority' => 70,
				'panel'    => 'header_image',
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

		$control_id = 'wmf_vision_lang1_rtl';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'CSS class', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
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

		$control_id = 'wmf_vision_lang2_rtl';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'CSS class', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
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

		$control_id = 'wmf_vision_lang3_rtl';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'CSS class', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
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

		$control_id = 'wmf_vision_lang4_rtl';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'CSS class', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
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

		$control_id = 'wmf_vision_lang5_rtl';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'CSS class', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
