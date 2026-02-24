<?php
/**
 * Handles page fact module for multiple facts.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['listings'] ) ) {
	return;
}

$listing_width = 3 === count( $template_args['listings'] ) ? 'w-32p' : 'w-45p';

?>

<div class="mw-1360 article-main mod-margin-bottom wysiwyg center">

	<?php if ( ! empty( $template_args['heading'] ) ) : ?>
		<h3 class="mar-bottom_lg"><?php echo esc_html( $template_args['heading'] ); ?></h3>
	<?php endif; ?>
	<div class="mw-1360 flex flex-medium flex-wrap">
		<?php foreach ( $template_args['listings'] as $listing ) : ?>
		<div class="listing-inner module-mu <?php echo esc_attr( $listing_width ); ?>">
			<?php wmf_get_template_part( 'template-parts/modules/employment/listing', $listing ); ?>
		</div>
		<?php endforeach; ?>
	</div>

</div>
