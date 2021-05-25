<?php
/**
 * Block pattern for populating a page migrated from the 'landing' template
 */

namespace WMF\Editor\Patterns\TemplateLanding;

use function WMF\Editor\Patterns\TweetColumns\pattern as tweet_columns_pattern;
use function WMF\Editor\Patterns\CommunicationModule\pattern as communication_module_pattern;

const NAME = 'shiro/template-landing';

/**
 * Get the pattern content.
 *
 * @return string
 */
function pattern() {
	$tweet_columns = tweet_columns_pattern();
	$communications_module = communication_module_pattern();

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

<!-- wp:shiro/spotlight {"className":"is-style-red90"} -->
<div class="wp-block-shiro-spotlight spotlight alignfull is-style-red90"><div class="spotlight__inner"><div class="spotlight__content"><h2 class="spotlight__heading is-style-h1"></h2><p class="spotlight__text"></p></div><figure class="spotlight__image-wrapper image-filter-inherit"></figure></div></div>
<!-- /wp:shiro/spotlight -->

$tweet_columns

$communications_module

<!-- wp:group {"align":"full","backgroundColor":"base80"} -->
<div class="wp-block-group alignfull has-base-80-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:shiro/double-heading /-->

<!-- wp:shiro/blog-list /--></div></div>
<!-- /wp:group -->

<!-- wp:shiro/double-heading /-->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg class="icon-open external-link__icon"><use href="http://wikimediafoundation.test/wp-content/themes/shiro/assets/dist/icons.svg#open"></use></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg class="icon-open external-link__icon"><use href="http://wikimediafoundation.test/wp-content/themes/shiro/assets/dist/icons.svg#open"></use></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
CONTENT;
}
