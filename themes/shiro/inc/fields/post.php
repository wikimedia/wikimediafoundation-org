<?php
/**
 * Fieldmanager Fields for Post template
 *
 * @package shiro
 */

/**
 * Add post options.
 */
function wmf_post_fields() {
	$opts = array(
		'home' => __( 'Home Page', 'shiro-admin' ),
	);

	$featured_on = new Fieldmanager_Checkboxes(
		array(
			'name'    => 'featured_on',
			'options' => $opts + wmf_get_landing_pages_options(),
		)
	);
	$featured_on->add_meta_box( __( 'Featured On:', 'shiro-admin' ), 'post' );

	$featured_profile = new Fieldmanager_Group(
		array(
			'name'     => 'featured_profile',
			'children' => array(
				'headline'   => new Fieldmanager_TextField( __( 'Profile Headline', 'shiro-admin' ) ),
				'teaser'     => new Fieldmanager_TextArea( __( 'Profile Teaser', 'shiro-admin' ) ),
				'link_title' => new Fieldmanager_TextField( __( 'Link Title', 'shiro-admin' ) ),
				'profile_id' => new Fieldmanager_Select(
					array(
						'options'     => wmf_get_profiles_options(),
						'first_empty' => true,
					)
				),

			),

		)
	);
	$featured_profile->add_meta_box( __( 'Featured Profile', 'shiro-admin' ), 'post' );
}
add_action( 'fm_post_post', 'wmf_post_fields' );
