<?php
/**
 * The sidebar that adds metadata to floated container
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wmfoundation
 */

$facts     = get_post_meta( get_the_ID(), 'sidebar_facts', true );
$downloads = get_post_meta( get_the_ID(), 'sidebar_downloads', true );

if ( empty( $facts ) && empty( $downloads ) ) {
	return;
}
?>

<div class="sidebar-float">
	<?php
	if ( ! empty( $facts ) ) {
		wmf_get_template_part( 'template-parts/sidebar/fact', $facts );
	}
	?>

	<?php
	if ( ! empty( $downloads ) ) {
		wmf_get_template_part( 'template-parts/sidebar/downloads', $downloads );
	}
	?>
</div>
