<?php

namespace WMF\Editor\Blocks\MailChimpSubscribe;

/**
 * Bootstrap hooks for the mailchimp block
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register' );
}

/**
 * Render mailchimp subscribe block. This is required because wp_kses_post will
 * strip <form> and <input> tags. For the mailchimp subscribe we need those
 * tags. Other than these changes, this is de facto a static block.
 */
function render_block( $block_attributes, $content ) {
	$default_action = 'https://wikimediafoundation.us11.list-manage.com/subscribe/post?u=7e010456c3e448b30d8703345&amp;id=246cd15c56';
	$default_additional_fields = '<input id="mce-group[4037]-4037-1" name="group[4037]" type="hidden" value="2" />';

	$action = ( $block_attributes['action'] ?? $default_action ) ?: $default_action;
	$additional_fields = ( $block_attributes['additional_fields'] ?? $default_additional_fields ) ?: $default_additional_fields;

	$form_start = '<form action="' . esc_attr( $action ) . '" method="POST">';
	$form_end   = '</form>';

	$content = str_replace( '<!-- additional_fields -->', $additional_fields, $content );
	$content = str_replace( '<!-- form_start -->', $form_start, $content );
	$content = str_replace( '<!-- form_end -->', $form_end, $content );

	return $content;
}

/**
 * Register the mailchimp block.
 */
function register() {
	register_block_type( 'shiro/mailchimp-subscribe', array(
		'apiVersion' => 2,
		'render_callback' => __NAMESPACE__ . '\\render_block'
	) );
}

/**
 * Strip all HTML except input fields.
 *
 * @param string $input_fields Input fields as set in the post editor.
 *
 * @return string Input fields without other HTML.
 */
function kses_input_fields( string $input_fields ): string {
	return wp_kses(
		$input_fields,
		array(
			'input'  => array(
				'type'        => array(),
				'name'        => array(),
				'id'          => array(),
				'class'       => array(),
				'required'    => array(),
				'value'       => array(),
				'checked'     => array(),
				'placeholder' => array(),
			),
			'label'  => array(
				'for'   => array(),
				'class' => array(),
			),
			'select' => array(
				'name'     => array(),
				'id'       => array(),
				'class'    => array(),
				'required' => array(),
			),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
		)
	);
}
