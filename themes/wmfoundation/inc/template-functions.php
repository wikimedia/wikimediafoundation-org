<?php
/**
 * Additional features to allow styling of the templates
 *
 * @package wmfoundation
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function wmf_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'wmf_body_classes' );

/**
 * Parse template data, get back container class.
 *
 * @return string Container classes to add.
 */
function wmf_get_header_container_class() {
	if ( is_front_page() ) {
		$class = 'header-home';
	} else {
		$class = 'header-default';
	}

	if ( ( is_single() || is_page() ) && has_post_thumbnail() ) {
		$post_type = get_post_type();

		if ( in_array( $post_type, array( 'profile', 'post' ), true ) ) {
			$class .= ' minimal--short';
		} else {
			$template = basename( get_page_template() );

			switch ( $template ) {
				case 'page-landing.php':
				case is_front_page():
					$class .= ' featured-photo--content-left';
					break;

				default:
					$class .= ' minimal--short featured-photo--photo-centered';
					break;
			}
		}
	} elseif ( is_404() ) {
		$class = ' featured-photo--content-left';
	} elseif ( is_home() ) {
		$class .= ' minimal--news';
	} else {
		$class .= ' minimal--short';
	}

	if ( is_page() ) {
		$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );

		$class .= isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? ' header-pink' : '';
	}

	return $class;
}

/**
 * Get image container class based on location.
 *
 * @return string Class name.
 */
function wmf_get_photo_class() {
	$class = 'photo-aspect-ratio';

	if ( ! is_singular( 'page' ) || is_front_page() ) {
		return $class;
	}

	$template = basename( get_page_template() );

	switch ( $template ) {
		case 'page.php':
			$class .= ' mw-900';
			break;
	}

	return $class;
}

/**
 * Parse template data, get back header button class.
 *
 * @return string button classes to add.
 */
function wmf_get_header_cta_button_class() {
	$class = '';

	$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );

	$class .= is_page() && isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? ' btn-blue' : ' btn-pink';

	return $class;
}

/**
 * Get all the child terms for a parent organized by hierarchy
 *
 * @param int $parent_id ID to query against.
 * @return array List of organized IDs.
 */
function wmf_get_role_hierarchy( $parent_id ) {
	$children   = array();
	$term_array = array();
	$terms      = get_terms(
		'role', array(
			'orderby' => 'name',
			'fields'  => 'id=>parent',
			'get'     => 'all',
		)
	);

	foreach ( $terms as $term_id => $parent ) {
		if ( 0 < $parent ) {
			$children[ $parent ][] = $term_id;
		}
	}

	foreach ( $children[ $parent_id ] as $child_id ) {
		$term_array[ $child_id ] = isset( $children[ $child_id ] ) ? $children[ $child_id ] : array();
	}

	return $term_array;
}

/**
 * Get posts for an individual term.
 *
 * @param int $term_id  Term to query against.
 * @return array List of and term name.
 */
function wmf_get_role_posts( $term_id ) {
	$term_query = get_term( $term_id, 'role' );
	$posts      = new WP_Query(
		array(
			'post_type' => 'profile',
			'fields'    => 'ids',
			'orderby'   => 'title',
			'order'     => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'role',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			),
		)
	); // WPCS: slow query ok.

	$post_list = $posts->posts;

	foreach ( $posts->posts as $i => $post_id ) {
		$featured = get_post_meta( $post_id, 'profile_featured', true );

		if ( $featured ) {
			unset( $post_list[ $i ] );
			array_unshift( $post_list, $post_id );
		}
	}

	return array(
		'posts' => $post_list,
		'name'  => $term_query->name,
	);
}

/**
 * Organize posts by their child terms in taxonomy
 *
 * For posts that are not parent posts (i.e. Staff & Contractors)
 * This will simply return a list of posts for a term.
 *
 * @param int $term_id  ID of parent term.
 * @return array list of organized posts or empty array.
 */
function wmf_get_posts_by_child_roles( $term_id ) {
	$post_list = array();

	$term = get_term( $term_id );
	if ( 0 !== $term->parent ) {
		$post_list[ $term_id ] = wmf_get_role_posts( $term_id );
		return $post_list;
	}

	$cached_posts = wp_cache_get( 'wmf_terms_list_' . $term_id );

	if ( ! empty( $cached_posts ) ) {
		return $cached_posts;
	}

	$child_terms = wmf_get_role_hierarchy( $term_id, 'role' );

	foreach ( $child_terms as $parent_id => $children ) {
		$featured_term = get_term_meta( $parent_id, 'featured_term', true );

		if ( true === boolval( $featured_term ) ) {
			$post_list = array(
				$parent_id => wmf_get_role_posts( $parent_id ),
			) + $post_list;
		} else {
			$post_list[ $parent_id ] = wmf_get_role_posts( $parent_id );
		}

		$post_list[ $parent_id ]['children'] = array();

		foreach ( $children as $child_id ) {
			$post_list[ $parent_id ]['children'][ $child_id ] = wmf_get_role_posts( $child_id );
		}
	}

	wp_cache_set( 'wmf_terms_list_' . $term_id, $post_list );

	return $post_list;
}

/**
 * Get a list of related profiles by terms
 *
 * To avoid a slow query, we get a lot more than we need,
 * and still allow random entries to be selected from the list.
 *
 * @param int $profile_id Profile ID to check against.
 * @return array List of profile IDs.
 */
