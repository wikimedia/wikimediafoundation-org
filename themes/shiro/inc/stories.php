<?php
namespace Stories_Customisations;

/**
 * Kick it off.
 */
function init() {
	add_action( 'update_post_metadata', __NAMESPACE__ . '\\link_stories_page_stories', 10, 5 );
}

/**
 * Sets the meta '_story_parent_page; key to the page ID for which the meta is being saved.
 *
 * @param null|bool $check      Whether to allow updating metadata for the given type.
 * @param int       $object_id  ID of the object metadata is for.
 * @param string    $meta_key   Metadata key.
 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed     $prev_value Optional. If specified, only update existing metadata entries
 */
function link_stories_page_stories( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
	if ( $meta_key !== 'stories' ) {
		return;
	}

	if ( empty( $meta_value['stories_list'] ) ) {
		return;
	}

	foreach ( $meta_value['stories_list'] as $story_id ) {
		update_post_meta( $story_id,'_story_parent_page', $object_id );
	}
}
