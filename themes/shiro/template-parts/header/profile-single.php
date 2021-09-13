<?php
/**
 * Adds Header for single profile pages.
 *
 * @package shiro
 */

$profile_header_data = $args;

$back_to_link = ! empty( $profile_header_data['back_to_link'] ) ? $profile_header_data['back_to_link'] : '';
$staff_name   = ! empty( $profile_header_data['back_to_label'] ) ? $profile_header_data['back_to_label'] : '';
$team_name    = ! empty( $profile_header_data['team_name'] ) ? $profile_header_data['team_name'] : false;
$role_name    = ! empty( $profile_header_data['role'] ) ? $profile_header_data['role'] : false;
$share_links  = ! empty( $profile_header_data['share_links'] ) ? $profile_header_data['share_links'] : '';
$role_desc    = join(', ', array_filter( [ $role_name, $team_name ] ) );

?>

<div class="header-main header-role">
	<div class="header-content">
		<h2 class="h4 eyebrow">
			<a class="back-arrow-link" href="<?php echo esc_url( $back_to_link ); ?>">
				<?php echo esc_html( $staff_name ); ?>
			</a>
		</h2>

		<h1><?php the_title(); ?></h1>

		<p class="post-meta">
			<?php echo esc_html( $role_desc ); ?>
		</p>
	</div>
</div>

</div>
</header>

<main id="content">
