<?php

namespace WMF\Editor\Blocks\InputField;

/**
 * Bootstrap hooks for the input field
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register' );
}

/**
 * Render input field. This is required because wp_kses_post will <input> tags.
 * As this is a fairly simple block, the PHP contains all the contents.
 */
function render_block( $block_attributes, $content ) {
	$input_field = '<input' .
					' class="wp-block-shiro-input-field"' .
					' id="wmf-subscribe-input-email"' .
					' name="EMAIL"' .
					' placeholder="' . __( 'Email address', 'shiro' ) . '"' .
					' required=""' .
					' type="email" />';

	$content = str_replace( '<!-- input_field -->', $input_field, $content );

	return $content;
}

/**
 * Register the mailchimp block.
 */
function register() {
	register_block_type( 'shiro/input-field', array(
		'apiVersion' => 2,
		'render_callback' => __NAMESPACE__ . '\\render_block'
	) );
}
