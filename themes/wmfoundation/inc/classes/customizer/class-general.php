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

		// RTL.
		$section_id = 'wmf_general_rtl';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'RTL Support', 'wmfoundation' ),
				'priority' => 10,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_enable_rtl';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Enable RTL', 'wmfoundation' ),
				'description' => __( 'If checked, this will cause the front end of site to shift from left to right to right to left display.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'checkbox',
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

		if ( ! wmf_is_main_site() ) {
			$control_id = 'wmf_incomplete_translation';
			$this->customize->add_setting(
				$control_id, array(
					'default' => __( 'This content has not yet been translated into the current language.', 'wmfoundation' ),
				)
			);
			$this->customize->add_control(
				$control_id, array(
					'label'       => __( 'Incomplete Translation Notice', 'wmfoundation' ),
					'description' => __( 'Shows in the header if the content has not been marked as having a complete translation.', 'wmfoundation' ),
					'section'     => $section_id,
					'type'        => 'text',
				)
			);
		}

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

		$control_id = 'wmf_projects_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Projects', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Projects Pre Heading', 'wmfoundation' ),
				'description' => __( 'Shows above projects module in landing page and the home page.', 'wmfoundation' ),
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

		$control_id = 'wmf_related_pages_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Related', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Related Pages Pre Heading', 'wmfoundation' ),
				'description' => __( 'Shows above related pages module throughout the site.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_image_credit_header';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Photo credits', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Photo Credits Heading', 'wmfoundation' ),
				'description' => __( 'Shows above photo credits module throughout the site.', 'wmfoundation' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		// Support Module.
		$section_id = 'wmf_general_support_module';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Support Module', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_support_image';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Media_Control(
				$this->customize, $control_id, array(
					'label'       => __( 'Image', 'wmfoundation' ),
					'description' => __( 'Image should be 16:9 aspect ratio with min width of 1200px for best appearance. The image will automatically crop to that size if larger.', 'wmfoundation' ),
					'section'     => $section_id,
				)
			)
		);

		$control_id = 'wmf_support_heading';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Heading', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_support_content';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Content', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		$control_id = 'wmf_support_link_uri';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'CTA Link URI', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_support_link_text';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'CTA Link Text', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		// Search Page.
        $section_id = 'wmf_search_page';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Search Page', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);
        
        $control_id = 'wmf_search_results_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search results for %s', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Search results message', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_search_sidebar_type';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Result Type', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header for result type', 'wmfoundation' ),
				'description' => __( 'Header for result type in search results sidebar.', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_search_sidebar_sortby';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Sort By', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header for sort by', 'wmfoundation' ),
				'description' => __( 'Header for Sort By field in search results sidebar.', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_search_sidebar_sort_des';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Title (descending)', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Sort by title (descending)', 'wmfoundation' ),
				'description' => __( 'Text for Title (descending) option in search results sidebar sort by field.', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_search_sidebar_sort_asc';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Title (ascending)', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Sort by title (ascending)', 'wmfoundation' ),
				'description' => __( 'Text for Title (ascending) option in search results sidebar sort by field.', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_search_sidebar_sort_new';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Newest', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Sort by newest', 'wmfoundation' ),
				'description' => __( 'Text for Newest option in search results sidebar sort by field.', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_search_sidebar_sort_old';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Oldest', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Sort by oldest', 'wmfoundation' ),
				'description' => __( 'Text for Oldest option in search results sidebar sort by field.', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_search_sidebar_submit';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Submit', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Submit button', 'wmfoundation' ),
				'description' => __( 'Text for submit button in search results sidebar.', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        
		// 404 Page.
		$section_id = 'wmf_404_page';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( '404 Page', 'wmfoundation' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_404_image';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Media_Control(
				$this->customize, $control_id, array(
					'label'       => __( 'Background Image', 'wmfoundation' ),
					'description' => __( 'Displayed in header.', 'wmfoundation' ),
					'section'     => $section_id,
				)
			)
		);

		$control_id = 'wmf_404_message';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '404 Error', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header message', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_404_title';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Imagine a world in which there is a page here', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header title', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_404_copy';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new Rich_Text_Control(
				$this->customize,
				$control_id,
				array(
					'label'   => __( 'Content', 'wmfoundation' ),
					'section' => $section_id,
				)
			)
		);

		$control_id = 'wmf_404_search_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Or try a search instead', 'wmfoundation' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Text above search bar', 'wmfoundation' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
	}

}
