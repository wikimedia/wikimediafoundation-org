<?php
/**
 * Block pattern for populating a page migrated from the 'landing' template
 */

namespace WMF\Editor\Patterns\TemplateLanding;

const NAME = 'shiro/template-landing';

const PATTERN = <<<CONTENT
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

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
	<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90","className":"has-border-radius has-radius-big"} -->
		<div class="wp-block-group has-border-radius has-radius-big has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
				<p class="is-style-h1"></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph -->
				<p></p>
				<!-- /wp:paragraph -->

				<!-- wp:shiro/tweet-this {"tweetText":"","tweetUrl":""} -->
				<a href="https://twitter.com/intent/tweet?text=" class="tweet-this wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue">Tweet this</a>
				<!-- /wp:shiro/tweet-this --></div></div>
		<!-- /wp:group --></div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90","className":"has-border-radius has-radius-big"} -->
		<div class="wp-block-group has-border-radius has-radius-big has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
				<p class="is-style-h1"></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph -->
				<p></p>
				<!-- /wp:paragraph -->

				<!-- wp:shiro/tweet-this {"tweetText":"","tweetUrl":""} -->
				<a href="https://twitter.com/intent/tweet?text=" class="tweet-this wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue">Tweet this</a>
				<!-- /wp:shiro/tweet-this --></div></div>
		<!-- /wp:group --></div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:group {"backgroundColor":"blue90","className":"has-border-radius has-radius-big"} -->
		<div class="wp-block-group has-border-radius has-radius-big has-blue-90-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"className":"is-style-h1"} -->
				<p class="is-style-h1"></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph -->
				<p></p>
				<!-- /wp:paragraph -->

				<!-- wp:shiro/tweet-this {"tweetText":"","tweetUrl":""} -->
				<a href="https://twitter.com/intent/tweet?text=" class="tweet-this wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue">Tweet this</a>
				<!-- /wp:shiro/tweet-this --></div></div>
		<!-- /wp:group --></div>
	<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph -->
<p>[Double heading block]</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
	<div class="wp-block-column"><!-- wp:shiro/mailchimp-subscribe -->
		<div class="wp-block-shiro-mailchimp-subscribe mailchimp-subscribe"><svg class="i icon icon-mail"><use xlink:href="http://wikimediafoundation.test/wp-content/themes/shiro/assets/dist/icons.svg#email" /></svg><!-- wp:heading {"level":3} -->
			<h3>Get email updates</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Subscribe to news about ongoing projects and initiatives</p>
			<!-- /wp:paragraph --><div class="mailchimp-subscribe__input-container"><div class="mailchimp-subscribe__column-input"><!-- input_field --></div><div class="mailchimp-subscribe__column-button"><button class="wp-block-shiro-button" type="submit">Subscribe</button></div></div><p class="mailchimp-subscribe__description has-base-30-color has-text-color has-small-font-size">This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site’s privacy policy.</p></div>
		<!-- /wp:shiro/mailchimp-subscribe --></div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:paragraph -->
		<p>[Contact block]</p>
		<!-- /wp:paragraph --></div>
	<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"full","backgroundColor":"base80"} -->
<div class="wp-block-group alignfull has-base-80-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph -->
		<p>[Double heading block]</p>
		<!-- /wp:paragraph -->

		<!-- wp:shiro/blog-list /--></div></div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p>[Double heading block]</p>
<!-- /wp:paragraph -->

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
