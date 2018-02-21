<?php
/**
 * Handles text CTA wrapper and initialization for landing page.
 *
 * @package wmfoundation
 */

$text_ctas = get_post_meta( get_the_ID(), 'text_cta', true );

if ( empty( $text_ctas ) ) {
	return;
}
?>
<div class="flex flex-medium flex-wrap mw-1360 fifty-fifty mod-margin-bottom">
<?php
foreach ( $text_ctas as $text_cta ) {
	wmf_get_template_part( 'template-parts/modules/mu/text', $text_cta );
}
?>
</div>
