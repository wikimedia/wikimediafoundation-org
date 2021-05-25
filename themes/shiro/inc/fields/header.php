<?php
/**
 * Fieldmanager Fields for Header options
 *
 * @package shiro
 */

/**
 * Add header options.
 */
function wmf_header_fields() {
	$header_opts = new Fieldmanager_Group(
		array(
			'name'     => 'page_header_background',
			'label'    => __( 'Background', 'shiro-admin' ),
			'children' => array(
				'color' => new Fieldmanager_Radios(
					array(
						'label'   => __( 'Color', 'shiro-admin' ),
						'options' => array(
							''     => __( 'Default', 'shiro-admin' ),
							'pink' => __( 'Pink', 'shiro-admin' ),
							'blue' => __( 'Blue', 'shiro-admin' ),
						),
					)
				),
				'image' => new Fieldmanager_Media( __( 'Image', 'shiro-admin' ) ),
			),
		)
	);

	$header_opts->add_meta_box( __( 'Header Options', 'shiro-admin' ), 'page' );

	$is_front_page   = (int) get_option( 'page_on_front' ) === (int) wmf_get_fields_post_id();
	$is_landing_page = wmf_using_template( [ 'page-landing', 'page-report', 'page-data', 'page-report-landing' ] );

	if ( $is_landing_page || $is_front_page ) {
		$subtitle = new Fieldmanager_Textfield(
			array(
				'name'     => 'sub_title',
				'sanitize' => 'wp_kses_post',
			)
		);
		$subtitle->add_meta_box( __( 'Subtitle', 'shiro-admin' ), 'page' );
	}
}
add_action( 'fm_post_page', 'wmf_header_fields', 1 );
