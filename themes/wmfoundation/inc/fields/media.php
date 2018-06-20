<?php
/**
 * Fieldmanager Fields for credits in media attachments.
 *
 * @package wmfoundation
 */


/**
 * Add Custom fields to attachments.
 *
 * @param array $attachment_fields List of existing fields.
 * @param object $post             Full Attachment object.
 * @return array Fields addded on.
 */
function wmf_add_media_custom_fields( $attachment_fields, $post ) {
	$credit_info    = get_post_meta( $post->ID, 'credit_info', true );

	$attachment_fields['credit_author'] = array(
		'value' => ! empty( $credit_info['author'] ) ? $credit_info['author'] : '',
		'label' => __( 'Credit Author', 'wmfoundation' ),
	);

	$attachment_fields['credit_license'] = array(
		'value' => ! empty( $credit_info['license'] ) ? $credit_info['license'] : '',
		'label' => __( 'License', 'wmfoundation' ),
	);

	$attachment_fields['credit_url'] = array(
		'value' => ! empty( $credit_info['url'] ) ? $credit_info['url'] : '',
		'label' => __( 'Credit URL', 'wmfoundation' ),
	);

	return $attachment_fields;
}

add_filter( 'attachment_fields_to_edit', 'wmf_add_media_custom_fields', 10, 2 );

/**
 * Save custom fields when attachments are saved.
 *
 * @param int $attachment_id Attachmend post ID.
 */
function wmf_save_attachment_custom_fields( $attachment_id ) {
	$request = $_REQUEST['attachments'][ $attachment_id ];

	if ( empty( $request ) ) {
		return;
	}

	$credit_info = array(
		'author' => ! empty( $request['credit_author'] ) ? $request['credit_author'] : '',
		'license' => ! empty( $request['credit_license'] ) ? $request['credit_license'] : '',
		'url' => ! empty( $request['credit_url'] ) ? $request['credit_url'] : '',
	);

	if ( ! empty( $credit_info ) ) {
		update_post_meta( $attachment_id, 'credit_info', $credit_info );
	}

}
add_action( 'edit_attachment', 'wmf_save_attachment_custom_fields' );
