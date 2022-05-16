<?php

/**
 * Register the content-language taxonomy.
 *
 * This taxonomy is used to describe the language of content in posts, etc.
 *
 * @return void
 */
function wmf_register_content_language_taxonomy(): void {
	$language_type_args = array(
		'heirarchical' => false,
		'show_in_rest' => true,
		'rewrite' => false,
		'label' => __( 'Content Language', 'shiro-admin' ),
	);
	register_taxonomy('content-language', apply_filters( 'wmf_content_language_post_types', [ 'post' ] ), $language_type_args );
}

/**
 * Get the slug (i.e. the locale code) for the main language of the current site.
 *
 * In practice, this will always be US English, but this is better than
 * hard-coding that.
 *
 * @return string
 * @throws \Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable
 */
function wmf_get_current_content_language_slug(): string {
	return \Inpsyde\MultilingualPress\siteLocale( get_current_blog_id() );
}

/**
 * Get the term corresponding to the main language, or null if none exists.
 *
 * @return \WP_Term|null
 * @throws \Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable
 */
function wmf_get_current_content_language_term(): ?WP_Term {
	$term = get_term_by('slug', wmf_get_current_content_language_slug(), 'content-language' );
	if ( ! is_a( $term, WP_Term::class ) ) {
		return null;
	}

	return $term;
}

/**
 * Get the term corresponding to the main language, creating it if it doesn't exist.
 *
 * Returns null if the term cannot be found and cannot be created.
 *
 * @return \WP_Term|null
 * @throws \Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable
 */
function wmf_get_and_maybe_create_current_language_term(): ?WP_Term {
	$term = wmf_get_current_content_language_term();
	if ( $term === null ) {
		$term = wmf_create_current_language_term();
	}

	return $term;
}

/**
 * Create a content-language term for the site's current main language.
 *
 * @return \WP_Term|null
 * @throws \Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable
 */
function wmf_create_current_language_term(): ?WP_Term {
	// Make sure that we always have the "main language" term.
	$main_locale = wmf_get_current_content_language_term();
	if ( ! is_a( $main_locale, WP_Term::class ) ) {
		$result = wp_create_term( wmf_get_current_content_language_slug(), 'content-language' );
		if ( is_wp_error( $result ) ) {
			return null;
		}
		return get_term( $result['term_id'], 'content-language' );
	}
	return $main_locale;
}

/**
 * Add the default language term if there is not already a content-language set.
 *
 * Primarily this is used as a hook on wp_insert_post--you usually won't be
 * calling it manually.
 *
 * @param int $post_ID
 *
 * @return void
 * @throws \Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable
 */
function wmf_add_default_content_language( int $post_ID ): void {
	$languages = wp_get_post_terms( $post_ID, 'content-language' );
	if ( is_wp_error( $languages ) ) {
		/*
		 * The `content-language` taxonomy doesn't exist. We can't recover
		 * from this kind of error, so bail early.
		 */
		return;
	}
	$main_locale = wmf_get_and_maybe_create_current_language_term();
	if ( $main_locale === null ) {
		/*
		 * The current language term doesn't exist and can't be created, which
		 * is not an error we can recover from.
		 */
		return;
	}
	if ( count( $languages ) === 0 ) {
		// No languages chosen, so set the default.
		wp_set_post_terms( $post_ID, array( $main_locale->term_id ), 'content-language' );
	}
}

add_action( 'wp_insert_post', 'wmf_add_default_content_language' );
add_action( 'admin_init', 'wmf_create_current_language_term' );
add_action( 'init', 'wmf_register_content_language_taxonomy' );
