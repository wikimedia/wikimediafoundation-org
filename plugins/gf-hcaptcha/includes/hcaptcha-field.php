<?php
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class hCaptcha_Field extends GF_Field {

	/**
	 * @var string $type The field type.
	 */

	public $type = 'hcaptcha';

	/**
	 * Return the field title, for use in the form editor.
	 *
	 * @return string
	 */

	public function get_form_editor_field_title() {
		return esc_attr__( 'hCaptcha', 'gf-hcaptcha' );
	}

	/**
	 * Assign the field button to the Advanced Fields group.
	 *
	 * @return array
	 */

	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * The settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */

	function get_form_editor_field_settings() {
		return array(
			'label_setting',
			'description_setting',
			'css_class_setting',
			'admin_label_setting',
			'label_placement_setting',
			'hcaptcha_theme',
			'hcaptcha_size',
			'hcaptcha_lang',
			'hcaptcha_sitekey',
			'hcaptcha_modus',
			'hcaptcha_compat'
		);
	}

	/**
     * Sets icon for field editor
	 */
	public function get_form_editor_field_icon() {
		return plugin_dir_url( __DIR__ ) . 'img/hcaptcha.svg';
	}

	/**
	 * Enable this field for use with conditional logic.
	 *
	 * @return bool
	 */

	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * The scripts to be included in the form editor.
	 *
	 * @return string
	 */

	public function get_form_editor_inline_script_on_page_render() {

		// Set the default field label
		$script = sprintf( "function SetDefaultValues_hcaptcha(field) { field.label = '%s'; field.isRequired = true }", $this->get_form_editor_field_title() ) . PHP_EOL;
		
		// Initialize the fields settings
		$script .= "jQuery(document).bind('gform_load_field_settings', function (event, field, form) {" . 
				   "var hcaptcha_sitekey = field.hcaptcha_sitekey == undefined ? '' : field.hcaptcha_sitekey;" .
				   "var hcaptcha_theme = field.hcaptcha_theme == undefined ? '' : field.hcaptcha_theme;" .
				   "var hcaptcha_size = field.hcaptcha_size == undefined ? '' : field.hcaptcha_size;" .
				   "var hcaptcha_lang = field.hcaptcha_lang == undefined ? 'default' : field.hcaptcha_lang;" .
				   "var hcaptcha_modus = field.hcaptcha_modus == undefined ? '' : field.hcaptcha_modus;" .
				   "var hcaptcha_compat = field.hcaptcha_compat == undefined ? '' : field.hcaptcha_compat;" .
				   "jQuery('#hcaptcha_sitekey').val(hcaptcha_sitekey);" .
				   "if(jQuery('#hcaptcha_theme_light').val() == hcaptcha_theme || !hcaptcha_theme) { jQuery('#hcaptcha_theme_light').attr('checked', true) };" .
				   "if(jQuery('#hcaptcha_theme_dark').val() == hcaptcha_theme) { jQuery('#hcaptcha_theme_dark').attr('checked', true) };" .
				   "if(jQuery('#hcaptcha_size_standard').val() == hcaptcha_size || !hcaptcha_size) { jQuery('#hcaptcha_size_standard').attr('checked', true) };" .
				   "if(jQuery('#hcaptcha_size_compact').val() == hcaptcha_size) { jQuery('#hcaptcha_size_compact').attr('checked', true) };" .
				   "jQuery('#hcaptcha_lang_selector').val(hcaptcha_lang);" .
				   "if(jQuery('#hcaptcha_modus_visible').val() == hcaptcha_modus || !hcaptcha_modus) { jQuery('#hcaptcha_modus_visible').attr('checked', true) };" .
				   "if(jQuery('#hcaptcha_modus_invisible').val() == hcaptcha_modus) { jQuery('#hcaptcha_modus_invisible').attr('checked', true) };" .
				   "if(hcaptcha_compat) { jQuery('#hcaptcha_compat_disable').attr('checked', true) };" .
				   "});" . PHP_EOL;
				   
		// Saving the hCaptcha settings
		$script .= "function SethCaptchaSitekey(value) {SetFieldProperty('hcaptcha_sitekey', value);}" . PHP_EOL;
		$script .= "function SethCaptchaTheme(value) {SetFieldProperty('hcaptcha_theme', value);}" . PHP_EOL;
		$script .= "function SethCaptchaSize(value) {SetFieldProperty('hcaptcha_size', value);}" . PHP_EOL;
		$script .= "function SethCaptchaLang(value) {SetFieldProperty('hcaptcha_lang', value);}" . PHP_EOL;
		$script .= "function SethCaptchaModus(value) {SetFieldProperty('hcaptcha_modus', value);}" . PHP_EOL;
		$script .= "function SethCaptchaCompat(value) {SetFieldProperty('hcaptcha_compat', value);}" . PHP_EOL;
		return $script;
	}

	/**
	 * Define the fields inner markup.
	 *
	 * @param array $form The Form Object currently being processed.
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array $entry Null or the Entry Object currently being edited.
	 *
	 * @return string
	 */

	public function get_field_input( $form, $value = '', $entry = null ) {
		$id              = absint( $this->id );
		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		if(!$is_form_editor) {

			// Prepare the value of the input ID attribute
			$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "hCaptcha_$id" : 'hCaptcha_' . $form_id . "_$id";
			$value = esc_attr( $value );

			// Prepare the hCaptcha classes
			$size         = $this->size;
			$class_suffix = $is_entry_detail ? '_admin' : '';
			$class        = $size . $class_suffix;

			// If got sitekey from the field or global
			if($this->hcaptcha_sitekey || get_option( 'gravityformsaddon_hcaptcha_settings' )[ 'hcaptcha-sitekey' ]) {

				// Load API in footer
				add_action( 'wp_footer', function() {
					wp_enqueue_script( 'gf-hcaptcha-script', 'https://js.hcaptcha.com/1/api.js', NULL, NULL, true );
					if( $this->hcaptcha_modus === 'invisible' ) wp_add_inline_script( 'gf-hcaptcha-script', 'function hCaptcha_onSubmit_' . $this->formId . '(token) {document.getElementById("gform_' . $this->formId . '").submit();};' );
				} );

				add_action( 'wp_footer', array( $this, 'ensure_hcaptcha_js' ), 21 );
				add_action( 'gform_preview_footer', array( $this, 'ensure_hcaptcha_js' ), 21 );

				// Replace all inline scripts to footer
				add_filter( 'gform_init_scripts_footer', '__return_true' );
			}

			// Prepare the hCaptcha attributes.
			$hcaptcha_theme		= $this->hcaptcha_theme ? ' data-theme="' . $this->hcaptcha_theme .'"' : '';
			$hcaptcha_size		= $this->hcaptcha_size ? ' data-size="' . $this->hcaptcha_size .'"' : '';
			$hcaptcha_lang		= $this->hcaptcha_lang && $this->hcaptcha_lang !== 'default' ? ' data-hl="' . $this->hcaptcha_lang .'"' : '';
			$hcaptcha_modus		= $this->hcaptcha_modus ? $this->hcaptcha_modus : 'visible';
			$hcaptcha_sitekey	= $this->hcaptcha_sitekey ? ' data-sitekey="' . $this->hcaptcha_sitekey . '"' : ' data-sitekey="' . get_option( 'gravityformsaddon_hcaptcha_settings' )[ 'hcaptcha-sitekey' ] . '"';
			$hcaptcha_render	= $this->hcaptcha_modus === 'visible' ? ' data-render="explicit"' : '';
			$hcaptcha_compat	= $this->hcaptcha_compat ? ' data-recaptchacompat="off"' : '';

			if( $hcaptcha_modus === 'invisible' ) {

				// Set attributes and classes to submit button current form
				add_filter( 'gform_submit_button_' . $form_id, function( $button, $form ) {
					$dom = new DOMDocument();
					$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $button );
					$input = $dom->getElementsByTagName( 'input' )->item( 0 );

					// Avoid duplicates, if same form is multiple times on same page
					if( ! $input->getAttribute( 'data-sitekey' ) ) {

						// Set class
						$input->setAttribute( 'class', $input->getAttribute( 'class' ) . " h-captcha" );

						// Set sitekey attribute
						$input->setAttribute( 'data-sitekey', ( $this->hcaptcha_sitekey ? $this->hcaptcha_sitekey : get_option( 'gravityformsaddon_hcaptcha_settings' )[ 'hcaptcha-sitekey' ] ) );

						// Set compat attribute
						if( $this->hcaptcha_compat ) $input->setAttribute( 'data-recaptchacompat', 'off' );

						// Set language attribute
						if( $this->hcaptcha_lang && $this->hcaptcha_lang !== 'default' ) $input->setAttribute( 'data-hl', $this->hcaptcha_lang );

						// Set callback attribute
						$input->setAttribute( 'data-callback', "hCaptcha_onSubmit_" . $form[ 'id' ] );
					}

					// Return the submit button
					return $dom->saveHtml( $input );
				}, 10, 2 );

				// Add hidden field to validate required state
				return "<input type='hidden' name='input_$id' value='true'>";
			} else {

				// Prepare the output for this field.
				$input = "<div id='{$field_id}' class='h-captcha {$class}'{$hcaptcha_sitekey}{$hcaptcha_compat}{$hcaptcha_render}{$hcaptcha_theme}{$hcaptcha_size}{$hcaptcha_lang}></div>";
				
				// Add hCaptcha container and hidden field to validate required state
				return sprintf( "<div class='ginput_container ginput_container_%s'><input type='hidden' name='input_$id' value='true'>%s</div>", $this->type, $input );
			}
		} else {

			// Show placeholder for form editor
			return '<div><img src="' . plugin_dir_url( __DIR__ ) . 'img/hcaptcha_logo.svg' . '" width="150"></div>';
		}
	}

	public function ensure_hcaptcha_js(){
		?>
		<script type="text/javascript">
			( function( $ ) {
				$( document ).bind( 'gform_post_render', function() {
					$( '.h-captcha' ).each( function( index, elem ) {
						if( ! $( elem ).html().length ) {
							hcaptcha.render( elem );
						}
					} );
				} );
			} )( jQuery );
		</script>

		<?php
	}

	// Call the validation function
	public function validate( $value, $form ) {
		$this->validate_hcaptcha( $form );
	}

	// Validate the hCaptcha field
	public function validate_hcaptcha( $form ) {
		$response_token 	= sanitize_text_field( rgpost( 'h-captcha-response' ) );
		$is_valid 			= $this->verify_hcaptcha_response( $response_token );

		if ( ! $is_valid ) {
			$this->failed_validation  = true;
			$this->validation_message = empty( $this->errorMessage ) ? __( 'The hCaptcha is invalid. Please try again.', 'gf-hcaptcha' ) : $this->errorMessage;
		}

	}

	// Verify the hCaptcha response
	public function verify_hcaptcha_response( $response, $secret_key = null ) {
		$verify_url = 'https://hcaptcha.com/siteverify';

		if ( $secret_key == null ) {
			$secret_key = get_option( 'gravityformsaddon_hcaptcha_settings' )[ 'hcaptcha-secretkey' ];
		}

		// Pass secret key and token for verification of whether the response was valid
		$response = wp_remote_post( $verify_url, array(
			'method' => 'POST',
			'body'   => array(
				'secret'   => $secret_key,
				'response' => $response
			),
		) );

		// Return success, on WP Error log error
		if ( ! is_wp_error( $response ) ) {
			$result = json_decode( wp_remote_retrieve_body( $response ) );

			return $result->success == true;
		} else {
			GFCommon::log_debug( __METHOD__ . '(): Validating the hCaptcha response has failed due to the following: ' . $response->get_error_message() );
		}

		return false;
	}
}

// Register the field
GF_Fields::register( new hCaptcha_Field() );