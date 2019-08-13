<?php
/**
 * Fieldmanager Fields for Story Post Type
 *
 * @package shiro
 */

/**
 * Add fields to story post type.
 */
function wmf_story_fields() {
	$info = new Fieldmanager_Group(
		array(
			'name'           => 'story_info',
			'label'          => __( 'Story Info', 'shiro' ),
			'serialize_data' => false,
			'add_to_prefix'  => false,
			'children'       => array(
				'story_type'     => new Fieldmanager_Textfield( __( 'Type', 'shiro' ) ),
				'story_featured' => new Fieldmanager_Checkbox( __( 'Featured?', 'shiro' ) ),
			),
		)
	);
	$info->add_meta_box( __( 'Story Info', 'shiro' ), 'story' );

}
add_action( 'fm_post_story', 'wmf_story_fields' );

/**
 * Add fields for the type taxonomy
 */
function wmf_type_fields() {
	$display_intro = new Fieldmanager_Checkbox(
		array(
			'name' => 'display_intro',
		)
	);

	$display_intro->add_term_meta_box( 'Display Intro?', 'type' );

	$term_heading = new Fieldmanager_Checkbox(
		array(
			'name' => 'term_heading',
		)
	);

	$term_heading->add_term_meta_box( 'Output Term Heading?', 'type' );

	$featured_term = new Fieldmanager_Checkbox(
		array(
			'name' => 'featured_term',
		)
	);

	$featured_term->add_term_meta_box( 'Featured Term?', 'type' );

	$button = new Fieldmanager_Group(
		array(
			'name'     => 'type_button',
			'label'    => __( 'Button', 'shiro' ),
			'children' => array(
				'text' => new Fieldmanager_Textfield( __( 'Button Text', 'shiro' ) ),
				'link' => new Fieldmanager_Link( __( 'Button Link', 'shiro' ) ),
			),
		)
	);

	$button->add_term_meta_box( '', 'type' );
}
add_action( 'fm_term_type', 'wmf_type_fields' );