<?php
/**
 * Sets up Offsite Links module.
 *
 * Even though this is "page" it can also be used on News Posts
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'off_site_links', true );

if ( empty( $template_args ) ) {
	return;
}

$template_args['split'] = false;
$template               = is_singular( 'page' ) ? basename( get_page_template() ) : '';

if ( ( 'page-landing.php' === $template ) || ( 'page-report.php' === $template ) || ( 'page-data.php' === $template ) ) {
	$template_args['split'] = true;
}

get_template_part( 'template-parts/modules/links/off-site-links', null, $template_args );
