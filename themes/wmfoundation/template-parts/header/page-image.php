<?php
/**
 * Adds Header for default pages
 *
 * @package wmfoundation
 */

$page_header_data = wmf_get_template_data();

$parent_section_link  = ! empty( $page_header_data['parent_section_link'] ) ? $page_header_data['parent_section_link'] : '';
$parent_section_title = ! empty( $page_header_data['parent_section_title'] ) ? $page_header_data['parent_section_title'] : '';
$image                = ! empty( $page_header_data['image'] ) ? $page_header_data['image'] : '';

?>

<div class="header-main bg-img--blue">
	<div class="photo-aspect-ratio">
		<div class="bg-img-container">
			<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>">

			</div>
		</div>
	</div>


	<?php
	wmf_get_template_part(
		'template-parts/header/header-content', array(
			'link'  => $parent_section_link,
			'title' => $parent_section_title,
		)
	);
	?>
</div>

</div>
</header>
