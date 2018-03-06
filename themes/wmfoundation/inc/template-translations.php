<?php
/**
 * Custom template tags to handle translation for this theme.
 *
 * @package wmfoundation
 */

// Actions and filters.
add_action( 'mlp_translation_meta_box_bottom', array( 'WMF\Translations\Metaboxes', 'mlp_translation_meta_box_bottom' ), 10, 3 );
add_filter( 'fm_element_markup_end', array( 'WMF\Translations\Metaboxes', 'fm_element_markup_end' ), 10, 2 );
add_filter( 'admin_init', array( 'WMF\Roles\Base', 'callback' ), 10, 2 );
add_filter( 'register_post_type_args', array( 'WMF\Roles\Base', 'post_type_args_filter' ), 10, 2 );

// Functions.
/**
 * Gets a formatted array of available translations.
 *
 * @param  bool $strict When TRUE (default) only sites with a matching translation for requested page will be included.
 * @return mixed array|bool
 */
function wmf_get_translations( $strict = true ) {
	$mlp_language_api = apply_filters( 'mlp_language_api', null ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

	if ( ! is_a( $mlp_language_api, 'Mlp_Language_Api_Interface' ) ) {
		return false;
	}

	$args = array(
		'strict'       => $strict,
		'include_base' => true,
	);

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
			$translation = get_post_meta( $content_id, $key, $args['single'] );
			break;
		case 'option':
			$translation = get_option( $key );
			break;
		case 'theme_mod':
			$translation = get_theme_mod( $key );
			break;
		case 'cpt_label':
			$translation = get_post_type_object( $key )->label;
			break;
	}

	restore_current_blog();

	return $translation;
}
