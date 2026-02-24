<?php
/**
 * Pulls in data for list template
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'list', true );

wmf_get_template_part( 'template-parts/modules/list/list', $template_args );
