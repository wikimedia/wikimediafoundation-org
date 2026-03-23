<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

?>


<article class="mw-900 mod-margin-bottom">
	<?php get_sidebar(); ?>
	<div class="article-main wysiwyg">
		<?php the_content(); ?>
	</div>
</article>
