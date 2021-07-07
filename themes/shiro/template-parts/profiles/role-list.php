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
	<h2 class="static-list-heading" id="section-<?php echo absint( $term_id ); ?>" class="static-list-heading"><?php echo esc_html( $name ); ?></h2>
	<?php endif; ?>

	<div class="static-list-contents">
		<?php if ( ! empty( $description ) ) : ?>
		<p class="mar-bottom"><?php echo wp_kses_post( $description ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $button ) ) : ?>
		<div class="link-list hover-highlight uppercase mar-bottom_lg">
			<a href="<?php echo esc_url( $button['link'] ); ?>">
				<?php echo esc_html( $button['text'] ); ?>
			</a>
		</div>
		<?php endif; ?>

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
	</div>

</div>
	<?php
endforeach;
