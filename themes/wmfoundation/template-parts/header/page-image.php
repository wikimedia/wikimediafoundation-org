<?php
/**
 * Adds Header for default pages
 *
 * @package wmfoundation
 */

$page_header_data = wmf_get_template_data();
$image            = ! empty( $page_header_data['image'] ) ? $page_header_data['image'] : '';

?>

<div class="header-main bg-img--blue">
	<div class="photo-aspect-ratio">
		<div class="bg-img-container">
			<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>">

			</div>
		</div>
	</div>

	<?php wmf_get_template_part( 'template-parts/header/header-content', $page_header_data ); ?>
</div>

</div>
</header>
