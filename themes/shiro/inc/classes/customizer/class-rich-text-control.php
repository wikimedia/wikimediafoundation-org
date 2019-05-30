<?php
/**
 * Theme Customizer Rich Text custom control.
 *
 * @package shiro
 */

namespace WMF\Customizer;

use WP_Customize_Control;

/**
 * Class Rich_Text_Control
 */
class Rich_Text_Control extends WP_Customize_Control {

	/**
	 * Control's Type.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	public $type = 'textarea';

	/**
	 * Constructor.
	 *
	 * Supplied `$args` override class property defaults.
	 *
	 * If `$args['settings']` is not defined, use the $id as the setting ID.
	 *
	 * @since 3.4.0
	 *
	 * @param \WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string                $id      Control ID.
	 * @param array                 $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$editor_settings = isset( $args['editor_settings'] ) ? $args['editor_settings'] : array();

		$default_settings = array(
			'media_buttons' => false,
			'teeny'         => true,
			'quicktags'     => false,
		);

		$this->input_attrs['data-editor'] = wp_parse_args( $editor_settings, $default_settings );
	}

	/**
	 * Render the control's content.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		$this->filter_editor_setting_link();
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php wp_editor( $this->value(), $this->id, $this->input_attrs['data-editor'] ); ?>
		</label>
		<?php
		do_action( 'admin_print_footer_scripts' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	}

	/**
	 * Adds filter to the_editor so customizer control works.
	 */
	public function filter_editor_setting_link() {
		add_filter( 'the_editor', array( $this, 'the_editor' ) );
	}

	/**
	 * Filters the editor text area so the customizer control works.
	 *
	 * @param  string $output The current output.
	 * @return string
	 */
	public function the_editor( $output ) {
		return preg_replace( '/<textarea/', '<textarea ' . $this->get_link(), $output, 1 );
	}

	/**
	 * Enqueue control related scripts/styles.
	 */
	public function enqueue() {
		wp_enqueue_editor();
		wp_enqueue_script(
			'rkv-customize-editor-control',
			get_template_directory_uri() . '/assets/dist/admin/customize-control.js',
			array(
				'editor',
			),
			filemtime( trailingslashit( get_template_directory() ) . 'assets/dist/admin/customize-control.js' ),
			true
		);
	}
}
