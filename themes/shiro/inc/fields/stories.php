<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package shiro
 */

/**
 * Add landing page options.
 */
function wmf_stories_module() {
	$is_front_page = (int) get_option( 'page_on_front' ) === (int) wmf_get_fields_post_id();

	if ( 'fm_post_page' === current_filter() && ( ! wmf_using_template( [ 'page-report', 'page-report-section', 'page-stories' ] ) && ! $is_front_page ) ) {
		return;
	}

	$custom_fields = array(
		'name'     => 'stories',
		'children' => array(
			'pre_heading'  => new Fieldmanager_Textfield( __( 'Section Pre-heading', 'shiro' ) ),
			'headline'     => new Fieldmanager_Textfield( __( 'Headline', 'shiro' ) ),
			'description'  => new Fieldmanager_TextArea( __( 'Description', 'shiro' ) ),
			'button_label' => new Fieldmanager_Textfield( __( 'Button Label', 'shiro' ) ),
			'button_link'  => new Fieldmanager_Link( __( 'Button Link', 'shiro' ) ),
		),
	);

	if ( 'fm_post_page' === current_filter() || $is_front_page ) {
		$custom_fields['children']['stories_list'] = new Fieldmanager_Checkboxes(
			array(
				'label'       => __( 'List of Stories to pull from', 'shiro' ),
				'description' => __( 'Select as many as are applicable. 3 stories will be selected from this list each time the page loads.', 'shiro' ),
				'options'     => wmf_get_stories_options(),
			)
		);
	}

	$social = new Fieldmanager_Group( $custom_fields );
	$social->add_meta_box( __( 'Stories', 'shiro' ), array( 'page', 'story' ) );
}
add_action( 'fm_post_page', 'wmf_stories_module' );
add_action( 'fm_post_story', 'wmf_stories_module' );
