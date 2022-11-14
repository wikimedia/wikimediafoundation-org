<?php
/**
 * Block pattern for converting from the "report-landing" template
 */

namespace WMF\Editor\Patterns\TemplateReportSection;

const NAME = 'shiro/template-report-section';

function pattern(): string {
	return <<<CONTENT
<!-- wp:shiro/toc-columns -->
<div class="wp-block-shiro-toc-columns"><!-- wp:columns {"className":"toc__section"} -->
<div class="wp-block-columns toc__section"><!-- wp:column {"width":"30%","className":"toc__sidebar"} -->
<div class="wp-block-column toc__sidebar" style="flex-basis:30%"><!-- wp:shiro/toc {"headingBlocks":[{"clientId":"25e75f66-8912-4c9a-b8b2-c3a9f6d47cec","name":"core/heading","isValid":true,"attributes":{"content":"Heading 1","level":2,"previousContent":"Heading 1","anchor":"a1-heading-1"},"innerBlocks":[]},{"clientId":"099f4505-d4f0-4fb5-b51e-927b26acde90","name":"core/heading","isValid":true,"attributes":{"content":"Heading 2","level":2,"previousContent":"Heading 2","anchor":"a2-heading-2"},"innerBlocks":[]}]} -->
<nav class="toc-nav" data-backdrop="inactive" data-dropdown="toc-nav" data-dropdown-content=".toc" data-dropdown-status="uninitialized" data-dropdown-toggle=".toc__button" data-sticky="false" data-toggleable="yes" data-trap="inactive" data-visible="false"><h2 class="toc__title screen-reader-text">Table of Contents</h2><button aria-expanded="false" class="toc__button" hidden><span class="btn-label-a11y">Navigate within this page.</span><span class="btn-label-active-item">Heading 1</span></button><ul class="wp-block-shiro-toc table-of-contents toc"><li class="toc__item"><a class="toc__link" href="#a1-heading-1">Heading 1</a></li><li class="toc__item"><a class="toc__link" href="#a2-heading-2">Heading 2</a></li></ul></nav>
<!-- /wp:shiro/toc --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"70%","className":"toc__content"} -->
<div class="wp-block-column toc__content" style="flex-basis:70%"><!-- wp:heading -->
<h2 id="a1-heading-1">Heading 1</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 id="a2-heading-2">Heading 2</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Purus sit amet volutpat consequat mauris. Sagittis orci a scelerisque purus semper eget duis at. Eget arcu dictum varius duis at consectetur lorem donec massa. Velit dignissim sodales ut eu sem integer vitae justo. Gravida in fermentum et sollicitudin ac orci phasellus. Quam elementum pulvinar etiam non quam lacus.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:shiro/toc-columns -->
CONTENT;
}
