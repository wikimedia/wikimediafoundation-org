<?php
/**
 * Page Customizer.
 *
 * @package wmfoundation
 */

namespace WMF\Customizer;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for general page settings
 */
class Page extends Base {

	/**
	 * Add Customizer fields for general pages.
	 */
	public function setup_fields() {
		$section_id = 'wmf_page_content';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Page Settings', 'wmfoundation' ),
				'priority' => 60,
			)
		);

		$control_id = 'wmf_no_results_title';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Nothing Found', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'No Results Title', 'wmfoundation' ),
				'description' => __( 'This displays on archive and search pages when there are no results found.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_no_results_description';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Sorry, but no results were found. Perhaps searching can help.', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'No Results Description', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		$control_id = 'wmf_pagination_newer';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Newer', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Newer Posts Copy', 'wmfoundation' ),
				'description' => __( 'This displays in pagination sections in a link for newer posts.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_pagination_older';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Older', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Older Posts Copy', 'wmfoundation' ),
				'description' => __( 'This displays in pagination sections in a link for older posts.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);
	}

}
