<?php
/**
 * Placeholder shortcode for the FM lists field.
 */

namespace WMF\Shortcodes\Lists;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_list', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_list] wrapper shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function shortcode_callback( $atts = [] ) {
	$html = shortcode_content( get_the_ID() );

	return wp_kses_post( $html );
}


/**
 * @param int $post_id The post ID.
 *
 * @return string
 */
function shortcode_content( int $post_id ) : string {
	$list_data = (array) get_post_meta( $post_id, 'list', true );
	if ( empty( $list_data ) ) {
		return '';
	}

	ob_start();
	foreach ( $list_data as $i => $list_section ) {
		?>
		<div class="mod-margin-bottom wysiwyg">
			<?php
			if ( ! empty( $list_section['title'] ) ) :
				// Stories injected as list items have headings with link targets.
				if ( ! empty( $list_section['link'] ) ) :
					?>
					<h2 id="section-<?php echo esc_attr( $i + 1 ); ?>" class="story-link">
						<a href="<?php echo esc_url( $list_section['link'] ); ?>">
							<?php echo esc_html( $list_section['title'] ); ?>
						</a>
					</h2>
				<?php
				else :
					?>
					<h2 id="section-<?php echo esc_attr( $i + 1 ); ?>">
						<?php echo esc_html( $list_section['title'] ); ?>
					</h2>
				<?php
				endif;
			endif;
			?>

			<?php if ( ! empty( $list_section['description'] ) ) : ?>
				<div class="mar-bottom">
					<?php echo wp_kses_post( do_shortcode( wpautop( $list_section['description'] ) ) ); ?>
				</div>
			<?php endif; ?>

			<ul class="link-list">
				<?php
				if ( isset( $list_section['links'] ) ) :
					foreach ( $list_section['links'] as $link ) :
						wmf_get_template_part( 'template-parts/modules/list/item', $link );
					endforeach;
				endif;
				?>
			</ul>
		</div>

		<?php
	}

	return (string) ob_get_clean();
}
