<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package wmfoundation
 */

/**
 * Add landing page options.
 */
function wmf_profiles_module() {
	if ( 'fm_post_page' === current_filter() && ! wmf_using_template( 'page-landing' ) ) {
		return;
	}

	$custom_fields = array(
		'name'     => 'profiles',
		'children' => array(
			'pre_heading' => new Fieldmanager_Textfield( __( 'Section Pre-heading', 'wmfoundation' ) ),
			'headline'    => new Fieldmanager_Textfield( __( 'Headline', 'wmfoundation' ) ),
			'description' => new Fieldmanager_RichTextArea( __( 'Description', 'wmfoundation' ) ),
		),
	);

	if ( 'fm_post_page' === current_filter() ) {
		$custom_fields['children']['profiles_list'] = new Fieldmanager_Checkboxes(
			array(
				'label'       => __( 'List of Profiles to pull from', 'wmfoundation' ),
				'description' => __( 'Select as many as are applicable. 3 profiles will be selected from this list each time the page loads.', 'wmfoundation' ),
				'options'     => wmf_get_profiles_options(),
			)
		);
	}

	$social = new Fieldmanager_Group( $custom_fields );
	$social->add_meta_box( __( 'Profiles', 'wmfoundation' ), array( 'page', 'profile' ) );
}
add_action( 'fm_post_page', 'wmf_profiles_module' );
add_action( 'fm_post_profile', 'wmf_profiles_module' );
