<?php
/**
 * Adds Header for single story pages.
 *
 * @package shiro
 */

$story_header_data = wmf_get_template_data();

// TODO: Fix for non-transparency report
//$back_to_link = ! empty( $story_header_data['back_to_link'] ) ? $story_header_data['back_to_link'] : '';
$back_to_link = '/about/transparency/';
$story_name   = ! empty( $story_header_data['back_to_label'] ) ? $story_header_data['back_to_label'] : '';

?>

<div class="header-main header-role">
	<div class="header-content">
		<h2 class="h4 eyebrow">
			<a class="back-arrow-link" href="<?php echo esc_url( $back_to_link ); ?>">
				<?php echo esc_html( $story_name ); ?>
			</a>
		</h2>

		<h1><?php the_title(); ?></h1>

	</div>
</div>

</div>
</header>

<main id="content">
