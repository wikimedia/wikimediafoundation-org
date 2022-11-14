<?php
/**
 * Pulls in data for data page template
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'stats_featured', true );

get_template_part( 'template-parts/modules/data-vis/stats-featured', null, $template_args );
