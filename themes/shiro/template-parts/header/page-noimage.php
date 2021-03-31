<?php
/**
 * Adds Header for default pages
 *
 * @package shiro
 */

$page_header_data = $args;

?>

<div class="header-main">
<?php get_template_part( 'template-parts/header/header', 'content', $page_header_data ); ?>

<!-- <?php get_template_part( 'template-parts/header/social' ); ?> -->
</div>

</div>
</header>

<main id="content">
