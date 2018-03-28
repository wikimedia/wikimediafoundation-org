<?php
/**
 * Handles Featured Post card output.
 *
 * @package wmfoundation
 */

use WMF\Images\Credits;

Credits::get_instance()->pause();
$image = get_the_post_thumbnail_url( get_the_ID(), 'image_4x3_large' );
Credits::get_instance()->resume();
?>

<div class="card card-vertical w-50p">
	<a href="<?php the_permalink(); ?>">
		<?php if ( ! empty( $image ) ) : ?>
		<div class="img-container">
			<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>);">
		<?php endif; ?>

		<div class="card-heading">
			<h3 class="color-white mar-bottom_sm"><?php the_title(); ?></h3>
			<span class="date color-white"><?php the_date(); ?></span>
		</div>


		<?php if ( ! empty( $image ) ) : ?>
			</div>
		</div>
		<?php endif; ?>
	</a>

	<div class="card-content">
		<?php the_excerpt(); ?>
	</div>
</div>

