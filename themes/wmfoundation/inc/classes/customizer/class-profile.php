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

		$control_id = 'wmf_profile_container_image';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new WP_Customize_Image_Control(
				$this->customize,
				$control_id,
				array(
					'label'    => __( 'Profile Container Image', 'wmfoundation' ),
					'section'  => 'wmf_profile_content',
					'settings' => $control_id,
				)
			)
		);
	}

}
