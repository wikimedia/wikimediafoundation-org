<?php
/**
 * Sets up Offsite Links module.
 *
 * Even though this is "page" it can also be used on News Posts
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'off_site_links', true );
wmf_get_template_part( 'template-parts/modules/links/off-site-links', $template_args );
