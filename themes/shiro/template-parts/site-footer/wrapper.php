<?php
/**
 * Wrap the individual parts of the site footer.
 *
 * @package shiro
 */
?>
<footer class="site-footer">
	<div class="site-footer__inner">
		<?php get_template_part( 'template-parts/site-footer/description' ) ?>
		<?php get_template_part( 'template-parts/site-footer/callout-nav' ) ?>
		<?php get_template_part( 'template-parts/site-footer/navigation' ) ?>
		<?php get_template_part( 'template-parts/site-footer/legal' ) ?>
	</div>
</footer>
