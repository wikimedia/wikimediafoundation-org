<?php
/**
 * Sets up page Connect module.
 *
 * Event though this is "page" it can also be used on News Posts
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'connect', true );

$rand_translation = wmf_get_random_translation(
	'connect', array(
		'source' => 'meta',
	)
);

$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

if ( empty( $template_args['hide'] ) ) {
	wmf_get_template_part( 'template-parts/modules/general/connect', $template_args );
}
