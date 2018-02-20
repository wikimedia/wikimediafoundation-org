<?php
/**
 * Adds Header for default pages
 *
 * @package wmfoundation
 */

$page_header_data = wmf_get_template_data();

$parent_section_title = ! empty( $page_header_data['parent_section_title'] ) ? $page_header_data['parent_section_title'] : '';
$parent_section_link  = ! empty( $page_header_data['parent_section_link'] ) ? $page_header_data['parent_section_link'] : '';

?>

<div class="header-main">
	<div class="header-content">
		<h2 class="h4 uppercase eyebrow">
			<a href="<?php echo esc_url( $parent_section_link ); ?>">
				<?php echo esc_html( $parent_section_link ); ?>
			</a>
		</h2>

		<h1 class="mar-bottom"><?php the_title(); ?></h1>
	</div>
</div>
