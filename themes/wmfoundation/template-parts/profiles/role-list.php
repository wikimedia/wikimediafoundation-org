<?php
/**
 * Adds a list of Roles
 *
 * @package wmfoundation
 */

$post_list = wmf_get_template_data();

if ( empty( $post_list ) ) {
	return;
}

foreach ( $post_list as $term_id => $term_data ) :
	$name        = ! empty( $term_data['name'] ) ? $term_data['name'] : '';
	$description = term_description( $term_id, 'role' );
	$button      = get_term_meta( $term_id, 'role_button', true );
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
			foreach ( wmf_sort_by_last_name( $term_data['posts'] ) as $post_id ) {
				wmf_get_template_part(
					'template-parts/profiles/role-item', array(
						'id' => $post_id,
					)
				);
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $term_data['children'] ) ) : ?>
		<div>
			<?php
			foreach ( $term_data['children'] as $child_term_id => $child_term_data ) :
				$name        = ! empty( $child_term_data['name'] ) ? $child_term_data['name'] : '';
				$description = term_description( $child_term_id, 'role' );
				$button      = get_term_meta( $child_term_id, 'role_button', true );
				$link        = ! empty( $button['link'] ) ? $button['link'] : '';
			?>

				<?php if ( ! empty( $name ) ) : ?>
				<h3 class="mar-bottom">
					<?php if ( ! empty( $link ) ) : ?>
					<a href="<?php echo esc_url( $link ); ?>">
					<?php endif; ?>

						<?php echo esc_html( $name ); ?>

					<?php if ( ! empty( $link ) ) : ?>
					<?php wmf_show_icon( 'open', 'material' ); ?>
					</a>
					<?php endif; ?>
				</h3>
				<?php endif; ?>

				<?php if ( ! empty( $description ) ) : ?>
				<p class="mar-bottom_lg"><?php echo wp_kses_post( $description ); ?></p>
				<?php endif; ?>

				<div class="mod-margin-bottom_xs staff-list">
					<?php

					foreach ( wmf_sort_by_last_name( $child_term_data['posts'] ) as $post_id ) :
						wmf_get_template_part(
							'template-parts/profiles/role-item', array(
								'id' => $post_id,
							)
						);
					endforeach;
					?>
				</div>

			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

</div>
<?php
endforeach;
