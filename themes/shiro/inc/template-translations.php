<?php
/**
 * Custom template tags to handle translation for this theme.
 *
 * @package shiro
 */

// Actions and filters.
add_action( 'mlp_translation_meta_box_bottom', array( 'WMF\Translations\Metaboxes', 'mlp_translation_meta_box_bottom' ), 10, 3 );
add_filter( 'fm_element_markup_end', array( 'WMF\Translations\Metaboxes', 'fm_element_markup_end' ), 10, 2 );
add_filter( 'admin_init', array( 'WMF\Roles\Base', 'callback' ), 10, 2 );
add_filter( 'register_post_type_args', array( 'WMF\Roles\Base', 'post_type_args_filter' ), 10, 2 );
add_action( 'restrict_manage_posts', array( 'WMF\Translations\Edit_Posts', 'restrict_manage_posts' ), 10, 2 );
add_action( 'mlp_show_translation_completed_checkbox', array( 'WMF\Translations\Flow', 'publish_actions_callback' ) );
add_action( 'mlp_pre_save_post_meta', array( 'WMF\Translations\Flow', 'pre_post_meta_callback' ), 10, 2 );
add_action( 'save_post', array( 'WMF\Translations\Flow', 'save_post_callback' ), 99 );
add_filter( 'manage_edit-post_columns', array( 'WMF\Translations\Notice', 'cpt_columns' ) );
add_filter( 'manage_edit-page_columns', array( 'WMF\Translations\Notice', 'cpt_columns' ) );
add_filter( 'manage_edit-profile_columns', array( 'WMF\Translations\Notice', 'cpt_columns' ) );
add_action( 'manage_post_posts_custom_column', array( 'WMF\Translations\Notice', 'cpt_column' ), 10, 2 );
add_action( 'manage_page_posts_custom_column', array( 'WMF\Translations\Notice', 'cpt_column' ), 10, 2 );
add_action( 'manage_profile_posts_custom_column', array( 'WMF\Translations\Notice', 'cpt_column' ), 10, 2 );

/**
 * Conditionally outputs the translation in progress notice on the post editor.
 */
function wmf_progress_notice() {
	global $typenow, $pagenow, $post;

	if ( 'post.php' !== $pagenow || ! in_array( $typenow, array( 'post', 'page', 'profile' ), true ) || empty( $post->ID ) ) {
		return;
	}

	if ( ! wmf_is_main_site() ) {
		return;
	}

	$notice = new WMF\Translations\Notice( $post->ID );
	$notice->check_progress();
	$notice->maybe_show_notice();
}
add_action( 'admin_notices', 'wmf_progress_notice' );

// Functions.
/**
 * Gets a formatted array of available translations.
 *
 * @param  bool   $strict     When TRUE (default) only sites with a matching translation for requested page will be included.
 * @param  int    $content_id The ID for the content. e.g post ID.
 * @param  string $type       The type of content. Usually post.
 * @return mixed  array|bool
 */
function wmf_get_translations( $strict = true, $content_id = 0, $type = '' ) {
	$mlp_language_api = apply_filters( 'mlp_language_api', null ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

	if ( ! is_a( $mlp_language_api, 'Mlp_Language_Api_Interface' ) ) {
		return false;
	}

	$args = array(
		'strict'       => $strict,
		'include_base' => true,
	);

	if ( ! empty( $content_id ) ) {
		$args['content_id'] = $content_id;
	}

	if ( ! empty( $type ) ) {
		$args['type'] = $type;
	}

	/**
	 * From MultilingualPress.
	 *
	 * @var Mlp_Language_Api_Interface $mlp_language_api
	 */
	$translations = $mlp_language_api->get_translations( $args );

	if ( empty( $translations ) ) {
		return false;
	}

	$ret_translations = array();

	/**
	 * From MultilingualPress.
	 *
	 * @type Mlp_Translation_Interface $translation
	 */
	foreach ( $translations as $translation ) {
		$translation_args = array(
			'selected'   => $translation->get_target_site_id() === get_current_blog_id(),
			'site_id'    => $translation->get_target_site_id(),
			'name'       => $translation->get_language()->get_name(),
			'shortname'  => $translation->get_language()->get_name( 'language_short' ),
			'uri'        => $translation->get_remote_url(),
			'content_id' => $translation->get_target_content_id(),
		);

		if ( $translation->get_target_site_id() === get_current_blog_id() ) {
			// Ensure active is returned as first element always.
			array_unshift( $ret_translations, $translation_args );
			continue;
		}

		$ret_translations[] = $translation_args;
	}

	return $ret_translations;
}

/**
 * Gets a random translation from the provided key.
 *
 * @param string $key  The key to check in either meta, theme_mode, or option.
 * @param array  $args Additional arguments to control output. Set `sources to theme_mod (default), meta, or option.
 *
 * @return bool|mixed|string
 */
function wmf_get_random_translation( $key, $args = array() ) {
	$defaults = array(
		'source' => 'theme_mod',
		'single' => true,
	);

	$args         = wp_parse_args( $args, $defaults );
	$strict       = 'meta' === $args['source'] ? true : false;
	$translations = wmf_get_translations( $strict );

	if ( false === $translations ) {
		return false;
	}

	// Remove the first item because it is the current item.
	array_shift( $translations );

	$rand_key           = array_rand( $translations );
	$target_translation = $translations[ $rand_key ];
	$content_id         = $target_translation['content_id'];

	switch_to_blog( $target_translation['site_id'] );

	$translation = false;

	switch ( $args['source'] ) {
		case 'meta':
			$translation['content'] = get_post_meta( $content_id, $key, $args['single'] );
			break;
		case 'option':
			$translation['content'] = get_option( $key );
			break;
		case 'theme_mod':
			$translation['content'] = get_theme_mod( $key );
			break;
		case 'cpt_label':
			$translation['content'] = get_post_type_object( $key )->label;
			break;
	}
    
    $translation['lang'] = $target_translation['shortname'];

	restore_current_blog();

	return $translation;
}

/**
 * Displays alert if the translation was never completed for the current content.
 */
function wmf_translation_alert() {
	if ( wmf_is_main_site() ) {
		return; // Never show alert on main site.
	}

	if ( ! is_page() && ! is_single() && ! is_front_page() ) {
		return; // Can't check status of archives.
	}

	if ( get_post_meta( get_the_ID(), '_translation_complete', true ) ) {
		return; // This has been marked as translation complete, no alert to show.
	}

	$alert = get_theme_mod( 'wmf_incomplete_translation', __( 'This content has not yet been translated into the current language.', 'shiro' ) );

	printf( '<div class="alert alert-warning" role="alert">%s</div>', esc_html( $alert ) );
}
