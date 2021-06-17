<?php
/**
 * Block pattern for "external link" columns
 */

namespace WMF\Editor\Patterns\LinkColumns;

const NAME = 'shiro/link-columns';

/**
 * Get the pattern content.
 *
 * Returned as a function because we need to dynamically generate the sprite
 * path for the specific environment.
 *
 * @return string
 */
const PATTERN = <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="external-link__icon"><path d="M0 0h24v24H0z" fill="none"></path><path d="M19 19H5V5h7V3H5a2 2 0 00-2 2v14a2 2 0 002 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"></path></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="external-link__icon"><path d="M0 0h24v24H0z" fill="none"></path><path d="M19 19H5V5h7V3H5a2 2 0 00-2 2v14a2 2 0 002 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"></path></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="external-link__icon"><path d="M0 0h24v24H0z" fill="none"></path><path d="M19 19H5V5h7V3H5a2 2 0 00-2 2v14a2 2 0 002 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"></path></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"><p class="external-link__heading"><a class="external-link__link"><span class="external-link__heading-text"></span><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="external-link__icon"><path d="M0 0h24v24H0z" fill="none"></path><path d="M19 19H5V5h7V3H5a2 2 0 00-2 2v14a2 2 0 002 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"></path></svg></a></p><p class="external-link__text"></p></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
