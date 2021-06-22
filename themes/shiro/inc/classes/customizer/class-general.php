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
				'title'    => __( 'General', 'shiro-admin' ),
				'priority' => 70,
			)
		);

		// RTL.
		$section_id = 'wmf_general_rtl';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'RTL Support', 'shiro-admin' ),
				'priority' => 10,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_enable_rtl';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Enable RTL', 'shiro-admin' ),
				'description' => __( 'If checked, this will cause the front end of site to shift from left to right to right to left display.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'checkbox',
			)
		);

		// Headings.
		$section_id = 'wmf_general_labels';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Labels & Headings', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		if ( ! wmf_is_main_site() ) {
			$control_id = 'wmf_incomplete_translation';
			$this->customize->add_setting(
				$control_id, array(
					'default' => __( 'This content has not yet been translated into the current language.', 'shiro-admin' ),
				)
			);
			$this->customize->add_control(
				$control_id, array(
					'label'       => __( 'Incomplete Translation Notice', 'shiro-admin' ),
					'description' => __( 'Shows in the header if the content has not been marked as having a complete translation.', 'shiro-admin' ),
					'section'     => $section_id,
					'type'        => 'text',
				)
			);
		}

		$control_id = 'wmf_featured_post_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'NEWS', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Featured Post Pre Heading', 'shiro-admin' ),
				'description' => __( 'Shows above featured posts module in landing page and the home page.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_projects_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Projects', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Projects Pre Heading', 'shiro-admin' ),
				'description' => __( 'Shows above projects module in landing page and the home page.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_off_site_links_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'ELSEWHERE IN WIKIMEDIA', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Off Site Links Pre Heading', 'shiro-admin' ),
				'description' => __( 'Shows above off site links module throughout the site.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_related_pages_pre_heading';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Related', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Related Pages Pre Heading', 'shiro-admin' ),
				'description' => __( 'Shows above related pages module throughout the site.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_image_credit_header';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Photo credits', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Photo Credits Heading', 'shiro-admin' ),
				'description' => __( 'Shows above photo credits module throughout the site.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		// Support Module.
		$section_id = 'wmf_general_support_module';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Support Module', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_support_reusable_block';
		$this->customize->add_setting($control_id);
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
		$resuable_blocks = get_posts([
			'post_type' => 'wp_block',
			'numberposts' => 50,
		]);
		// phpcs:enable

		$selectable_blocks = [];
		foreach ($resuable_blocks as $block) {
			if ( has_block( 'shiro/spotlight', $block->ID ) ) {
				$selectable_blocks[$block->ID] = $block->post_title;
			}
		}

		// We're using `+` instead of `array_merge` because array_merge rewrites numeric IDs
		$choices = [ 0 => 'No CTA' ] + $selectable_blocks;
		$this->customize->add_control(
			$control_id, [
				'label' => __( 'Support CTA' ),
				'type' => 'select',
				'choices' => $choices,
				'section' => $section_id,
				'description' => count( $selectable_blocks ) > 0
					? __( 'Select a spotlight block to be shown in the "Support" area.', 'shiro-admin' )
					: sprintf( __( '<strong>There are no spotlight blocks available!</strong> Please <a href="%s">create one</a>.', 'shiro-admin' ), admin_url( 'edit.php?post_type=wp_block' ) ),
			]
		);

		// Search Page.
        $section_id = 'wmf_search_page';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Search Page', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

        $control_id = 'wmf_search_results_copy';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( /** translators: Current search term */ 'Search results for %s', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Search results message', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

        $control_id = 'wmf_no_results_title';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Nothing Found', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'No Results Title', 'shiro-admin' ),
				'description' => __( 'This displays on archive and search pages when there are no results found.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_no_results_description';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Sorry, but no results were found. Perhaps searching can help.', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'No Results Description', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);

		// 404 Page.
		$section_id = 'wmf_404_page';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( '404 Page', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'wmf_404_image';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			new \WP_Customize_Media_Control(
				$this->customize, $control_id, array(
					'label'       => __( 'Background Image', 'shiro-admin' ),
					'description' => __( 'Displayed in header.', 'shiro-admin' ),
					'section'     => $section_id,
				)
			)
		);

		$control_id = 'wmf_404_message';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( '404 Error', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header message', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_404_title';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Imagine a world in which there is a page here', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Header title', 'shiro-admin' ),
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
					'label'   => __( 'Content', 'shiro-admin' ),
					'section' => $section_id,
				)
			)
		);

		$control_id = 'wmf_404_search_text';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Or try a search instead', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Text above search bar', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		// ARIA support.
		$section_id = 'wmf_general_aria_support';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'ARIA Support', 'shiro-admin' ),
				'priority' => 70,
				'panel'    => $panel_id,
			)
		);

        $control_id = 'wmf_search_toggle';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Toggle search', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Toggle search copy', 'shiro-admin' ),
				'description' => __( 'This changes the labels exposed only to assistive technology. This can be set in each translation to localize the label.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_search_aria_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Search Wikimedia Foundation site', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Search input label for assistive technology', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_toggle_menu_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Toggle menu', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Toggle menu label', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_skip2_content_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Skip to content', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Skip to content label', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_skip2_navigation_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Skip to navigation', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Skip to navigation label', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_select_language_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Select language', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Select language label', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wmf_current_language_label';
		$this->customize->add_setting(
			$control_id, array(
				'default' => __( 'Current language:', 'shiro-admin' ),
			)
		);
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Current language label', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		// Blackout Modal support.
		$section_id = 'wmf_general_blackout_modal';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Blackout Modal', 'shiro-admin' ),
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
				'label'       => __( 'Blackout Modal Enabled', 'shiro-admin' ),
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
				'label'       => __( 'Blackout Modal Content', 'shiro-admin' ),
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
				'label'       => __( 'Blackout Modal Cookie', 'shiro-admin' ),
				'description' => __( 'Useful when changing the content of the modal, adjusting this would allow you to display the modal to users that have dismissed it previously.', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		// Wikipedia 20 support.
		$section_id = 'wmf_general_wikipedia20_modal';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Wikipedia 20 content', 'shiro-admin' ),
				'priority' => 80,
				'panel'    => $panel_id,
			)
		);

		$control_id = 'en_wikipedia';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'English Wikipedia', 'shiro-admin' ),
				'description' => __( 'Used e.g. to say "One human just edited English Wikipedia."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'ar_wikipedia';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Arabic Wikipedia', 'shiro-admin' ),
				'description' => __( 'Used e.g. to say "One human just edited Arabic Wikipedia."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'es_wikipedia';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Spanish Wikipedia', 'shiro-admin' ),
				'description' => __( 'Used e.g. to say "One human just edited Spanish Wikipedia."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'de_wikipedia';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'German Wikipedia', 'shiro-admin' ),
				'description' => __( 'Used e.g. to say "One human just edited German Wikipedia."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'fr_wikipedia';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'French Wikipedia', 'shiro-admin' ),
				'description' => __( 'Used e.g. to say "One human just edited French Wikipedia."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'ru_wikipedia';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Russian Wikipedia', 'shiro-admin' ),
				'description' => __( 'Used e.g. to say "One human just edited Russian Wikipedia."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'zh_wikipedia';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Chinese Wikipedia', 'shiro-admin' ),
				'description' => __( 'Used e.g. to say "One human just edited Chinese Wikipedia."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wikipedia_article_views';
		$this->customize->add_setting( $control_id, array(
				'default' => __('views', 'shiro-admin'),
			) );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Wikipedia article views', 'shiro-admin' ),
				'description' => __( '"views" - used to show stats about Wikipedia articles', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wikipedia_article_edits';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Wikipedia article edits', 'shiro-admin' ),
				'description' => __( '"edits" - used to show stats about Wikipedia articles', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wikipedia_article_most_viewed';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Most viewed articles', 'shiro-admin' ),
				'description' => __( '"Most viewed articles" - used to show stats about Wikipedia articles', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wikipedia_article_most_edited';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'Most edited articles', 'shiro-admin' ),
				'description' => __( '"Most edited articles" - used to show stats about Wikipedia articles', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wikipedia_article_or';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'or', 'shiro-admin' ),
				'description' => __( '"or" - used to show stats about Wikipedia articles by edits or views', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wikipedia_article_in';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'in', 'shiro-admin' ),
				'description' => __( '"in" - used to show stats about Wikipedia articles per year', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

		$control_id = 'wikipedia_article_no_data';
		$this->customize->add_setting( $control_id );
		$this->customize->add_control(
			$control_id, array(
				'label'       => __( 'No data message', 'shiro-admin' ),
				'description' => __( '"There is not data for the options you selected. Please change the options above."', 'shiro-admin' ),
				'section'     => $section_id,
				'type'        => 'text',
			)
		);

	}

}
