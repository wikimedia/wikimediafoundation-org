<?php
/**
 * Featured Post card
 *
 * @package shiro
 */

$card_data = wmf_get_template_data();

if ( empty( $card_data ) ) {
	return;
}

$link       = ! empty( $card_data['link'] ) ? $card_data['link'] : '';
$image_id   = ! empty( $card_data['image_id'] ) ? $card_data['image_id'] : '';
$title      = ! empty( $card_data['title'] ) ? $card_data['title'] : '';
$authors    = ! empty( $card_data['authors'] ) ? $card_data['authors'] : '';
$date       = ! empty( $card_data['date'] ) ? $card_data['date'] : '';
$isodate    = ! empty( $card_data['isodate'] ) ? $card_data['isodate'] : '';
$excerpt    = ! empty( $card_data['excerpt'] ) ? $card_data['excerpt'] : '';
$categories = ! empty( $card_data['categories'] ) ? $card_data['categories'] : '';

$image = wp_get_attachment_image_src( $image_id, 'image_16x19_large' );

?>
<div class="card">
	<?php if ( ! empty( $image_id ) ) : ?>
		<div class="bg-img-container">
			<div class="bg-img rounded" style="background-image: url(<?php echo esc_url( $image[0] ); ?>);"></div>
		</div>
	<?php endif; ?>

	<div class="card-content w-55p module-mu">
		<div class="card-content-text">

			<?php if ( ! empty( $title ) ) : ?>
			<h2 class="small">
				<a href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html( $title ); ?>
				</a>
			</h2>
			<?php endif; ?>

			<?php if ( ! empty( $categories ) ) : ?>
			<h4 class="category-container">
				<?php
				foreach ( $categories as $category ) {
					printf( '<a class="category mar-right" href="%1$s">%2$s</a> ', esc_url( get_category_link( $category->term_id ) ), esc_html( $category->name ) );
				}
				?>
			</h4>
			<?php endif; ?>

			<?php if ( ! empty( $excerpt ) ) : ?>
			<div class="cta-footer">
				<?php echo wp_kses_post( wpautop( $excerpt ) ); ?>
			</div>
			<?php endif; ?>

			<div class="post-meta ">
				<?php if ( ! empty( $date ) ) : ?>
				<time datetime="<?php echo esc_attr( $isodate ); ?>">
					<?php echo esc_html( $date ); ?>
				</time>
				<?php endif; ?>
				<?php if ( ! empty( $authors ) ) : ?>
				<span>
					<?php echo wp_kses_post( $authors ); ?>
				</span>
				<?php endif; ?>
			</div>

			<a href="<?php echo esc_url( $link ); ?>" class="arrow-link">Read more</a>

		</div>

	</div>
</div>
