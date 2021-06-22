<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 */

get_header(); ?>

<?php

$posts_page = get_option( 'page_for_posts' );

$template_args = array(
	'h1_title' => get_the_archive_title(),
	'h4_link'  => get_permalink( $posts_page ),
	'h4_title' => get_the_title( $posts_page ),
);

get_template_part( 'template-parts/header/page-noimage', null, $template_args );

?>

<div class="w-100p news-list-container news-card-list mod-margin-bottom">
	<div class="mw-980">
		<?php if ( have_posts() ) : ?>
		<div class="blog-list">
			<?php
			while ( have_posts() ) :
				the_post();

				echo WMF\Editor\Blocks\BlogPost\render_block(
					[ 'post_id' => $post->ID ]
				);
			endwhile;
			?>
		</div>
			<?php
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>
</div>

<?php
if ( have_posts() ) :
	get_template_part( 'template-parts/pagination' );
endif;

get_footer();
