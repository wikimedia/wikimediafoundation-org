<?php
/**
 * Handles general connect module which will appear on all pages unless excluded.
 *
 * @package shiro
 */

$template_args = $args;

$defaults = array(
	// Headings.
	'pre_heading'                 => get_theme_mod( 'wmf_connect_pre_heading', __( 'Connect', 'shiro-admin' ) ),
	'heading'                     => get_theme_mod( 'wmf_connect_heading', __( 'Stay up-to-date on our work.', 'shiro-admin' ) ),

	// Subscribe box.
	'subscribe_action'            => get_theme_mod( 'wmf_subscribe_action', 'https://wikimediafoundation.us11.list-manage.com/subscribe/post?u=7e010456c3e448b30d8703345&amp;id=246cd15c56' ),
	'subscribe_additional_fields' => get_theme_mod( 'wmf_subscribe_additional_fields', '<input type="hidden" value="2" name="group[4037]" id="mce-group[4037]-4037-1">' ),
	'subscribe_heading'           => get_theme_mod( 'wmf_subscribe_heading', __( 'Subscribe to our newsletter', 'shiro-admin' ) ),
	'subscribe_content'           => get_theme_mod( 'wmf_subscribe_content', __( 'Here is a brief description of the content and frequency for this newsletter. Also a promise not to spam or share personal data.', 'shiro-admin' ) ),
	'subscribe_placeholder'       => get_theme_mod( 'wmf_subscribe_placeholder', __( 'Email address', 'shiro-admin' ) ),
	'subscribe_button'            => get_theme_mod( 'wmf_subscribe_button', __( 'Subscribe', 'shiro-admin' ) ),

	// Contact box.
	'contact_heading'             => get_theme_mod( 'wmf_contact_heading', __( 'Say hello', 'shiro-admin' ) ),
	'contact_content'             => get_theme_mod( 'wmf_contact_content', __( 'How to get in touch with the team connected to this content. Whether it’s a site to visit, contact person, etc. Rich text box.', 'shiro-admin' ) ),
	'contact_link'                => get_theme_mod( 'wmf_contact_link', __( 'email@domain.url', 'shiro-admin' ) ),
	'contact_link_text'           => get_theme_mod( 'wmf_contact_link_text', __( 'email@domain.url', 'shiro-admin' ) ),
);

$rand_translation_title = wmf_get_random_translation( 'wmf_connect_pre_heading' );

// We don't want empty fields from the page to affect the output.
foreach ( $defaults as $key => $default ) {
	$template_args[ $key ] = empty( $template_args[ $key ] ) ? $default : $template_args[ $key ];
}

$contact_link_href = is_email( $template_args['contact_link'] ) ? sprintf( 'mailto:%s', $template_args['contact_link'] ) : $template_args['contact_link'];
$contact_link_text = ! empty( $template_args['contact_link_text'] ) ? $template_args['contact_link_text'] : $template_args['contact_link'];

?>

<div class="connect-container white-bg mod-margin-bottom">
	<div class="mw-980">
		<?php if ( ! empty( $template_args['pre_heading'] ) ) : ?>
			<p class="double-heading__secondary is-style-h5">
				<?php echo esc_html( $template_args['pre_heading'] ); ?>
				<?php if ( ! empty( $rand_translation_title['lang'] ) ) : ?>
				— <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span>
                <?php endif; ?>
			</p>
        <?php endif; ?>
		<?php if ( ! empty( $template_args['heading'] ) ) : ?>
			<h2 class="double-heading__primary is-style-h3">
				<?php echo esc_html( $template_args['heading'] ); ?>
			</h2>
		<?php endif; ?>

		<div class="flex flex-medium flex-space-between">

			<div class="module-mu w-48p rounded gray-module">
				<?php wmf_show_icon( 'mail' ); ?>
				<?php if ( ! empty( $template_args['subscribe_heading'] ) ) : ?>
					<h3 class="is-style-sans-h3"><?php echo esc_html( $template_args['subscribe_heading'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $template_args['subscribe_content'] ) ) : ?>
					<div class="wysiwyg">
						<?php echo wp_kses_post( wpautop( $template_args['subscribe_content'] ) ); ?>
					</div>
				<?php endif; ?>
				<div class="email-signup">
					<form action="<?php echo esc_url( $template_args['subscribe_action'] ); ?>" method="post" target="_blank">
						<label for="wmf-subscribe-input-email" class="screen-reader-text"><?php echo esc_html( $template_args['subscribe_placeholder'] ); ?></label>
						<div class="mailchimp-subscribe__input-container">
							<div class="mailchimp-subscribe__column-input">
								<input id="wmf-subscribe-input-email" type="email" placeholder="<?php echo esc_attr( $template_args['subscribe_placeholder'] ); ?>" name="EMAIL" class="mailchimp-subscribe__input-field" required>
							</div>
							<div class="mailchimp-subscribe__column-button">
								<button class="wp-block-shiro-button" type="submit" name="button"><?php echo esc_html( $template_args['subscribe_button'] ); ?></button>
							</div>
						</div>
						<?php if ( ! empty( $template_args['subscribe_additional_fields'] ) ) : ?>
						<div class="mailchimp-subscribe__description">
							<?php
								echo \WMF\Editor\Blocks\MailChimpSubscribe\kses_input_fields( $template_args['subscribe_additional_fields'] );
							?>
						</div>
						<?php endif; ?>
					</form>
				</div>
			</div><!-- End .multi-use -->

			<div class="module-mu w-48p rounded">
				<?php wmf_show_icon( 'userAvatar' ); ?>
				<?php if ( ! empty( $template_args['contact_heading'] ) ) : ?>
					<h3 class="is-style-sans-h3"><?php echo esc_html( $template_args['contact_heading'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $template_args['contact_content'] ) ) : ?>
					<div class="wysiwyg">
						<?php echo wp_kses_post( wpautop( $template_args['contact_content'] ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $contact_link_href ) ) : ?>
					<div class="wysiwyg">
						<!-- Single link -->
						<a class="arrow-link" href="<?php echo esc_url( $contact_link_href ); ?>" target="_blank"><?php echo esc_html( $contact_link_text ); ?></a>
					</div>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/modules/social/follow', 'horizontal', $template_args ); ?>
			</div><!-- End .multi-use -->
		</div>
	</div>
</div>
