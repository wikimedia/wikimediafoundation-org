<?php
/**
 * Post Customizer.
 *
 * @package wmfoundation
 */

namespace WMF\Customizer;

use WP_Customize_Image_Control;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for general post settings
 */
class Post extends Base {

	/**
	 * Add Customizer fields for general pages.
	 */
	public function setup_fields() {
		$section_id = 'wmf_post_content';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Post Settings', 'wmfoundation' ),
				'priority' => 60,
			)
		);

		$control_id = 'wmf_posts_container_image';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new WP_Customize_Image_Control(
				$this->customize,
				$control_id,
				array(
					'label'    => __( 'Posts Page Frame Image', 'wmfoundation' ),
					'section'  => $section_id,
					'settings' => $control_id,
				)
			)
		);

		$control_id = 'wmf_related_posts_title';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Related', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Related Posts Section Title', 'wmfoundation' ),
				'description' => __( 'This displays at the bottom of each single post.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_related_posts_description';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Read further in the pursuit of knowledge', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Related Posts Section Description', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);
	}

}
