<?php
/**
 * Handles Featured Post card output.
 *
 * @package shiro
 */

use WMF\Images\Credits;


$image = get_the_post_thumbnail_url( get_the_ID(), 'image_4x3_large' );
Credits::get_instance()->pause();
?>

<div class="card card-vertical w-50p">
	<a href="<?php the_permalink(); ?>">
		<?php if ( ! empty( $image ) ) : ?>
		<div class="img-container rounded shadow">
			<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>);">
		<?php endif; ?>
		<?php if ( ! empty( $image ) ) : ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="card-heading">
			<span class="date"><?php the_date(); ?></span>
			<h3 class="mar-bottom_sm"><?php the_title(); ?></h3>
		</div>
	</a>

	<div class="card-content">
		<?php the_excerpt(); ?>
	</div>
	<?php // TODO: Make this a template arg ?>
	<a class="arrow-link" href="<?php the_permalink(); ?>">
		<?php esc_html_e( 'Read more', 'shiro' ); ?>
	</a>
</div>
<?php Credits::get_instance()->resume(); ?>
