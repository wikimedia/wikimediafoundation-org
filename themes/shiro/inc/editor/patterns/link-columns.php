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
<div class="wp-block-shiro-external-link external-link"></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/external-link -->
<div class="wp-block-shiro-external-link external-link"></div>
<!-- /wp:shiro/external-link --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
