<?php
/**
 * Set up the profiles module
 *
 * Pull profiles meta and pass it along to template
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'profiles', true );
if ( ! empty( $template_args ) ) {
	wmf_get_template_part( 'template-parts/modules/profiles/list', $template_args );
}
