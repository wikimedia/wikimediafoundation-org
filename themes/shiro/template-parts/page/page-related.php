<?php
/**
 * Pulls in data for list template
 *
 * @package shiro
 */

$template_args               = get_post_meta( get_the_ID(), 'related_pages', true );
$template_args['preheading'] = get_theme_mod( 'wmf_related_pages_pre_heading', __( 'Related', 'shiro-admin' ) );

get_template_part( 'template-parts/modules/related/pages', null, $template_args );
