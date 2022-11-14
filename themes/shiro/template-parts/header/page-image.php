<?php
/**
 * Adds Header for default pages
 *
 * @package shiro
 */

$page_header_data = $args;
$image            = ! empty( $page_header_data['image'] ) ? $page_header_data['image'] : '';
$bg_opts          = wmf_get_background_image();
$bg_color         = $bg_opts['color'] ? 'pink' : 'blue';

?>

	<?php get_template_part( 'template-parts/header/header-content', null, $page_header_data ); ?>

	<?php get_template_part( 'template-parts/header/social' ); ?>

</div>
</header>

<main id="content">
