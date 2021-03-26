<?php
/**
 * Block patterns for content editors to quickly create content.
 */

namespace WMF\Editor\Patterns;

/**
 * Hook into WordPress
 */
function bootstrap() {
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_pattern' );
}

function register_pattern() {
	register_block_pattern_category( 'wikimedia-columns', [
		'label' => __( 'Wikimedia columns', 'shiro' ),
	] );

	register_block_pattern( 'shiro/fact-columns', [
		'title' => __( 'Numbered fact columns' ),
		'categories' => [ 'wikimedia-columns' ],
		'content' => <<<CONTENT
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
CONTENT,
	] );

	register_block_pattern( 'shiro/tweet-columns', [
		'title' => __( 'Tweet this columns', 'shiro' ),
		'categories' => [ 'wikimedia-columns' ],
		'content' => <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"accent90"} -->
<div class="wp-block-group has-accent-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
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
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"accent90"} -->
<div class="wp-block-group has-accent-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
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
<div class="wp-block-column"><!-- wp:group {"backgroundColor":"accent90"} -->
<div class="wp-block-group has-accent-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
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

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group -->
<div class="wp-block-group"><div class="wp-block-group__inner-container"></div></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT,
	] );

	register_block_pattern( 'shiro/card-columns', [
		'title' => __( 'Cards' ),
		'categories' => [ 'wikimedia-columns' ],
		'content' => <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/card {"imageId":0} -->
<div class="wp-block-shiro-card content-card click-to-call-to-action"><div class="content-card__contents"><h2 class="content-card__heading is-style-h3">Research</h2><p class="content-card__body has-small-font-size">We conduct our own research and partner with researchers worldwide to address change in society and technology.</p><a class="content-card__call-to-action call-to-action" href="https://wikimediafoundation.org/">More about research</a></div><img class="size-image_16x9_small content-card__image" src="https://s.w.org/images/core/5.3/MtBlanc1.jpg"/></div>
<!-- /wp:shiro/card --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/card {"imageId":0} -->
<div class="wp-block-shiro-card content-card click-to-call-to-action"><div class="content-card__contents"><h2 class="content-card__heading is-style-h3">Technology</h2><p class="content-card__body has-small-font-size">From site reliability to machine learning, our open-source technology makes Wikipedia faster, more reliable, and more accessible worldwide.</p><a class="content-card__call-to-action call-to-action" href="https://wikimediafoundation.org/">More about technology</a></div><img class="size-image_16x9_small content-card__image" src="https://s.w.org/images/core/5.3/Sediment_off_the_Yucatan_Peninsula.jpg"/></div>
<!-- /wp:shiro/card --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/card {"imageId":0} -->
<div class="wp-block-shiro-card content-card click-to-call-to-action"><div class="content-card__contents"><h2 class="content-card__heading is-style-h3">Advocacy</h2><p class="content-card__body has-small-font-size">We conduct our own research and partner with researchers worldwide to address change in society and technology.</p><a class="content-card__call-to-action call-to-action" href="https://wikimediafoundation.org/">More about advocacy</a></div><img class="size-image_16x9_small content-card__image" src="https://s.w.org/images/core/5.3/Windbuchencom.jpg"/></div>
<!-- /wp:shiro/card --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT,
	] );
}
