<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package wmfoundation
 */

if ( ! function_exists( 'wmf_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function wmf_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'wmfoundation' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'wmfoundation' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'wmf_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function wmf_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'wmfoundation' ) );
			if ( $categories_list && wmf_categorized_blog() ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'wmfoundation' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'wmfoundation' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'wmfoundation' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'wmfoundation' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}
	}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function wmf_categorized_blog() {
	$all_the_cool_cats = get_transient( 'wmf_categories' );
	if ( false === $all_the_cool_cats ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number'     => 2,
			)
		);

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'wmf_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 || is_preview() ) {
		// This blog has more than 1 category so wmf_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so wmf_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in wmf_categorized_blog.
 */
function wmf_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'wmf_categories' );
}
add_action( 'edit_category', 'wmf_category_transient_flusher' );
add_action( 'save_post', 'wmf_category_transient_flusher' );

/**
 * Display SVG Icon based on title
 *
 * @param  string $name Name of SVG in sprite.
 */
function wmf_show_icon( $name ) {
?>
	<svg class="icon icon-<?php echo esc_attr( $name ); ?>">
		<use xlink:href="<?php echo esc_url( get_template_directory_uri() . '/assets/dist/icons.svg#' . $name ); ?>"></use>
	</svg>
<?php
}

/**
 * Builds the URI for social sharing.
 *
 * @param string $service The service to build URi for.
 * @param array  $args    Args for building the URI.
 *
 * @return string
 */
function wmf_get_share_url( $service, $args ) {
	$default = array(
		'uri'     => get_permalink(),
		'message' => '',
	);

	$args = wp_parse_args( $args, $default );

	$uri = '';

	switch ( $service ) {
		case 'facebook':
			$uri = sprintf(
				'http://www.facebook.com/sharer/sharer.php?s=100&p[url]=%1$s&&p[title]=%2$s',
				$args['uri'],
				$args['message']
			);
			break;
		case 'twitter':
			$uri = sprintf(
				'http://twitter.com/intent/tweet?text=%2$s %1$s',
				$args['uri'],
				$args['message']
			);
			break;
	}

	return $uri;
}
