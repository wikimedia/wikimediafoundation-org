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
class Social extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$section_id = 'wmf_social';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Social', 'wmfoundation' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_twitter_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Twitter URI', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_facebook_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Facebook URI', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_instagram_url';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Instagrams URI', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
	}

}
