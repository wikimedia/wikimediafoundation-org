<?php
/**
 * Sets up page specific CTA.
 *
 * Event though this is "page" it can also be used on News Posts
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'page_cta', true );
wmf_get_template_part( 'template-parts/modules/cta/page', $template_args );
