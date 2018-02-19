<?php
/**
 * Wikimedia Foundation functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wmfoundation
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wmf_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on wmfoundation, use a find and replace
	 * to change 'wmfoundation' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'wmfoundation', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Enable support for Custom Header.
	 *
	 * @link https://codex.wordpress.org/Custom_Headers
	 */
	add_theme_support( 'custom-header' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'wmfoundation' ),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background', apply_filters(
			'wmf_custom_background_args', array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'wmf_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wmf_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wmf_content_width', 640 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
}
add_action( 'after_setup_theme', 'wmf_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wmf_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'wmfoundation' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wmf_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function wmf_scripts() {
	wp_enqueue_style( 'wmfoundation-gfonts', 'https://fonts.googleapis.com/css?family=Noto+Sans:400,400i,700,700i|Material+Icons' );
	wp_enqueue_style( 'wmfoundation-style', get_stylesheet_uri() );

	wp_enqueue_script( 'wmfoundation-flickity', get_stylesheet_directory_uri() . '/assets/dist/flickity-min.js', array( 'jquery' ), '0.0.1', true );
	wp_enqueue_script( 'wmfoundation-script', get_stylesheet_directory_uri() . '/assets/dist/scripts.min.js', array( 'jquery', 'wmfoundation-flickity' ), '0.0.1', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wmf_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/classes/customizer/class-base.php';

/**
 * Custom Fields functions.
 */
require get_template_directory() . '/inc/fields.php';

/**
 * Custom Taxonomies.
 */
require get_template_directory() . '/inc/taxonomies.php';

/**
 * Add Custom Post Types.
 */
require get_template_directory() . '/inc/post-types/profile.php';

/**
 * Add Template Data Helper.
 */
require get_template_directory() . '/inc/classes/class-rkv-template-data.php';
