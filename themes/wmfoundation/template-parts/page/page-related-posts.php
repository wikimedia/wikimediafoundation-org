<?php
/**
 * Get related posts using Jetpack
 *
 * @package wmfoundation
 */

$related_posts = wmf_get_related_posts( get_the_ID() );

wmf_get_template_part( 'template-parts/modules/related/posts', $related_posts );


