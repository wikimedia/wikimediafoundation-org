<?php
/**
 * Related posts module
 *
 * @package shiro
 */

use WMF\Editor\Blocks\BlogPost;

$template_data = $args;

if ( empty( $template_data ) || empty( $template_data['posts'] ) ) {
	return;
}

$title            = ! empty( $template_data['title'] ) ? $template_data['title'] : '';
$description      = ! empty( $template_data['description'] ) ? $template_data['description'] : '';
$connected_user   = get_post_meta( get_the_ID(), 'connected_user', true );
$authorlink       = wmf_get_author_link( $connected_user );
$rand_translation_title = wmf_get_random_translation( 'wmf_related_posts_title' );

?>

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

	<?php foreach ($template_data['posts'] as $post) {
		echo BlogPost\render_block( [ 'post_id' => $post->ID ] );
	} ?>
</div>
