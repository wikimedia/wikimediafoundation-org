<?php

register_activation_hook( __DIR__ . '/wikipediapreview.php', 'wikipediapreview_activate' );
add_action( 'admin_init', 'wikipediapreview_redirect' );

function wikipediapreview_activate() {
	add_option( 'wikipediapreview_do_activation_redirect', true );
}

function wikipediapreview_redirect() {
	if ( get_option( 'wikipediapreview_do_activation_redirect', false ) ) {
		delete_option( 'wikipediapreview_do_activation_redirect' );
		// phpcs:ignore
		$multi                 = isset( $_GET['activate-multi'] );
		$classic_editor_active = is_plugin_active( 'classic-editor/classic-editor.php' );
		$can_manage_options    = current_user_can( 'manage_options' );
		if ( ! $multi && ! $classic_editor_active && $can_manage_options ) {
			wp_safe_redirect( 'options-general.php?page=wikipediapreview_intro' );
			exit();
		}
	}
}

add_action( 'admin_menu', 'wikipediapreview_intro_submenu_page' );

function wikipediapreview_intro_submenu_page() {
	$submenu = add_submenu_page(
		'options-general.php',
		__( 'Wikipedia Preview', 'wikipedia-preview' ),
		__( 'Wikipedia Preview', 'wikipedia-preview' ),
		'manage_options',
		'wikipediapreview_intro',
		'wikipediapreview_intro_submenu_page_callback'
	);
	add_action( 'load-' . $submenu, 'wikipediapreview_load_style' );
	# Remove the submenu right away so that it is not permanent under Settings menu
	remove_submenu_page( 'options-general.php', 'wikipediapreview_intro' );
}

function wikipediapreview_load_style() {
	add_action( 'admin_enqueue_scripts', 'wikipediapreview_enqueue_style' );
}

function wikipediapreview_enqueue_style() {
	wp_register_style( 'wikipediapreview_intro_style', plugin_dir_url( __FILE__ ) . 'intro.css', array(), '1' );
	wp_enqueue_style( 'wikipediapreview_intro_style' );
}

define(
	'WIKIPEDIAPREVIEW_IMAGE_TAGS',
	array(
		'img' => array(
			'src'   => array(),
			'class' => array(),
		),
	)
);

function wikipediapreview_image( $filename, $class_suffix ) {
	$src = plugin_dir_url( __FILE__ ) . 'images/' . $filename;
	return "<img src=\"$src\" class=\"wikipediapreview-intro-$class_suffix\" />";
}

function wikipediapreview_intro_submenu_page_callback() {
	$img_wordmark     = wikipediapreview_image( 'wordmark.png', 'wordmark' );
	$title            = __( 'Enhance your website with free knowledge straight from Wikipedia!', 'wikipedia-preview' );
	$img_illustration = wikipediapreview_image( 'illustration01.png', 'illustration' );
	$p1               = __( 'Wikipedia Preview lets you show a popup card with a short summary from Wikipedia when a reader clicks or hovers over a link.', 'wikipedia-preview' );
	$p2               = __( 'Wikipedia Preview is easy to set up and use. Simply follow these steps:', 'wikipedia-preview' );
	$step1_text       = __( 'Highlight the text you want to link to a Wikipedia article.', 'wikipedia-preview' );
	$step1_img        = wikipediapreview_image( 'Step-1-detailed.png', 'step' );
	$step2_text       = __( 'Select ‘Wikipedia Preview’ from the menu.', 'wikipedia-preview' );
	$step2_img        = wikipediapreview_image( 'Step-2-detailed.png', 'step' );
	$step3_text       = __( 'You will see a list of suggested articles. Select the one you want to link to.', 'wikipedia-preview' );
	$step3_img        = wikipediapreview_image( 'Step-3-detailed.png', 'step' );
	$step4_text       = __( 'Wikipedia Preview will automatically turn the link into a preview of the relevant Wikipedia article. You can easily edit or remove this.', 'wikipedia-preview' );
	$step4_img        = wikipediapreview_image( 'Step-4-detailed.png', 'step' );
	$html             = <<<HTML
		<div class="wrap wikipediapreview-intro">
			{$img_wordmark}
			<h1>{$title}</h1>
			{$img_illustration}
			<p>{$p1}</p>
			<p>{$p2}</p>
			<ol>
				<li>
					<div>{$step1_text}</div>
					{$step1_img}
				</li>
				<li>
					<div>{$step2_text}</div>
					{$step2_img}
				</li>
				<li>
					<div>{$step3_text}</div>
					{$step3_img}
				</li>
				<li>
					<div>{$step4_text}</div>
					{$step4_img}
				</li>
			</ol>
		</div>
	HTML;
	$allowed_tags     = array(
		'div' => array( 'class' => array() ),
		'h1'  => array(),
		'img' => array(
			'src'   => array(),
			'class' => array(),
		),
		'p'   => array(),
		'ol'  => array(),
		'li'  => array(),
	);
	echo wp_kses( $html, $allowed_tags );
}
