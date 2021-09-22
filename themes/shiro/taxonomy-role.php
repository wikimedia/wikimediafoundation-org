<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 */

get_header();

$h4_title = '';
$h4_link  = '';

$current_term_id = get_queried_object_id();
$term            = get_term( $current_term_id );

$profile_parent_page = 'community' === $term->slug ? get_theme_mod( 'wmf_community_profile_parent_page' ) : get_theme_mod( 'wmf_profile_parent_page' );

// Set up the back arrow link for top-level terms.
if ( ! empty( $profile_parent_page ) ) {
	$h4_title = get_the_title( $profile_parent_page );
	$h4_link  = get_the_permalink( $profile_parent_page );
}

// If this is a nested term, use its parent for the back arrow link.
if ( isset( $term->parent ) && 0 !== $term->parent ) {
	$term_parent = get_term_by( 'id', $term->parent, 'role' );
	$h4_title    = $term_parent->name;
	$h4_link     = get_term_link( $term_parent );
}

$display_intro = get_term_meta( $current_term_id, 'display_intro', true );

if ( ! empty( $display_intro ) ) {
	$description  = get_theme_mod( 'wmf_profile_archive_text', __( 'The Wikimedia Foundation is part of a broad global network of individuals, organizations, chapters, clubs and communities who together work to create the most powerful examples of volunteer collaboration and open content sharing in the world today.', 'shiro-admin' ) );
	$button_label = get_theme_mod( 'wmf_profile_archive_button', __( 'We\'re Hiring', 'shiro-admin' ) );
	$button_link  = get_theme_mod( 'wmf_profile_archive_button_link', '#' );
}

$post_list = wmf_get_posts_by_child_roles( $current_term_id );

?>

<?php
	$header_args = array(
		'h1_title' => single_term_title( '', false ),
		'h4_link'  => $h4_link,
		'h4_title' => $h4_title,
	);

	get_template_part(
		'template-parts/header/page-noimage',
		null,
		$header_args
	);

	?>

<?php if ( ! empty( $display_intro ) ) : ?>
<div class="mw-980 mod-margin-bottom_sm">
	<div class="page-intro wysiwyg taxonomy-role">
		<?php if ( ! empty( $description ) ) : ?>
		<p>
			<?php echo esc_html( $description ); ?>
		</p>
		<?php endif; ?>

		<?php if ( ! empty( $button_label ) ) : ?>
			<a href="<?php echo esc_url( isset( $button_link ) ? $button_link : '#' ); ?>" class="btn btn-blue">
				<?php echo esc_html( $button_label ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<div class="mw-980 mod-margin-bottom flex flex-medium role-template toc__section">
	<?php if ( ! empty( $post_list ) && count( $post_list ) > 1 ) : ?>
		<div class="w-32p toc__sidebar">
			<?php get_sidebar( 'list', [ 'template_args' => $post_list ] ); ?>
		</div>
	<?php endif; ?>

	<div class="w-68p toc__content">
		<?php get_template_part( 'template-parts/profiles/role-list', null, $post_list ); ?>
	</div>
</div>

<?php
get_footer();
