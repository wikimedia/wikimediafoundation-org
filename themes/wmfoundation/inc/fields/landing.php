<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package wmfoundation
 */

/**
 * Add landing page options.
 */
function wmf_landing_fields() {
	if ( ! wmf_using_template( 'page-landing' ) ) {
		return;
	}

	$header_opts = new Fieldmanager_Textfield(
		array(
			'name' => 'sub_title',
		)
	);

	$header_opts->add_meta_box( __( 'Subtitle', 'wmfoundation' ), 'page' );

	$intro = new Fieldmanager_Textarea(
		array(
			'name' => 'page_intro',
		)
	);

	$intro->add_meta_box( __( 'Page Intro', 'wmfoundation' ), 'page' );

	$text_cta = new Fieldmanager_Group(
		array(
			'name'           => 'text_cta',
			'add_more_label' => __( 'Add Text CTA', 'wmfoundation' ),
			'sortable'       => true,
			'limit'          => 0,
			'children'       => array(
				'heading'   => new Fieldmanager_Textfield( __( 'Heading', 'wmfoundation' ) ),
				'copy'      => new Fieldmanager_RichTextArea( __( 'Content', 'wmfoundation' ) ),
				'link_url'  => new Fieldmanager_Link( __( 'Link URI', 'wmfoundation' ) ),
				'link_text'   => new Fieldmanager_Textfield( __( 'Link Text', 'wmfoundation' ) ),
			),
		)
	);

	$text_cta->add_meta_box( __( 'Text CTA', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_landing_fields' );
