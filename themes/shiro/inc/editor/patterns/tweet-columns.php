<?php
/**
 * Block pattern for the "Tweet this" columns.
 */

namespace WMF\Editor\Patterns\TweetColumns;

const NAME = 'shiro/tweet-columns';

const PATTERN = <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90"} -->
<div class="wp-block-group has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">200,000+</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Editors contribute to Wikimedia projects every month</strong></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-tertiary has-icon has-icon-social-twitter-blue"} -->
<div class="wp-block-button is-style-tertiary has-icon has-icon-social-twitter-blue"><a class="wp-block-button__link">Tweet this</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90"} -->
<div class="wp-block-group has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">68+ million</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Media files on Wikimedia Commons</strong></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-tertiary has-icon has-icon-social-twitter-blue"} -->
<div class="wp-block-button is-style-tertiary has-icon has-icon-social-twitter-blue"><a class="wp-block-button__link">Tweet this</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90"} -->
<div class="wp-block-group has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">1+ billion</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Unique devices access Wikimedia projects every month</strong></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-tertiary has-icon has-icon-social-twitter-blue"} -->
<div class="wp-block-button is-style-tertiary has-icon has-icon-social-twitter-blue"><a class="wp-block-button__link">Tweet this</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
