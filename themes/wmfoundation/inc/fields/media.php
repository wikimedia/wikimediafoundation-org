<?php
/**
 * Fieldmanager Fields for credits in media attachments.
 *
 * @package wmfoundation
 */

/**
 * Add Custom fields to attachments.
 *
 * @param array  $attachment_fields List of existing fields.
 * @param object $post             Full Attachment object.
 * @return array Fields addded on.
 */
function wmf_add_media_custom_fields( $attachment_fields, $post ) {
	$credit_info = get_post_meta( $post->ID, 'credit_info', true );

	$attachment_fields['credit_author'] = array(
		'value' => ! empty( $credit_info['author'] ) ? esc_html( $credit_info['author'] ) : '',
		'label' => __( 'Author', 'wmfoundation' ),
	);

	$attachment_fields['credit_license'] = array(
		'value' => ! empty( $credit_info['license'] ) ? esc_html( $credit_info['license'] ) : '',
		'label' => __( 'License', 'wmfoundation' ),
	);

	$attachment_fields['credit_url'] = array(
		'value' => ! empty( $credit_info['url'] ) ? esc_url( $credit_info['url'] ) : '',
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
function wmf_save_attachment_custom_fields( $post, $attachment ) {
	$credit_info = array(
		'author'  => ! empty( $attachment['credit_author'] ) ? sanitize_text_field( wp_unslash( $attachment['credit_author'] ) ) : '', // WPCS: Input var CSRF ok.

		'license' => ! empty( $attachment['credit_license'] ) ? sanitize_text_field( wp_unslash( $attachment['credit_license'] ) ) : '', // WPCS: Input var CSRF ok.

		'url'     => ! empty( $attachment['credit_url'] ) ? esc_url_raw( wp_unslash( $attachment['credit_url'] ) ) : '', // WPCS: Input var CSRF ok.
	);

	if ( ! empty( $credit_info ) ) {
		update_post_meta( $post['ID'], 'credit_info', $credit_info );
	}
}
add_filter( 'attachment_fields_to_save', 'wmf_save_attachment_custom_fields', 10, 2 );
