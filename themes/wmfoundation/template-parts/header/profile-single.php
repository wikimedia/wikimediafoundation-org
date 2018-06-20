<?php
/**
 * Adds Header for single profile pages.
 *
 * @package wmfoundation
 */

$profile_header_data = wmf_get_template_data();

$back_to_link = ! empty( $profile_header_data['back_to_link'] ) ? $profile_header_data['back_to_link'] : '';
$staff_name   = ! empty( $profile_header_data['back_to_label'] ) ? $profile_header_data['back_to_label'] : '';
$team_name    = ! empty( $profile_header_data['team_name'] ) ? $profile_header_data['team_name'] : '';
$role         = ! empty( $profile_header_data['role'] ) ? $profile_header_data['role'] : '';
$share_links  = ! empty( $profile_header_data['share_links'] ) ? $profile_header_data['share_links'] : '';

?>

<div class="header-main">
	<div class="header-content">
		<h2 class="h4 uppercase eyebrow">
			<a href="<?php echo esc_url( $back_to_link ); ?>">
				<?php wmf_show_icon( 'arrow-back', 'icon-white material' ); ?>
				<?php echo esc_html( $staff_name ); ?>
			</a>
		</h2>

		<h1 class="mar-bototm"><?php the_title(); ?></h1>

		<div class="post-meta h4">
			<span>
				<?php
				if ( ! empty( $role ) || ! empty( $team_name ) ) :
					printf( '%1$s, %2$s', esc_html( $role ), esc_html( $team_name ) );
				endif;
				?>

			</span>
		</div>
	</div>

	<?php if ( ! empty( $share_links ) ) : ?>
	<div class="rise-up">
		<?php
		foreach ( $share_links as $link ) :
			// Use esc_html for encoded links so encoding is not stripped.
			?>
		<span class="link-list hover-highlight color-white uppercase mar-right">
			<a href="<?php echo strpos( $link['link'], 'mailto' ) !== false ? esc_html( $link['link'] ) : esc_url( $link['link'] ); ?>" class="color-white">
				<?php echo esc_html( $link['title'] ); ?>
			</a>
		</span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>

</div>
</header>
