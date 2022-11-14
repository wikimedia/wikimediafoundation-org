<?php
/**
 * Individual fact in default page sidebar
 *
 * @package shiro
 */

$template_data = $args;
$callout       = ! empty( $template_data['callout'] ) ? $template_data['callout'] : '';
$caption       = ! empty( $template_data['caption'] ) ? $template_data['caption'] : '';

if ( empty( $caption ) && empty( $callout ) ) {
	return;
}

?>

<div class="fact-container fact-inline">
	<div class="fact-inner">

	<?php if ( ! empty( $callout ) ) : ?>
		<h2 class="fact-number"><?php echo esc_html( $callout ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $caption ) ) : ?>
	<div class="fact-text-wrap">
		<h3 class="fact"><?php echo esc_html( $caption ); ?></h3>
	</div>
	<?php endif; ?>

	<?php
	$template_args = array(
		'message'    => sprintf( '%1$s - %2$s', $callout, $caption ),
		'list_class' => '',
	);
	get_template_part( 'template-parts/modules/social/share', 'vertical', $template_args );
	?>

	</div>
</div>
