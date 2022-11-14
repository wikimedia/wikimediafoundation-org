<?php
/**
 * Define shortcodes for section with quotes from the internet.
 * Author: Hang Do Thi Duc
 *
 * @package shiro
 */

/**
 * Define a [quotes_section] shortcode that renders tweets container with nested [quote_box].
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_quotes_section_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'quotes_section' );
	$content = do_shortcode( $content );

	// exclude p tag to avoid empty ones
	$allowed_tags = [ 'span' => [ 'class' => [], 'style' => [] ], 'img' => [ 'src' => [], 'height' => [], 'width' => [], 'alt' => [], 'style' => [], 'class' => [], 'style' => [] ], 'em' => [], 'strong' => [], 'a' => [ 'href' => [], 'class' => [], 'title' => [], 'rel' => [], 'target' => [] ], 'h3' => [ 'class' => [], 'style' => [] ], 'div' => [ 'class' => [] ] ];

	ob_start();
	?>

	<div class="mw-980 mod-margin-bottom <?php echo esc_attr( $atts['class'] ) ?>">
		<?php if ( !empty( $atts['title'] ) ) { ?>
			<h2><?php echo esc_html( $atts['title'] ) ?></h2>
		<?php } ?>
		<div class="quotes-section-container">
			<div class="flex flex-medium flex-wrap flex-space-between">
				<?php echo wp_kses( $content, $allowed_tags ) ?>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'quotes_section', 'wmf_quotes_section_callback' );

/**
 * Define a [quote_box] shortcode that renders 1 tweet that readers can tweet.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_quote_box_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'count' => '3',
		'uri' => '',
		'class' => '',
		'author' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'quote_box' );
	static $index = 0;
	$auto_tweet_width = 3 === (int)$atts['count'] ? 'w-32p' : 'w-48p';
	$index++;

	ob_start();
	?>

	<a href="<?php echo esc_url( $atts['uri'] ); ?>" class="quotes-section-inner rounded shadow mar-bottom_lg color-blue <?php echo esc_attr( $auto_tweet_width . ' unit-' . $index . ' ' . $atts['class'] ) ; ?>" target="_blank">
		<div class="quotes-section-text-wrap">
			<h3 class="quotes-section mar-bottom p"><?php echo wp_kses_post( $content ) ?></h3>
		</div>
		<?php if ( !empty( $atts['author'] ) ) { ?>
			<div class="social-share social-share-home">
				<span class="inline-social-list">
					— <?php echo esc_html( $atts['author'] ) ?>
				</span>
			</div>
		<?php } ?>
	</a>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'quote_box', 'wmf_quote_box_callback' );
