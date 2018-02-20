<?php
/**
 * Adds Header for single profile pages.
 *
 * @package wmfoundation
 */

$back_to_link = get_post_type_archive_link( 'profile' );

// Need to confirm that this is either Staff or Community / separate taxonomy.
$staff_name = 'Staff';

$role        = get_post_meta( get_the_ID(), 'profile_role', true );
$team_name   = get_the_terms( get_the_ID(), 'team' );
$team_name   = ! empty( $team_name ) && isset( $team_name[0]->name ) ? $team_name[0]->name : '';
$share_links = get_post_meta( get_the_ID(), 'contact_links', true );

?>

<div class="header-main">
	<div class="header-content">
		<h2 class="h4 uppercase eyebrow">
			<a href="<?php echo esc_url( $back_to_link ); ?>">
				<i class="material-icons" style="vertical-align: sub;">arrow_back</i>
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
		<?php foreach ( $share_links as $link ) : ?>
		<span class="link-list hover-highlight color-white uppercase mar-right">
			<a href="<?php echo esc_url( $link['link'] ); ?>" class="color-white">
				<?php echo esc_html( $link['title'] ); ?>
			</a>
		</span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>

</div>
</header>
