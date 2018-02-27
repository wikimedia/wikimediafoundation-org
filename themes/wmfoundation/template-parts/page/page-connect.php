<?php
/**
 * Sets up page Connect module.
 *
 * Event though this is "page" it can also be used on News Posts
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'connect', true );

if ( empty( $template_args['hide'] ) ) {
	wmf_get_template_part( 'template-parts/modules/general/connect', $template_args );
}
