<?php
/**
 * Related posts module
 *
 * @package shiro
 */

$template_data = wmf_get_template_data();

if ( empty( $template_data ) || empty( $template_data['posts'] ) ) {
	return;
}

$title            = ! empty( $template_data['title'] ) ? $template_data['title'] : '';
$description      = ! empty( $template_data['description'] ) ? $template_data['description'] : '';
$connected_user   = get_post_meta( get_the_ID(), 'connected_user', true );
$authorlink       = wmf_get_author_link( $connected_user );
$rand_translation_title = wmf_get_random_translation( 'wmf_related_posts_title' );

?>

<div class="w-100p news-list-container mod-margin-bottom">
	<div class="mw-980">
		<?php if ( ! empty( $title ) ) : ?>
		<h3 class="h3 color-gray uppercase">
			<?php echo esc_html( $title ); ?>
            <?php if ( ! empty( $rand_translation_title['content'] ) ) : ?>
				— <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span>
            <?php endif; ?>
		</h3>
		<?php endif; ?>

		<?php if ( ! empty( $description ) ) : ?>
		<h2 class="h2">
			<?php echo esc_html( $description ); ?>
            <?php if ( ! empty( $authorlink ) ) : ?>
                <span class="authorlink"><a href="/news/author/<?php echo esc_attr( $authorlink ); ?>">View all</a></span>
            <?php endif; ?>
        </h2>
		<?php endif; ?>


		<div class="flex flex-medium flex-wrap-reverse">
			<?php
			foreach ( $template_data['posts'] as $post ) :
				setup_postdata( $post );
				wmf_get_template_part(
					'template-parts/modules/cards/card-vertical', array(
						'title'      => get_the_title(),
						'link'       => get_the_permalink(),
						'image_id'   => get_post_thumbnail_id(),
						'authors'    => wmf_byline(),
						'date'       => get_the_date(),
						'isodate'    => get_the_date(c),
						'excerpt'    => get_the_excerpt(),
						'categories' => get_the_category(),

					)
				);
				wp_reset_postdata();
		endforeach;
			?>
		</div>
	</div>
</div>
