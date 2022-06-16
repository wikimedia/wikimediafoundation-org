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

foreach ( $post_list as $term_id => $term_data ) :
	$name        = ! empty( $term_data['name'] ) ? $term_data['name'] : '';
	$description = term_description( $term_id, 'role' );
	$button      = get_term_meta( $term_id, 'role_button', true );
	$executive   = get_term_meta( $term_id, 'role_executive', true );
	$experts     = get_term_meta( $term_id, 'role_experts', true );
	$term        = get_term( $term_id, 'role' );
	$term_slug   = $term->slug;
	$name        = ( is_wp_error( $term ) || empty( $term->parent ) ) ? '' : $name;
	$class       = 'role__section wysiwyg';

	if ( ! empty( $name ) && ! is_tax( 'role', $term_id ) ) {
		$class = $class . ' has-h2';
	}
	?>

<section class="<?php echo esc_attr( $class ); ?>">

	<?php if ( ! empty( $name ) && ! is_tax( 'role', $term_id ) ) : ?>
	<h2 class="role__heading" id="<?php echo esc_attr( $term_slug ); ?>">
		<?php echo esc_html( $name ); ?>
	</h2>
	<?php endif; ?>

	<?php
	if ( ! empty( $description ) ) {
		echo wp_kses_post( $description );
	}
	?>

	<?php
	if ( is_tax( 'role', 'staff-contractors' ) && ! ( empty ( $executives ) && empty( $experts ) ) ) :
		if ( ! empty( $executive ) ) {
			get_template_part(
				'template-parts/profiles/role',
				'item',
				array(
					'id'   => $executive,
					'list' => false,
					'role' => 'executive',
				)
			);
		}

		if ( ! empty( $experts ) ) :
		?>
		<h3 class="role__staff-title__experts is-style-h4">
			<?php echo esc_html__( 'Department experts', 'shiro' ); ?>
		</h3>
		<ul class="role__staff-list">
			<?php
			foreach ( $experts as $expert_id ) {
				get_template_part(
					'template-parts/profiles/role',
					'item',
					array(
						'id'   => $expert_id,
						'role' => 'expert',
					)
				);
			}
			?>
		</ul>
		<?php
		endif;

	else :
		if ( ! empty( $term_data ) ) :
		?>
		<ul class="role__staff-list">
			<?php
			foreach ( $term_data['posts'] as $term_data_post_id ) {
				get_template_part(
					'template-parts/profiles/role',
					'item',
					array(
						'id' => $term_data_post_id,
					)
				);
			}
			?>
		</ul>
		<?php endif; ?>

		<?php if ( ! empty( $term_data['children'] ) ) : ?>
			<div>
				<?php
				foreach ( $term_data['children'] as $child_term_id => $child_term_data ) :
					$name        = ! empty( $child_term_data['name'] ) ? $child_term_data['name'] : '';
					$description = term_description( $child_term_id, 'role' );
					?>

					<?php
					if ( empty( $child_term_data['posts'] ) ) {
						continue;
					}
					?>

					<?php if ( ! empty( $name ) ) : ?>
					<h3 class="role__staff-title__nested">
						<?php echo esc_html( $name ); ?>
					</h3>
					<?php endif; ?>

					<?php
					if ( ! empty( $description ) ) :
						echo wp_kses_post( $description );
					endif;
					?>

					<ul class="role__staff-list">
						<?php
						foreach ( $child_term_data['posts'] as $post_id ) :
							get_template_part(
								'template-parts/profiles/role',
								'item',
								array(
									'id' => $post_id,
								)
							);
						endforeach;
						?>
					</ul>

				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php
	if ( ! empty( $button['link_to_archive'] ) && ! is_tax( 'role', $term_id ) ) :
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
		<div class="role__read-more">
			<a href="<?php echo esc_url( $link_url ); ?>" class="arrow-link">
				<?php echo esc_html( $link_text ); ?>
			</a>
		</div>
	<?php endif; ?>

</section>
	<?php
endforeach;
