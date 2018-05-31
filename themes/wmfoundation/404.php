<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package wmfoundation
 */

get_header();

$template_args = array(
	'h2_title' => get_theme_mod( 'wmf_404_message', __( '404 Error', 'wmfoundation' ) ),
	'h1_title' => get_theme_mod( 'wmf_404_title', __( 'Imagine a world in which there is a page here', 'wmfoundation' ) ),
);

$wmf_search_button      = get_theme_mod( 'wmf_search_button_copy', __( 'Search', 'wmfoundation' ) );
$wmf_search_placeholder = get_theme_mod( 'wmf_search_placeholder_copy', __( 'Enter search terms', 'wmfoundation' ) );
$wmf_404_copy           = get_theme_mod( 'wmf_404_copy' );
$wmf_404_search_text    = get_theme_mod( 'wmf_404_search_text', __( 'Or try a search instead', 'wmfoundation' ) );

wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );

?>

<?php if ( $wmf_404_copy ) : ?>
<div class="page-intro mw-1360 mod-margin-bottom wysiwyg">
	<div class="page-intro-text">
		<?php echo wp_kses_post( wpautop( $wmf_404_copy ) ); ?>
	</div>
</div>
<?php endif; ?>

<div class="mw-1360 mod-margin-bottom">
	<div class="w-75p">
		<h3 class="h3 mar-bottom"><?php echo esc_html( $wmf_404_search_text ); ?></h3>
		<div class="search-container">
			<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input class="search-input mar-bottom" type="search" placeholder="<?php echo esc_attr( $wmf_search_placeholder ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
				<button class="btn btn-pink search-btn" type="submit"><?php echo esc_html( $wmf_search_button ); ?></button>
			</form>
		</div>
	</div>
</div>

<?php
get_footer();
