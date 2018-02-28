<?php
/**
 * Sets up page projects module.
 *
 * Used on the Home Page template.
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'projects_module', true );
wmf_get_template_part( 'template-parts/modules/projects/projects', $template_args );
