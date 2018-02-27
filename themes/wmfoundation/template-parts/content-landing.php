<?php
/**
 * Template part for displaying landing page content in page-landing.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

$modules = array(
	'intro',
	'social',
	'framing-copy',
	'cta',
	'facts',
	'connect',
	// Todo: add profile module here.
	'listings',
	'featured-posts',
	'offsite-links',
	'support',
);

wmf_get_template_part( 'template-parts/page/modules', $modules );