function wmf_get_related_profiles( $profile_id ) {
	$profile_list = array();

	if ( empty( $profile_id ) ) {
		return $profile_list;
	}

	$profile_id = absint( $profile_id );
	$terms      = get_the_terms( $profile_id, 'role' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return $profile_list;
	}

	$term_ids = wp_list_pluck( $terms, 'term_id' );

	if ( empty( $term_ids ) ) {
		return $profile_list;
	}

	$cache_key = md5( sprintf( 'wmf_profiles_for_term_%s', $term_ids[0] ) );

	$profile_list = wp_cache_get( $cache_key );

	if ( empty( $profile_list ) ) {
		$profiles_query = new WP_Query(
			array(
				'posts_per_page' => 100,
				'no_found_rows'  => true,
				'fields'         => 'ids',
				'post_type'      => 'profile',
				'tax_query'      => array(
					array(
						'taxonomy' => 'role',
						'terms'    => $term_ids[0],
					),
				),
			)
		); // WPCS: slow query ok.

		$profile_list = $profiles_query->posts;
		wp_cache_add( $cache_key, $profile_list );
	}

	$key = array_search( $profile_id, $profile_list, true );
	if ( false !== $key ) {
		unset( $profile_list[ $key ] );
	}

	return $profile_list;
}

/**
 * Get a list of related posts by tags.
 *
 * @param int $post_id Post ID to check against.
 * @return array List of post objects.
 */
function wmf_get_related_posts( $post_id ) {
	$post_list = array();

	if ( empty( $post_id ) ) {
		return $post_list;
	}

	$post_id = absint( $post_id );

	$terms    = get_the_terms( $post_id, 'post_tag' );
	$term_ids = ! empty( $terms ) ? wp_list_pluck( $terms, 'term_id' ) : false;

	if ( empty( $term_ids ) ) {
		return $post_list;
	}

	$cache_key = md5( sprintf( 'wmf_posts_for_post_%s', $post_id ) );

	$post_list = wp_cache_get( $cache_key );

	if ( empty( $post_list ) ) {
		$posts_query = new WP_Query(
			array(
				'posts_per_page' => 4,
				'no_found_rows'  => true,
				'post_type'      => 'post',
				'ignore_sticky'  => true,
				'tax_query'      => array(
					array(
						'taxonomy' => 'post_tag',
						'terms'    => $term_ids,
					),
				),
			)
		); // WPCS: Slow query ok.

		$post_list = $posts_query->posts;
		foreach ( $post_list as $i => $post ) {
			if ( $post->ID === $post_id ) {
				unset( $post_list[ $i ] );
			}
		}
		$post_list = array_splice( $post_list, 0, 3 );
		wp_cache_add( $cache_key, $post_list );
	}

	return $post_list;
}

/**
 * Remove the word "category" from body class since it has
 * intherited styles.
 *
 * @param string $classes Class names to filter.
 * @return string Classes with category taken out.
 */
function wmf_remove_category_body_class( $classes ) {
	return str_replace( 'category', '', $classes );
}
add_filter( 'body_class', 'wmf_remove_category_body_class' );

/**
 * Get the background image in header.
 *
 * @return int ID of attachment.
 */
function wmf_get_background_image() {
	if ( is_404() ) {
		return array(
			'image' => get_theme_mod( 'wmf_404_image' ),
		);
	}

	$post_id = is_home() ? get_option( 'page_for_posts' ) : get_the_ID();

	return get_post_meta( $post_id, 'page_header_background', true );
}

/**
 * Filter out the more excerpt text.
 *
 * @return string Filtered read more string.
 */
function wmf_filter_more() {
	return '&hellip;.';
}
add_filter( 'excerpt_more', 'wmf_filter_more' );

/**
 * Remove coauthors filter since it doesn't return properly.
 */
function wmf_remove_coauthors_archive_filter() {
	global $coauthors_plus;

	remove_filter( 'get_the_archive_title', array( $coauthors_plus, 'filter_author_archive_title' ), 10, 2 );
}
add_action( 'init', 'wmf_remove_coauthors_archive_filter' );

/**
 * Add a wrapper around images that are added through the editor,
 * and are not aligned left or right, or have a caption.
 *
 * @param string $html    Full HTML of image.
 * @param string $id      ID of image.
 * @param string $caption Caption.
 * @param string $title   Title attribute.
 * @param string $align   Align attributes.
 * @return string Modified HTML string.
 */
function wmf_add_image_container( $html, $id, $caption, $title, $align ) {
	if ( 'left' === $align || 'right' === $align || ! empty( $caption ) ) {
		return $html;
	}

	$output  = '<div class="article-img img-in-text">';
	$output .= '<div class="img-container mar-bottom">';
	$output .= $html;
	$output .= '</div></div>';

	return $output;
}
add_filter( 'image_send_to_editor', 'wmf_add_image_container', 10, 5 );

/**
 * Also wrap the caption in a container that sets image markup properly.
 *
 * @param string $output Current output of caption shortcode.
 * @param array  $attr List of shortcode attributes.
 * @param string $content Full shortcode.
 * @return string HTML to output.
 */
function wmf_filter_caption_shortcode( $output, $attr, $content ) {
	$attachment_id = str_replace( 'attachment_', '', $attr['id'] );
	$attachment    = get_post( $attachment_id );
	$caption       = $attachment->post_excerpt;
	$credit        = $attachment->post_content;

	$html = sprintf( '<div class="article-img img-in-text" id="%1$s"><div class="img-in-text">%2$s</div><div class="img-caption"><span class="photo-caption">%3$s</span> <span class="photo-credit">%4$s</span></div></div>', esc_attr( $attr['id'] ), do_shortcode( $content ), wp_kses_post( $caption ), wp_kses_post( $credit ) );

	return $html;
}
add_filter( 'img_caption_shortcode', 'wmf_filter_caption_shortcode', 10, 3 );
