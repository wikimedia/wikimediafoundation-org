<?php
/**
 * Profile Pages Customizer.
 *
 * @package shiro
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
				'title'    => __( 'Profile Pages', 'shiro' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_profile_parent_page';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Profiles Parent Page', 'shiro' ),
				'description' => __( 'This changes the parent link at the top of individual profile pages like Staff & Contractors.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'select',
				'choices'     => $this->page_choices(),
			)
		);

		$control_id = 'wmf_community_profile_parent_page';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Community Profiles Parent Page', 'shiro' ),
				'description' => __( 'This changes the parent link at the top of the Wikimedia Community profile.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'select',
				'choices'     => $this->page_choices(),
			)
		);

		$control_id = 'wmf_profile_archive_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'The Wikimedia Foundation is part of a broad global network of individuals, organizations, chapters, clubs and communities who together work to create the most powerful examples of volunteer collaboration and open content sharing in the world today.', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Profiles List Page Text', 'shiro' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		$control_id = 'wmf_profile_archive_button';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'We\'re Hiring', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Profiles List Page Button Label', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

        $control_id = 'wmf_profiles_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Profiles', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Label for profiles', 'shiro' ),
				'description' => __( 'This is the label used to describe the profiles post type.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_profile_archive_button_link';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Profiles List Page Button Link', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_related_profiles_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Other members of ', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Related Profiles default headline', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
	}

}
