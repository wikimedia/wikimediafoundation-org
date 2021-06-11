<?php
/**
 * Block pattern for converting from the "default" template
 */

namespace WMF\Editor\Patterns\TemplateDefault;

const NAME = 'shiro/template-default';

function pattern(): string {
	$communicationModule = \WMF\Editor\Patterns\CommunicationModule\PATTERN;

	return <<<CONTENT
<!-- wp:shiro/spotlight {"className":"is-style-red90"} -->
<div class="wp-block-shiro-spotlight spotlight alignfull is-style-red90"><div class="spotlight__inner"><div class="spotlight__content"><h2 class="spotlight__heading is-style-h1"></h2><p class="spotlight__text"></p></div><figure class="spotlight__image-wrapper image-filter-inherit"></figure></div></div>
<!-- /wp:shiro/spotlight -->
$communicationModule
CONTENT;
}
