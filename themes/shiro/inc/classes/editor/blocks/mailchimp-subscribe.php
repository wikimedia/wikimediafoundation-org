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
	$action = get_theme_mod( 'wmf_subscribe_action', 'https://wikimediafoundation.us11.list-manage.com/subscribe/post?u=7e010456c3e448b30d8703345&amp;id=246cd15c56' );
	$additional_fields = get_theme_mod( 'wmf_subscribe_additional_fields', '<input type="hidden" value="2" name="group[4037]" id="mce-group[4037]-4037-1">' );
	$additional_fields = kses_input_fields( $additional_fields );

	$form_start = '<form action="' . esc_attr( $action ) . '" method="POST">';
	$form_end   = '</form>';
	$input_field = '<input' .
	               ' class="mailchimp-subscribe__input-field"' .
	               ' id="wmf-subscribe-input-email"' .
	               ' name="EMAIL"' .
	               ' placeholder="' . __( 'Email address', 'shiro' ) . '"' .
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
