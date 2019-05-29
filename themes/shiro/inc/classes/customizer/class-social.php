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
class Social extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$section_id = 'wmf_social';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Social', 'shiro' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_social_follow_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Follow', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Follow Text', 'shiro' ),
				'description' => __( 'This is used above follow links in multiple modules.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_social_share_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Share', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Share Text', 'shiro' ),
				'description' => __( 'This is used above share links in multiple modules.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_twitter_id';
		$this->customize->add_setting(
			$control_id, array(
				'default' => 'Wikimedia',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Twitter @', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_twitter_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Twitter URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_facebook_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Facebook', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Facebook Label', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_facebook_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Facebook URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_instagram_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Instagram', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Instagram Label', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_instagram_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Instagram URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_blog_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Wikimedia Blog', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Blog Label', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
		$control_id = 'wmf_blog_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Blog URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
	}

}
