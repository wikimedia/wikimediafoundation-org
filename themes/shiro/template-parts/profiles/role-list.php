<?php
/**
 * Adds a list of Roles
 *
 * @package shiro
 */

$post_list = $args;

if ( empty( $post_list ) ) {
	return;
}

$show_heading = get_term_meta( get_queried_object_id(), 'term_heading', true );

foreach ( $post_list as $term_id => $term_data ) :
	$name        = ! empty( $term_data['name'] ) ? $term_data['name'] : '';
	$description = term_description( $term_id, 'role' );
	$button      = get_term_meta( $term_id, 'role_button', true );
	$term        = get_term( $term_id, 'role' );
	$name        = ( ! $show_heading && ( is_wp_error( $term ) || empty( $term->parent ) ) ) ? '' : $name;
	?>

<div class="static-list-item mod-margin-bottom_xs wysiwyg">

	<?php if ( ! empty( $name ) ) : ?>
	<h2 class="static-list-heading" id="section-<?php echo absint( $term_id ); ?>">
		<?php echo esc_html( $name ); ?>
	</h2>
	<?php endif; ?>

	<div class="static-list-contents">
		<?php
		if ( ! empty( $description ) ) {
			echo wp_kses_post( $description );
		}
		?>

		<?php if ( ! empty( $term_data ) ) : ?>
		<div class="mod-margin-bottom_xs staff-list">
			<?php
			foreach ( $term_data['posts'] as $post_id ) {
				get_template_part(
					'template-parts/profiles/role',
					'item',
					array(
						'id' => $post_id,
					)
				);
			}
			?>
		</div>
		<?php endif; ?>

		<?php
		if ( ! empty( $button['link_to_archive'] ) ) :
			$link_text = ! empty( $button['text'] )
				? $button['text']
				: __(
					sprintf(
						/* translators: The name of the current taxonomy. */
						'View full %s team',
						$name
					),
					'shiro'
				);
			$link_url  = ! empty( $button['link'] ) ? $button['link'] : get_term_link( $term_id, 'role' );
		?>
			<div class="mod-margin-bottom_sm">
				<a href="<?php echo esc_url( $link_url ); ?>" class="arrow-link">
					<?php echo esc_html( $link_text ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>

</div>
	<?php
endforeach;
