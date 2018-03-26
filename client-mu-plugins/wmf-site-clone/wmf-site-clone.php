<?php
/**
 * Includes callback for the mlp_duplicated_blog action to handle instantiating the site clone logic.
 *
 * @package wmf-site-clone
 */

use WMFClone\Site;

add_action( 'mlp_duplicated_blog', 'wmf_mlp_duplicated_blog' );
/**
 * Triggers the logic to add the new translation status to all posts.
 *
 * @param array $context The site context.
 */
function wmf_mlp_duplicated_blog( $context ) {

	require_once dirname( __FILE__ ) . '/inc/classes/class-site.php';

	$site_clone = new Site();

	switch_to_blog( $context['new_blog_id'] );

	$site_clone->register_translation_status_terms();
	$site_clone->set_posts();
	$site_clone->add_term();

	restore_current_blog();
}
