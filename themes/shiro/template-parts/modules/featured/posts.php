<?php
/**
 * Handles Featured Posts output for home and landing pages.
 *
 * @package shiro
 */

$template_args = $args;

$context  = empty( $template_args['context'] ) ? '' : $template_args['context'];
$subtitle = empty( $template_args['subtitle'] ) ? '' : $template_args['subtitle'];
// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$title    = get_theme_mod( 'wmf_featured_post_pre_heading', __( 'NEWS', 'shiro-admin' ) );
// phpcs:enable

$rand_translation_title = wmf_get_random_translation( 'wmf_featured_post_pre_heading' );

if ( empty( $context ) ) {
	return;
}

$cache_key      = md5( 'wmf_featured_posts_for' . $context );
$featured_posts = wp_cache_get( $cache_key );

if ( empty( $featured_posts ) ) {
	$featured_posts = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 2,
			'no_found_rows'  => true,
			'meta_query'     => array(
				array(
					'key'     => 'featured_on',
					'value'   => $context,
					'compare' => 'LIKE',
				),
			),
		)
	); // WPCS: slow query ok.
	wp_cache_add( $cache_key, $featured_posts );
}

if ( ! $featured_posts->have_posts() ) {
	return;
}
?>
<div class="w-100p bg-white related-news-container mod-margin-bottom bg-ltgray">
	<div class="mw-980 std-mod">
		<p class="double-heading__secondary is-style-h5">
			<?php echo esc_html( $title ); ?> —&nbsp;<span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?>&nbsp;</span>
		</p>

		<?php if ( ! empty( $subtitle ) ) : ?>
			<h2 class="double-heading__primary is-style-h3">
				<?php echo esc_html( $subtitle ); ?>
			</h2>
		<?php endif; ?>

		<div class="related-news">
			<?php
			while ( $featured_posts->have_posts() ) {
				$featured_posts->the_post();
				get_template_part( 'template-parts/modules/featured/post', 'card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>
