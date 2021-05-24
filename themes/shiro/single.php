<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package shiro
 */

get_header();
while ( have_posts() ) {
	the_post();
	$intro       = get_post_meta( get_the_ID(), 'page_intro', true );
	$parent_page = get_option( 'page_for_posts' );
    $allowed_tags         = [ 'span' => [ 'class' => [], 'style' => [] ], 'img' => [ 'src' => [], 'height' => [], 'width' => [], 'alt' => [], 'style' => [], 'class' => [] ], 'em' => [], 'strong' => [], 'a' => [ 'href' => [], 'class' => [], 'title' => [], 'rel' => [] ], 'p' => [], 'br' => [] ];

	get_template_part(
		'template-parts/header/page',
		'single',
		array(
			'h4_link'   => get_the_permalink( $parent_page ),
			'h4_title'  => get_the_title( $parent_page ),
			'h1_title'  => get_the_title(),
			'page_meta' => sprintf( '<span>%s</span><span class="separator">&bull;</span><time datetime="%s">%s</time>', wmf_byline(), get_the_date( 'c' ), get_the_date() ),
		)
	);

	get_template_part(
		'template-parts/thumbnail',
		'full',
		array(
			'inner_image' => get_post_thumbnail_id(),
		)
	);

	$has_read_more_categories = has_block( 'shiro/read-more-categories' );
	$has_social_share         = has_block( 'shiro/share-article' );
	?>

	<?php if ( ! empty( $intro ) ) : ?>
	<div class="article-title">
		<?php echo wp_kses( $intro, $allowed_tags ); ?>
	</div>
	<?php endif; ?>

	<article class="mw-784 wysiwyg">
		<?php the_content(); ?>

		<?php
			if ( ! $has_social_share ) {
				echo \WMF\Editor\Blocks\ShareArticle\render_block( [
					'enableTwitter'  => true,
					'enableFacebook' => true,
				] );
			}

			if ( ! $has_read_more_categories ) {
				echo \WMF\Editor\Blocks\ReadMoreCategories\render_block( [] );
			}
		?>
	</article>

	<?php
}

$has_blog_list = has_block( 'shiro/blog-list' );
$has_connect   = has_block( 'shiro/mailchimp-subscribe' ) || has_block( 'shiro/contact' );

$modules = array(
	'profile',
	'offsite-links',
	'cta',
	$has_blog_list ? false : 'related-posts',
	'support',
	$has_connect ? false : 'connect',
);

$modules = array_filter( $modules );

foreach ( $modules as $module ) {
	get_template_part( 'template-parts/page/page', $module );
}
get_footer();
