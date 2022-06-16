<?php
/**
 * Register the shiro/blog-list block.
 */

namespace WMF\Editor\Blocks\BlogList;

use WMF\Editor\Blocks\BlogPost;

const BLOCK_NAME = 'shiro/blog-list';
const MAX_POSTS = 20; // Set a reasonable limit on posts returned.
const TRANSIENT_PREFIX = 'blog_list_';
/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_block' );
}

/**
 * Register the block here.
 */
function register_block() {
	register_block_type(
		BLOCK_NAME,
		[
			'apiVersion' => 2,
			'render_callback' => __NAMESPACE__ . '\\render_block',
			'attributes' => [
				'postsToShow' => [
					'type' => 'integer',
					'default' => 2,
				],
				'categories' => [
					'type' => 'array',
					'items' => [
						'type' => 'object',
					],
				],
				'order' => [
					'type' => 'string',
					'default' => 'desc',
				],
				'orderBy' => [
					'type' => 'string',
					'default' => 'date',
				],
				'selectedAuthor' => [
					'type' => 'number',
				],
				'fetchUrlBase' => [
					'type' => 'string',
				],
				'useRemote' => [
					'type' => 'boolean',
					'default' => false,
				],
			],
		]
	);
}

/**
 * Callback for server-side rendering for the blog-list block.
 *
 * @param [] $attributes  Parsed block attributes.
 *
 * @return string HTML markup.
 */
function render_block( $attributes ) {

	$use_remote = $attributes['useRemote'] ?? false;
	$remote_url_base = $attributes['fetchUrlBase'] ?? null;

	$args = [
		'posts_per_page' => $attributes['postsToShow'],
		'post_status' => 'publish',
		'order' => $attributes['order'],
		'orderby' => $attributes['orderBy'],
		'suppress_filters' => false,
	];

	if ( $use_remote && filter_var( $remote_url_base, FILTER_VALIDATE_URL ) ) {
		$remote_args = [
			'author' => $attributes['selectedAuthor'] ?? null,
			'categories' => array_column( $attributes['categories'], 'id' ),
			'order' => $attributes['order'],
			'orderby' => $attributes['orderBy'],
			'per_page' => $attributes['postsToShow'],
		];

		$recent_posts = get_remote_posts( untrailingslashit( $remote_url_base ), $remote_args );

	} else {

		if ( isset( $attributes['categories'] ) ) {
			$args['category__in'] = array_column( $attributes['categories'], 'id' );
		}

		if ( isset( $attributes['selectedAuthor'] ) ) {
			$args['author'] = $attributes['selectedAuthor'];
		}

		$recent_posts = array_map( function ( \WP_Post $post ) {
			$image = get_post_thumbnail_id( $post );
			if ( $image ) {
				$mime = get_post_mime_type( $post );
				$type = strtok( $mime, '/' );
				$media = [
					'type' => $type,
					'mime' => $mime,
					'url' => wp_get_attachment_image_url( $image, 'image_4x3_large' ),
				];
			}

			return [
				'id' => $post->ID,
				'date' => $post->post_date,
				'modified' => $post->post_modified,
				'link' => get_permalink( $post->ID ),
				'title' => get_the_title( $post ),
				'content' => apply_filters( 'the_content', $post->post_content ),
				'excerpt' => get_the_excerpt( $post ),
				'author' => [
					'url' => get_author_posts_url( $post->post_author ),
					'name' => get_the_author_meta( 'display_name', $post->post_author ),
				],
				'featured_media' => $media,
				'categories' => array_map( function ( \WP_Term $term ) {
					return [
						'name' => $term->name,
						'slug' => $term->slug,
						'url' => get_term_link( $term ),
					];
				}, get_the_category( $post->ID ) ?? [] ),
				'tags' => array_map( function ( \WP_Term $term ) {
					return [
						'name' => $term->name,
						'slug' => $term->slug,
						'url' => get_term_link( $term ),
					];
				}, get_the_tags( $post->ID ) ?? [] ),
			];
		}, get_posts( $args ) );
	}

	if ( count( $recent_posts ) > 0 ) {
		$output = '';

		foreach ( $recent_posts as $recent_post ) {
			$output .= BlogPost\render_block( [ 'post_id' => $recent_post->ID ] );
		}

		return $output;
	}
}

/**
 * Gets a set of posts based on $args.
 *
 * @param string $url Base URL for WP REST API on remote site.
 * @param array $args Set of keyed arguments.
 *
 * @return array
 * @throws \Exception
 */
