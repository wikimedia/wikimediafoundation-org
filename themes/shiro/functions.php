<?php
/**
 * Wikimedia Foundation functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package shiro
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wmf_setup() {
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
			'header'            => esc_html__( 'Header', 'shiro-admin' ),
			'footer-under-text' => esc_html__( 'Footer Under Text', 'shiro-admin' ),
			'footer-projects'   => esc_html__( 'Footer Projects', 'shiro-admin' ),
			'footer-affiliates' => esc_html__( 'Footer Movement Affiliates', 'shiro-admin' ),
			'footer-legal'      => esc_html__( 'Footer Legal', 'shiro-admin' ),
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

	add_image_size( 'profile_thumb', '206', '257', true );
	add_image_size( 'image_4x3_small', '400', '300', true );
	add_image_size( 'image_4x3_large', '800', '600', true );
	add_image_size( 'image_4x5_small', '400', '500', true );
	add_image_size( 'image_4x5_large', '800', '1000', true );
	add_image_size( 'image_16x9_large', '1200', '675', true );
	add_image_size( 'image_16x9_small', '600', '338', true );
	add_image_size( 'image_square_medium', '250', '250', true );

}
add_action( 'after_setup_theme', 'wmf_setup' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wmf_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'shiro-admin' ),
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
	$style_version = md5_file( get_theme_file_path( 'style.css' ) );
	$script_version = md5_file( get_theme_file_path( 'assets/dist/scripts.min.js' ) );

	wp_enqueue_style( 'shiro-style', get_stylesheet_uri(), array(), $style_version );

	if ( get_theme_mod( 'wmf_enable_rtl' ) ) {
		wp_enqueue_style( 'shiro-style-rtl', get_stylesheet_directory_uri() . '/rtl.css', array(), $style_version );
	}
	wp_enqueue_script( 'shiro-svg4everybody', get_stylesheet_directory_uri() . '/assets/dist/svg4everybody.min.js', array( 'jquery' ), '0.0.1', true );
	wp_enqueue_script( 'shiro-script', get_stylesheet_directory_uri() . '/assets/dist/scripts.min.js', array( 'jquery', 'shiro-svg4everybody' ), $script_version, true );
	Asset_Loader\enqueue_asset(
		\WMF\Assets\get_manifest_path(),
		'shiro.js',
		[
			'dependencies' => [],
			'handle' => 'shiro-modern',
		]
	);

	wp_localize_script(
		'shiro-script', 'shiro', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);

	wp_localize_script(
		'shiro-script', 'wmfrtl', array(
			'enable' => get_theme_mod( 'wmf_enable_rtl' ) ? true : false,
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_page_template( 'page-data.php' ) ) {
		wp_enqueue_script( 'd3', get_stylesheet_directory_uri() . '/assets/src/datavisjs/libraries/d3.min.js', array( ), '0.0.1', true );
		wp_enqueue_script( 'datavis', get_stylesheet_directory_uri() . '/assets/dist/datavis.min.js', array( 'jquery' ), '0.0.1', true );
	}
}
add_action( 'wp_enqueue_scripts', 'wmf_scripts' );

/**
 * Adds Piwik Analytics to the footer of each page.
 */
function wmf_add_piwik_analytics() {
	?>
	<!-- Matomo -->
<script>
var _paq = _paq || [];
/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
var u="//piwik.wikimedia.org/";
_paq.push(['setTrackerUrl', u+'piwik.php']);
_paq.push(['setSiteId', '17']);
var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
})();
</script>
<!-- End Matomo Code -->
	<?php
}
add_action( 'wp_footer', 'wmf_add_piwik_analytics' );

/**
 * Adds publicly querable option to page so it can be filtered
 *
 * @param array  $args      List of Post type args.
 * @param string $post_type Current Post Type name.
 * @return array Filtered args.
 */
function wmf_edit_page_post_type( $args, $post_type ) {
	if ( 'page' === $post_type ) {
		$args['publicly_queryable'] = true;
	}

	return $args;
}
add_filter( 'register_post_type_args', 'wmf_edit_page_post_type', 10, 2 );

/**
 * Enqueue admin scripts and styles.
 */
