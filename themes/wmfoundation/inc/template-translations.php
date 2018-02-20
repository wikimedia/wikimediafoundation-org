<?php
/**
 * Custom template tags to handle translation for this theme.
 *
 * @package wmfoundation
 */

/**
 * Gets a formatted array of available translations.
 *
 * @return mixed array|bool
 */
function wmf_get_translations() {
	$mlp_language_api = apply_filters( 'mlp_language_api', null ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

	if ( ! is_a( $mlp_language_api, 'Mlp_Language_Api_Interface' ) ) {
		return false;
	}

	$args = array(
		'strict'       => true,
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
			'selected' => $translation->get_target_site_id() === get_current_blog_id(),
			'site_id'  => $translation->get_target_site_id(),
			'name'     => $translation->get_language()->get_name(),
			'uri'      => $translation->get_remote_url(),
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
