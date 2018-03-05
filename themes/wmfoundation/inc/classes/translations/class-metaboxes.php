<?php
/**
 * Adds meta translations to translation metabox.
 *
 * @package wmfoundation
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

		switch_to_blog( $remote_blog_id );
		$meta = get_post_custom( $remote_post->ID );
		restore_current_blog();

		$header = '';

		foreach ( $meta as $name => $value ) {
			if ( empty( $this->translation_meta_opts[ $name ] ) ) {
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

			$value = maybe_unserialize( $value[0] );

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
				$selected_value = __( 'Not set', 'wmfoundation' );

				foreach ( $data->data as $opts ) {
					$selected_value = $meta_data === $opts['value'] ? $opts['name'] : $selected_value;
				}

				// Translators: The placeholder is for the selected value.
				printf( '<p><strong>%1$s</strong>: %2$s</p>', esc_html( $opt_label ), sprintf( esc_html__( 'Selected value is: %s', 'wmfoundation' ), esc_html( $selected_value ) ) );
				break;

			case 'media':
				switch_to_blog( $remote_blog_id );

				$text = empty( $meta_data ) ? __( 'Image not selected', 'wmfoundation' ) : __( 'Selected image is:', 'wmfoundation' );

				printf(
					'<p><strong>%1$s</strong>: %2$s</p>%3$s', esc_html( $opt_label ), esc_html( $text ), wp_get_attachment_image(
						$meta_data, 'thumbnail', false, array(
							'style' => 'max-width: 200px; max-height: 200px; background: #333;',
						)
					)
				);

				restore_current_blog();
				break;

			case 'text':
			case 'textarea':
			case 'richtext':
				$lines = substr_count( $meta_data, "\n" ) + 1;
				$rows  = min( $lines, 10 );

				$text_area = sprintf(
					'<textarea class="large-text" cols="80" rows="%d$1" placeholder="%2$s" readonly>%3$s</textarea>',
					esc_attr( $rows ),
					esc_attr_x( 'No content yet.', 'placeholder for empty translation textarea', 'wmfoundation' ),
					esc_textarea( $meta_data )
				);
				printf( '<p><strong>%1$s</strong>:</p>%2$s', esc_html( $opt_label ), $text_area ); // WPCS: xss ok.
				break;

			case 'element':
				if ( is_a( $data, 'Fieldmanager_Checkbox' ) ) {
					$text = empty( $meta_data ) ? __( 'Is not checked', 'wmfoundation' ) : __( 'Is checked', 'wmfoundation' );
					printf( '<p><strong>%1$s</strong>: %2$s</p>', esc_html( $opt_label ), esc_html( $text ) );
				}
				break;

			case 'checkboxes':
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
							'wmfoundation'
						)
					), esc_html( implode( ', ', $checked ) )
				);
				break;
			case 'group':
				if ( empty( $meta_data ) ) {
					return;
				}


				foreach ( $meta_data as $group_item ) {
					printf( '<div class="wmf-translation-group"><strong class="wmf-translation-group-heading">%s</strong>', __( 'Group', 'wmfoundation' ) );
					foreach ( $group_item as $single_key => $single_value ) {
						if ( ! empty( $data->children[ $single_key ] ) ) {
							$this->show_remote_meta( $remote_blog_id, $single_value, $data->children[ $single_key ] );
						}
					}
					echo '</div>';
				}
				break;
		}
	}

}
