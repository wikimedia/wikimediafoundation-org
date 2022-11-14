<?php
/**
 * Sets up page social.
 *
 * This is specific to the landing page template.
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'social_share', true );
get_template_part( 'template-parts/modules/social/page', null, $template_args );
