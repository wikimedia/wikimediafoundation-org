<?php
/**
 * Block pattern for the newsletter & contact section.
 */

namespace WMF\Editor\Patterns\CommunicationModule;

const NAME = 'shiro/communication-module';

const PATTERN = <<<CONTENT
<!-- wp:group {"align":"wide"} -->
<div class="wp-block-group alignwide"><div class="wp-block-group__inner-container"><!-- wp:shiro/double-heading {"primaryHeading":"Stay up-to-date on our work.","secondaryHeadings":[{"text":"Connect","lang":"en","switchRtl":false},{"text":"Échanger","switchRtl":false,"lang":"fr"},{"text":"اتصل","switchRtl":true,"lang":"ar"},{"text":"連接","switchRtl":false,"lang":"zh"},{"text":"Связь","switchRtl":false,"lang":"ru"},{"text":"Conecta","switchRtl":false,"lang":"es"},{"text":"Verbindung","switchRtl":false,"lang":"de"}],"className":"is-style-align-wide-blog"} /-->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/mailchimp-subscribe -->
<div class="wp-block-shiro-mailchimp-subscribe mailchimp-subscribe"><svg width="1em" height="1em" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="i icon icon-mail"><path fillrule="evenodd" cliprule="evenodd" d="M2 2h16a2 2 0 012 2v2l-10 4L0 6V4a2 2 0 012-2zm0 16a2 2 0 01-2-2V8l10 4 10-4v8a2 2 0 01-2 2H2z" fill="#000"></path></svg><!-- wp:heading {"level":3} -->
<h3>Get email updates</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Subscribe to news about ongoing projects and initiatives</p>
<!-- /wp:paragraph --><div class="mailchimp-subscribe__input-container"><div class="mailchimp-subscribe__column-input"><!-- input_field --></div><div class="mailchimp-subscribe__column-button"><button class="wp-block-shiro-button" type="submit">Subscribe</button></div></div><p class="mailchimp-subscribe__description has-base-30-color has-text-color has-small-font-size">This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site’s privacy policy.</p></div>
<!-- /wp:shiro/mailchimp-subscribe --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/contact -->
<div class="wp-block-shiro-contact contact"><svg width="1em" height="1em" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="contact__icon"><path fillrule="evenodd" cliprule="evenodd" d="M14.5 5.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM2 16c0-2 2.083-5 8-5s8 3 8 5v3H2v-3z" fill="#000"></path></svg><h3 class="contact__title">Contact a human</h3><div class="contact__description">Questions about the Wikimedia Foundation or our projects? Get in touch with our team.</div><a class="contact__call-to-action" href="/about/contact/">Contact</a><h4 class="contact__social-title">Follow</h4><!-- wp:buttons -->
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
<!-- /wp:columns --></div></div>
<!-- /wp:group -->
CONTENT;
