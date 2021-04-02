<?php
/**
 * Sets up page specific CTA.
 *
 * Event though this is "page" it can also be used on News Posts
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'page_cta', true );
get_template_part( 'template-parts/modules/cta/page', null, $template_args );
