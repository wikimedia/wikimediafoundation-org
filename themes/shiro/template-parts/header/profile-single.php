<?php
/**
 * Adds Header for single profile pages.
 *
 * @package shiro
 */

$profile_header_data = wmf_get_template_data();

$back_to_link = ! empty( $profile_header_data['back_to_link'] ) ? $profile_header_data['back_to_link'] : '';
$staff_name   = ! empty( $profile_header_data['back_to_label'] ) ? $profile_header_data['back_to_label'] : '';
$team_name    = ! empty( $profile_header_data['team_name'] ) ? $profile_header_data['team_name'] : '';
$role         = ! empty( $profile_header_data['role'] ) ? $profile_header_data['role'] : '';
$share_links  = ! empty( $profile_header_data['share_links'] ) ? $profile_header_data['share_links'] : '';

?>

<div class="header-main header-role">
	<div class="header-content">
		<h2 class="h4 uppercase eyebrow">
			<a class="back-arrow-link" href="<?php echo esc_url( $back_to_link ); ?>">
				<?php echo esc_html( $staff_name ); ?>
			</a>
		</h2>

		<h1><?php the_title(); ?></h1>

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
</div>

</div>
</header>

<main id="content" role="main">
