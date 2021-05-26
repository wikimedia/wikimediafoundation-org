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

		$header_section->title = __( 'Header', 'shiro-admin' );

		$this->customize->add_panel( 'header_image', (array) $header_section );

		$header_section->panel = 'header_image';
		$header_section->title = $header_image_title;

		$section_id = 'wmf_alt_header_image';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Alternative header image', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_alt_header_image_url';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'URL to alternative header image', 'shiro-admin' ),
				'description' => __( 'URL will be applied as inline style background image', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$section_id = 'wmf_header_general';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'General', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_selected_translation_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Languages', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Languages Translations Copy', 'shiro-admin' ),
				'description' => __( 'This changes the languages label copy found in the translation bar at the top of the page.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_menu_button_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'MENU', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Menu button copy', 'shiro-admin' ),
				'description' => __( 'This changes the button copy for mobile devices. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$section_id = 'wmf_header_link';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Header link', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_header_link_href';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Link, clickable on whole header image', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_header_link_aria_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Aria label for link', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$section_id = 'wmf_header_donate';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Donate', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_donate_now_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Donate Now', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Navigation donate button Copy', 'shiro-admin' ),
				'description' => __( 'This changes the donate copy. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_donate_now_uri';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '#', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Navigation donate button URI', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

        $control_id = 'wmf_homedonate_button';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Donate now', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Homepage donate button copy', 'shiro-admin' ),
				'description' => __( 'This changes the homepage donate button copy. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_homedonate_uri';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '#', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Homepage bonate button URI', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

        $control_id = 'wmf_homedonate_intro';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Protect and sustain Wikipedia', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Homepage bonate button intro', 'shiro-admin' ),
				'description' => __( 'This changes the homepage donate button intro copy.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

        $control_id = 'wmf_homedonate_secure';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'SECURE DONATIONS', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Homepage bonate button secure copy', 'shiro-admin' ),
				'description' => __( 'This changes the homepage donate button secure notice copy.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$section_id = 'wmf_header_search';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Search', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_search_button_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search Button Copy', 'shiro-admin' ),
				'description' => __( 'This changes the search button copy. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_placeholder_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'What are you looking for?', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search Placeholder Copy', 'shiro-admin' ),
				'description' => __( 'This changes the search placeholder copy. This can be set in each translation to localize the button.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_esc_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'esc', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Label for escape button in search popup', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$section_id = 'wmf_header_vision';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Vision', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_vision_lang1';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Imagine a world in which every single human being can freely share in the sum of all knowledge.', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 1', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang1_class';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 1 - CSS class', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang1_langcode';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 1 - language code', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang2';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 2', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang2_class';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 2 - CSS class', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang2_langcode';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 2 - language code', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang3';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 3', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang3_class';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 3 - CSS class', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang3_langcode';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 3 - language code', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang4';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 4', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang4_class';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 4 - CSS class', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang4_langcode';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 4 - language code', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang5';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 5', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_vision_lang5_class';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 5 - CSS class', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_vision_lang5_langcode';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Vision language 5 - language code', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$section_id = 'wmf_emergency_messages';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Emergency message', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_emergency_message';
		$this->customize->add_setting(
			$control_id, array(
				'default' => '',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Emergency message for display on homepage', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);
	}

}
