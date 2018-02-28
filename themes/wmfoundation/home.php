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
 * @package wmfoundation
 */

get_header();

$post_id = get_option( 'page_for_posts' );
$featured_post_id = get_post_meta( $post_id, 'featured_post', true );

$featured_categories = get_post_meta( $post_id, 'featured_categories', true );

?>

<?php
$template_args = array(
	'h4_title' => get_the_archive_title(),
	'h4_link'  => '#',
);

wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );

?>

<div class="w-100p cta mod-margin-bottom cta-secondary cta-news no-duotone">
	<div class="mw-1360">

	<?php
	$post = get_post( $featured_post_id );
	if ( ! empty( $post ) ) {
		setup_postdata( $post );
		wmf_get_template_part( 'template-parts/modules/cards/card-featured', array(
			'link'       => get_the_permalink(),
			'image_id'   => get_post_thumbnail_id(),
			'title'      => get_the_title(),
			'authors'    => get_the_author_link(),
			'date'       => get_the_date(),
			'excerpt'    => get_the_excerpt(),
			'categories' => get_the_category(),
		));

		wp_reset_postdata();
	}
	?>
	</div>
</div>

<?php if ( ! empty( $featured_categories ) ) : ?>
<div class="news-categories">
	<div class="news-category-inner mw-1360">
		<ul class="link-list color-gray uppercase bold">
			<?php foreach ( $featured_categories as $category ) :
				$term = get_term( absint( $category ) );
				?>
				<li>
					<a data-id="<?php echo absint( $category ); ?>" class="js-category-filter" href="<?php echo esc_url( get_term_link( $term->slug, 'category' ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>

<div class="w-100p news-list-container news-card-list mod-margin-bottom">
	<div class="mw-1360">
		<?php if ( have_posts() ) : ?>
			<div class="card-list-container">
			<?php
			while ( have_posts() ) :
				the_post();

				wmf_get_template_part(
					'template-parts/modules/cards/card-horizontal', array(
						'link'       => get_the_permalink(),
						'image_id'   => get_post_thumbnail_id(),
						'title'      => get_the_title(),
						'authors'    => get_the_author_link(),
						'date'       => get_the_date(),
						'excerpt'    => get_the_excerpt(),
						'categories' => get_the_category(),
					)
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
