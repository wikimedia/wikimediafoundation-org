<?php

namespace WMF\Editor\Blocks\MailChimpSubscribe;

use WMF\Customizer\Connect;

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
	$action            = get_theme_mod( 'wmf_subscribe_action', Connect::defaults( 'wmf_subscribe_action' ) );
	$additional_fields = get_theme_mod( 'wmf_subscribe_additional_fields',
		Connect::defaults( 'wmf_subscribe_additional_fields' ) );
	$additional_fields = kses_input_fields( $additional_fields );

	/*
	 * This setting was misused for actual content, strip the current content on
	 * production. **Can be removed once the setting is correctly used with only
	 * fields**
	 */
	$additional_fields = str_replace( "This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site's privacy policy.", '', $additional_fields );

	$input_placeholder = empty( $block_attributes['inputPlaceholder'] ) ?
		__( 'Email address', 'shiro' ) :
		$block_attributes['inputPlaceholder'];

	$form_start = '<form action="' . esc_url( $action ) . '" method="POST" class="mailchimp-subscribe__form">';
	$form_end   = '</form>';
	$input_field = '<input' .
	               ' class="mailchimp-subscribe__input-field"' .
	               ' id="wmf-subscribe-input-email"' .
	               ' name="EMAIL"' .
	               ' placeholder="' . esc_attr( $input_placeholder ) . '"' .
	               ' required=""' .
	               ' type="email" />';

	$content = str_replace( '<!-- input_field -->', $input_field, $content );

	return $form_start . $content . $additional_fields . $form_end;
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
