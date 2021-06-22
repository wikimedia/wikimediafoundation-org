<?php
/**
 * Filters related to the page-redirect.php template, to handle redirecting to
 * the most-recently-updated child of that page and properly excluding the page
 * from site breadcrumbs in the page navigation.
 *
 * @package shiro
 */

/**
 * Reusable helper to check if an ID refers to a page using the page-redirect template.
 *
 * @param int $page_id The ID of the page to check.
 * @return bool Whether the provided ID is for a page using page-redirect.php
 */
function wmf_is_redirect_template_page( $page_id ) {
	$template = get_page_template_slug( $page_id );

	return 'page-redirect.php' === $template;
}

/**
 * Skip Redirect-template pages when computing page links.
 *
 * @param string $link    The page's permalink.
 * @param int    $page_id The ID of the page.
 * @return string The filtered permalink.
 */
function wmf_skip_redirect_template_in_page_link( $link, $page_id ) {
	if ( ! wmf_is_redirect_template_page( $page_id ) ) {
		return $link;
	}

	// Check for a parent page and use the parent's link if found.
	$parent_page = wp_get_post_parent_id( $page_id );
	if ( ! empty( $parent_page ) ) {
		return get_the_permalink( $parent_page );
	}

	// No parent found.
	return '';
}
add_filter( 'page_link', 'wmf_skip_redirect_template_in_page_link', 10, 2 );

/**
 * Skip Redirect-template pages when computing page titles.
 *
 * @param string $title The post title.
 * @param int    $id    The post ID.
 * @return string The filtered page title.
 */
function wmf_skip_redirect_template_in_title( $title, $id ) {
	if ( ! wmf_is_redirect_template_page( $id ) ) {
		return $title;
	}

	// Check for a parent page and use that parent's title if found.
	$parent_page = wp_get_post_parent_id( $id );
	if ( ! empty( $parent_page ) ) {
		return get_the_title( $parent_page );
	}

	// No parent found.
	return '';
}
add_filter( 'the_title', 'wmf_skip_redirect_template_in_title', 10, 2 );

/**
 * Show an admin warning about Redirect Page template behavior while editing
 * pages using that template.
 *
 * @return void
 */
function wmf_redirect_template_warning_notice() {
	$screen = get_current_screen();
	if ( ! isset( $screen ) ) {
		return;
	}

	if ( 'edit' !== $screen->parent_base || 'page' !== $screen->post_type ) {
		return;
	}

	if ( ! wmf_is_redirect_template_page( get_the_ID() ) ) {
		return;
	}

	?>
	<div class="notice notice-warning">
		<p>
			<?php esc_html_e( 'This page is using the "Redirect Page" page template.', 'shiro-admin' ); ?>
			<?php esc_html_e( 'It will redirect to the newest child page which declares this page as its parent.', 'shiro-admin' ); ?>
			<?php esc_html_e( 'Change the template if you wish to edit this page directly.', 'shiro-admin' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'wmf_redirect_template_warning_notice' );

/**
 * Remove any special title or permalink handling when using the admin.
 *
 * @return void
 */
function wmf_undo_redirect_template_changes_in_admin() {
	remove_filter( 'page_link', 'wmf_skip_redirect_template_in_page_link' );
	remove_filter( 'the_title', 'wmf_skip_redirect_template_in_title' );
}
add_action( 'admin_init', 'wmf_undo_redirect_template_changes_in_admin' );

/**
 * Find and return the URI of the most recent child of a page.
 *
 * @param int $page_id The ID of the page for which to find a child.
 * @return string The URI of the newest child.
 */
function wmf_get_most_recent_child_page_uri( $page_id ) {
	$child_pages = get_posts(
		array(
			'post_type'        => 'page',
			'post_status'      => 'publish',
			'post_parent'      => $page_id,
			'posts_per_page'   => 1,
			'orderby'          => 'date',
			'suppress_filters' => false,
		)
	);

	if ( ! empty( $child_pages ) ) {
		return get_permalink( $child_pages[0] );
	}

	return '';
}
