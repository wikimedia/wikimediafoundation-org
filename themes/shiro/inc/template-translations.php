<?php
/**
 * Custom template tags to handle translation for this theme.
 *
 * @package shiro
 */

// Actions and filters.
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\WordpressContext;
use function Inpsyde\MultilingualPress\resolve;

add_filter( 'fm_element_markup_end', array( 'WMF\Translations\Metaboxes', 'fm_element_markup_end' ), 10, 2 );
add_filter( 'admin_init', array( 'WMF\Roles\Base', 'callback' ), 10, 2 );
add_filter( 'register_post_type_args', array( 'WMF\Roles\Base', 'post_type_args_filter' ), 10, 2 );
add_action( 'restrict_manage_posts', array( 'WMF\Translations\Edit_Posts', 'restrict_manage_posts' ), 10, 2 );
add_action( 'post_submitbox_misc_actions', array( 'WMF\Translations\Flow', 'publish_actions_callback' ) );
add_action( 'save_post', array( 'WMF\Translations\Flow', 'save_post_callback' ), 99 );
add_action( 'init', array( 'WMF\Translations\Flow', 'register_custom_meta' ) );
add_filter( 'manage_edit-post_columns', array( 'WMF\Translations\Notice', 'cpt_columns' ) );
add_filter( 'manage_edit-page_columns', array( 'WMF\Translations\Notice', 'cpt_columns' ) );
add_filter( 'manage_edit-profile_columns', array( 'WMF\Translations\Notice', 'cpt_columns' ) );
add_action( 'manage_post_posts_custom_column', array( 'WMF\Translations\Notice', 'cpt_column' ), 10, 2 );
add_action( 'manage_page_posts_custom_column', array( 'WMF\Translations\Notice', 'cpt_column' ), 10, 2 );
add_action( 'manage_profile_posts_custom_column', array( 'WMF\Translations\Notice', 'cpt_column' ), 10, 2 );

/**
 * Copy post meta to remote site if the option is set in the translation metabox.
 */
function wmf_copy_post_meta( $keysToSync, $context, $request ) {

	$multilingualpress = $request->bodyValue(
		'multilingualpress',
		INPUT_POST,
		FILTER_DEFAULT,
		FILTER_FORCE_ARRAY
	);
	$remote_site_id    = $context->remoteSiteId();
	$remote_post_id    = $context->remotePostId();

	switch_to_blog( $remote_site_id );

	// String post meta.
	$string_post_meta = [
		'page_template',
		'sub_title',
		'page_intro',
		'featured_post_sub_title',
		'landing_page_sidebar_menu_label',
	];

	// Array post meta
	$array_meta_keys = [
		'connect',
		'contact_links',
		'featured_on',
		'featured_post',
		'featured_profile',
		'focus_blocks',
		'framing_copy',
		'intro_button',
		'list',
		'listings',
		'off_site_links',
		'page_cta',
		'page_facts',
		'page_header_background',
		'profiles',
		'projects_module',
		'related_pages',
		'share_links',
		'sidebar_facts',
		'social_share',
		'stats_featured',
		'stats_graph',
		'stats_plain',
		'stats_profiles',
		'stories',
	];

	foreach ( $multilingualpress as $translationMetabox ) {
		if ( $translationMetabox['remote-content-copy'] === '1' ) {
			foreach ( $string_post_meta as $meta_key ) {
				$meta_value = (string) $request->bodyValue(
					$meta_key,
					INPUT_POST,
					FILTER_SANITIZE_STRING
				);
				update_post_meta( $remote_post_id, $meta_key, $meta_value );
			}

			$connected_user_value = (int) $request->bodyValue(
				'connected_user',
				INPUT_POST,
				FILTER_SANITIZE_NUMBER_INT
			);
			update_post_meta( $remote_post_id, 'connected_user', $connected_user_value );

			foreach ( $array_meta_keys as $meta_key ) {
				// get post meta value from source site
				$meta_value = $request->bodyValue(
					$meta_key,
					INPUT_POST,
					FILTER_DEFAULT,
					FILTER_FORCE_ARRAY
				);

				// switch to remote sites and save post meta
				update_post_meta( $remote_post_id, $meta_key, $meta_value );
			}

			restore_current_blog();
		}
	}

	return $keysToSync;
}
add_filter('multilingualpress.sync_post_meta_keys', 'wmf_copy_post_meta', 10, 3 );

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
	$translations     = wmf_multilingualpress_get_translations();
	$ret_translations = array();

	if ( empty( $translations ) ) {
		return $ret_translations;
	}

	/**
	 * From MultilingualPress.
	 *
	 * @type (Inpsyde\MultilingualPress\Framework\Api\Translation) $translation
	 */
	foreach ( $translations as $translation ) {
		$translation_args = array(
			'selected'   => $translation->remoteSiteId() === get_current_blog_id(),
			'site_id'    => $translation->remoteSiteId(),
			'name'       => $translation->language()->name(),
			'shortname'  => $translation->language()->isoCode(),
			'uri'        => $translation->remoteUrl(),
			'content_id' => $translation->remoteContentId(),
			'is_rtl'     => $translation->language()->isRtl(),
		);

		// Default English is now presented as "English (United States)", convert it to just "English".
		if ( 'en' === $translation_args['shortname'] ) {
			$translation_args['name'] = 'English';
		}

		if ( $translation->remoteSiteId() === get_current_blog_id() ) {
			// Ensure active is returned as first element always.
			array_unshift( $ret_translations, $translation_args );
			continue;
		}

		$ret_translations[] = $translation_args;
	}

	return $ret_translations;
}

/**
 * Get possible translations.
 *
 * @return mixed
 */
function wmf_multilingualpress_get_translations() {
	if ( ! class_exists( TranslationSearchArgs::class ) ) {
		return false;
	}
	$args = TranslationSearchArgs::forContext( new WordpressContext() )->forSiteId( get_current_blog_id() )->includeBase();

	return resolve(
		\Inpsyde\MultilingualPress\Framework\Api\Translations::class
	)->searchTranslations( $args );
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

	if ( empty( $translations ) ) {
		return false;
	}

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

	$alert = get_theme_mod( 'wmf_incomplete_translation', __( 'This content has not yet been translated into the current language.', 'shiro-admin' ) );

	printf( '<div class="alert alert-warning" role="alert">%s</div>', esc_html( $alert ) );
}