function get_remote_posts( string $url, array $args ) : array {
	$defaults = [
		'per_page' => 2,
	];
	$args = array_merge( $defaults, $args );

	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		// Without a valid URL, we can't do anything.
		return [];
	}

	$validated_args = array_filter( $args, __NAMESPACE__ . '\\validate_remote_arg', ARRAY_FILTER_USE_BOTH );

	if ( count( $validated_args ) === 0 ) {
		// If no args validate, don't try to get anything.
		return [];
	}

	$request_url = add_query_arg( $args, $url );

	return get_url( $request_url, function ( $data ) {
		$posts = array_map( function ( $post ) {

			// We're only concerned with the rendered data.
			$post['title'] = $post['title']['rendered'] ?? $post['title'];
			$post['content'] = $post['content']['rendered'] ?? $post['content'];
			$post['excerpt'] = $post['excerpt']['rendered'] ?? $post['excerpt'];

			return array_filter( $post, function ( $field ) {
				return in_array( $field, [
					'id',
					'date',
					'modified',
					'link',
					'title',
					'content',
					'excerpt',
					'author',
					'featured_media',
					'categories',
					'tags',
					'_links',
				], true );
			}, ARRAY_FILTER_USE_KEY );
		}, $data );

		return array_map( __NAMESPACE__ . '\\collect_remote_post_data', $posts );
	}, [
		'expiration' => DAY_IN_SECONDS,
	] );
}

/**
 * Returns a key based on the MD5 hash of a URL.
 *
 * @param string $url URL to be hashed for key.
 *
 * @return string
 */
function get_transient_name_from_url( string $url ) : string {
	return sprintf(
		'%sremote_cache_%s',
		TRANSIENT_PREFIX,
		md5( $url )
	);
}

/**
 * Get transient name for post.
 *
 * This is keyed off the ID and modified date, to avoid returning stale
 * data when a remote post changes.
 *
 * @param int $id ID of post.
 * @param string $modified Modified date of post.
 *
 * @return string
 */
function get_post_transient_name_from_id_and_modified( int $id, string $modified ) : string {
	return sprintf(
		'%sremote_cache_post_%s',
		TRANSIENT_PREFIX,
		md5( sprintf( '%d%s', $id, $modified ) )
	);
}

/**
 * Request, process, and cache additional post-specific data from _links.
 *
 * @param array $post Post data from a REST request.
 *
 * @return array
 * @throws \Exception
 */
function collect_remote_post_data( array $post ) : array {
	$key = get_post_transient_name_from_id_and_modified( $post['id'], $post['modified'] );
	$cached = get_transient( $key );
	if ( $cached ) {
		return $cached;
	}

	$remote_links = [];

	$term_links = array_column( $post['_links']['wp:term'] ?? [], 'href', 'taxonomy' );
	$remote_links[] = [
		'categories',
		$term_links['category'] ?? null,
		function ( $data ) {
			$indexed = array_column( $data, null, 'id' );

			return array_map( function ( $category ) {
				return [
					'name' => $category['name'],
					'slug' => $category['slug'],
					'url' => $category['link'],
				];
			}, $indexed );
		},
	];
	$remote_links[] = [
		'tags',
		$term_links['post_tag'] ?? null,
		function ( $data ) {
			$indexed = array_column( $data, null, 'id' );

			return array_map( function ( $tag ) {
				return [
					'name' => $tag['name'],
					'slug' => $tag['slug'],
					'url' => $tag['link'],
				];
			}, $indexed );
		},
	];

	$remote_links[] = [
		'featured_media',
		$post['_links']['wp:featuredmedia'][0]['href'] ?? null,
		function ( $data ) {
			return [
				'type' => $data['media_type'],
				'mime' => $data['mime_type'],
				'url' => $data['source_url'],
			];
		},
	];

	$remote_links[] = [
		'author',
		$post['_links']['author'][0]['href'] ?? null,
		function ( $author ) {
			return [
				'name' => $author['name'],
				'url' => $author['link'],
			];
		},
	];

	$remote_data = array_column( array_map(
		function ( $row ) {
			[ $type, $url, $callback ] = $row;

			if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
				return null;
			}

			return [ $type, get_url( $url, $callback, [ 'expiration' => DAY_IN_SECONDS ] ) ];
		},
		apply_filters( 'block_blog_list_get_secondary_postdata', $remote_links, $post )
	), 1, 0 );

	return array_merge( $post, array_filter( $remote_data ) );
}

