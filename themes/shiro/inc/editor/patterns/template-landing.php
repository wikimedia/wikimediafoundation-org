<?php
/**
 * Block pattern for populating a page migrated from the 'landing' template
 */

namespace WMF\Editor\Patterns\TemplateLanding;

use function WMF\Editor\Patterns\TweetColumns\pattern as tweet_columns_pattern;
use WMF\Editor\Patterns\LinkColumns as LinkColumns;

const NAME = 'shiro/template-landing';

/**
 * Get the pattern content.
 *
 * @return string
 */
function pattern(): string {
	$tweet_columns         = tweet_columns_pattern();
	$external_links        = LinkColumns\PATTERN;
	$support_module        = wmf_get_reusable_block_module_insert( 'support' );
	$communications_module = wmf_get_reusable_block_module_insert( 'connect' );

	return <<<CONTENT
<!-- wp:shiro/landing-page-hero {"className":"is-style-yellow50"} -->
<div class="wp-block-shiro-landing-page-hero hero is-style-yellow50"><header class="hero__header"><div class="hero__text-column"><small class="hero__kicker"></small><h1 class="hero__title"></h1></div><figure class="hero__image-container image-filter-inherit"><img alt="" class="hero__image"/></figure></header><div class="hero__intro"><p></p></div></div>
<!-- /wp:shiro/landing-page-hero -->

<!-- wp:shiro/stairs -->
<div class="wp-block-shiro-stairs"><!-- wp:shiro/stair -->
<div class="wp-block-shiro-stair stair"><h2 class="stair__heading is-style-h3"></h2><p class="stair__body"></p></div>
<!-- /wp:shiro/stair -->

<!-- wp:shiro/stair -->
<div class="wp-block-shiro-stair stair"><h2 class="stair__heading is-style-h3"></h2><p class="stair__body"></p></div>
<!-- /wp:shiro/stair -->

<!-- wp:shiro/stair -->
<div class="wp-block-shiro-stair stair"><h2 class="stair__heading is-style-h3"></h2><p class="stair__body"></p></div>
<!-- /wp:shiro/stair -->

<!-- wp:shiro/stair -->
<div class="wp-block-shiro-stair stair"><h2 class="stair__heading is-style-h3"></h2><p class="stair__body"></p></div>
<!-- /wp:shiro/stair --></div>
<!-- /wp:shiro/stairs -->

$support_module

$tweet_columns

$communications_module

<!-- wp:group {"align":"full","backgroundColor":"base80"} -->
<div class="wp-block-group alignfull has-base-80-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:shiro/double-heading /-->

<!-- wp:shiro/blog-list /--></div></div>
<!-- /wp:group -->

<!-- wp:shiro/double-heading /-->

$external_links

CONTENT;
}
