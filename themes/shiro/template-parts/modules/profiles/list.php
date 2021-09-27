<?php
/**
 * Profiles Module
 *
 * @package shiro
 */

$template_data = $args;

$profiles = ! empty( $template_data['profiles_list'] ) ? $template_data['profiles_list'] : '';

if ( empty( $profiles ) || ! is_array( $profiles ) || count( $profiles ) < 3 ) {
	return;
}

$profile_list = array_rand( array_flip( $profiles ), 3 );
$headline     = ! empty( $template_data['headline'] ) ? $template_data['headline'] : '';
$description  = ! empty( $template_data['description'] ) ? $template_data['description'] : '';
$button_label = ! empty( $template_data['button_label'] ) ? $template_data['button_label'] : '';
$button_link  = ! empty( $template_data['button_link'] ) ? $template_data['button_link'] : '';

$pre_heading            = get_theme_mod( 'wmf_profiles_label', __( 'Profiles', 'shiro-admin' ) );
$rand_translation_title = wmf_get_random_translation( 'wmf_profiles_label' );
?>
<div class="w-100p mod-margin-bottom">
	<div class="mw-980 std-mod">
		<p class="double-heading__secondary is-style-h5">
			<?php echo esc_html( $pre_heading ); ?> — <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span>
		</p>

		<?php if ( ! empty( $headline ) ) : ?>
		<h2 class="double-heading__primary is-style-h3"><?php echo esc_html( $headline ); ?></h2>
		<?php endif; ?>
	</div>

	<div class="mw-980 std-mod mod-margin-bottom_xs">

		<div class="profile-list">
		<?php
		foreach ( $profile_list as $profile_id ) {
			$team_name = '';
			$team      = get_the_terms( $profile_id, 'role' );
			if ( ! empty( $team ) && ! is_wp_error( $team ) ) {
				$team_name = $team[0]->name;
			}

			/**
			 * Get the permalink, but check to see if it's a broken url pattern.
			 * For some reason, these permalink urls are being rewritten to the
			 * home/profile pattern, which is not a valid URL (or the permalink).
			 *
			 * See https://github.com/humanmade/wikimedia/issues/146
			 */
			$profile_link = get_the_permalink( $profile_id );
			$profile_base = home_url( 'profile/' );
			if ( $profile_link === $profile_base ) {
				$profile      = get_post( $profile_id );
				$profile_link = $profile_base . $profile->post_name;
			}
			get_template_part(
				'template-parts/modules/profiles/card',
				null,
				array(
					'title'  => get_the_title( $profile_id ),
					'img_id' => get_post_thumbnail_id( $profile_id ),
					'link'   => $profile_link,
					'role'   => get_post_meta( $profile_id, 'profile_role', true ),
					'team'   => $team_name,
				)
			);
		}
		?>
		</div>
	</div>

	<div class="mw-980">
		<?php if ( ! empty( $description ) ) : ?>
		<div class="h3 color-gray mar-bottom_lg join-movement w-68p">
			<?php echo wp_kses_post( $description ); ?>
			<?php if ( ! empty( $button_label ) && ! empty( $button_link ) ) : ?>
			<p>
				<a class="btn btn-blue" href="<?php echo esc_url( $button_link ); ?>">
					<?php echo esc_html( $button_label ); ?>
				</a>
			</p>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
