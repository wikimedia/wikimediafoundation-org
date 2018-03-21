<?php
/**
 * Sets up page intro.
 *
 * Event though this is "page" it can also be used on News Posts
 *
 * @package wmfoundation
 */

$template_args = array(
	'intro' => get_the_content(),
);
wmf_get_template_part( 'template-parts/modules/intro/page', $template_args );
