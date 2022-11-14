<?php
/**
 * Template Name: Redirect Page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 * 
 * This template does not output any content of its own, and should get no special
 * handling or metaboxes within the admin. Instead, when rendered it will redirect
 * to its most-recently-published child page without appearing in site breadcrumbs.
 *
 * This is most useful for the semiannual Transparency Report, the most recent of
 * which should always be accessible at a predictable URL.
 */

$newest_child_uri = wmf_get_most_recent_child_page_uri( get_the_ID() );
wp_safe_redirect( $newest_child_uri );
exit;
