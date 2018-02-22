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
 * @package wmfoundation
 */

get_header();

$parent_section_title = '';
$parent_section_link  = '';

$profile_parent_page = get_theme_mod( 'wmf_profile_parent_page' );
if ( ! empty( $profile_parent_page ) ) {
	$parent_section_title = get_the_title( $profile_parent_page );
	$parent_section_link  = get_the_permalink( $profile_parent_page );
}

$description  = get_theme_mod( 'wmf_profile_archive_text', __( 'The Wikimedia Foundation is part of a broad global network of individuals, organizations, chapters, clubs and communities who together work to create the most powerful examples of volunteer collaboration and open content sharing in the world today.', 'wmfoundation' ) );
$button_label = get_theme_mod( 'wmf_profile_archive_button', __( 'We\'re Hiring', 'wmfoundation' ) );
$button_link  = get_theme_mod( 'wmf_profile_archive_button_link', '#' );

$current_term_id = get_queried_object_id();
$post_list       = wmf_get_posts_by_child_terms( $current_term_id, 'role' );

?>

<?php
	wmf_get_template_part(
		'template-parts/header/page-noimage',
		array(
			'title'                => 'Staff and Contractors',
			'parent_section_link'  => $parent_section_link,
			'parent_section_title' => $parent_section_title,
		)
	);

?>

<div class="mw-1360 mod-margin-bottom flex flex-medium">
	<div class="w-68p">
		<div class="page-intro mod-margin-bottom wysiwyg">
			<?php if ( ! empty( $description ) ) : ?>
			<p class="h3 color-gray">
				<?php echo esc_html( $description ); ?>
			</p>
			<?php endif; ?>

			<?php if ( ! empty( $button_label ) ) : ?>
				<a href="<?php echo esc_url( $button_link ); ?>" class="btn btn-pink search-btn">
					<?php echo esc_html( $button_label ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="mod-margin-bottom">
			<?php wmf_get_template_part( 'template-parts/profiles/role-list', $post_list ); ?>
		</div>

	</div>
</div>

<?php
get_footer();
