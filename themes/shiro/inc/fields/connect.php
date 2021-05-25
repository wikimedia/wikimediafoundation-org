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
	$blog_url = get_theme_mod( 'wmf_blog_url' );
	$blog_label = get_theme_mod( 'wmf_blog_label', 'Wikimedia Blog' );

    $connect    = new Fieldmanager_Group(
		array(
			'name'     => 'connect',
			'children' => array(
				'hide'                  => new Fieldmanager_Checkbox( __( 'Hide Connect Module', 'shiro-admin' ) ),

				// Headings.
				'pre_heading'           => new Fieldmanager_Textfield( __( 'Section Pre Heading', 'shiro-admin' ) ),
				'heading'               => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro-admin' ) ),

				// Subscribe Box.
				'subscribe_heading'     => new Fieldmanager_Textfield( __( 'Subscribe Heading', 'shiro-admin' ) ),
				'subscribe_content'     => new Fieldmanager_RichTextArea( __( 'Subscribe Content', 'shiro-admin' ) ),
				'subscribe_placeholder' => new Fieldmanager_Textfield( __( 'Email Input Placeholder', 'shiro-admin' ) ),
				'subscribe_button'      => new Fieldmanager_Textfield( __( 'Subscribe Button Text', 'shiro-admin' ) ),

				// Contact box.
				'contact_heading'       => new Fieldmanager_Textfield( __( 'Contact Heading', 'shiro-admin' ) ),
				'contact_content'       => new Fieldmanager_RichTextArea( __( 'Contact Content', 'shiro-admin' ) ),
				'contact_link'          => new Fieldmanager_Textfield( __( 'Contact Link', 'shiro-admin' ) ),
				'contact_link_text'     => new Fieldmanager_Textfield( __( 'Contact Link Text', 'shiro-admin' ) ),

				'follow_text'           => new Fieldmanager_Textfield( __( 'Follow Text', 'shiro-admin' ) ),
				'facebook_url'          => new Fieldmanager_Link( __( 'Facebook URL', 'shiro-admin' ) ),
				'facebook_label'        => new Fieldmanager_Textfield( __( 'Facebook Label', 'shiro-admin' ) ),
				'twitter_url'           => new Fieldmanager_Link( __( 'Twitter URL', 'shiro-admin' ) ),
				'twitter_id'            => new Fieldmanager_Textfield( __( 'Twitter ID', 'shiro-admin' ) ),
				'instagram_url'         => new Fieldmanager_Link( __( 'Instagram URL', 'shiro-admin' ) ),
				'instagram_label'       => new Fieldmanager_Textfield( __( 'Instagram Label', 'shiro-admin' ) ),
				'blog_url'              => new Fieldmanager_Link( __( 'Blog URI', 'shiro-admin' ) ),
				'blog_label'            => new Fieldmanager_Textfield( __( 'Blog Label', 'shiro-admin' ) ),

			),
		)
	);
	$connect->add_meta_box( __( 'Connect', 'shiro-admin' ), array( 'page', 'post', 'profile' ) );
}
add_action( 'fm_post_post', 'wmf_connect_fields' );
add_action( 'fm_post_page', 'wmf_connect_fields' );
add_action( 'fm_post_profile', 'wmf_connect_fields' );
