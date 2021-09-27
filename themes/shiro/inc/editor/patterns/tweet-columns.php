<?php
/**
 * Block pattern for the "Tweet this" columns.
 */

namespace WMF\Editor\Patterns\TweetColumns;

use function WMF\Editor\get_admin_post;

const NAME = 'shiro/tweet-columns';

/**
 * Get the pattern content.
 *
 * Returned as a function because we need to dynamically generate a URL to use
 * as the tweet URL. If we don't the tweet-this block will always fail block
 * validation. That would show the user the invalid content warning.
 *
 * @return string
 */
function pattern() {
	$post_id = get_admin_post() ?? false;
	$permalink = $post_id ? get_permalink( $post_id ) : get_home_url();
	$permalink_encoded = urlencode( $permalink );

	return <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns is-style-align-buttons-bottom tweet-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90","className":"has-border-radius has-radius-big"} -->
<div class="wp-block-group has-border-radius has-radius-big has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">200,000+</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Editors contribute to Wikimedia projects every month</strong></p>
<!-- /wp:paragraph -->

<!-- wp:shiro/tweet-this {"tweetText":"200,000+ editors contribute to Wikimedia projects every month", "tweetUrl":"$permalink"} -->
<a href="https://twitter.com/intent/tweet?text=200%2C000%2B%20editors%20contribute%20to%20Wikimedia%20projects%20every%20month $permalink_encoded" class="tweet-this wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue">Tweet this</a>
<!-- /wp:shiro/tweet-this --></div></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90","className":"has-border-radius has-radius-big"} -->
<div class="wp-block-group has-border-radius has-radius-big has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">68+ million</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Media files on Wikimedia Commons</strong></p>
<!-- /wp:paragraph -->

<!-- wp:shiro/tweet-this {"tweetText":"68+ million media files on Wikimedia Commons", "tweetUrl":"$permalink"} -->
<a href="https://twitter.com/intent/tweet?text=68%2B%20million%20media%20files%20on%20Wikimedia%20Commons $permalink_encoded" class="tweet-this wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue">Tweet this</a>
<!-- /wp:shiro/tweet-this --></div></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90","className":"has-border-radius has-radius-big"} -->
<div class="wp-block-group has-border-radius has-radius-big has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">1+ billion</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Unique devices access Wikimedia projects every month</strong></p>
<!-- /wp:paragraph -->

<!-- wp:shiro/tweet-this {"tweetText":"1+ billion unique devices access Wikimedia projects every month", "tweetUrl":"$permalink"} -->
<a href="https://twitter.com/intent/tweet?text=1%2B%20billion%20unique%20devices%20access%20Wikimedia%20projects%20every%20month $permalink_encoded" class="tweet-this wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue">Tweet this</a>
<!-- /wp:shiro/tweet-this --></div></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
}
