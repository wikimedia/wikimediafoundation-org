<?php
/**
 * Define the shortcodes for the Wikipedia 20th birthday page.
 *
 * @package shiro
 */

/**
 * Define a [collage] shortcode that renders a collage of different messages.
 *
 * @param array $atts Shortcode attributes array.
 * @return string Rendered shortcode output.
 */
function wmf_collage_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'label' => '1 human just edited',
		'id' => 'wp20-collage',
		'click' => 'click me',
	];
	$atts = shortcode_atts( $defaults, $atts, 'collage' );
	$content = do_shortcode( $content );
	$content = preg_replace( ['/\s*<br\s*\/?>\s*/', '/\s*<p\s*\/?>\s*/'], '', $content );

	wp_enqueue_script( 'd3', get_stylesheet_directory_uri() . '/assets/src/datavisjs/libraries/d3.min.js', array( ), '0.0.1', true );
	wp_enqueue_script( 'collage', get_stylesheet_directory_uri() . '/assets/dist/shortcode-collage.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'collage', "var collageAtts = " . json_encode($atts) . ";");

	ob_start();
	?>

	<div class="collage mod-margin-bottom">
		<div id="<?php echo esc_attr($atts['id']) ?>" class="collage-content">
			<div class="intro hidden">
				<div class="intro-text">
					<p>Irure magna aliqua aute veniam nulla veniam dolor sed ut aute sint esse irure minim eu officia proident quis aliquip cupidatat tempor ad velit eiusmod sed ad veniam.</p>
				</div>
				<div class="scroll-indicator">↓</div>
			</div>
			<div class="recent-edits hidden">
				<p><span class="label"></span></p>
				<p><span class="title"></span></p>
			</div>
			<h1 class="hidden" style="font-family: Linux Libertine, serif;"><?php echo esc_html($atts['title']) ?></h1>
			<div class="story-overlay hidden">
				<span class="close"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/close.svg"></span>
				<div class="story-content-container"><?php echo wp_kses_post( $content ) ?></div>
				<p class="story-nav">
					<span class="next-story">Next story</span>
				</p>
			</div>
		</div>
		<div class="fake-scroll"></div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'collage', 'wmf_collage_callback' );

/**
 * Define a [volunteer] shortcode that creates a volunteer story.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_volunteer_shortcode_callback( $atts = [], $content = '' ) {
	$defaults = [
		'name' => '',
		'since' => '',
		'location' => '',
		'img' => '',
		'quote' => '',
		'quote_label' => 'Quote',
	];
	$atts = shortcode_atts( $defaults, $atts, 'volunteer' );
	$attachment = get_page_by_title($atts['img'], OBJECT, 'attachment');

	if ( $attachment != Null ) {
		$img_id = $attachment->ID;
		$image_url = wp_get_attachment_image_url($img_id, array(400, 400));
	}

	ob_start();
	?>
	<div class="story-content" style="display: none;">
		<h2><?php echo esc_html( $atts['name'] ); ?></h2>
		<?php if ( $image_url ) { ?>
			<div class="story-image" style="background-image: url(<?php echo $image_url ?>);"></div>
		<?php } ?>
		<p class="story-location"><?php echo esc_html( $atts['location'] ); ?></p>
		<p class="story-since"><?php echo esc_html( $atts['since'] ); ?></p>
		<p class="story-desc p"><?php echo wp_kses_post( $content ) ?></p>
		<?php if ( !empty($atts['quote'] ) ) { ?>
			<div class="story-quote">
				<h3><?php echo esc_html( $atts['quote_label'] ); ?></h3>
				<blockquote class="p"><?php echo esc_html( $atts['quote'] ); ?></blockquote>
			</div>
		<?php } ?>
	</div>

	<?php 
	return (string) ob_get_clean();
}
add_shortcode( 'volunteer', 'wmf_volunteer_shortcode_callback' );


/**
 * Define a [timeline] wrapper shortcode that renders a timeline of milestones.
 *
 * @param array $atts Shortcode attributes array.
 * @return string Rendered shortcode output.
 */
function wmf_timeline_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'more_link' => '',
		'more_href' => '',
		'background-color' => '#f8f9fa',
		'img' => '',
		'id' => 'wp20-timeline',
	];
	$atts = shortcode_atts( $defaults, $atts, 'timeline' );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );

	wp_enqueue_script( 'timeline', get_stylesheet_directory_uri() . '/assets/dist/shortcode-timeline.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'timeline', "var  timelineAtts = " . json_encode($atts) . ";");

	ob_start();
	?>

	<div class="timeline mod-margin-bottom" style="background-color: <?php echo esc_attr($atts["background-color"]) ?>">
		<div class="mw-980">
			<div id="<?php echo esc_attr($atts['id']) ?>" class="milestones">
			</div>
			<div>
				<?php echo wp_kses_post( $content ) ?>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'timeline', 'wmf_timeline_callback' );

/**
 * Define a [wmf_section] wrapper shortcode that creates a HTML wrapper with mw-980 class, optional margin class, optional columns.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_section_shortcode_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'columns' => '1',
		'img' => '',
		'margin' => '1',
		'reverse' => '0',
	];
	$atts = shortcode_atts( $defaults, $atts, 'wmf_section' );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	$margin = $atts['margin'] === '1' ? ' mod-margin-bottom' : '';
	$attachment = get_page_by_title($atts['img'], OBJECT, 'attachment');

	if ( $attachment != Null ) {
		$img_id = $attachment->ID;
		$image = wp_get_attachment_image($img_id, array(600, 400));
	}

	if ( $atts['columns'] === '1' ) {
		$o = '<div class="mw-980' . $margin . '"><h1 style="font-family: Linux Libertine, Charis SIL, serif;">' . esc_html($atts['title']) . '</h1><p>' . wp_kses_post( $content ) . '</p></div>';
		return $o;
	} else {
		if ( empty($image) ) {
			$col_1 = '<div class="w-48p"><h1 style="font-family: Linux Libertine, Charis SIL, serif;">' . esc_html($atts['title']) . '</h1></div>';
			$col_2 = '<div class="w-48p"><p>' . wp_kses_post( $content ) . '</p></div>';
		} else {
			$col_1 = '<div class="w-48p"><h1 style="font-family: Linux Libertine, Charis SIL, serif;">' . esc_html($atts['title']) . '</h1><p>' . wp_kses_post( $content ) . '</p></div>';
			$col_2 = '<div class="w-48p">' . $image . '</div>';
		}

		if ( $atts['reverse'] === '0') {
			return '<div class="mw-980 flex flex-medium flex-space-between' . $margin . '">' . $col_1 . $col_2 . '</div>';
		} else {
			return '<div class="mw-980 flex flex-medium flex-space-between columns-wrapper columns-mobile-reverse' . $margin . '">' . $col_2 . $col_1 . '</div>';
		}
	}
}
add_shortcode( 'wmf_section', 'wmf_section_shortcode_callback' );

/**
 * Define a [projects] shortcode that renders Wikimedia projects.
 *
 * @param array $atts Shortcode attributes array.
 * @return string Rendered shortcode output.
 */
function wmf_projects_callback( $atts ) {
	ob_start();
	?>

	<div class="projects">
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		<span class="special"></span>
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		<span></span>
		<span></span>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'projects', 'wmf_projects_callback' );