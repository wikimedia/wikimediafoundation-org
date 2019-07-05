<?php
/**
 * Pulls in data for list template
 *
 * @package shiro
 */

$template_args               = get_post_meta( get_the_ID(), 'related_pages', true );
$template_args['preheading'] = get_theme_mod( 'wmf_related_pages_pre_heading', __( 'Related', 'shiro' ) );

$rand_translation                        = wmf_get_random_translation( 'wmf_related_pages_pre_heading' );
$template_args['rand_translation_title'] = ! empty( $rand_translation ) ? $rand_translation : '';

wmf_get_template_part( 'template-parts/modules/related/pages', $template_args );
