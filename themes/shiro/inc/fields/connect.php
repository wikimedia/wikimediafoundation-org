<?php
/**
 * Fieldmanager Fields for Connect Module
 *
 * @package shiro
 */

/**
 * Add connect page options.
 */
function wmf_connect_fields() {
	$blog = get_theme_mod( 'wmf_blog_url' );

	$blog_label = get_theme_mod( 'wmf_blog_label', 'Wikimedia Blog' );
	$connect    = new Fieldmanager_Group(
		array(
			'name'     => 'connect',
			'children' => array(
				'hide'                  => new Fieldmanager_Checkbox( __( 'Hide Connect Module', 'shiro' ) ),

				// Headings.
				'pre_heading'           => new Fieldmanager_Textfield( __( 'Section Pre Heading', 'shiro' ) ),
				'heading'               => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro' ) ),

				// Subscribe Box.
				'subscribe_heading'     => new Fieldmanager_Textfield( __( 'Subscribe Heading', 'shiro' ) ),
				'subscribe_content'     => new Fieldmanager_RichTextArea( __( 'Subscribe Content', 'shiro' ) ),
				'subscribe_placeholder' => new Fieldmanager_Textfield( __( 'Email Input Placeholder', 'shiro' ) ),
				'subscribe_button'      => new Fieldmanager_Textfield( __( 'Subscribe Button Text', 'shiro' ) ),

				// Contact box.
				'contact_heading'       => new Fieldmanager_Textfield( __( 'Contact Heading', 'shiro' ) ),
				'contact_content'       => new Fieldmanager_RichTextArea( __( 'Contact Content', 'shiro' ) ),
				'contact_link'          => new Fieldmanager_Textfield( __( 'Contact Link', 'shiro' ) ),
				'contact_link_text'     => new Fieldmanager_Textfield( __( 'Contact Link Text', 'shiro' ) ),

				'follow_text'           => new Fieldmanager_Textfield( __( 'Follow Text', 'shiro' ) ),
				'facebook_url'          => new Fieldmanager_Link( __( 'Facebook URL', 'shiro' ) ),
				'facebook_label'        => new Fieldmanager_Textfield( __( 'Facebook Label', 'shiro' ) ),
				'twitter_url'           => new Fieldmanager_Link( __( 'Twitter URL', 'shiro' ) ),
				'twitter_id'            => new Fieldmanager_Textfield( __( 'Twitter ID', 'shiro' ) ),
				'instagram_url'         => new Fieldmanager_Link( __( 'Instagram URL', 'shiro' ) ),
				'instagram_label'       => new Fieldmanager_Textfield( __( 'Instagram Label', 'shiro' ) ),
				'blog_url'              => new Fieldmanager_Link( __( 'Blog URI', 'shiro' ) ),
				'blog_label'            => new Fieldmanager_Textfield( __( 'Blog Label', 'shiro' ) ),

			),
		)
	);
	$connect->add_meta_box( __( 'Connect', 'shiro' ), array( 'page', 'post', 'profile' ) );
}
add_action( 'fm_post_post', 'wmf_connect_fields' );
add_action( 'fm_post_page', 'wmf_connect_fields' );
add_action( 'fm_post_profile', 'wmf_connect_fields' );
