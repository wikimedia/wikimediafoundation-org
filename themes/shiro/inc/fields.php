<?php
/**
 * Setup some baseline custom fields functions
 *
 * @package shiro
 */

use function WMF\Editor\admin_post_has_blocks;

/**
 * Verify a template is being used on the backend.
 *
 * @param mixed $template_name Template to check against, without .php as a string (single template) or array (multiple templates).
 * @return bool
 */
function wmf_using_template( $template_name ) {
	$id = wmf_get_fields_post_id();

	if ( empty( $id ) ) {
		return false;
	}
	$page_template = get_post_meta( $id, '_wp_page_template', true );

	if ( ! empty( $page_template ) ) {
		$current_page_template = explode( '.', $page_template );

		// Allow an array or string.
		if ( is_array( $template_name ) && in_array( $current_page_template[0], $template_name, true ) ) {
			return true;
		} elseif ( $current_page_template[0] === $template_name ) {
			return true;
		}
	}

	return false;
}

/**
 * In Fieldmanager context, check if is on home page.
 *
 * @return boolean
 */
function wmf_is_posts_page() {
	$id = wmf_get_fields_post_id();

	if ( empty( $id ) ) {
		return false;
	}
	$posts_page = get_option( 'page_for_posts' );

	return absint( $id ) === absint( $posts_page );
}

/**
 * Gets the post ID for the edited post.
 *
 * @return int
 */
function wmf_get_fields_post_id() {
	$post_request_id = filter_input(
		INPUT_POST, 'post_ID', FILTER_CALLBACK, array(
			'options' => 'intval',
		)
	);
	$get_request_id  = filter_input(
		INPUT_GET, 'post', FILTER_CALLBACK, array(
			'options' => 'intval',
		)
	);

	return ! empty( $get_request_id ) ? $get_request_id : $post_request_id;
}

/**
 * Gets available landing pages in an array suitable for fieldmanager options.
 *
 * phpcs:disable WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
 *
 * @return array
 */
function wmf_get_landing_pages_options() {
	$landing_pages = wp_cache_get( 'wmf_landing_pages_opts' );

	if ( empty( $landing_pages ) ) {
		$landing_pages = array();

		$args  = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => 100,
			'meta_query'     => array(
				array(
					'key'   => '_wp_page_template',
					'value' => 'page-landing.php',
				),
			),
		); // WPCS: slow query ok.
		$pages = new WP_Query( $args );

		if ( $pages->have_posts() ) {
			while ( $pages->have_posts() ) {
				$pages->the_post();
				$landing_pages[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();

		wp_cache_add( 'wmf_landing_pages_opts', $landing_pages );
	}

	return $landing_pages;
}

/**
 * Gets available landing pages in an array suitable for fieldmanager options.
 *
 * @return array
 */
function wmf_get_pages_options() {
	$all_pages = wp_cache_get( 'wmf_all_pages_opts' );

	if ( empty( $all_pages ) ) {
		$all_pages = array();

		$args  = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => 100,
		);
		$pages = new WP_Query( $args );

		if ( $pages->have_posts() ) {
			while ( $pages->have_posts() ) {
				$pages->the_post();
				$all_pages[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();

		wp_cache_add( 'wmf_all_pages_opts', $all_pages );
	}

	return $all_pages;
}

/**
 * Gets available profiles and formats them for Fieldmanager
 *
 * @return array
 */
function wmf_get_profiles_options() {
	$profiles = wp_cache_get( 'wmf_profiles_opts' );

	if ( empty( $profiles ) ) {
		$profiles = array();

		$args  = array(
			'post_type'      => 'profile',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => 400, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_posts_per_page
		); // WPCS: Slow query okay.
		$pages = new WP_Query( $args );

		if ( $pages->have_posts() ) {
			while ( $pages->have_posts() ) {
				$pages->the_post();
				$profiles[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();

		wp_cache_add( 'wmf_profiles_opts', $profiles );
	}

	return $profiles;
}

/**
 * Gets available stories and formats them for Fieldmanager
 *
 * @return array
 */
function wmf_get_stories_options() {
	$stories = wp_cache_get( 'wmf_stories_opts' );

	if ( empty( $stories ) ) {
		$stories = array();

		$args  = array(
			'post_type'      => 'story',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => 100, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_posts_per_page
		); // WPCS: Slow query okay.
		$pages = new WP_Query( $args );

		if ( $pages->have_posts() ) {
			while ( $pages->have_posts() ) {
				$pages->the_post();
				$stories[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();

		wp_cache_add( 'wmf_stories_opts', $stories );
	}

	return $stories;
}

/**
 * Gets available posts in an array suitable for fieldmanager options.
 *
 * @return array
 */
function wmf_get_posts_options() {
	$posts = wp_cache_get( 'wmf_posts_opts' );

	if ( empty( $posts ) ) {
		$posts = array();

		$args  = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => 100,
		);
		$pages = new WP_Query( $args );

		if ( $pages->have_posts() ) {
			while ( $pages->have_posts() ) {
				$pages->the_post();
				$posts[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();

		wp_cache_add( 'wmf_posts_opts', $posts );
	}

	return $posts;
}

/**
 * Gets available posts in an array suitable for fieldmanager options.
 *
 * @return array
 */
function wmf_get_categories_options() {
	$category_list = wp_cache_get( 'wmf_category_opts' );

	if ( empty( $category_list ) ) {
		$category_list = array();

		$categories = get_categories();

		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$category_list[ $category->term_id ] = $category->name;
			}
		}
		wp_cache_add( 'wmf_category_opts', $category_list );
	}

	return $category_list;
}

// As soon as a post has blocks we should no longer register these fields.
// Registering these fields would generate confusion.
if ( ! admin_post_has_blocks() ) {
	require get_template_directory() . '/inc/fields/button.php';
	require get_template_directory() . '/inc/fields/common.php';
	require get_template_directory() . '/inc/fields/connect.php';
	require get_template_directory() . '/inc/fields/datapage.php';
	require get_template_directory() . '/inc/fields/default.php';
	require get_template_directory() . '/inc/fields/header.php';
	require get_template_directory() . '/inc/fields/home.php';
	require get_template_directory() . '/inc/fields/landing.php';
	require get_template_directory() . '/inc/fields/links.php';
	require get_template_directory() . '/inc/fields/list.php';
	require get_template_directory() . '/inc/fields/listing.php';
	require get_template_directory() . '/inc/fields/media.php';
	require get_template_directory() . '/inc/fields/page-cta.php';
	require get_template_directory() . '/inc/fields/post.php';
	require get_template_directory() . '/inc/fields/posts-page.php';
	require get_template_directory() . '/inc/fields/profile.php';
	require get_template_directory() . '/inc/fields/profiles.php';
	require get_template_directory() . '/inc/fields/projects.php';
	require get_template_directory() . '/inc/fields/related-pages.php';
	require get_template_directory() . '/inc/fields/stories.php';
	require get_template_directory() . '/inc/fields/support.php';
}
