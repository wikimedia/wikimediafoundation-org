<?php
/**
 * Block pattern for "external link" columns
 */

namespace WMF\Editor\Patterns\LinkColumns;

const NAME = 'shiro/link-columns';

const PATTERN = <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg class="icon-open external-link__icon"><use href="http://wikimediafoundation.test/wp-content/themes/shiro/assets/dist/icons.svg#open"></use></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg class="icon-open external-link__icon"><use href="http://wikimediafoundation.test/wp-content/themes/shiro/assets/dist/icons.svg#open"></use></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg class="icon-open external-link__icon"><use href="http://wikimediafoundation.test/wp-content/themes/shiro/assets/dist/icons.svg#open"></use></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg class="icon-open external-link__icon"><use href="http://wikimediafoundation.test/wp-content/themes/shiro/assets/dist/icons.svg#open"></use></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
