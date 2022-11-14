<?php
/**
 * Basic index horizontal card
 *
 * @package shiro
 */

$card_data = $args;

if ( empty( $card_data ) ) {
	return;
}

$link       = $card_data['link'] ?? '';
$image_id   = $card_data['image_id'] ?? '';
$title      = $card_data['title'] ?? '';
$authors    = $card_data['authors'] ?? '';
$date       = $card_data['date'] ?? '';
$isodate    = $card_data['isodate'] ?? '';
$excerpt    = $card_data['excerpt'] ?? '';
$categories = $card_data['categories'] ?? [];
$sidebar    = boolval( $card_data['sidebar'] ?? false );
$image_size = $sidebar ? 'image_4x5_large' : 'image_4x3_large';
$class      = $card_data['class'] ?? 'blog-post';

?>
	<div class="<?php echo esc_attr( $class ); ?>">

	<?php if ( ! empty( $image_id ) ) : ?>
		<a class="blog-post__image-link" alt="" tabindex="-1" href="<?php echo esc_url( $link ); ?>">
			<?php echo wp_get_attachment_image(
				$image_id,
				$image_size,
				null,
				[ 'class' => 'blog-post__image' ]
			); ?>
		</a>
	<?php endif; ?>

	<div class="blog-post__content">
		<?php if ( ! empty( $title ) ) : ?>
			<h3 class="blog-post__title">
				<a href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html( $title ); ?>
				</a>
			</h3>
		<?php endif; ?>

		<?php if ( ! empty( $categories ) ) : ?>
			<div class="blog-post__categories">
				<?php
					foreach ( $categories as $category ) {
						printf( '<a class="blog-post__category-link" href="%1$s">%2$s</a> ', esc_url( get_category_link( $category->term_id ) ), esc_html( $category->name ) );
					}
					?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $excerpt ) ) : ?>
			<div class="blog-post__excerpt">
				<?php echo wp_kses_post( wpautop( $excerpt ) ); ?>
			</div>
		<?php endif; ?>

		<div class="blog-post__meta">
			<?php if ( ! empty( $date ) ) : ?>
				<time class="blog-post__published" datetime="<?php echo esc_attr( $isodate ); ?>">
					<?php echo esc_html( $date ); ?>
				</time>
			<?php endif; ?>

			<?php if ( ! empty( $authors ) ) : ?>
				<span class="blog-post__authors">
					<?php echo wp_kses_post( $authors ); ?>
				</span>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( $link ); ?>"
		   class="blog-post__read-more"
		   aria-label="<?php /* translators: 1. the post title. */
		   esc_html_e( sprintf( 'Read more about %s', $title ), 'shiro' ); ?>">
			<?php esc_html_e( 'Read more', 'shiro' ); ?>
		</a>
	</div>
</div>
