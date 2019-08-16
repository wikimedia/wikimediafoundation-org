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
			'label'    => __( 'Background', 'shiro' ),
			'children' => array(
				'color' => new Fieldmanager_Radios(
					array(
						'label'   => __( 'Color', 'shiro' ),
						'options' => array(
							''     => __( 'Blue', 'shiro' ),
							'pink' => __( 'Pink', 'shiro' ),
						),
					)
				),
				'image' => new Fieldmanager_Media( __( 'Image', 'shiro' ) ),
			),
		)
	);

	$header_opts->add_meta_box( __( 'Header Options', 'shiro' ), 'page' );

	$is_front_page   = (int) get_option( 'page_on_front' ) === (int) wmf_get_fields_post_id();
	$is_landing_page = wmf_using_template( 'page-landing' );

	if ( $is_landing_page || $is_front_page ) {
		$subtitle = new Fieldmanager_Textfield(
			array(
				'name' => 'sub_title',
                'sanitize' => "wp_kses_post",
			)
		);
		$subtitle->add_meta_box( __( 'Subtitle', 'shiro' ), 'page' );
	}
}
add_action( 'fm_post_page', 'wmf_header_fields', 1 );
