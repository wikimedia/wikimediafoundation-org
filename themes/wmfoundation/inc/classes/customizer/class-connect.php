<?php
/**
 * Contact Customizer.
 *
 * @package wmfoundation
 */

namespace WMF\Customizer;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for the contact details
 */
class Connect extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$panel_id = 'wmf_connect';
		$this->customize->add_panel(
			$panel_id, array(
				'title'    => __( 'Connect', 'wmfoundation' ),
				'priority' => 70,
			)
		);

		// Headings.
		$section_id = 'wmf_contact_headings';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Main Headings', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_connect_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Connect', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Pre Heading', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_connect_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Stay up-to-date on our work.', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Heading', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		// Subscribe Box.
		$section_id = 'wmf_contact_subscribe';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Subscribe Box', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_subscribe_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Subscribe to our newsletter', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Heading', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_subscribe_content';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Content', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		$control_id = 'wmf_subscribe_placeholder';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Email address', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Email Placeholder', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_subscribe_button';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Subscribe', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Button Text', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		// Contact box.
		$section_id = 'wmf_contact_contact';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Contact Box', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_contact_heading';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Heading', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_contact_content';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Content', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		$control_id = 'wmf_contact_link';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Link', 'wmfoundation' ),
				'description' => __( 'This can be a URI or email address.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_contact_link_text';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Link Text', 'wmfoundation' ),
				'description' => __( 'If this is empty, the Link value will be used automatically.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
