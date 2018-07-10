<?php
/**
 * Common Header partial
 *
 * @package wmfoundation
 */

$page_header_data = wmf_get_template_data();

$h4_link              = ! empty( $page_header_data['h4_link'] ) ? $page_header_data['h4_link'] : '';
$h4_title             = ! empty( $page_header_data['h4_title'] ) ? $page_header_data['h4_title'] : '';
$h2_link              = ! empty( $page_header_data['h2_link'] ) ? $page_header_data['h2_link'] : '';
$h2_title             = ! empty( $page_header_data['h2_title'] ) ? $page_header_data['h2_title'] : '';
$title                = ! empty( $page_header_data['h1_title'] ) ? $page_header_data['h1_title'] : '';
$meta                 = ! empty( $page_header_data['page_meta'] ) ? $page_header_data['page_meta'] : '';
$allowed_tags         = wp_kses_allowed_html( 'post' );
$allowed_tags['time'] = true;

?>

<div class="header-content mar-bottom_lg">

	<?php if ( is_front_page() ) : ?>
	<div class="logo-nav-container">
		<?php get_template_part( 'template-parts/header/logo-home' ); ?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $h4_title ) ) : ?>
	<h2 class="h4 uppercase eyebrow">
		<?php if ( ! empty( $h4_link ) ) : ?>
		<a href="<?php echo esc_url( $h4_link ); ?>">
		<?php wmf_show_icon( 'arrow-back', 'icon-white material' ); ?>
		<?php endif; ?>
			<?php echo esc_html( $h4_title ); ?>
		<?php if ( ! empty( $h4_link ) ) : ?>
		</a>
		<?php endif; ?>
	</h2>
	<?php endif; ?>

	<?php if ( is_home() && ! empty( $h2_title ) ) : ?>
		<h2 class="h1 eyebrow"><?php echo esc_html( $h2_title ); ?></h2>
	<?php elseif ( ! empty( $h2_title ) ) : ?>
		<h2 class="h2 uppercase eyebrow">
			<?php if ( ! empty( $h2_link ) ) : ?>
			<a href="<?php echo esc_url( $h2_link ); ?>">
				<?php endif; ?>
				<?php echo esc_html( $h2_title ); ?>
				<?php if ( ! empty( $h2_link ) ) : ?>
			</a>
		<?php endif; ?>
		</h2>
	<?php endif; ?>

	<?php if ( ! empty( $title ) ) : ?>
	<h1 class="mar-bottom"><?php echo wp_kses( $title, array( 'span' => array( 'class' ) ) ); ?></h1>
	<?php endif; ?>

	<?php if ( ! empty( $meta ) ) : ?>
	<div class="post-meta h4">
		<?php echo wp_kses( $meta, $allowed_tags ); ?>
	</div>
	<?php endif; ?>
</div>
