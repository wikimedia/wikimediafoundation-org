<?php
/**
 *
 * Template for displaying search results
 *
 * @package wmfoundation
 */

get_header(); ?>

<?php
$template_args = array(
	'h1_title' => sprintf( __( 'Search results for %s' ), get_search_query() ),
);

wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );

$post_types = get_post_types();
var_dump( $post_types );

?>

<div class="mw-1360 mod-margin-bottom flex flex-medium news-card-list">
		<?php if ( have_posts() ) : ?>
		<div class="card-list-container w-68p">
			<?php
			while ( have_posts() ) :
				the_post();

				wmf_get_template_part(
					'template-parts/modules/cards/card-horizantal', array(
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

		<div class="module-mu wysiwyg w-32p">
			<div class="mar-bottom_lg">
				<h4 class="uppercase small mar-bottom">Result Type</h4>
				<form role="serach" method="GET" action="<?php echo esc_url( home_url( '/' ) ); ?>">

				</form>


			</div>
		</div>
		<?php
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
</div>

<?php
if ( have_posts() ) :
	get_template_part( 'template-parts/pagination' );
endif;

get_footer();
