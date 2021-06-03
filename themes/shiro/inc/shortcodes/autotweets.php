<?php
/**
 * Define shortcodes for section with prewritten tweets, links to Twitter.
 * Author: Hang Do Thi Duc
 *
 * @package shiro
 */

/**
 * Define a [autotweets] shortcode that renders tweets container with nested [autotweet].
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_autotweets_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'autotweets' );
	$content = do_shortcode( $content );

	// exclude p tag to avoid empty ones
	$allowed_tags = [ 'span' => [ 'class' => [], 'style' => [] ], 'img' => [ 'src' => [], 'height' => [], 'width' => [], 'alt' => [], 'style' => [], 'class' => [], 'style' => [] ], 'em' => [], 'strong' => [], 'a' => [ 'href' => [], 'class' => [], 'title' => [], 'rel' => [], 'target' => [] ], 'h3' => [ 'class' => [], 'style' => [] ], 'div' => [ 'class' => [] ] ];

	ob_start();
	?>

	<div class="mw-980 mod-margin-bottom <?php echo esc_attr( $atts['class'] ) ?>">
		<?php if ( !empty( $atts['title'] ) ) { ?>
			<h2><?php echo esc_html( $atts['title'] ) ?></h2>
		<?php } ?>
		<div class="auto-tweet-container">
			<div class="flex flex-medium flex-wrap flex-space-between">
				<?php echo wp_kses( $content, $allowed_tags ) ?>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'autotweets', 'wmf_autotweets_callback' );

/**
 * Define a [autotweet] shortcode that renders 1 tweet that readers can tweet.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_autotweet_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'count' => '3',
		'uri' => '',
		'hashtag' => '',
		'empty' => '0',
	];
	$atts = shortcode_atts( $defaults, $atts, 'autotweet' );
	static $index = 0;
	$auto_tweet_width = 3 === (int)$atts['count'] ? 'w-32p' : 'w-48p';
	$index++;

	$share_text    = get_theme_mod( 'wmf_tweet_this_copy', __( 'Tweet this', 'shiro-admin' ) );
	$args = array(
		'uri' => $atts['uri']  . '&hashtags=' . $atts['hashtag'],
		'message' => $atts['empty'] === '0' ? wp_strip_all_tags( $content ) : '',
	);

	ob_start();
	?>

	<a href="<?php echo esc_url( wmf_get_share_url( 'twitter', $args ) ); ?>" class="auto-tweet-inner rounded shadow <?php echo esc_attr( $auto_tweet_width ); ?> mar-bottom_lg color-blue" target="_blank">
		<div class="auto-tweet-text-wrap">
			<h3 class="auto-tweet mar-bottom p"><?php echo wp_kses_post( $content ) ?></h3>
		</div>
		<div class="social-share social-share-home">
			<span class="inline-social-list">
				<img class="icon" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/src/svg/individual/social-twitter-blue.svg' ); ?>">
				<?php echo esc_html( $share_text ); ?>
			</span>
		</div>
	</a>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'autotweet', 'wmf_autotweet_callback' );
