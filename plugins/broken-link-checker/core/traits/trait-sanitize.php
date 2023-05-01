<?php
/**
 * Wrapper class for sanitizing input.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Traits
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Traits;

// Abort if called directly.
use function is_bool;
use function is_numeric;
use function strip_tags;

defined( 'WPINC' ) || die;

/**
 * Class Sanitize
 *
 * @package WPMUDEV_BLC\Core\Traits
 */
trait Sanitize {
	/**
	 * Sanitize an array.
	 *
	 * @param array $options The options to sanitize.
	 *
	 * @return array Returns the sanitized array.
	 * @since 1.0.0
	 */
	protected function sanitize_array( array $options = array() ) {
		if ( ! is_array( $options ) ) {
			return $this->sanitize_single( $options );
		}

		$sanitized_options = array();

		foreach ( $options as $key => $value ) {
			$sanitized_options[ sanitize_key( $key ) ] = is_array( $value ) ? $this->sanitize_array( $value ) : $this->sanitize_single( $value );
		}

		return $sanitized_options;
	}

	/**
	 * Sanitize an array.
	 *
	 * @param string|int|bool|float $input The option to sanitize.
	 *
	 * @return string|int|bool|float Returns the sanitized value.
	 * @since 2.0.0
	 */
	protected function sanitize_single( $input = '' ) {
		if ( ! \is_null( $input ) && ! \is_array( $input ) && ! \is_object( $input ) ) {
			if ( $this->has_email_format( $input ) ) {
				$input = filter_var( $input, FILTER_SANITIZE_EMAIL );
			} elseif ( preg_match( '/\R/', $input ) ) {
				$input = sanitize_textarea_field( $input );
			} elseif ( wp_strip_all_tags( $input ) !== $input ) {
				$input = wp_kses_post( $input );
			} elseif ( ! is_numeric( $input ) && ! is_bool( $input ) ) {
				$input = sanitize_text_field( $input );
			}
		}

		return $input;
	}

	/**
	 * Checks the format of input if it looks like an email. It doesn't validate against forbidden characters.
	 *
	 * @param string $input The email address.
	 *
	 * @return bool
	 */
	protected function has_email_format( $input ) {
		return ( preg_match( '/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $input ) || ! preg_match( '/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/', $input ) ) ? false : true;
	}
}
