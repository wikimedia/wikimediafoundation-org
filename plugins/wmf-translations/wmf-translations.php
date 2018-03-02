<?php
/*
Plugin Name: Wikimedia Foundation Translations
Plugin URI:  https://wikimediafoundation.com/
Description: Adds additional translation functionality to Multilingual Press.
Version:     1.0.0
Author:      ReaktivStudios
Author URI:  http://reaktivstudios.com
Text Domain: wmf-translations
Domain Path: /languages
 */

defined( 'ABSPATH' ) or die( 'Really?' );

$wmf_translation_meta_opts = array();

add_action( 'mlp_translation_meta_box_bottom', function( $post, $remote_blog_id, $remote_post ) {
	global $wmf_translation_meta_opts, $wp_meta_boxes;

	switch_to_blog( $remote_blog_id );
	$meta = get_post_custom( $remote_post->ID );
	restore_current_blog();

	$header = '';

	foreach ( $meta as $name => $value ) {
		if ( empty( $wmf_translation_meta_opts[ $name ] ) ) {
			continue;
		}

		foreach ( $wp_meta_boxes['page']['normal']['default'] as $meta_box ) {

			if (
				isset( $meta_box['callback'][0] ) &&
				is_a( $meta_box['callback'][0], 'Fieldmanager_Context' ) &&
				$meta_box['callback'][0]->fm === $wmf_translation_meta_opts[ $name ]
			) {
				$header = $meta_box['title'];
			}
		}

		$label = empty( $wmf_translation_meta_opts[ $name ]->label ) ? '' : $wmf_translation_meta_opts[ $name ]->label;

		if ( ! empty( $header ) ) {
			printf( '<h3>%s</h3>', esc_html( $header ) );
		}

		if ( ! empty( $label ) ) {
			printf( '<h4>%s</h4>', esc_html( $label ) );
		}

		$value = maybe_unserialize( $value[0] );

		foreach ( $value as $key => $maybe_single ) {

			if ( ! empty( $wmf_translation_meta_opts[ $name ]->children[ $key ] ) ) {

				show_remote_meta( $remote_blog_id, $maybe_single, $wmf_translation_meta_opts[ $name ]->children[ $key ] );

			}
		}
	}
}, 10, 3 );

add_filter( 'fm_element_markup_end', function( $out, $object ) {
	global $wmf_translation_meta_opts;

	if ( ! empty( $object->name ) ) {
		$wmf_translation_meta_opts[ $object->name ] = $object;
	}

	return $out;
}, 10, 2 );

function show_remote_meta( $remote_blog_id, $maybe_single, $data ) {

	$opt_label = $data->label;

	switch ( $data->field_class ) {
		case 'radio' :
			$selected_value = __( 'Not set', 'wmfoundation' );

			foreach ( $data->data as $opts ) {
				$selected_value = $maybe_single === $opts['value'] ? $opts['name'] : $selected_value;
			}

			printf( '<p><strong>%1$s</strong>: %2$s</p>', esc_html( $opt_label ), sprintf( esc_html__( 'Selected value is: %s', 'wmfoundation' ), $selected_value ) );
			break;

		case 'media':
			switch_to_blog( $remote_blog_id );

			$text = empty( $maybe_single ) ? __( 'Image not selected', 'wmfoundation' ) : __( 'Selected image is:', 'wmfoundation' );

			printf( '<p><strong>%1$s</strong>: %2$s</p>%3$s', esc_html( $opt_label ), $text, wp_get_attachment_image( $maybe_single, 'thumbnail', false, array(
				'style' => 'max-width: 200px; max-height: 200px; background: #333;',
			) ) );

			restore_current_blog();
			break;

		case 'text':
		case 'textarea':
		case 'richtext':
			$lines = substr_count( $maybe_single, "\n" ) + 1;
			$rows  = min( $lines, 10 );

			$text_area = sprintf(
				'<textarea class="large-text" cols="80" rows="%d$1" placeholder="%2$s" readonly>%3$s</textarea>',
				esc_attr( $rows ),
				esc_attr_x( 'No content yet.', 'placeholder for empty translation textarea', 'multilingual-press' ),
				esc_textarea( $maybe_single )
			);
			printf( '<p><strong>%1$s</strong>:</p>%2$s', esc_html( $opt_label ), $text_area );
			break;

		case 'element':
			if ( is_a( $data, 'Fieldmanager_Checkbox' ) ) {
				$text = empty( $maybe_single ) ? __( 'Is not checked', 'wmfoundation' ) : __( 'Is checked', 'wmfoundation' );
				printf( '<p><strong>%1$s</strong>: %2$s</p>', esc_html( $opt_label ), esc_html( $text ) );
			} else {
				echo '<pre><code>'; var_dump( $data ); echo '</code></pre>'; exit;
			}
			break;

		case 'checkboxes':
			$checked = array();

			foreach ( $maybe_single as $value ) {
				$is_remote_post = false;

				if ( is_numeric( $value ) ) {
					switch_to_blog( $remote_blog_id );

					$post = get_post( $value );

					if ( $post && ! is_wp_error( $post ) ) {
						$checked[] = $post->post_title;
						$is_remote_post = true;
					}

					restore_current_blog();
				}

				if ( false === $is_remote_post && isset( $data->options[ $value ] ) ) {
					$checked[] = $data->options[ $value ];
				}
			}
			printf( '<p><strong>%1$s</strong>: %2$s <strong>%3$s</strong></p>', esc_html( $opt_label ), esc_html( _n(
				'Checked option is:',
				'Checked options are:',
				count( $checked ),
				'wmfoundation' ) ), implode( ', ', $checked ) );
			break;
		case 'group':
			if ( empty( $maybe_single ) ) {
				return;
			}
			foreach ( $maybe_single as $group_item ) {
				foreach ( $group_item as $single_key => $single_value ) {
					if ( ! empty( $data->children[ $single_key ] ) ) {
						show_remote_meta( $remote_blog_id, $single_value, $data->children[ $single_key ] );
					}
				}
			}
			break;
		default:
			echo '<pre><code>'; var_dump( $data ); echo '</code></pre>'; exit;
	}
	
}