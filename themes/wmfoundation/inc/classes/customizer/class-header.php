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
class Header extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$header_section = $this->customize->get_section( 'header_image' );

		$header_image_title = $header_section->title;

		$header_section->title = __( 'Header', 'wmfoundation' );

		$this->customize->add_panel( 'header_image', (array) $header_section );

		$header_section->panel = 'header_image';
		$header_section->title = $header_image_title;

		$section_id = 'header_image';
		$control_id = 'wmf_mobile_logo';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Image_Control(
				$this->customize, $control_id, array(
					'label'   => __( 'Mobile Logo', 'wmfoundation' ),
					'section' => $section_id,
				)
			)
		);

		$section_id = 'wmf_header_content';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Content', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => 'header_image',
			)
		);

		$control_id = 'wmf_search_button_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search Button Copy', 'wmfoundation' ),
				'description' => __( 'This changes the search button copy. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_placeholder_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Enter search terms', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search Placeholder Copy', 'wmfoundation' ),
				'description' => __( 'This changes the search placeholder copy. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_donate_now_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Donate Now', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Donate button Copy', 'wmfoundation' ),
				'description' => __( 'This changes the donate copy. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_donate_now_uri';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '#', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Donate button URI', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_menu_button_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'MENU', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Menu button copy', 'wmfoundation' ),
				'description' => __( 'This changes the button copy for mobile devices. This can be set in each translation to localize the button.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
