<?php
/**
 * Handles general connect module which will appear on all pages unless excluded.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

$defaults = array(
	// Headings.
	'pre_heading'            => get_theme_mod( 'wmf_connect_pre_heading', __( 'Connect', 'wmfoundation' ) ),
	'heading'                => get_theme_mod( 'wmf_connect_heading', __( 'Stay up-to-date on our work.', 'wmfoundation' ) ),
	'rand_translation_title' => wmf_get_random_translation( 'wmf_connect_pre_heading' ),

	// Subscribe box.
	'subscribe_heading'      => get_theme_mod( 'wmf_subscribe_heading', __( 'Subscribe to our newsletter', 'wmfoundation' ) ),
	'subscribe_content'      => get_theme_mod( 'wmf_subscribe_content', __( 'Here is a brief description of the content and frequency for this newsletter. Also a promise not to spam or share personal data.', 'wmfoundation' ) ),
	'subscribe_placeholder'  => get_theme_mod( 'wmf_subscribe_placeholder', __( 'Email address', 'wmfoundation' ) ),
	'subscribe_button'       => get_theme_mod( 'wmf_subscribe_button', __( 'Subscribe', 'wmfoundation' ) ),

	// Contact box.
	'contact_heading'        => get_theme_mod( 'wmf_contact_heading', __( 'Say hello', 'wmfoundation' ) ),
	'contact_content'        => get_theme_mod( 'wmf_contact_content', __( 'How to get in touch with the team connected to this content. Whether it’s a site to visit, contact person, etc. Rich text box.', 'wmfoundation' ) ),
	'contact_link'           => get_theme_mod( 'wmf_contact_link', __( 'email@domain.url', 'wmfoundation' ) ),
	'contact_link_text'      => get_theme_mod( 'wmf_contact_link_text', __( 'email@domain.url', 'wmfoundation' ) ),
);

// We don't want empty fields from the page to affect the output.
foreach ( $defaults as $key => $default ) {
	$template_args[ $key ] = empty( $template_args[ $key ] ) ? $default : $template_args[ $key ];
}

$contact_link_href = is_email( $template_args['contact_link'] ) ? sprintf( 'mailto:%s', $template_args['contact_link'] ) : $template_args['contact_link'];
$contact_link_text = ! empty( $template_args['contact_link_text'] ) ? $template_args['contact_link_text'] : $template_args['contact_link'];

?>

<div class="connect-container white-bg mod-margin-bottom">
	<div class="mw-1360">
		<?php if ( ! empty( $template_args['pre_heading'] ) ) : ?>
			<h3 class="h3 color-gray uppercase"><?php echo esc_html( $template_args['pre_heading'] ); ?> — <span><?php echo esc_html( $template_args['rand_translation_title'] ); ?></span></h3>
		<?php endif; ?>
		<?php if ( ! empty( $template_args['heading'] ) ) : ?>
			<h2 class="h2"><?php echo esc_html( $template_args['heading'] ); ?></h2>
		<?php endif; ?>

		<div class="flex flex-medium">

			<div class="module-mu w-50p">
				<?php if ( ! empty( $template_args['subscribe_heading'] ) ) : ?>
					<h3 class="h3"><?php echo esc_html( $template_args['subscribe_heading'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $template_args['subscribe_content'] ) ) : ?>
					<div class="wysiwyg">
						<?php echo wp_kses_post( wpautop( $template_args['subscribe_content'] ) ); ?>
					</div>
				<?php endif; ?>
				<div class="email-signup">
					<label for="wmf-subscribe-input-email" class="sr-only"><?php echo esc_html( $template_args['subscribe_placeholder'] ); ?></label>
					<input id="wmf-subscribe-input-email" type="text" placeholder="<?php echo esc_attr( $template_args['subscribe_placeholder'] ); ?>">
					<?php wmf_show_icon( 'mail' ); ?>
					<button class="btn btn-pink" type="button" name="button"><?php echo esc_html( $template_args['subscribe_button'] ); ?></button>
				</div>
			</div><!-- End .multi-use -->

			<div class="module-mu w-32p">
				<?php if ( ! empty( $template_args['contact_heading'] ) ) : ?>
					<h3 class="h3"><?php echo esc_html( $template_args['contact_heading'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $template_args['contact_content'] ) ) : ?>
					<div class="wysiwyg">
						<?php echo wp_kses_post( wpautop( $template_args['contact_content'] ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $contact_link_href ) ) : ?>
				<div class="link-list hover-highlight uppercase">
					<!-- Single link -->
					<a href="<?php echo esc_url( $contact_link_href ); ?>" target="_blank"><?php echo esc_attr( $contact_link_text ); ?></a>
				</div>
				<?php endif; ?>
			</div><!-- End .multi-use -->

			<?php wmf_get_template_part( 'template-parts/modules/social/follow', $template_args, 'vertical' ); ?>
		</div>
	</div>
</div>
