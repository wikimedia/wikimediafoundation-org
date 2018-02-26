<?php
/**
 * Handles Featured Post card output.
 *
 * @package wmfoundation
 */

$image = get_the_post_thumbnail( get_the_ID(), 'image_4x3_large' );
?>

<a href="<?php the_permalink(); ?>" class="card card-vertical card-news hover-img-zoom w-50p">
	<?php if ( ! empty( $image ) ) : ?>
	<div class="img-container">
		<?php echo $image; // WPCS: xss ok. ?>
	</div>
	<?php endif; ?>

	<div class="card-content card-content_clear top-into-img">
		<h3 class="color-white"><?php the_title(); ?></h3>
		<p class="person-title p color-gray mar-bottom_lg"><?php the_date(); ?></p>
		<?php the_excerpt(); ?>
	</div>
</a>
