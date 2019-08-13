<?php
/**
 * Stories Module
 *
 * @package shiro
 */

$template_data = wmf_get_template_data();

$stories = ! empty( $template_data['stories_list'] ) ? $template_data['stories_list'] : '';

if ( empty( $stories ) || ! is_array( $stories ) || count( $stories ) < 3 ) {
	return;
}

$story_list = array_rand( array_flip( $stories ), 3 );
$headline     = ! empty( $template_data['headline'] ) ? $template_data['headline'] : '';
$description  = ! empty( $template_data['description'] ) ? $template_data['description'] : '';
$button_label = ! empty( $template_data['button_label'] ) ? $template_data['button_label'] : '';
$button_link  = ! empty( $template_data['button_link'] ) ? $template_data['button_link'] : '';

$pre_heading            = get_theme_mod( 'wmf_stories_label', __( 'Stories', 'shiro' ) );
$rand_translation_title = wmf_get_random_translation( 'wmf_stories_label' );
?>
<div class="w-100p mod-margin-bottom">
	<div class="mw-980 std-mod">
		<h3 class="h3 color-gray uppercase">
			<?php echo esc_html( $pre_heading ); ?> — <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span>
		</h3>

		<?php if ( ! empty( $headline ) ) : ?>
		<h2><?php echo esc_html( $headline ); ?></h2>
		<?php endif; ?>
	</div>

	<div class="mw-980 std-mod people-container mod-margin-bottom_xs">

		<div class="people slider-on-mobile flex flex-medium">
		<?php
		foreach ( $story_list as $story_id ) {
			$team_name = '';
			$team      = get_the_terms( $story_id, 'role' );
			if ( ! empty( $team ) && ! is_wp_error( $team ) ) {
				$team_name = $team[0]->name;
			}
			wmf_get_template_part(
				'template-parts/modules/stories/card', array(
					'title'  => get_the_title( $story_id ),
					'img_id' => get_post_thumbnail_id( $story_id ),
					'link'   => get_the_permalink( $story_id ),
					'excerpt'   => get_the_excerpt( $story_id ),
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