/**
 * Check an argument to make sure it's valid for a remote request.
 *
 * @param mixed $value The value of the argument.
 * @param string $name Name of the argument.
 *
 * @return bool
 */
function validate_remote_arg( $value, $name ) : bool {
	$allowed_args = [
		'author',
		'categories',
		'categories_exclude',
		'order',
		'orderby',
		'per_page',
	];

	if ( ! in_array( $name, $allowed_args, true ) ) {
		return false;
	}
	if ( $value === null ) {
		return false; // Nothing uses a null value.
	}
	switch ( $name ) {
		case 'author':
			return is_numeric( $value );
		case 'per_page':
			// Set a reasonable limit on posts returned.
			return is_numeric( $value ) && ( intval( $value ) < apply_filters( 'block_blog_list_max_posts',
						MAX_POSTS ) );
		case 'categories':
		case 'categories_exclude':
			if ( is_string( $value ) ) {
				$cats = explode( ',', $value );
			} elseif ( is_numeric( $value ) ) {
				$cats = [ intval( $value ) ];
			} elseif ( is_array( $value ) ) {
				$cats = $value;
			} else {
				return false; // Can't be valid if not among the above types.
			}

			return ! in_array( false, filter_var_array( $cats, FILTER_VALIDATE_INT ), true );
		case 'order':
			return in_array( strtolower( $value ), [ 'asc', 'desc' ], true );
		case 'orderby':
			return in_array( strtolower( $value ), [
				'author',
				'date',
				'id',
				'include',
				'modified',
				'parent',
				'relevance',
				'slug',
				'include_slugs',
				'title',
			], true );
		default:
			// By filtering out non-acceptable arguments we should never get here, but better safe than sorry.
			return false;
	}
}

/**
 * Generic function to handle REST requests.
 *
 * This will retrieve the data from $url, call $callback with the returned
 * data as the only argument, and then save the result of $callback into a
 * transient keyed to $url. If a transient already exists for $url, it will
 * skip all processing and simply return the stored value.
 *
 * $args supports the following:
 *
 * - 'expiration' - Time in seconds to store the transient. Automatic anti-
 *   stampede functionality will increase this value by a random number of
 *   seconds, up to a full minute.
 *
 * @param string $url URL to send request to.
 * @param callable $callback Function that will process the result.
 * @param array $args Arguments modifying behavior.
 *
 * @return array
 * @throws \Exception
 */
function get_url( string $url, callable $callback, array $args ) : array {
	[
		'expiration' => $expiration,
	] = $args;
	$key = get_transient_name_from_url( $url );
	$data = get_transient( $key );
	if ( $data ) {
		return $data;
	}

	$response = wp_remote_get( $url );

	if ( is_wp_error( $response ) ) {
		error_log( $response->get_error_message() );
		set_failed_transient( $key );

		return [];
	}

	try {
		$decoded_response = json_decode( wp_remote_retrieve_body( $response ), true, 256, JSON_THROW_ON_ERROR );
	} catch ( \JsonException $exception ) {
		error_log( $exception->getMessage() );
		set_failed_transient( $key );

		return [];
	}

	$processed = $callback( $decoded_response );
	if ( ! is_array( $processed ) ) {
		set_failed_transient( $key );
	}

	set_transient(
		$key,
		$processed,
		// This is a minor effort to avoid a cache stampede.
		intval( $expiration ) + random_int( 1, 120 )
	);

	return $processed;
}

/**
 * Stores a "failed" type value in the specified transient.
 *
 * When using transients to store remote data, sometimes the remote
 * process may fail, or the local processing callback may fail. In this
 * case we don't want to store *no* value, because if we do then the
 * server will just continue trying to set the transient every time
 * it's called, potentially overwhelming (or at least hammering)
 * the remote server when the remote server may well be failing.
 * Instead, we set the transient in question to a non-empty value
 * that all consuming code should have reasonable contingencies for,
 * but add a short expiration so that the data will soon be re-evaluated.
 *
 * @param string $key Transient to set as "failed."
 *
 * @return void
 */
function set_failed_transient( $key ) {
	set_transient( $key, [], apply_filters( 'block_blog_list_failure_wait', MINUTE_IN_SECONDS, $key ) );
	error_log( sprintf( 'Process to generate data for "%s" transient failed.', $key ) );
}