function wmf_admin_scripts() {
	wp_enqueue_style(
		'shiro-editor',
		get_stylesheet_directory_uri() . '/assets/dist/admin/admin.css',
		array(),
		filemtime( trailingslashit( get_stylesheet_directory() ) . 'assets/dist/admin/admin.css' )
	);

	wp_enqueue_script(
		'shiro-editor-js',
		get_stylesheet_directory_uri() . '/assets/src/admin/post-meta.js',
		array( 'jquery' ),
		filemtime( trailingslashit( get_stylesheet_directory() ) . 'assets/src/admin/post-meta.js' ),
		true
	);

	wp_enqueue_script(
		'shiro-media-js',
		get_stylesheet_directory_uri() . '/assets/src/admin/media.js',
		array( 'jquery' ),
		filemtime( trailingslashit( get_stylesheet_directory() ) . 'assets/src/admin/media.js' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'wmf_admin_scripts' );

/**
 * Functions for enqueuing assets.
 *
 * Loading this early so we'll have access to it when enqueuing
 */
require_once __DIR__ . '/inc/assets.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom functions to handle translation.
 */
require get_template_directory() . '/inc/template-translations.php';

/**
 * Ajax related functions
 */
require get_template_directory() . '/inc/ajax.php';

/**
 * Block editor functionality.
 */
require get_template_directory() . '/inc/editor/namespace.php';
require get_template_directory() . '/inc/editor/blocks/blog-list.php';
require get_template_directory() . '/inc/editor/blocks/blog-post.php';
require get_template_directory() . '/inc/editor/blocks/double-heading.php';
require get_template_directory() . '/inc/editor/blocks/inline-languages.php';
require get_template_directory() . '/inc/editor/blocks/mailchimp-subscribe.php';
require get_template_directory() . '/inc/editor/blocks/read-more-categories.php';
require get_template_directory() . '/inc/editor/blocks/share-article.php';
require get_template_directory() . '/inc/editor/blocks/profile.php';
require get_template_directory() . '/inc/editor/blocks/profile-list.php';
require get_template_directory() . '/inc/editor/has-blocks-column.php';
require get_template_directory() . '/inc/editor/intro.php';
require get_template_directory() . '/inc/editor/patterns.php';
require get_template_directory() . '/inc/editor/patterns/blog-list.php';
require get_template_directory() . '/inc/editor/patterns/card-columns.php';
require get_template_directory() . '/inc/editor/patterns/fact-columns.php';
require get_template_directory() . '/inc/editor/patterns/link-columns.php';
require get_template_directory() . '/inc/editor/patterns/tweet-columns.php';
require get_template_directory() . '/inc/editor/patterns/communication-module.php';
require get_template_directory() . '/inc/editor/patterns/template-default.php';
require get_template_directory() . '/inc/editor/patterns/template-landing.php';
require get_template_directory() . '/inc/editor/patterns/template-list.php';

WMF\Editor\bootstrap();
WMF\Editor\HasBlockColumn\bootstrap();
WMF\Editor\Blocks\BlogList\bootstrap();
WMF\Editor\Blocks\BlogPost\bootstrap();
WMF\Editor\Blocks\InlineLanguages\bootstrap();
WMF\Editor\Blocks\DoubleHeading\bootstrap();
WMF\Editor\Blocks\MailchimpSubscribe\bootstrap();
WMF\Editor\Blocks\ReadMoreCategories\bootstrap();
WMF\Editor\Blocks\ShareArticle\bootstrap();
WMF\Editor\Blocks\Profile\bootstrap();
WMF\Editor\Blocks\ProfileList\bootstrap();
WMF\Editor\Patterns\bootstrap();

/**
 * Adjustments to queries.
 */
require get_template_directory() . '/inc/queries.php';

/**
 * Class autoloader.
 */
require get_template_directory() . '/inc/classes/class-autoload.php';

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
require get_template_directory() . '/inc/post-types/story.php';
require get_template_directory() . '/inc/post-types/post.php';

/**
 * Logic for Custom Page Templates.
 */
require get_template_directory() . '/inc/template-redirect.php';
require get_template_directory() . '/inc/template-stories.php';

/**
 * Add Cache related functions.
 */
require get_template_directory() . '/inc/cache.php';

/**
 * Safe Redirect mods.
 */
require get_template_directory() . '/inc/safe-redirect.php';

/**
 * Shortcodes.
 */
require get_template_directory() . '/inc/shortcodes/columns.php';
require get_template_directory() . '/inc/shortcodes/facts.php';
require get_template_directory() . '/inc/shortcodes/simple-bar-graph.php';
require get_template_directory() . '/inc/shortcodes/wp20.php';
require get_template_directory() . '/inc/shortcodes/autotweets.php';
require get_template_directory() . '/inc/shortcodes/quotes-section.php';
require get_template_directory() . '/inc/shortcodes/focus-blocks.php';

/**
 * Stories page template customizations.
 */
require_once get_template_directory() . '/inc/stories.php';
Stories_Customisations\init();

/**
 * Modify the document title for the search and 404 pages
 */

// 404 page
function theme_slug_filter_wp_404title( $title_parts ) {
    if ( is_404() ) {
        $title_parts['title'] = get_theme_mod( 'wmf_404_message', __( '404 Error', 'shiro-admin' ) );
    }

    return $title_parts;
}

// Hook into document_title_parts
add_filter( 'document_title_parts', 'theme_slug_filter_wp_404title' );

// Search page
function theme_slug_filter_wp_searchtitle( $title_parts ) {
    if ( is_search() ) {
        $title_parts['title'] = sprintf( __( get_theme_mod( 'wmf_search_results_copy', __( 'Search results for %s', 'shiro-admin' ) ), 'shiro' ), get_search_query() );
   }

    return $title_parts;
}

// Hook into document_title_parts
add_filter( 'document_title_parts', 'theme_slug_filter_wp_searchtitle' );

// Rewrite URL for roles to not require /news/ prefix
add_rewrite_rule( '^role/(.+?)$', 'index.php?role=$matches[1]', 'top' );

/**
 * Whitelist a very limited subset of SVG for use in post content to support
 * the simple_bar_graph shortcode used in transparency report tables.
 *
 * @param array[]|string $context      Context by which to judge allowed tags.
 * @param string         $context_type Context name.
 * @return mixed Filtered context.
 */
function wmf_filter_post_kses_tags( $context, $context_type ) {
	if ( 'post' !== $context_type || ! is_array( $context ) ) {
		return $context;
	}

	return array_merge(
		$context,
		[
			'svg'  => [
				'viewBox' => true,
				'width'   => true,
				'height'  => true,
				'class'   => true,
				'xmlns'   => true,
			],
			'path' => [
				'd'    => true,
				'fill' => true,
			],
			'rect' => [
				'fill'   => true,
				'width'  => true,
				'height' => true,
				'x'      => true,
				'y'      => true,
			],
			'use' => [
				'xlink:href' => true,
				'href'       => true,
			],
			'h1' => array_merge(
				$context['h1'],
				[
					'lang'  => true,
				]
			),
		]
	);
}
add_filter( 'wp_kses_allowed_html', 'wmf_filter_post_kses_tags', 10, 2 );

/**
 * Insert a span element to all header nav menu items.
 *
 * @param stdClass $args An object of wp_nav_menu() arguments.
 * @param WP_Post  $item Menu item data object.
 * @param int      $depth Depth of menu item. Used for padding.
 *
 * @return stdClass
 */
function wmf_filter_nav_menu_items( $args, $item, $depth ) {
	if ( 'header' !== $args->theme_location ) {
		return $args;
	}

	$args->link_before = '<span>';
	$args->link_after  = '</span>';

	return $args;
}
add_filter( 'nav_menu_item_args', 'wmf_filter_nav_menu_items', 10, 3 );

/**
 * Add reusable blocks link to admin menu.
 */
function link_reusable_blocks_url() {
	add_menu_page(
		esc_html__( 'Reusable Blocks', 'shiro-admin' ),
		esc_html__( 'Reusable Blocks', 'shiro-admin' ),
		'manage_options',
		'edit.php?post_type=wp_block', '',
		'dashicons-editor-table',
		22
	);
}

add_action( 'admin_menu', 'link_reusable_blocks_url' );

/**
 * Add page slug as body class.
 *
 * @param array $classes An array of body class names.
 * @return array
 */
function shiro_add_slug_body_class( $classes ) {
	global $post;

	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}

	return $classes;
}
add_filter( 'body_class', 'shiro_add_slug_body_class' );
