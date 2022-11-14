<?php
/**
 * Fieldmanager Fields for Listing Module
 *
 * @package shiro
 */

/**
 * Add listing page options.
 */
function wmf_listing_fields() {
	if ( 'fm_post_page' === current_filter() && ! wmf_using_template( 'page-landing' ) ) {
		return;
	}

	$listing = new Fieldmanager_Group(
		array(
			'name'     => 'listings',
			'children' => array(
				'heading'  => new Fieldmanager_Textfield( __( 'Heading', 'shiro-admin' ) ),
				'listings' => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Listing', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 3,
						'children'       => array(
							'heading'   => new Fieldmanager_Textfield( __( 'Heading', 'shiro-admin' ) ),
							'content'   => new Fieldmanager_Textfield( __( 'Content', 'shiro-admin' ) ),
							'link'      => new Fieldmanager_Textfield( __( 'CTA URI', 'shiro-admin' ) ),
							'link_text' => new Fieldmanager_Textfield( __( 'CTA Text', 'shiro-admin' ) ),
						),
					)
				),
			),
		)
	);
	$listing->add_meta_box( __( 'Employment Listings', 'shiro-admin' ), array( 'page', 'profile' ) );
}
add_action( 'fm_post_page', 'wmf_listing_fields' );
add_action( 'fm_post_profile', 'wmf_listing_fields' );
