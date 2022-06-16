<?php
/**
 * Block pattern for converting from the "report-landing" template
 */

namespace WMF\Editor\Patterns\TemplateReportLanding;

const NAME = 'shiro/template-report-landing';

function pattern(): string {
	return <<<CONTENT
<!-- wp:shiro/report-landing-hero -->
<div class="wp-block-shiro-report-landing-hero hero is-style-base90"><header class="hero-report__header"><div class="hero-report__text-column"><small class="hero-report__kicker"></small><h1 class="hero-report__title"></h1></div><figure class="hero-report__image-container image-filter-inherit"><img alt="" class="hero-report__image"/></figure></header></div>
<!-- /wp:shiro/report-landing-hero -->

<!-- wp:shiro/linked-toc-columns -->
<div class="wp-block-shiro-linked-toc-columns"></div>
<!-- /wp:shiro/linked-toc-columns -->
CONTENT;
}
