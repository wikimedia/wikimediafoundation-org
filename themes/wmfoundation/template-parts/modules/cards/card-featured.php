<?php
/**
 * Featured Post card
 *
 * @package wmfoundation
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
$excerpt    = ! empty( $card_data['excerpt'] ) ? $card_data['excerpt'] : '';
$categories = ! empty( $card_data['categories'] ) ? $card_data['categories'] : '';

$image = wp_get_attachment_image_src( $image_id, 'image_16x19_large' );

?>
<div class="card">
	<?php if ( ! empty( $image_id ) ) : ?>
		<div class="bg-img-container">
			<div class="bg-img" style="background-image: url(<?php echo esc_url( $image[0] ); ?>)"></div>
		</div>
	<?php endif; ?>

	<div class="card-content w-55p module-mu">
		<?php if ( ! empty( $categories ) ) : ?>
		<h4 class="category">
			<?php
			foreach ( $categories as $category ) {
				printf( '<a href="%1$s">%2$s</a>', esc_url( get_category_link( $category->term_id ) ), esc_html( $category->name ) );
			}
			?>
		</h4>
		<?php endif; ?>

		<div class="card-content-text">

			<?php if ( ! empty( $title ) ) : ?>
			<h2 class="h2">
				<a href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html( $title ); ?>
				</a>
			</h3>
			<?php endif; ?>

			<div class="post-meta ">
				<?php if ( ! empty( $authors ) ) : ?>
				<span>
					<?php echo wp_kses_post( $authors ); ?>
				</span>
				<?php endif; ?>

				<?php if ( ! empty( $date ) ) : ?>
				<time>
					<?php echo esc_html( $date ); ?>
				</time>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $excerpt ) ) : ?>
			<div class="cta-footer">
				<?php echo wp_kses_post( wpautop( sprintf( '<p class="h4">%s</p>', $excerpt ) ) ); ?>
			</div>
			<?php endif; ?>

		</div>

	</div>
</div>
