<?php
/**
 * Sets up Support module.
 *
 * Even though this is "page" it can also be used on News Posts
 *
 * @package shiro
 */

$hide_support_module = get_post_meta( get_the_ID(), 'hide_support_module', true );
if ( empty( $hide_support_module ) ) {
	get_template_part( 'template-parts/modules/cta/support' );
}
