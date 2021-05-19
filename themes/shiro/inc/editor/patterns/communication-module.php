<?php
/**
 * Block pattern for the newsletter & contact section.
 */

namespace WMF\Editor\Patterns\CommunicationModule;

const NAME = 'shiro/communication-module';

function pattern(): string {
	$themeUri = get_stylesheet_directory_uri();

	return <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/mailchimp-subscribe -->
<div class="wp-block-shiro-mailchimp-subscribe mailchimp-subscribe"><svg class="i icon icon-mail"><use xlink:href="$themeUri/assets/dist/icons.svg#email" /></svg><!-- wp:heading {"level":3} -->
<h3>Get email updates</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Subscribe to news about ongoing projects and initiatives</p>
<!-- /wp:paragraph --><div class="mailchimp-subscribe__input-container"><div class="mailchimp-subscribe__column-input"><!-- input_field --></div><div class="mailchimp-subscribe__column-button"><button class="wp-block-shiro-button" type="submit">Subscribe</button></div></div><p class="mailchimp-subscribe__description has-base-30-color has-text-color has-small-font-size">This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site’s privacy policy.</p></div>
<!-- /wp:shiro/mailchimp-subscribe --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/contact -->
<div class="wp-block-shiro-contact contact"><svg class="icon-contact contact__icon"><use href="$themeUri/assets/dist/icons.svg#contact"></use></svg><h3 class="contact__title">Contact a human</h3><div class="contact__description">Questions about the Wikimedia Foundation or our projects? Get in touch with our team.</div><a class="contact__call-to-action" href="/about/contact">Contact</a><h4 class="contact__social-title">Follow</h4><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-as-link has-icon has-icon-social-facebook-blue"} -->
<div class="wp-block-button is-style-as-link has-icon has-icon-social-facebook-blue"><a class="wp-block-button__link" href="https://www.facebook.com/wikimediafoundation/" target="_blank" rel="noreferrer noopener">Facebook</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-as-link has-icon has-icon-social-twitter-blue"} -->
<div class="wp-block-button is-style-as-link has-icon has-icon-social-twitter-blue"><a class="wp-block-button__link" href="https://twitter.com/wikimedia" target="_blank" rel="noreferrer noopener">Twitter</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-as-link has-icon has-icon-social-instagram-blue"} -->
<div class="wp-block-button is-style-as-link has-icon has-icon-social-instagram-blue"><a class="wp-block-button__link" href="https://www.instagram.com/wikimediafoundation/" target="_blank" rel="noreferrer noopener">Instagram</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-as-link has-icon has-icon-social-linkedin-blue"} -->
<div class="wp-block-button is-style-as-link has-icon has-icon-social-linkedin-blue"><a class="wp-block-button__link" href="https://www.linkedin.com/company/wikimedia-foundation" target="_blank" rel="noreferrer noopener">LinkedIn</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:shiro/contact --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
}
