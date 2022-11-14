<?php
/**
 * Block pattern for a full-wdth group containing a header and two recent posts.
 */

namespace WMF\Editor\Patterns\BlogList;

const NAME = 'shiro/blog-list';

const PATTERN = <<<CONTENT
<!-- wp:group {"align":"full","backgroundColor":"base90"} -->
<div class="wp-block-group alignfull has-base-90-background-color has-background"><div class="wp-block-group__inner-container">
<!-- wp:heading -->
<h2>The latest news from Wikimedia Foundation</h2>
<!-- /wp:heading -->

<!-- wp:shiro/blog-list /-->
</div></div>
<!-- /wp:group -->
CONTENT;
