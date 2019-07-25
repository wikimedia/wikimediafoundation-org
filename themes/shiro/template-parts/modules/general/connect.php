<?php
/**
 * Handles general connect module which will appear on all pages unless excluded.
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

$defaults = array(
	// Headings.
	'pre_heading'                 => get_theme_mod( 'wmf_connect_pre_heading', __( 'Connect', 'shiro' ) ),
	'heading'                     => get_theme_mod( 'wmf_connect_heading', __( 'Stay up-to-date on our work.', 'shiro' ) ),

	// Subscribe box.
	'subscribe_action'            => get_theme_mod( 'wmf_subscribe_action', 'https://wikimediafoundation.us11.list-manage.com/subscribe/post?u=7e010456c3e448b30d8703345&amp;id=246cd15c56' ),
	'subscribe_additional_fields' => get_theme_mod( 'wmf_subscribe_additional_fields', '<input type="hidden" value="2" name="group[4037]" id="mce-group[4037]-4037-1">' ),
	'subscribe_heading'           => get_theme_mod( 'wmf_subscribe_heading', __( 'Subscribe to our newsletter', 'shiro' ) ),
	'subscribe_content'           => get_theme_mod( 'wmf_subscribe_content', __( 'Here is a brief description of the content and frequency for this newsletter. Also a promise not to spam or share personal data.', 'shiro' ) ),
	'subscribe_placeholder'       => get_theme_mod( 'wmf_subscribe_placeholder', __( 'Email address', 'shiro' ) ),
	'subscribe_button'            => get_theme_mod( 'wmf_subscribe_button', __( 'Subscribe', 'shiro' ) ),

	// Contact box.
	'contact_heading'             => get_theme_mod( 'wmf_contact_heading', __( 'Say hello', 'shiro' ) ),
	'contact_content'             => get_theme_mod( 'wmf_contact_content', __( 'How to get in touch with the team connected to this content. Whether it’s a site to visit, contact person, etc. Rich text box.', 'shiro' ) ),
	'contact_link'                => get_theme_mod( 'wmf_contact_link', __( 'email@domain.url', 'shiro' ) ),
	'contact_link_text'           => get_theme_mod( 'wmf_contact_link_text', __( 'email@domain.url', 'shiro' ) ),
);

$rand_translation_title = wmf_get_random_translation( 'wmf_connect_pre_heading' );

if ($rand_translation_title['content'] = $template_args['pre_heading']) {
    $rand_translation_title = '';
}
    
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
			<h3 class="h3 color-gray uppercase"><?php echo esc_html( $template_args['pre_heading'] ); ?>
				<?php if ( ! empty( $rand_translation_title['content'] ) ) : ?>
				— <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span>
                <?php endif; ?></h3>
        <?php endif; ?>
		<?php if ( ! empty( $template_args['heading'] ) ) : ?>
			<h2 class="h2"><?php echo esc_html( $template_args['heading'] ); ?></h2>
		<?php endif; ?>

		<div class="flex flex-medium flex-space-between">

			<div class="module-mu w-48p rounded gray-module">
				<?php wmf_show_icon( 'mail' ); ?>
				<?php if ( ! empty( $template_args['subscribe_heading'] ) ) : ?>
					<h3 class="h2"><?php echo esc_html( $template_args['subscribe_heading'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $template_args['subscribe_content'] ) ) : ?>
					<div class="wysiwyg">
						<?php echo wp_kses_post( wpautop( $template_args['subscribe_content'] ) ); ?>
					</div>
				<?php endif; ?>
				<div class="email-signup">
					<form action="<?php echo esc_url( $template_args['subscribe_action'] ); ?>" method="post" target="_blank">
						<label for="wmf-subscribe-input-email" class="sr-only"><?php echo esc_html( $template_args['subscribe_placeholder'] ); ?></label>
						<div class="flex flex-medium flex-wrap fifty-fifty">
							<div class="w-68p">
								<input id="wmf-subscribe-input-email" type="email" placeholder="<?php echo esc_attr( $template_args['subscribe_placeholder'] ); ?>" name="EMAIL" required>
							</div>
							<div class="w-32p">
								<button class="btn btn-blue" type="submit" name="button"><?php echo esc_html( $template_args['subscribe_button'] ); ?></button>
							</div>
						</div>
						<?php if ( ! empty( $template_args['subscribe_additional_fields'] ) ) : ?>
						<div class="field-group input-group">
							<?php
							echo wp_kses(
								$template_args['subscribe_additional_fields'], array(
									'input'  => array(
										'type'        => array(),
										'name'        => array(),
										'id'          => array(),
										'class'       => array(),
										'required'    => array(),
										'value'       => array(),
										'checked'     => array(),
										'placeholder' => array(),
									),
									'label'  => array(
										'for'   => array(),
										'class' => array(),
									),
									'select' => array(
										'name'     => array(),
										'id'       => array(),
										'class'    => array(),
										'required' => array(),
									),
									'option' => array(
										'value'    => array(),
										'selected' => array(),
									),
								)
							);
							?>
						</div>
						<?php endif; ?>
					</form>
				</div>
			</div><!-- End .multi-use -->

			<div class="module-mu w-48p rounded">
				<?php wmf_show_icon( 'person' ); ?>
				<?php if ( ! empty( $template_args['contact_heading'] ) ) : ?>
					<h3 class="h2"><?php echo esc_html( $template_args['contact_heading'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $template_args['contact_content'] ) ) : ?>
					<div class="wysiwyg">
						<?php echo wp_kses_post( wpautop( $template_args['contact_content'] ) ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $contact_link_href ) ) : ?>
					<!-- Single link -->
					<a class="arrow-link" href="<?php echo esc_url( $contact_link_href ); ?>" target="_blank"><?php echo esc_html( $contact_link_text ); ?></a>
				<?php endif; ?>
				<?php wmf_get_template_part( 'template-parts/modules/social/follow', $template_args, 'horizontal' ); ?>
			</div><!-- End .multi-use -->
		</div>
	</div>
</div>
