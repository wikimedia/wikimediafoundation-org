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

// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$title                  = ! empty( $template_data['title'] ) ? $template_data['title'] : '';
$description            = ! empty( $template_data['description'] ) ? $template_data['description'] : '';
$connected_user         = get_post_meta( get_the_ID(), 'connected_user', true );
$authorlink             = wmf_get_author_link( $connected_user );
$rand_translation_title = wmf_get_random_translation( 'wmf_related_posts_title' );
// phpcs:enable

?>

<div class="block-area">
	<div class="wysiwyg mw-980">
		<?php /** This uses the structure & styles of the double heading, but can't
		 * use render_blocks() directly because the double-heading block
		 * structures its data very differently.
		 */ ?>
		<div class="double-heading">
			<?php if ( ! empty( $title ) ) : ?>
				<p class="double-heading__secondary is-style-h5">
					<span><?php echo esc_html( $title ) ?></span>
				</p>
			<?php endif; ?>
			<h2 class="double-heading__primary is-style-h3">
				<?php echo esc_html( $description ) ?>
				<?php if ( ! empty( $authorlink ) ) : ?>
					<span class="authorlink"><a
								href="/news/author/<?php echo esc_attr( $authorlink ); ?>">View all</a></span>
				<?php endif; ?>
			</h2>
		</div>

		<?php
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		foreach ( $template_data['posts'] as $post ) {
			echo wp_kses_post( BlogPost\render_block( [ 'post_id' => $post->ID ] ) );
		}
		// phpcs:enable
		?>
	</div>
</div>
