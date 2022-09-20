<?php

/*
 * This option will contain the UNIX timestamp of when the banner
 * was dismissed or the value 0 to indicate it should never be shown again.
 */
DEFINE( 'WIKIPEDIA_PREVIEW_BANNER_OPTION', 'wikipediapreview_banner_dismissed' );

function should_show_banner() {
	if ( ! is_admin() ) {
		// Only for admin site
		return false;
	}

	$default = -1;
	$value   = get_option( WIKIPEDIA_PREVIEW_BANNER_OPTION, $default );

	if ( $value === $default ) {
		// not dismissed yet
		return true;
	}

	if ( 0 === $value ) {
		// dismiss forever
		return false;
	}

	// remind later
	$days = ( time() - $value ) / ( 60 * 60 * 24 );
	return $days >= 7;
}

function review_banner() {
	if ( ! should_show_banner() ) {
		return;
	}

	$msg          = __( 'Love Wikipedia Preview? Help others discover it by leaving your rating on WordPress.', 'wikipedia-preview' );
	$rate_btn     = __( 'Rate Wikipedia Preview', 'wikipedia-preview' );
	$remind_btn   = __( 'Remind me later', 'wikipedia-preview' );
	$rate_url     = 'https://wordpress.org/support/plugin/wikipedia-preview/reviews/#new-post';
	$html         = <<<HTML
		<div class="notice notice-wikipediapreview notice-info is-dismissible">
			<p>{$msg}</p>
			<a href="{$rate_url}" target="_blank" class="button button-primary button-rate">{$rate_btn}</a>
			<button class="button button-secondary button-remind">{$remind_btn}</button>
		</div>
	HTML;
	$allowed_tags = array(
		'div'    => array( 'class' => array() ),
		'p'      => array(),
		'a'      => array(
			'class'  => array(),
			'href'   => array(),
			'target' => array(),
		),
		'button' => array( 'class' => array() ),
		'span'   => array( 'class' => array() ),
	);
	echo wp_kses( $html, $allowed_tags );
}

function review_banner_script() {
	if ( ! should_show_banner() ) {
		return;
	}

	$nonce = wp_create_nonce( 'wikipediapreview-banner-dismiss' );
	$html  = <<<HTML
		<script type='text/javascript'>
			jQuery( function( $ ) {
				$( '.notice-wikipediapreview' ).on(
					'click',
					'.button-rate, .notice-dismiss, .button-remind',
					function () {
						jQuery.post( ajaxurl, {
							_ajax_nonce: '{$nonce}',
							action: 'dismiss_review_banner',
							remind: $( this ).hasClass( 'button-remind' )
						} );
						$( '.notice-wikipediapreview' ).hide();
					}
				);
			} );
		</script>
	HTML;
	echo wp_kses( $html, array( 'script' => array( 'type' => array() ) ) );
}

function dismiss_review_banner() {
	check_ajax_referer( 'wikipediapreview-banner-dismiss' );
	$remind = isset( $_POST['remind'] ) ? sanitize_key( $_POST['remind'] ) : 'false';
	update_option(
		WIKIPEDIA_PREVIEW_BANNER_OPTION,
		'true' === $remind ? time() : 0
	);
	wp_die();
}

add_action( 'admin_notices', 'review_banner' );
add_action( 'admin_footer', 'review_banner_script' );
add_action( 'wp_ajax_dismiss_review_banner', 'dismiss_review_banner' );
