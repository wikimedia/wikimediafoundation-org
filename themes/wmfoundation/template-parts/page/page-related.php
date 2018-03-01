<?php
/**
 * Pulls in data for list template
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'related_pages', true );

wmf_get_template_part( 'template-parts/modules/related/pages', $template_args );
