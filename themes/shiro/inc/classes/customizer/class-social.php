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
				'title'    => __( 'Social', 'shiro-admin' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_tweet_this_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Tweet this', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Tweet this copy', 'shiro-admin' ),
				'description' => __( 'Copy used for "Tweet this" label beneath facts.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_social_follow_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Follow', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Follow Text', 'shiro-admin' ),
				'description' => __( 'This is used above follow links in multiple modules.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_social_share_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Share', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Share Text', 'shiro-admin' ),
				'description' => __( 'This is used above share links in multiple modules.', 'shiro-admin' ),
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
				'label'   => __( 'Twitter @', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_twitter_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Twitter URI', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_facebook_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Facebook', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Facebook Label', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_facebook_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Facebook URI', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_instagram_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Instagram', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Instagram Label', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_instagram_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Instagram URI', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_blog_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Wikimedia Blog', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Blog Label', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
		$control_id = 'wmf_blog_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Blog URI', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
	}

}
