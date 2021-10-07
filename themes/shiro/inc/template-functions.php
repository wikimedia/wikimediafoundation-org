<?php
/**
 * Additional features to allow styling of the templates
 *
 * @package shiro
 */

use function Asset_Loader\Manifest\get_active_manifest;
use function Asset_Loader\Manifest\load_asset_manifest;

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
	if ( is_front_page() && !has_blocks() ) {
		$class = 'header-home';
	} else {
		$class = 'header-default';
	}

	if ( ( is_single() || is_page() ) && has_post_thumbnail() ) {
		$post_type = get_post_type();

		if ( in_array( $post_type, array( 'profile', 'story', 'post' ), true ) ) {
			$class .= ' minimal--short';
		} else {
			$template = basename( get_page_template() );

			switch ( $template ) {
				case 'page-landing.php':
				case 'page-report.php':
				case 'page-data.php':
				case is_front_page():
					$class .= ' featured-photo--content-left';
					break;

				default:
					$class .= ' minimal--short featured-photo--photo-centered';
					break;
			}
		}
	} elseif ( is_404() ) {
		$class .= ' featured-photo--content-left';
	} elseif ( is_home() ) {
		$class .= ' minimal--news';
	} else {
		$class .= ' minimal--short';
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

	if ( empty( $children[ $parent_id ] ) ) {
		return false;
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
			'post_type'      => 'profile',
			'fields'         => 'ids',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'posts_per_page' => 100,
			'tax_query'      => array(
				array(
					'taxonomy'         => 'role',
					'field'            => 'term_id',
					'terms'            => $term_id,
					'include_children' => false,
				),
			),
		)
	); // WPCS: slow query ok.

	$post_list     = wmf_sort_by_last_name( $posts->posts );
	$featured_list = array();

	foreach ( $post_list as $i => $post_id ) {
		$featured = get_post_meta( $post_id, 'profile_featured', true );

		if ( $featured ) {
			unset( $post_list[ $i ] );
			$featured_list[ $i ] = $post_id;
		}
	}

	return array(
		'posts' => $featured_list + $post_list,
		'name'  => $term_query->name,
		'slug'  => $term_query->slug,
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

	$child_terms = wmf_get_role_hierarchy( $term_id );

	if ( empty( $child_terms ) ) {
		$post_list[ $term_id ] = wmf_get_role_posts( $term_id );
	} else {
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
				'posts_per_page' => 3,
                'orderby'        => 'date',
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
 * Get a list of recent posts by an author
 *
 * @param int $author_id Author ID to check against.
 * @return array List of post objects.
 */
function wmf_get_recent_author_posts( $author_id ) {
	$post_list = array();

	if ( empty( $author_id ) ) {
		return $post_list;
	}

	$author_id = absint( $author_id );

	$cache_key = md5( sprintf( 'wmf_author_posts_for_%s', $author_id ) );

	$post_list = wp_cache_get( $cache_key );

	if ( empty( $post_list ) ) {
		$post = get_post( $author_id );

		if ( ! empty( $post ) ) {
			$posts_query = new WP_Query(
				array(
                    'orderby'        => 'date',
					'posts_per_page' => 2,
					'no_found_rows'  => true,
					'post_type'      => 'post',
					'ignore_sticky'  => true,
					'author_name'    => $post->post_name,
				)
			); // WPCS: Slow query ok.

			$post_list = $posts_query->posts;
			wp_cache_add( $cache_key, $post_list );
		}
	}

	return $post_list;
}

/**
 * Get author link for use in profiles
 *
 * @param int $author_id Author ID to check against.
 * @return array List of post objects.
 */
function wmf_get_author_link( $author_id ) {
	$author_link = '';

	if ( empty( $author_id ) ) {
		return $author_link;
	}

	$author_id = absint( $author_id );

	$cache_key = md5( sprintf( 'wmf_author_link_for_%s', $author_id ) );

	$post_list = wp_cache_get( $cache_key );

	if ( empty( $post_list ) ) {
		$post = get_post( $author_id );
	}

    $author_link = $post->post_name;

	return $author_link;
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

	remove_filter( 'get_the_archive_title', array( $coauthors_plus, 'filter_author_archive_title' ), 10 );
}
add_action( 'init', 'wmf_remove_coauthors_archive_filter' );

/**
 * Add the coauthors template tag to feed.
 *
 * @param string $the_author Author name for feed.
 * @return string Coatuhors version of author name.
 */
function wmf_coauthors_in_rss( $the_author ) {
	if ( ! is_feed() || ! function_exists( 'coauthors' ) ) {
		return $the_author;
	}
	return coauthors( null, null, null, null, false );
}
add_filter( 'the_author', 'wmf_coauthors_in_rss' );

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

	if ( empty( $attachment ) ) {
		return '';
	}

	$caption = $attachment->post_excerpt;
	$credit  = $attachment->post_content;

	$html = sprintf( '<div class="article-img img-in-text" id="%1$s"><div class="img-in-text">%2$s</div><div class="img-caption"><span class="photo-caption">%3$s</span> <span class="photo-credit">%4$s</span></div></div>', esc_attr( $attr['id'] ), wp_kses_post( do_shortcode( $content ) ), wp_kses_post( $caption ), wp_kses_post( $credit ) );

	return $html;
}
add_filter( 'img_caption_shortcode', 'wmf_filter_caption_shortcode', 10, 3 );

/**
 * Initiates a new Profile\Sorter and returns the sorted posts.
 *
 * @param array $posts The posts to sort.
 *
 * @return array
 */
function wmf_sort_by_last_name( $posts ) {
	$sorter = new WMF\Profiles\Sorter( $posts );

	return $sorter->get_sorted();
}

/**
 * Register custom RSS templates.
 */
add_action( 'after_setup_theme', 'wmf_rss_templates' );
function wmf_rss_templates()
{
    foreach( array( 'offset1', 'images' ) as $name )
    {
        add_feed( $name,
            function() use ( $name )
            {
                get_template_part( 'feed', $name );
            }
        );
    }
}

/**
 * Setup offset for offset1 RSS feed.
 */
function wpsites_exclude_latest_post( $query ) {
if ( $query->is_main_query() && $query->is_feed( 'offset1' )) {
    $query->set( 'offset', '1' );
    }
}
add_action( 'pre_get_posts', 'wpsites_exclude_latest_post', 1 );

/**
 * Check whether the current post is part of a new-style transparency report
 * layout, based on the page's assigned page template.
 */
function wmf_is_transparency_report_page() {
	$included_templates = array(
		'page-report-landing.php',
		'page-report-section.php',
		'page-stories.php',
	);
	return in_array( get_page_template_slug(), $included_templates, true );
}

/**
 * Find the ID of the nearest page using the "Report Landing Page" template.
 *
 * @param int $page_id ID of the page from which to start the search.
 * @return int ID of a Report Landing Page, or 0 for no match.
 */
function wmf_locate_report_landing_page_id( $page_id ) {
	$parent_id = $page_id;
	while ( 0 !== $parent_id && 'page-report-landing.php' !== get_page_template_slug( $parent_id ) ) {
		$parent_id = wp_get_post_parent_id( $parent_id );
	}
	return $parent_id;
}

/**
 * Get the information necessary to render a Transparency Report sidebar.
 *
 * Returns an array of child pages within a report parent, prepended with the
 * introductory Report Landing Page.
 *
 * @return array Array of [ id, title, url, active ] nav list items in the report.
 */
function wmf_get_report_sidebar_data() {
	$current_page = get_the_ID();

	// If this post is not a report landing template, find an ancestor with that template.
	$report_landing_page = wmf_locate_report_landing_page_id( $current_page );

	if ( 0 === $report_landing_page ) {
		return null;
	}

	$child_pages = get_posts(
		array(
			'post_type'        => 'page',
			'post_status'      => 'publish',
			'post_parent'      => $report_landing_page,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'posts_per_page'   => 15,
			'suppress_filters' => false,
		)
	);

	$report_sidebar_label = get_post_meta( $report_landing_page, 'landing_page_sidebar_menu_label', true );
	if ( empty( $report_sidebar_label ) ) {
		$report_sidebar_label = get_the_title( $report_landing_page );
	}

	return array_merge(
		// Prepend the report landing page.
		array(
			array(
				'id'     => $report_landing_page,
				'title'  => $report_sidebar_label,
				'url'    => get_permalink( $report_landing_page ),
				'active' => $report_landing_page === $current_page,
			),
		),
		// Continue with all direct child pages.
		array_map(
			function( $page ) use ( $current_page ) {
				return array(
					'id'     => $page->ID,
					'title'  => $page->post_title,
					'url'    => get_permalink( $page ),
					'active' => $current_page === $page->ID,
				);
			},
			$child_pages
		)
	);
}

/**
 * Get the Stories associated with the current page.
 *
 * @return array Array of post objects.
 */
function wmf_get_page_stories() {
	// See the "Stories" field for how this data gets set.
	$stories   = get_post_meta( get_the_ID(), 'stories', true );
	$story_ids = $stories['stories_list'] ?? [];

	if ( empty( $story_ids ) ) {
		return [];
	}

	$stories = get_posts(
		array(
			'post_type'        => 'story',
			'post_status'      => 'publish',
			'post__in'         => $story_ids,
			'posts_per_page'   => count( $story_ids ),
			'suppress_filters' => false,
		)
	);

	return $stories;
}

/**
 * Get the uri for an SVG in the theme, by name.
 *
 * @param string $name
 *
 * @return string
 */
function wmf_get_svg_uri( string $name ): string
{
	$name = str_replace( '.svg', '', $name);
	$uri = get_stylesheet_directory_uri() . '/assets/src/svg/' . $name . '.svg';
	return esc_url($uri);
}

/**
 * Returns a probable path for an asset processed by gulp.
 *
 * Some gulp assets are versioned, and used a different versioning system than
 * asset processed by webpack. This function takes that into account, and
 * will return a hashed asset if one exists.
 *
 * @param string $name
 *
 * @return string
 */
function wmf_get_gulp_asset_uri( string $name ): string {
	$dist_path = get_stylesheet_directory_uri() . '/assets/dist/';
	$manifest  = load_asset_manifest( get_active_manifest( [
			get_stylesheet_directory() . '/assets/dist/rev-manifest.json',
		] ) ) ?? [];

	$resolved_name = $manifest[ $name ] ?? $name;

	return $dist_path . $resolved_name;
}

/**
 * Echo & wrap a piece of text with an href if the possible URL is set.
 *
 * @param string $text The text to wrap
 * @param string $possible_url The URL to put in the href of the link.
 */
function wmf_shiro_echo_wrap_with_link( $text, $possible_url = '' ) {
	if ( empty( $possible_url ) ) :
		echo esc_html( $text );
	else :
	?>
	<a href="<?php echo esc_url( $possible_url ); ?>" target="_blank" rel="noopener noreferrer">
		<?php echo esc_html( $text ); ?>
	</a>
	<?php
	endif;
}

/**
 * Determine if a given block exists in a post, include in reusable blocks.
 *
 * @see https://kybernaut.cz/en/clanky/check-for-has_block-inside-reusable-blocks/
 *
 * @param                  $block_name
 * @param int|WP_Post|null $post
 *
 * @return bool
 */
function wmf_enhanced_has_block( $block_name, $post = null ): bool {
	if ( has_block( $block_name, $post ) ) {
		return true;
	}

	if ( has_block( 'core/block', $post ) ) {
		$content = get_post_field( 'post_content', $post );
		$blocks  = parse_blocks( $content );

		return wmf_search_reusable_blocks_within_innerblocks( $blocks, $block_name, $post );
	}

	return false;
}

/**
 * Recursively search for a block within innerblocks.
 *
 * @see https://kybernaut.cz/en/clanky/check-for-has_block-inside-reusable-blocks/
 *
 * @param                  $blocks
 * @param                  $block_name
 * @param int|WP_Post|null $post
 *
 * @return bool
 */
function wmf_search_reusable_blocks_within_innerblocks( $blocks, $block_name, $post = null ): bool {
	foreach ( $blocks as $block ) {
		if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) ) {
			wmf_search_reusable_blocks_within_innerblocks( $block['innerBlocks'], $block_name, $post );
		} elseif ( $block['blockName'] === 'core/block' && ! empty( $block['attrs']['ref'] ) && \has_block( $block_name,
				$block['attrs']['ref'] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get an arbitrary reusable block module.
 *
 * Returns the id of the reusable block if found; 0 otherwise.
 *
 * @param string $module
 *
 * @return int
 */
function wmf_get_reusable_block_module_id( string $module ): int {
	$available_blocks = [
		'connect' => 'wmf_connect_reusable_block',
		'support' => 'wmf_support_reusable_block',
	];

	if ( ! isset( $available_blocks[ $module ] ) ) {
		return 0;
	}

	$id = get_theme_mod( $available_blocks[ $module ] );

	$valid = is_numeric( $id )
	         && $id > 0
	         && get_post_type( $id ) === 'wp_block';

	return $valid ? (int) $id : 0;
}

/**
 * Get the WP_Post object for a reusable block module.
 *
 * Returns null if none found.
 *
 * @param string $module
 *
 * @return null|WP_Post
 */
function wmf_get_reusable_block_module( string $module ) {
	$id = wmf_get_reusable_block_module_id( $module );

	return $id > 0 ? get_post( $id ) : null;
}

/**
 * Returns the "comment" necessary to insert a reusable block, for the module requested.
 *
 * Returns an empty string if block does not exist or cannot be found.
 *
 * @param string $module
 *
 * @return string
 */
function wmf_get_reusable_block_module_insert( string $module ): string {
	$id = wmf_get_reusable_block_module_id( $module );

	if ( $id < 1 ) {
		return '';
	}

	return sprintf( '<!-- wp:block {"ref":%d} /-->', $id );
}
