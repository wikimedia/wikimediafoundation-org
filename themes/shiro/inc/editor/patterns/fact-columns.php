<?php
/**
 * Block pattern for the numbered "facts" columns.
 */

namespace WMF\Editor\Patterns\FactColumns;

const NAME = 'shiro/fact-columns';

const PATTERN = <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">1</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-h3"} -->
<h2 class="is-style-h3">Wikimedia projects belong to everyone</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">You made it. It is yours to use. For free. That <a href="https://google.com">means</a> you can use it, adapt it, or share what you find on Wikimedia sites. Just <a href="https://google.com">do not write your own bio</a>, or copy/paste it into your homework.</p>
<!-- /wp:paragraph -->

<!-- wp:group -->
<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">2</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-h3"} -->
<h2 class="is-style-h3">We respect your data and privacy</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">We do not sell your email address or any of your personal information to third parties. More information about our privacy practices are available at the <a href="https://google.com">Wikimedia Foundation privacy policy</a>, <a href="https://google.com">donor privacy policy</a>, <a href="https://google.com">and data retention guidelines</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">3</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-h3"} -->
<h2 class="is-style-h3">People like you keep Wikipedia accurate</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Readers <a href="https://google.com">verify the facts</a>. Articles are collaboratively created and edited by a community of volunteers using <a href="https://google.com">reliable sources</a>, so no single person or company owns a Wikipedia article. The Wikimedia Foundation does not write or edit, but <a href="https://google.com">you and everyone you know can help</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:paragraph {"className":"is-style-h1"} -->
<p class="is-style-h1">4</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-h3"} -->
<h2 class="is-style-h3">Not all wikis are Wikimedia</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-default","fontSize":"small"} -->
<p class="is-style-default has-small-font-size">The word “<a href="https://google.com">wiki</a>” refers to a website built using collaborative editing software. Projects with no past or existing affiliation with Wikipedia or the Wikimedia Foundation, such as Wikileaks and wikiHow, also use the term. Although these sites also use "wiki" in their name, they have nothing to do with Wikimedia.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
