<?php
/**
 * General Customizer.
 *
 * @package shiro
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
				'title'    => __( 'General', 'shiro' ),
				'priority' => 70,
			)
		);

		// RTL.
		$section_id = 'wmf_general_rtl';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'RTL Support', 'shiro' ),
				'priority' => 10,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_enable_rtl';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Enable RTL', 'shiro' ),
				'description' => __( 'If checked, this will cause the front end of site to shift from left to right to right to left display.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'checkbox',
			)
		);

		// Headings.
		$section_id = 'wmf_general_labels';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Labels & Headings', 'shiro' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		if ( ! wmf_is_main_site() ) {
			$control_id = 'wmf_incomplete_translation';
			$this->customize->add_setting(
				$control_id, array(
					'default' => __( 'This content has not yet been translated into the current language.', 'shiro' ),
				)
			);
			$this->customize->add_control(
				$control_id, array(
					'label'       => __( 'Incomplete Translation Notice', 'shiro' ),
					'description' => __( 'Shows in the header if the content has not been marked as having a complete translation.', 'shiro' ),
					'section'     => $section_id,
					'type'        => 'text',
				)
			);
		}

		$control_id = 'wmf_featured_post_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'NEWS', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Featured Post Pre Heading', 'shiro' ),
				'description' => __( 'Shows above featured posts module in landing page and the home page.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_projects_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Projects', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Projects Pre Heading', 'shiro' ),
				'description' => __( 'Shows above projects module in landing page and the home page.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_off_site_links_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'ELSEWHERE IN WIKIMEDIA', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Off Site Links Pre Heading', 'shiro' ),
				'description' => __( 'Shows above off site links module throughout the site.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_related_pages_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Related', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Related Pages Pre Heading', 'shiro' ),
				'description' => __( 'Shows above related pages module throughout the site.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_image_credit_header';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Photo credits', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Photo Credits Heading', 'shiro' ),
				'description' => __( 'Shows above photo credits module throughout the site.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		// Support Module.
		$section_id = 'wmf_general_support_module';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Support Module', 'shiro' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_support_image';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Media_Control(
				$this->customize, $control_id, array(
					'label'       => __( 'Image', 'shiro' ),
					'description' => __( 'Image should be 16:9 aspect ratio with min width of 1200px for best appearance. The image will automatically crop to that size if larger.', 'shiro' ),
					'section'     => $section_id,
				)
			)
		);

		$control_id = 'wmf_support_heading';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Heading', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_support_content';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Content', 'shiro' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		$control_id = 'wmf_support_link_uri';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'CTA Link URI', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_support_link_text';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'CTA Link Text', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		// Search Page.
        $section_id = 'wmf_search_page';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Search Page', 'shiro' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);
        
        $control_id = 'wmf_search_results_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search results for %s', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Search results message', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);
        
        $control_id = 'wmf_no_results_title';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Nothing Found', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'No Results Title', 'shiro' ),
				'description' => __( 'This displays on archive and search pages when there are no results found.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_no_results_description';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Sorry, but no results were found. Perhaps searching can help.', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'No Results Description', 'shiro' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		// 404 Page.
		$section_id = 'wmf_404_page';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( '404 Page', 'shiro' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_404_image';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Media_Control(
				$this->customize, $control_id, array(
					'label'       => __( 'Background Image', 'shiro' ),
					'description' => __( 'Displayed in header.', 'shiro' ),
					'section'     => $section_id,
				)
			)
		);

		$control_id = 'wmf_404_message';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '404 Error', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header message', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_404_title';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Imagine a world in which there is a page here', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header title', 'shiro' ),
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
					'label'   => __( 'Content', 'shiro' ),
					'section' => $section_id,
				)
			)
		);

		$control_id = 'wmf_404_search_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Or try a search instead', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Text above search bar', 'shiro' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		// ARIA support.
		$section_id = 'wmf_general_aria_support';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'ARIA Support', 'shiro' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);
        
        $control_id = 'wmf_search_toggle';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Toggle search', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Toggle search copy', 'shiro' ),
				'description' => __( 'This changes the labels exposed only to assistive technology. This can be set in each translation to localize the label.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_aria_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search Wikimedia Foundation site', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search input label for assistive technology', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_toggle_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Toggle menu', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Toggle menu label', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_skip2_content_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Skip to content', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Skip to content label', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_skip2_navigation_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Skip to navigation', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Skip to navigation label', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_select_language_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Select language', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Select language label', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_current_language_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Current language:', 'shiro' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Current language label', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		// Blackout Modal support.
		$section_id = 'wmf_general_blackout_modal';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Blackout Modal', 'shiro' ),
				'priority' => 80,
				'panel'    => $panel_id,
			)
		);

        $control_id = 'wmf_blackout_modal_enabled';
		$this->customize->add_setting(
			$control_id, array(
				'default' => false,
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Blackout Modal Enabled', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'checkbox',
			)
		);

		$control_id = 'wmf_blackout_modal_content';
		$this->customize->add_setting(
			$control_id, array(
				'capability'           => 'edit_theme_options',
				'default'              => '<h1>Black Lives Matter.<br>
											Black History Matters.<br>
											Black Communities Matter.</h1>
											<h2><a href="https://medium.com/freely-sharing-the-sum-of-all-knowledge">Read the Wikimedia Foundation\'s statement.</a></h2>
											<h2><a href="https://meta.wikimedia.org/wiki/Black_Lives_Matter">Take action on Wikimedia.</a></h2>',
				'sanitize_callback'    => 'wp_kses_post',
				'sanitize_js_callback' => 'wp_kses_post',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Blackout Modal Content', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'textarea',
			)
		);

		$control_id = 'wmf_blackout_modal_cookie';
		$this->customize->add_setting(
			$control_id, array(
				'default' => 'blackoutModalDismissed',
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Blackout Modal Cookie', 'shiro' ),
				'description' => __( 'Useful when changing the content of the modal, adjusting this would allow you to display the modal to users that have dismissed it previously.', 'shiro' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

	}

}
