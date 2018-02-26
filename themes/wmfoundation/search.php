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
$post_types = array(
	'News' => 'post',
	'Profiles' => 'profile',
);

$current_post_types = get_query_var( 'post_type' );

?>

<div class="mw-1360 mod-margin-bottom flex flex-medium news-card-list">
	<div id="search-results" class="card-list-container w-68p">
		<?php if ( have_posts() ) : ?>
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
						'sidebar'    => true,
					)
				);
			endwhile;
			?>

		<?php
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>

	<div class="module-mu wysiwyg w-32p">
		<div class="mar-bottom_lg">
			<h4 class="uppercase small mar-bottom">Result Type</h4>
			<form id="searchFilter" role="serach" method="GET" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php foreach( $post_types as $post_type_name => $post_type_label ) :
				$is_checked = in_array( $post_type_label, (array) $current_post_types, true ) ? 'checked' : '';
					?>
				<div class="checkbox-row">
					<input type="checkbox" name="post_type[]" value="<?php echo esc_attr( $post_type_label ); ?>" id="<?php echo esc_attr( $post_type_label ); ?>" <?php echo esc_attr( $is_checked ); ?> />
					<label for="<?php echo esc_attr( $post_type_label ); ?>">
						<?php echo esc_html( $post_type_name ); ?>
					</label>
				</div>
				<?php endforeach; ?>

				<input type="hidden" id="keyword" name="s" value="<?php the_search_query(); ?>" />
				<input type="submit" id="searchsubmit" value="Submit" />
			</form>


		</div>
	</div>
</div>

<div id="pagination">
	<?php
	if ( have_posts() ) :
		get_template_part( 'template-parts/pagination' );
	endif;
	?>
</div>

<?php
get_footer();
