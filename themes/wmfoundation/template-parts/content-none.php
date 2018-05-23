<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

$header      = get_theme_mod( 'wmf_no_results_title', __( 'Nothing Found', 'wmfoundation' ) );
$description = get_theme_mod( 'wmf_no_results_description', __( 'Sorry, but no results were found. Perhaps searching can help.', 'wmfoundation' ) );
?>

<div class="w-100p news-list-container news-card-list mod-margin-bottom">
	<div class="mw-1360">
		<h3><?php echo esc_html( $header ); ?></h3>

		<div class="wysiwyg">
			<?php echo esc_html( $description ); ?>
		</div>
	</div>
</div>
