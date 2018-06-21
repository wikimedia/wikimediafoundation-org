<?php
/**
 * Profile Pages Customizer.
 *
 * @package wmfoundation
 */

namespace WMF\Customizer;

use WP_Customize_Image_Control;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for the profile single and archive
 */
class Profile extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$section_id = 'wmf_profile_content';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Profile Pages', 'wmfoundation' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_profile_parent_page';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Profiles Parent Page', 'wmfoundation' ),
				'description' => __( 'This changes the parent link at the top of individual profile pages like Staff & Contractors.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'select',
				'choices'     => $this->page_choices(),
			)
		);

		$control_id = 'wmf_profile_archive_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'The Wikimedia Foundation is part of a broad global network of individuals, organizations, chapters, clubs and communities who together work to create the most powerful examples of volunteer collaboration and open content sharing in the world today.', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Profiles List Page Text', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		$control_id = 'wmf_profile_archive_button';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'We\'re Hiring', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Profiles List Page Button Label', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_profile_archive_button_link';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Profiles List Page Button Link', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_related_profiles_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Other members of ', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Related Profiles default headline', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
	}

}
