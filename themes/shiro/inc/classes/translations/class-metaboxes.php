<?php
/**
 * Adds meta translations to translation metabox.
 *
 * @package shiro
 */

namespace WMF\Translations;

/**
 * Handles callbacks for fm_element_markup_end and mlp_translation_meta_box_bottom to control meta output..
 */
class Metaboxes {

	/**
	 * The translation options used in field manager.
	 *
	 * @var array
	 */
	public $translation_meta_opts = array();

	/**
	 * The object instance.
	 *
	 * @var Metaboxes
	 */
	public static $instance;

	/**
	 * Gets the current instance of the object.
	 *
	 * @return Metaboxes
	 */
	public static function get_instance() {
		if ( empty( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initiates the set_translation_meta_opts method.
	 *
	 * @param string              $out    The output.
	 * @param \Fieldmanager_Field $object The Fieldmanager_Field object.
	 *
	 * @return mixed
	 */
	public static function fm_element_markup_end( $out, $object ) {
		static::get_instance()->set_translation_meta_opts( $object );

		return $out;
	}

	/**
	 * Sets the $translation_meta_opts property.
	 *
	 * @param \Fieldmanager_Field $object The Fieldmanager_Field object.
	 */
	public function set_translation_meta_opts( $object ) {
		if ( ! empty( $object->name ) ) {
			$this->translation_meta_opts[ $object->name ] = $object;
		}
	}

	/**
	 * Initiates the translation_meta_output method.
	 *
	 * @param \WP_Post $post           The current post.
	 * @param int      $remote_blog_id The remote blog ID.
	 * @param \WP_POST $remote_post    The remote post.
	 */
	public static function mlp_translation_meta_box_bottom( $post, $remote_blog_id, $remote_post ) {
		if ( is_wp_error( $post ) ) {
			return; // Shouldn't happen but this way the IDE doesn't think $post is unused.
		}
		static::get_instance()->translation_meta_output( $remote_blog_id, $remote_post );
	}

	/**
	 * Loops the post meta for the remote post then outputs it in the translation meta box.
	 *
	 * @param int      $remote_blog_id The remote blog ID.
	 * @param \WP_Post $remote_post    The remote post.
	 */
	public function translation_meta_output( $remote_blog_id, $remote_post ) {
		global $wp_meta_boxes;

		if ( wmf_is_main_site() ) {
			return;
		}

		switch_to_blog( $remote_blog_id );
		$meta = get_post_custom( $remote_post->ID );
		restore_current_blog();

		$header = '';

		if ( empty( $meta ) || ! is_array( $meta ) ) {
			return;
		}

		foreach ( $meta as $name => $value ) {
			if ( empty( $this->translation_meta_opts[ $name ] ) ) {
				continue;
			}

			if ( empty( $value[0] ) ) {
				continue;
			}

			$value = maybe_unserialize( $value[0] );

			if ( empty( $value ) || ! is_array( $value ) ) {
				continue;
			}

			foreach ( $wp_meta_boxes['page']['normal']['default'] as $meta_box ) {
				if (
					isset( $meta_box['callback'][0] ) &&
					is_a( $meta_box['callback'][0], 'Fieldmanager_Context' ) &&
					$meta_box['callback'][0]->fm === $this->translation_meta_opts[ $name ]
				) {
					$header = $meta_box['title'];
				}
			}

			$label = empty( $this->translation_meta_opts[ $name ]->label ) ? '' : $this->translation_meta_opts[ $name ]->label;

			if ( ! empty( $header ) ) {
				printf( '<h3>%s</h3>', esc_html( $header ) );
			}

			if ( ! empty( $label ) ) {
				printf( '<h4>%s</h4>', esc_html( $label ) );
			}

			foreach ( $value as $key => $meta_data ) {
				if ( ! empty( $this->translation_meta_opts[ $name ]->children[ $key ] ) ) {
					$this->show_remote_meta( $remote_blog_id, $meta_data, $this->translation_meta_opts[ $name ]->children[ $key ] );
				}
			}

			echo '<hr class="wmf-translation-seperator"/>';
		}
	}

	/**
	 * Processes the type of meta and formats the meta output.
	 *
	 * @param int     $remote_blog_id The remote blog ID.
	 * @param mixed   $meta_data      The metadata.
	 * @param \object $data           The data about the metadata from \Fieldmanager_Field.
	 */
	public function show_remote_meta( $remote_blog_id, $meta_data, $data ) {
		$opt_label = $data->label;

		switch ( $data->field_class ) {
			case 'radio':
				$this->radio_field( $opt_label, $meta_data, $data );
				break;

			case 'media':
				$this->media_field( $opt_label, $meta_data, $remote_blog_id );
				break;

			case 'text':
			case 'textarea':
			case 'richtext':
				$this->text_field( $opt_label, $meta_data );
				break;

			case 'element':
				if ( is_a( $data, 'Fieldmanager_Checkbox' ) ) {
					$this->checkbox_field( $opt_label, $meta_data );
				}
				break;

			case 'checkboxes':
				$this->checkboxes_field( $opt_label, $meta_data, $data, $remote_blog_id );
				break;
			case 'group':
				$this->group_field( $meta_data, $data, $remote_blog_id );
				break;
		}
	}

	/**
	 * Outputs details about a Radio field.
	 *
	 * @param string  $opt_label      Label for the option.
	 * @param mixed   $meta_data      The metadata.
	 * @param \object $data           The data about the metadata from \Fieldmanager_Field.
	 */
	public function radio_field( $opt_label, $meta_data, $data ) {
		$selected_value = __( 'Not set', 'shiro-admin' );

		foreach ( $data->data as $opts ) {
			$selected_value = $meta_data === $opts['value'] ? $opts['name'] : $selected_value;
		}

		// Translators: The placeholder is for the selected value.
		printf( '<p><strong>%1$s</strong>: %2$s</p>', esc_html( $opt_label ), sprintf( esc_html__( 'Selected value is: %s', 'shiro-admin' ), esc_html( $selected_value ) ) );
	}

	/**
	 * Outputs details about a Media field.
	 *
	 * @param string $opt_label      Label for the option.
	 * @param mixed  $meta_data      The metadata.
	 * @param int    $remote_blog_id The remote blog ID.
	 */
	public function media_field( $opt_label, $meta_data, $remote_blog_id ) {
		switch_to_blog( $remote_blog_id );

		$text = empty( $meta_data ) ? __( 'Image not selected', 'shiro-admin' ) : __( 'Selected image is:', 'shiro-admin' );

		printf(
			'<p><strong>%1$s</strong>: %2$s</p>%3$s', esc_html( $opt_label ), esc_html( $text ), wp_get_attachment_image(
				$meta_data, 'thumbnail', false, array(
					'style' => 'max-width: 200px; max-height: 200px; background: #333;',
				)
			)
		);

		restore_current_blog();
	}

	/**
	 * Outputs details about a Text field.
	 *
	 * This includes inputs, rich text fields, and text areas.
	 *
	 * @param string $opt_label Label for the option.
	 * @param mixed  $meta_data The metadata.
	 */
	public function text_field( $opt_label, $meta_data ) {
		$lines = substr_count( $meta_data, "\n" ) + 1;
		$rows  = min( $lines, 10 );

		$text_area = sprintf(
			'<textarea class="large-text" cols="80" rows="%d$1" placeholder="%2$s" readonly>%3$s</textarea>',
			esc_attr( $rows ),
			esc_attr_x( 'No content yet.', 'placeholder for empty translation textarea', 'shiro-admin' ),
			esc_textarea( $meta_data )
		);
		printf( '<p><strong>%1$s</strong>:</p>%2$s', esc_html( $opt_label ), $text_area ); // WPCS: xss ok.
	}

	/**
	 * Outputs details about a Checkbox field.
	 *
	 * @param string $opt_label Label for the option.
	 * @param mixed  $meta_data The metadata.
	 */
	public function checkbox_field( $opt_label, $meta_data ) {
		$text = empty( $meta_data ) ? __( 'Is not checked', 'shiro-admin' ) : __( 'Is checked', 'shiro-admin' );
		printf( '<p><strong>%1$s</strong>: %2$s</p>', esc_html( $opt_label ), esc_html( $text ) );
	}

	/**
	 * Outputs details about a Checkboxes field.
	 *
	 * @param string  $opt_label      Label for the option.
	 * @param mixed   $meta_data      The metadata.
	 * @param \object $data           The data about the metadata from \Fieldmanager_Field.
	 * @param int     $remote_blog_id The remote blog ID.
	 */
	public function checkboxes_field( $opt_label, $meta_data, $data, $remote_blog_id ) {
		$checked = array();

		foreach ( $meta_data as $value ) {
			$is_remote_post = false;

			if ( is_numeric( $value ) ) {
				switch_to_blog( $remote_blog_id );

				$post = get_post( $value );

				if ( $post && ! is_wp_error( $post ) ) {
					$checked[]      = $post->post_title;
					$is_remote_post = true;
				}

				restore_current_blog();
			}

			if ( false === $is_remote_post && isset( $data->options[ $value ] ) ) {
				$checked[] = $data->options[ $value ];
			}
		}
		printf(
			'<p><strong>%1$s</strong>: %2$s <strong>%3$s</strong></p>', esc_html( $opt_label ), esc_html(
				_n(
					'Checked option is:',
					'Checked options are:',
					count( $checked ),
					'shiro-admin'
				)
			), esc_html( implode( ', ', $checked ) )
		);
	}

	/**
	 * Outputs details about a Group field.
	 *
	 * @param mixed   $meta_data      The metadata.
	 * @param \object $data           The data about the metadata from \Fieldmanager_Field.
	 * @param int     $remote_blog_id The remote blog ID.
	 */
	public function group_field( $meta_data, $data, $remote_blog_id ) {
		if ( empty( $meta_data ) ) {
			return;
		}

		foreach ( $meta_data as $group_item ) {
			printf( '<div class="wmf-translation-group"><strong class="wmf-translation-group-heading">%s</strong>', esc_html__( 'Group', 'shiro-admin' ) );
			foreach ( $group_item as $single_key => $single_value ) {
				if ( ! empty( $data->children[ $single_key ] ) ) {
					$this->show_remote_meta( $remote_blog_id, $single_value, $data->children[ $single_key ] );
				}
			}
			echo '</div>';
		}
	}

}
