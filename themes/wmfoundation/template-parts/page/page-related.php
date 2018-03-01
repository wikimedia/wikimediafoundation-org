<?php
/**
 * Pulls in data for list template
 *
 * @package wmfoundation
 */

$template_args               = get_post_meta( get_the_ID(), 'related_pages', true );
$template_args['preheading'] = get_theme_mod( 'wmf_related_pages_pre_heading', __( 'Related', 'wmfoundation' ) );

wmf_get_template_part( 'template-parts/modules/related/pages', $template_args );
