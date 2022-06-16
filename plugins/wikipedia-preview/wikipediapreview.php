<?php
/**
 * Plugin Name: Wikipedia Preview
 * Plugin URI: https://github.com/wikimedia/wikipedia-preview
 * Description: Provide context to your readers by displaying a Wikipedia article preview when a reader clicks or hovers over a word or concept.
 * Text Domain: wikipedia-preview
 * Version: 1.3.0
 * Requires at least: 4.6
 * Requires PHP: 5.6.39
 * Author: Wikimedia Foundation
 * Author URI: https://wikimediafoundation.org/
 * License: MIT
 * License URI: https://github.com/wikimedia/wikipedia-preview/blob/main/LICENSE
 */

DEFINE( 'WIKIPEDIA_PREVIEW_PLUGIN_VERSION', '1.3.0' );

function wikipediapreview_enqueue_scripts() {
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

register_deactivation_hook( __FILE__, 'wikipediapreview_detect_deletion' );
add_action( 'wp_enqueue_scripts', 'wikipediapreview_enqueue_scripts' );
add_action( 'enqueue_block_editor_assets', 'wikipediapreview_guten_enqueue' );
add_action( 'init', 'myguten_set_script_translations' );
add_action( 'init', 'register_detectlinks_postmeta' );
