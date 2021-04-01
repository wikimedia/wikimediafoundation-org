<?php
/**
 * Block pattern for 3-up cards with call to action in each.
 */

namespace WMF\Editor\Patterns\CardColumns;

const NAME = 'shiro/card-columns';

const PATTERN = <<<CONTENT
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/card {"imageId":0} -->
<div class="wp-block-shiro-card content-card click-to-call-to-action"><div class="content-card__contents"><h2 class="content-card__heading is-style-h3">Research</h2><p class="content-card__body has-small-font-size">We conduct our own research and partner with researchers worldwide to address change in society and technology.</p><a class="content-card__call-to-action call-to-action" href="https://wikimediafoundation.org/">More about research</a></div><img class="size-image_16x9_small content-card__image" src="https://s.w.org/images/core/5.3/MtBlanc1.jpg" height="338" width="600"/></div>
<!-- /wp:shiro/card --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/card {"imageId":0} -->
<div class="wp-block-shiro-card content-card click-to-call-to-action"><div class="content-card__contents"><h2 class="content-card__heading is-style-h3">Technology</h2><p class="content-card__body has-small-font-size">From site reliability to machine learning, our open-source technology makes Wikipedia faster, more reliable, and more accessible worldwide.</p><a class="content-card__call-to-action call-to-action" href="https://wikimediafoundation.org/">More about technology</a></div><img class="size-image_16x9_small content-card__image" src="https://s.w.org/images/core/5.3/Sediment_off_the_Yucatan_Peninsula.jpg" height="338" width="600"/></div>
<!-- /wp:shiro/card --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shiro/card {"imageId":0} -->
<div class="wp-block-shiro-card content-card click-to-call-to-action"><div class="content-card__contents"><h2 class="content-card__heading is-style-h3">Advocacy</h2><p class="content-card__body has-small-font-size">We conduct our own research and partner with researchers worldwide to address change in society and technology.</p><a class="content-card__call-to-action call-to-action" href="https://wikimediafoundation.org/">More about advocacy</a></div><img class="size-image_16x9_small content-card__image" src="https://s.w.org/images/core/5.3/Windbuchencom.jpg" height="338" width="600"/></div>
<!-- /wp:shiro/card --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
CONTENT;
