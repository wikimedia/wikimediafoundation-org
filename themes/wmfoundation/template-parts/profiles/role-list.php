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
	$name        = $term_data['name'];
	$description = term_description( $term_id, 'role' );
	$button      = get_term_meta( $term_id, 'role_button', true );
?>

<h2><?php echo esc_html( $name ); ?></h2>

<?php if ( ! empty( $description ) ) : ?>
<p class="mar-bottom"><?php echo wp_kses_post( $description ); ?></p>
<?php endif; ?>

<?php if ( ! empty( $button ) ) : ?>
<div class="link-list hover-highlight uppercase">
	<a href="<?php echo esc_url( $button['link'] ); ?>">
		<?php echo esc_html( $button['text'] ); ?>
	</a>
</div>
<?php endif; ?>

<div class="mod-margin-bottom staff-list">
	<?php
	foreach ( $term_data['posts'] as $post_id ) :
		wmf_get_template_part(
			'template-parts/profiles/role-item', array(
				'id' => $post_id,
			)
		);
	endforeach;
	?>
</div>

<?php if ( ! empty( $term_data['children'] ) ) : ?>
<div>
	<?php
	foreach ( $term_data['children'] as $child_term_id => $child_term_data ) :
		$name        = $child_term_data['name'];
		$link        = get_term_link( $child_term_id );
		$description = term_description( $child_term_id, 'role' );
		$button      = get_term_meta( $child_term_id, 'role_button', true );
	?>

		<h3 class="mar-bottom">
			<a href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_html( $name ); ?>
			</a>
		</h3>

		<?php if ( ! empty( $description ) ) : ?>
		<p class="mar-bottom_lg"><?php echo wp_kses_post( $description ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $button ) ) : ?>
		<div class="link-list hover-highlight uppercase">
			<a href="<?php echo esc_url( $button['link'] ); ?>">
				<?php echo esc_html( $button['text'] ); ?>
			</a>
		</div>
		<?php endif; ?>

		<div class="mod-margin-bottom staff-list">
			<?php
			foreach ( $child_term_data['posts'] as $post_id ) :
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

<?php
endforeach;
