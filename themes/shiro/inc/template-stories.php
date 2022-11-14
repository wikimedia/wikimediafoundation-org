<?php
/**
 * Filters related to the page-stories.php template.
 *
 * @package shiro
 */

/**
 * Reusable helper to check if an ID refers to a page using the page-stories template.
 *
 * @param int $page_id The ID of the page to check.
 * @return bool Whether the provided ID is for a page using page-stories.php
 */
function wmf_is_stories_template_page( $page_id ) {
	$template = get_page_template_slug( $page_id );

	return 'page-stories.php' === $template;
}

/**
 * Show an admin warning about Stories template behavior while editing
 * pages using that template.
 *
 * @return void
 */
function wmf_stories_template_admin_notice() {
	$screen = get_current_screen();
	if ( ! isset( $screen ) ) {
		return;
	}

	if ( 'edit' !== $screen->parent_base || 'page' !== $screen->post_type ) {
		return;
	}

	if ( ! wmf_is_stories_template_page( get_the_ID() ) ) {
		return;
	}

	?>
	<div class="notice">
		<p>
			<?php esc_html_e( 'This page is using the "Stories Page" page template.', 'shiro-admin' ); ?>
			<?php esc_html_e( 'Selected stories from the "stories" module below will be displayed in place of the normal Report Section "list" module', 'shiro-admin' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'wmf_stories_template_admin_notice' );

/**
 * Inject the stories as list items when on a stories page.
 *
 * @param null|array|string $value     The value get_metadata() should return - a single metadata value,
 *                                     or an array of values.
 * @param int               $object_id ID of the object metadata is for.
 * @param string            $meta_key  Metadata key.
 *
 * @return null|array Filtered meta value, or null for no change.
 */
function wmf_inject_stories_as_list( $value, $object_id, $meta_key ) {
	if ( 'list' !== $meta_key || ! wmf_is_stories_template_page( $object_id ) ) {
		return $value;
	}

	// If we are on the Stories template and 'list' meta has been requested,
	// return an array of stories in the same shape as the expected List items.
	return [
		array_map(
			function( $story ) {
				$image_id    = get_post_thumbnail_id( $story );
				$credit_info = get_post_meta( $image_id, 'credit_info', true );
				$image_url   = ! empty( $credit_info['url'] ) ? $credit_info['url'] : '';
				$image       = get_the_post_thumbnail( $story, 'image_4x3_large' );

				if ( ! empty( $image_url ) ) {
					$image = sprintf( '<a href="%s">%s</a>', esc_url( $image_url ), $image );
				}

				return [
					'title'       => $story->post_title,
					'description' => $image . $story->post_content,
					'link'        => get_the_permalink( $story ),
				];
			},
			wmf_get_page_stories()
		),
	];
}
add_filter( 'get_post_metadata', 'wmf_inject_stories_as_list', 10, 3 );
