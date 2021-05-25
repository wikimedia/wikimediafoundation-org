<?php
/**
 * Fieldmanager Fields for credits in media attachments.
 *
 * @package shiro
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
		'label' => __( 'Author', 'shiro-admin' ),
	);

	$attachment_fields['credit_license'] = array(
		'value' => ! empty( $credit_info['license'] ) ? esc_html( $credit_info['license'] ) : '',
		'label' => __( 'License', 'shiro-admin' ),
		'helps' => __( 'Should use standard formatting (example: CC BY-SA 4.0)', 'shiro-admin' ),
	);

	$attachment_fields['credit_url'] = array(
		'value' => ! empty( $credit_info['url'] ) ? esc_url( $credit_info['url'] ) : '',
		'label' => __( 'Photo credit URL', 'shiro-admin' ),
		'helps' => __( 'URL to original photo page - preferably on Wikimedia Commons', 'shiro-admin' ),
	);

	$attachment_fields['credit_license_url'] = array(
		'value' => ! empty( $credit_info['license_url'] ) ? esc_url( $credit_info['license_url'] ) : '',
		'label' => __( 'License URL', 'shiro-admin' ),
		'helps' => __( 'License URL only necessary if not using a standard site media license (CC BY (2.0, 2.5, 3.0, 4.0), CC SA, CC BY-SA (2.0, 3.0, 4.0), CC0 1.0, GFDL-1.2, and Public domain)', 'shiro-admin' ),
	);

	return $attachment_fields;
}

add_filter( 'attachment_fields_to_edit', 'wmf_add_media_custom_fields', 10, 2 );

/**
 * Save custom fields when attachments are saved.
 *
 * @param  array $post Full attachment post data.
 * @param  array $attachment All data from saved attachment.
 * @return array
 */
function wmf_save_attachment_custom_fields( $post, $attachment ) {
	$credit_info = array(
		'author'      => ! empty( $attachment['credit_author'] ) ? sanitize_text_field( wp_unslash( $attachment['credit_author'] ) ) : '', // WPCS: Input var CSRF ok.

		'license'     => ! empty( $attachment['credit_license'] ) ? sanitize_text_field( wp_unslash( $attachment['credit_license'] ) ) : '', // WPCS: Input var CSRF ok.

		'url'         => ! empty( $attachment['credit_url'] ) ? esc_url_raw( wp_unslash( $attachment['credit_url'] ) ) : '', // WPCS: Input var CSRF ok.

		'license_url' => ! empty( $attachment['credit_license_url'] ) ? esc_url_raw( wp_unslash( $attachment['credit_license_url'] ) ) : '', // WPCS: Input var CSRF ok.
	);

	if ( ! empty( $credit_info ) ) {
		update_post_meta( $post['ID'], 'credit_info', $credit_info );
	}
	return $post;
}
add_filter( 'attachment_fields_to_save', 'wmf_save_attachment_custom_fields', 10, 2 );
