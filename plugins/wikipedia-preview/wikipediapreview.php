<?php
/**
 * Plugin Name: Wikipedia Preview
 * Plugin URI: https://github.com/wikimedia/wikipedia-preview
 * Description: Provide context to your readers by displaying a Wikipedia article preview when a reader clicks or hovers over a word or concept.
 * Text Domain: wikipedia-preview
 * Version: 1.9.0
 * Requires at least: 6.1
 * Requires PHP: 5.6.39
 * Author: Wikimedia Foundation
 * Author URI: https://wikimediafoundation.org/
 * License: MIT
 * License URI: https://github.com/wikimedia/wikipedia-preview/blob/main/LICENSE
 */

DEFINE( 'WIKIPEDIA_PREVIEW_PLUGIN_VERSION', '1.9.0' );

function wikipediapreview_enqueue_scripts() {
	if ( ! in_array( get_post_type(), array( 'post', 'page' ), true ) ) {
		return;
	}
	$build_dir       = plugin_dir_url( __FILE__ ) . 'build/';
	$libs_dir        = plugin_dir_url( __FILE__ ) . 'libs/';
	$media_type_all  = 'all';
	$no_dependencies = array();
	$in_footer       = true;

	wp_enqueue_script(
		'wikipedia-preview',
		$libs_dir . 'wikipedia-preview.production.js',
		$no_dependencies,
		WIKIPEDIA_PREVIEW_PLUGIN_VERSION,
		$in_footer
	);

	wp_enqueue_script(
		'wikipedia-preview-init',
		$build_dir . 'init.js',
		$no_dependencies,
		WIKIPEDIA_PREVIEW_PLUGIN_VERSION,
		$in_footer
	);

	global $post;
	if ( isset( $post->ID ) ) {
		$options = array(
			'detectLinks' => get_post_meta( $post->ID, 'wikipediapreview_detectlinks', true ),
		);
		wp_localize_script( 'wikipedia-preview-init', 'wikipediapreview_init_options', $options );
	}

	wp_enqueue_style(
		'wikipedia-preview-link-style',
		$libs_dir . 'wikipedia-preview-link.css',
		$no_dependencies,
		WIKIPEDIA_PREVIEW_PLUGIN_VERSION,
		$media_type_all
	);
}

function wikipediapreview_detect_deletion() {
	delete_option( 'wikipediapreview_options_detect_links' );
}

function wikipediapreview_guten_enqueue() {
	if ( ! in_array( get_post_type(), array( 'post', 'page' ), true ) ) {
		return;
	}
	$build_dir       = plugin_dir_url( __FILE__ ) . 'build/';
	$libs_dir        = plugin_dir_url( __FILE__ ) . 'libs/';
	$media_type_all  = 'all';
	$no_dependencies = array();
	$in_footer       = true;

	wp_enqueue_script(
		'wikipedia-preview-edit-link',
		$build_dir . 'index.js',
		$no_dependencies,
		WIKIPEDIA_PREVIEW_PLUGIN_VERSION,
		$in_footer
	);

	wp_enqueue_style(
		'wikipedia-preview-style',
		$build_dir . 'style-index.css',
		$no_dependencies,
		WIKIPEDIA_PREVIEW_PLUGIN_VERSION,
		$media_type_all
	);

	wp_enqueue_style(
		'wikipedia-preview-link-style',
		$libs_dir . 'wikipedia-preview-link.css',
		$no_dependencies,
		WIKIPEDIA_PREVIEW_PLUGIN_VERSION,
		$media_type_all
	);
}

function myguten_set_script_translations() {
	wp_set_script_translations( 'wikipedia-preview-localization', 'wikipedia-preview' );
}

function register_detectlinks_postmeta() {
	$all_post_types = '';
	$meta_name      = 'wikipediapreview_detectlinks';
	$options        = array(
		'show_in_rest'  => true,
		'auth_callback' => true,
		'single'        => true,
		'type'          => 'boolean',
		'default'       => true, // it could default to false when the gutenburg support is released
	);
	register_post_meta( $all_post_types, $meta_name, $options );
}

function make_link( $text, $url ) {
	return '<a target="_BLANK" href="' . esc_url( $url ) . '">' . $text . '</a>';
}

function add_meta_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
	if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
		$links_array = array_merge(
			$links_array,
			array(
				make_link( __( 'Review', 'wikipedia-preview' ), 'https://wordpress.org/support/plugin/wikipedia-preview/reviews/#new-post' ),
				make_link( __( 'Support', 'wikipedia-preview' ), 'https://wordpress.org/support/plugin/wikipedia-preview/' ),
			)
		);
	}

	return $links_array;
}

add_filter( 'plugin_row_meta', 'add_meta_links', 10, 4 );
register_deactivation_hook( __FILE__, 'wikipediapreview_detect_deletion' );
add_action( 'wp_enqueue_scripts', 'wikipediapreview_enqueue_scripts' );
add_action( 'enqueue_block_editor_assets', 'wikipediapreview_guten_enqueue' );
add_action( 'init', 'myguten_set_script_translations' );
add_action( 'init', 'register_detectlinks_postmeta' );

require __DIR__ . '/banner.php';
require __DIR__ . '/intro.php';
