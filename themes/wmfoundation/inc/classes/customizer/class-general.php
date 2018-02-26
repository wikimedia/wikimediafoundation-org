<?php
/**
 * General Customizer.
 *
 * @package wmfoundation
 */

namespace WMF\Customizer;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for the contact details
 */
class General extends Base {

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$panel_id = 'wmf_general';
		$this->customize->add_panel(
			$panel_id, array(
				'title'    => __( 'General', 'wmfoundation' ),
				'priority' => 70,
			)
		);

		// Headings.
		$section_id = 'wmf_general_labels';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Labels & Headings', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_featured_post_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'NEWS', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Featured Post Pre Heading', 'wmfoundation' ),
				'description' => __( 'Shows above featured posts module in landing page and the home page.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_off_site_links_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'ELSEWHERE IN WIKIMEDIA', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Off Site Links Pre Heading', 'wmfoundation' ),
				'description' => __( 'Shows above off site links module throughout the site.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
