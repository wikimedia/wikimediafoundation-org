<?php
/**
 * Define the shortcodes for the Wikipedia 20th birthday page.
 * Author: Hang Do Thi Duc
 *
 * @package shiro
 */

/**
 * Define a [collage] shortcode that renders a collage of different messages.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_collage_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'label' => '1 human just edited',
		'intro_img' => '',
		'intro_1_h' => '',
		'intro_2_h' => '',
		'intro_3_h' => '',
		'intro_1' => '',
		'intro_2' => '',
		'intro_4' => '',
		'story_rgba' => "(0,0,0,1)",
		'id' => 'wp20-collage',
		'click' => 'click me',
		'scroll' => 'scroll',
	];
	$atts = shortcode_atts( $defaults, $atts, 'collage' );
	$content = do_shortcode( $content );
	$content = preg_replace( ['/\s*<br\s*\/?>\s*/', '/\s*<p\s*\/?>\s*/'], '', $content );
	$intro1 = preg_split('/\|/', $atts['intro_1']);
	$intro2 = preg_split('/\|/', $atts['intro_2']);
	$intro4 = preg_split('/\|/', $atts['intro_4']);
	$attachment = get_page_by_title($atts['intro_img'], OBJECT, 'attachment');

	if ( !empty($attachment) ) {
		$img_id = $attachment->ID;
		$image_url = wp_get_attachment_image_url($img_id, array(800, 600));
	}

	wp_enqueue_script( 'd3', get_stylesheet_directory_uri() . '/assets/src/datavisjs/libraries/d3.min.js', array( ), '0.0.1', true );
	wp_enqueue_script( 'collage', get_stylesheet_directory_uri() . '/assets/dist/shortcode-collage.min.js', array( 'jquery', 'd3' ), '0.0.1', true );
	wp_add_inline_script( 'collage', "var collageAtts = " . json_encode($atts) . ";");

	ob_start();
	?>

	<div class="collage mod-margin-bottom">
		<div id="<?php echo esc_attr($atts['id']) ?>" class="collage-content">
			<div id="intro-1" class="intro hidden">
				<div class="intro-text">
					<?php if ( isset($image_url) ) { ?>
						<img src="<?php echo esc_attr($image_url); ?>">
					<?php } ?>
					<h1 class="wikipedia-h1">
						<?php echo esc_html($atts['intro_1_h']); ?>
					</h1>
					<?php for ($i=0; $i < sizeof($intro1); $i++) { ?>
						<p>
							<?php echo esc_html( $intro1[$i] ); ?>
						</p>
					<?php } ?>
				</div>
				<div class="scroll-indicator">
					<p><?php echo esc_html( $atts['scroll'] ) ?></p>
					<div class="scroll-animator">↓</div>
				</div>
			</div>
			<div id="intro-2" class="intro hidden">
				<div class="intro-text">
					<h1 class="wikipedia-h1">
						<?php echo esc_html($atts['intro_2_h']); ?>
					</h1>
					<?php for ($i=0; $i < sizeof($intro2); $i++) { ?>
						<p>
							<?php echo esc_html( $intro2[$i] ); ?>
						</p>
					<?php } ?>
				</div>
				<div class="scroll-indicator">
					<p><?php echo esc_html( $atts['scroll'] ) ?></p>
					<div class="scroll-animator">↓</div>
				</div>
			</div>
			<div id="intro-3" class="intro hidden">
				<div class="intro-text">
					<h1 class="wikipedia-h1">
						<?php echo esc_html($atts['intro_3_h']); ?>
					</h1>
				</div>
			</div>
			<div class="story-overlay hidden">
				<span class="close"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/close.svg"></span>
				<div class="story-content-container"><?php echo wp_kses_post( $content ) ?></div>
				<div class="story-nav flex flex-all flex-space-between">
					<a class="prev-story">←</a>
					<span class="index"></span>
					<a class="next-story">→</a>
				</div>
			</div>
			<div id="intro-4" class="intro hidden">
				<div class="intro-text">
					<?php for ($i=0; $i < sizeof($intro4); $i++) { ?>
						<p>
							<?php echo esc_html( $intro4[$i] ); ?>
						</p>
					<?php } ?>
					<div class="recent-edits hidden">
						<p><strong><span class="label"></span></strong></p>
						<div class="accent"></div>
						<p><span class="title"></span></p>
					</div>
				</div>
				<div class="scroll-indicator">
					<p><?php echo esc_html( $atts['scroll'] ) ?></p>
					<div class="scroll-animator">↓</div>
				</div>
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
	];
	$atts = shortcode_atts( $defaults, $atts, 'volunteer' );
	$attachment = get_page_by_title($atts['img'], OBJECT, 'attachment');

	if ( !empty($attachment) ) {
		$img_id = $attachment->ID;
		$image_url = wp_get_attachment_image_url($img_id, array(400, 400));
	}

	ob_start();
	?>
	<div class="story-content wysiwyg" style="display: none;">
		<?php if ( isset($image_url) ) { ?>
			<div class="story-image" style="background-image: url(<?php echo esc_attr($image_url) ?>);"></div>
		<?php } ?>
		<h2><?php echo esc_html( $atts['name'] ); ?></h2>

		<?php if ( !empty($atts['location'] ) ) { ?>
			<p class="story-location flex flex-all">
				<img class="story-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/map pin.svg"><span><?php echo esc_html( $atts['location'] ); ?></span>
			</p>
		<?php } ?>
		
		<?php if ( !empty($atts['since'] ) ) { ?>
			<p class="story-since flex flex-all">
				<img class="story-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/calendar.svg"><span><?php echo esc_html( $atts['since'] ); ?></span>
			</p>
		<?php } ?>

		<p class="story-desc p"><?php echo wp_kses_post( $content ) ?></p>
		<?php if ( !empty($atts['quote'] ) ) { ?>
			<div class="story-quote">
				<blockquote class="p"><?php echo esc_html( $atts['quote'] ); ?><span>– <?php echo esc_html( $atts['name'] ); ?></span></blockquote>
			</div>
		<?php } ?>
	</div>

	<?php 
	return (string) ob_get_clean();
}
add_shortcode( 'volunteer', 'wmf_volunteer_shortcode_callback' );


/**
 * Define a [timeline] wrapper shortcode that renders wrapper for a timeline of milestones, see [year] shortcode.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_timeline_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'background_color' => 'white',
		'label_left' => '',
		'label_right' => '',
		'id' => 'wp20-timeline'
	];
	$atts = shortcode_atts( $defaults, $atts, 'timeline' );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	$padding = $atts['background_color'] === 'white' ? " timeline-white" : " timeline-grey";
	$classes = "timeline mod-margin-bottom" . $padding;

	ob_start();
	?>

	<div id="<?php echo esc_attr($atts['id']) ?>" class="<?php echo esc_attr($classes) ?>">
		<div class="mw-980 wysiwyg">
			<div class="milestones">
				<div class="flex flex-medium flex-space-between">
					<div class="w-25p label label-left"><?php echo esc_html( $atts['label_left'] ) ?></div>
					<div class="w-25p label label-right"><?php echo esc_html( $atts['label_right'] ) ?></div>
				</div>
				<?php echo wp_kses_post( $content ) ?>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'timeline', 'wmf_timeline_callback' );

/**
 * Define a [year] wrapper shortcode that renders one year for the timeline, see [timeline].
 * [culture] and [milestone] should be nested in this
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_year_callback( $atts = [], $content = '' ) {
	$defaults = [
		'highlight' => '',
		'big' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'year' );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	$culture = strpos($content, "with-content") === false ? "" : " with-culture";
	$highlight = $atts['highlight'] === '' ? " highlight" : " not-highlight";
	$big = $atts['big'] === '' ? " big" : " not-big";
	$classes = "year" . $highlight . $culture . $big;

	ob_start();
	?>

	<div class="<?php echo esc_attr($classes) ?>">
		<?php echo wp_kses_post( $content ) ?>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'year', 'wmf_year_callback' );

/**
 * Define a [culture] wrapper shortcode that renders cultural context for one [year] for the timeline, see [timeline].
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_year_culture_callback( $atts = [], $content = '' ) {
	$defaults = [
		'img1' => '',
		'img2' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'culture' );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	$attachment1 = get_page_by_title($atts['img1'], OBJECT, 'attachment');
	$attachment2 = get_page_by_title($atts['img2'], OBJECT, 'attachment');

	if ( !empty($attachment1) ) {
		$img_id1 = $attachment1->ID;
		$image1 = '<span style="background-image: url(' . wp_get_attachment_image_url($img_id1, array(200, 200)) . ');"></span>';
	}

	if ( !empty($attachment2) ) {
		$img_id2 = $attachment2->ID;
		$image2 = '<span style="background-image: url(' . wp_get_attachment_image_url($img_id2, array(200, 200)) . ');"></span>';
	}

	ob_start();
	?>

	<div class="top-articles rounded <?php if (!empty($content)) echo " with-content" ?>">
		<p><?php echo wp_kses_post( $content ) ?></p>
		<div class="top-edited"><?php if ( isset($image1) ) echo $image1; ?></div>
		<div class="top-viewed"><?php if ( isset($image2) ) echo $image2; ?></div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'culture', 'wmf_year_culture_callback' );

/**
 * Define a [milestone] wrapper shortcode that renders the milestone for one [year] for the timeline, see [timeline].
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_year_milestone_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'year' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'milestone' );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );

	ob_start();
	?>

	<div class="year-label"><span class="p"><?php echo esc_html( $atts['year'] ) ?></span></div>
	<div class="milestone">
		<p class="milestone-heading"><strong><?php echo esc_html( $atts['title'] ) ?></strong></p>
		<p><?php echo wp_kses_post( $content ) ?></p>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'milestone', 'wmf_year_milestone_callback' );

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
	$margin = $atts['margin'] === '1' ? 'mod-margin-bottom ' : '';
	$classes = "mw-980 wysiwyg section " . $margin;
	$id = strtolower( str_replace(" ", "-", $atts['title']) );
	$attachment = get_page_by_title($atts['img'], OBJECT, 'attachment');

	if ( !empty($attachment) ) {
		$img_id = $attachment->ID;
		$image = wp_get_attachment_image($img_id, array(600, 400));
	}

	if ( $atts['columns'] === '1' ) {
		$o = '<div id="' . $id . '" class="' . $classes . '"><h1 class="wikipedia-h1">' . esc_html($atts['title']) . '</h1><p>' . wp_kses_post( $content ) . '</p></div>';
		return $o;
	} else {
		if ( isset($image) ) {
			$col_1 = '<div class="w-48p"><h1 class="wikipedia-h1">' . esc_html($atts['title']) . '</h1><p>' . wp_kses_post( $content ) . '</p></div>';
			$col_2 = '<div class="w-48p">' . $image . '</div>';
		} else {
			$col_1 = '<div class="w-48p"><h1 class="wikipedia-h1">' . esc_html($atts['title']) . '</h1></div>';
			$col_2 = '<div class="w-48p"><p>' . wp_kses_post( $content ) . '</p></div>';
		}

		if ( $atts['reverse'] === '0') {
			return '<div id="' . $id . '" class="' . $classes . 'flex flex-medium flex-space-between">' . $col_1 . $col_2 . '</div>';
		} else {
			return '<div id="' . $id . '" class="' . $classes . 'flex flex-medium flex-space-between columns-wrapper columns-mobile-reverse">' . $col_2 . $col_1 . '</div>';
		}
	}
}
add_shortcode( 'wmf_section', 'wmf_section_shortcode_callback' );

/**
 * Define a [movement] wrapper shortcode that renders Wikimedia projects and affiliates.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_movement_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'projects' => 'Wikipedia|Wikibooks|Wiktionary|Wikiquote|Wikimedia Commons|Wikisource|Wikiversity|Wikispecies|Wikidata|MediaWiki|Wikivoyage|Wikinews|Meta-Wiki',
		'colors' => '(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)|(51,102,204,1)',
		'project_desc' => 'All the world\'s knowledge|E-book textbooks and annotated texts|A dictionary for over 170 languages|Find quotes across your favorite books, movies, authors and more|60 million images, photographs, videos and music files and counting|The free library|Access learning resources, projects and research at any level of study|The free species directory|The database of structured information collaboratively edited|The software platform that makes Wikipedia possible|The ultimate travel guide|The free news source|Project coordination software tool for global collaboration',
		'id' => 'movement-content',
	];
	$atts = shortcode_atts( $defaults, $atts, 'movement' );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	$projects = preg_split('/\|/', $atts['projects']);
	$colors = preg_split('/\|/', $atts['colors']);

	wp_enqueue_script( 'movement', get_stylesheet_directory_uri() . '/assets/dist/shortcode-movement.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'movement', "var movementAtts = " . json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr($atts['id']) ?>" class="movement mod-margin-bottom">
		<div class="mw-980 wysiwyg">
			<div class="w-68p">
				<h1 class="wikipedia-h1"><?php echo esc_html( $atts['title'] ); ?></h1>
				<p><?php echo wp_kses_post( $content ); ?></p>
			</div>
		</div>
		<div class="movement-vis">
			<div class="tooltip hidden"></div>
			<div class="main-vis">
				<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 973 483"><path d="M268.859 102l7.671 13.288h-15.343L268.859 102z" fill="#72777D"/><path d="M12.859 221l7.671 13.288H5.187L12.859 221zM89.859 293l7.671 13.288H82.187L89.859 293z" fill="#A2A9B1"/><path d="M221.859 321l7.671 13.288h-15.343L221.859 321z" fill="#202122"/><path d="M179.859 227l7.671 13.288h-15.343L179.859 227zM823.324 347.86l7.672 13.288h-15.343l7.671-13.288z" fill="#C8CCD1"/><path d="M866.879 412.084l7.672 13.288h-15.344l7.672-13.288zM98.859 176l7.671 13.288H91.187L98.859 176z" fill="#A2A9B1"/><path d="M368.585 339.001l7.672 13.288h-15.343l7.671-13.288zM465.291 302.091l7.672 13.288H457.62l7.671-13.288z" fill="#72777D"/><path d="M393.685 126.396l7.671 13.288h-15.343l7.672-13.288zM309.528 245.987l7.672 13.288h-15.343l7.671-13.288z" fill="#A2A9B1"/><path d="M738.859 105l7.671 13.288h-15.343L738.859 105z" fill="#EAECF0"/><path d="M563.474 413.561l7.671 13.288h-15.343l7.672-13.288z" fill="#C8CCD1"/><path d="M483.008 395.844l7.672 13.288h-15.343l7.671-13.288z" fill="#A2A9B1"/><path d="M903.051 82.103l7.672 13.288H895.38l7.671-13.288z" fill="#72777D"/><path d="M877.952 157.401l7.672 13.288H870.28l7.672-13.288zM576.761 144.113h11.073v11.073h-11.073z" fill="#A2A9B1"/><path d="M958.811 247.335L952.906 237 947 247.335l5.906 10.335 5.905-10.335z" fill="#C8CCD1"/><path d="M650.811 383.335L644.906 373 639 383.335l5.906 10.335 5.905-10.335z" fill="#202122"/><path d="M688.811 185.335L682.906 175 677 185.335l5.906 10.335 5.905-10.335z" fill="#C8CCD1"/><path d="M27.811 130.335L21.906 120 16 130.335l5.906 10.335 5.905-10.335zM172.811 358.335L166.906 348 161 358.335l5.906 10.335 5.905-10.335z" fill="#202122"/><path d="M148.811 96.335L142.906 86 137 96.335l5.906 10.335 5.905-10.335z" fill="#72777D"/><path d="M470.811 154.335L464.906 144 459 154.335l5.906 10.335 5.905-10.335z" fill="#A2A9B1"/><circle cx="29.846" cy="334.846" r="1.846" fill="#72777D"/><circle cx="145.846" cy="283.846" r="1.846" fill="#A2A9B1"/><circle cx="667.192" cy="256.691" r="1.846" fill="#202122"/><circle cx="574.916" cy="375.543" r="1.846" fill="#EAECF0"/><circle cx="335.735" cy="171.796" r="1.846" fill="#202122"/><circle cx="746.181" cy="250.047" r="1.846" fill="#72777D"/><circle cx="848.055" cy="206.492" r="1.846" fill="#72777D"/><circle cx="370.431" cy="372.59" r="1.846" fill="#A2A9B1"/><circle cx="755.04" cy="213.136" r="1.846" fill="#C8CCD1"/><circle cx="326.876" cy="207.969" r="1.846" fill="#72777D"/><circle cx="288.489" cy="290.648" r="1.846" fill="#C8CCD1"/><circle cx="433.179" cy="303.936" r="1.846" fill="#C8CCD1"/><circle cx="396.268" cy="346.014" r="1.846" fill="#202122"/><circle cx="301.039" cy="151.865" r="1.846" fill="#A2A9B1"/><circle cx="777.924" cy="421.312" r="1.846" fill="#202122"/><circle cx="555.722" cy="348.967" r="1.846" fill="#72777D"/><circle cx="942.546" cy="300.245" r="1.846" fill="#EAECF0"/><circle cx="69.846" cy="108.846" r="1.846" fill="#202122"/><circle cx="73.846" cy="256.846" r="1.846" fill="#72777D"/><circle cx="752.825" cy="480.369" r="1.846" fill="#EAECF0"/><circle cx="190.846" cy="260.846" r="1.846" fill="#C8CCD1"/><circle cx="846.578" cy="413.192" r="1.846" fill="#72777D"/><circle cx="944.022" cy="133.409" r="1.846" fill="#C8CCD1"/><circle cx="722.558" cy="324.606" r="1.846" fill="#202122"/><circle cx="315.803" cy="391.784" r="1.846" fill="#72777D"/><circle cx="434.656" cy="427.956" r="1.846" fill="#C8CCD1"/><circle cx="1.846" cy="202.846" r="1.846" fill="#72777D"/><circle cx="761.684" cy="320.915" r="1.846" fill="#202122"/><circle cx="724.035" cy="180.655" r="1.846" fill="#202122"/><circle cx="356.405" cy="265.549" r="1.846" fill="#EAECF0"/><circle cx="892.347" cy="224.209" r="1.846" fill="#EAECF0"/><circle cx="109.846" cy="348.846" r="1.846" fill="#EAECF0"/><circle cx="666.846" cy="134.846" r="1.846" fill="#C8CCD1"/><circle cx="226.846" cy="384.846" r="1.846" fill="#EAECF0"/><circle cx="910.064" cy="387.354" r="1.846" fill="#C8CCD1"/><circle cx="828.861" cy="446.411" r="1.846" fill="#C8CCD1"/><circle cx="866.51" cy="479.631" r="1.846" fill="#EAECF0"/><circle cx="968.383" cy="299.507" r="1.846" fill="#C8CCD1"/><circle cx="539.482" cy="464.128" r="1.846" fill="#EAECF0"/><circle cx="91.846" cy="143.846" r="1.846" fill="#EAECF0"/><circle cx="765.375" cy="287.696" r="1.846" fill="#A2A9B1"/><circle cx="228.846" cy="202.846" r="1.846" fill="#202122"/><circle cx="791.212" cy="370.375" r="1.846" fill="#72777D"/><circle cx="797.856" cy="221.256" r="1.846" fill="#72777D"/><circle cx="890.871" cy="51.468" r="1.846" fill="#202122"/><circle cx="684.846" cy="432.846" r="1.846" fill="#202122"/><circle cx="166.846" cy="171.846" r="1.846" fill="#C8CCD1"/><circle cx="625.853" cy="311.318" r="1.846" fill="#202122"/><circle cx="569.748" cy="226.424" r="1.846" fill="#202122"/><circle cx="786.846" cy="126.846" r="1.846" fill="#C8CCD1"/><circle cx="970.598" cy="2.746" r="1.846" fill="#72777D"/><circle cx="844.363" cy="120.121" r="1.846" fill="#EAECF0"/><circle cx="281.846" cy="338.632" r="1.846" fill="#A2A9B1"/><circle cx="938.855" cy="229.377" r="1.846" fill="#72777D"/><circle cx="614.846" cy="460.846" r="1.846" fill="#202122"/><circle cx="970.598" cy="462.652" r="1.846" fill="#EAECF0"/><circle cx="484.116" cy="206.492" r="1.846" fill="#C8CCD1"/><circle cx="627.329" cy="207.969" r="1.846" fill="#A2A9B1"/><circle cx="522.503" cy="272.931" r="1.846" fill="#72777D"/><circle cx="431.703" cy="247.832" r="1.846" fill="#72777D"/><path d="M809.593 77.138c3.739-.53 6.605-3.727 6.605-7.586 0-2.358-1.064-4.45-2.733-5.864l-3.872 3.86v9.59zm-2.172 0v-9.59l-3.871-3.874a7.632 7.632 0 00-2.733 5.863c0 3.874 2.866 7.071 6.604 7.601zm7.772-14.26a9.347 9.347 0 012.763 6.659c0 2.504-.99 4.89-2.763 6.659a9.402 9.402 0 01-6.678 2.755 9.44 9.44 0 01-6.679-2.755 9.371 9.371 0 01-2.777-6.66c0-2.518.989-4.89 2.763-6.658.147-.147.31-.295.472-.442l-2.157-2.15c-.162.146-.31.294-.473.441a12.216 12.216 0 00-2.674 3.963 12.367 12.367 0 00-.99 4.847c0 1.68.325 3.314.99 4.847a12.693 12.693 0 002.674 3.963 12.255 12.255 0 003.975 2.666c1.537.648 3.177.987 4.861.987 1.684 0 3.324-.324 4.861-.987a12.74 12.74 0 003.975-2.666 12.217 12.217 0 002.674-3.963c.65-1.532.99-3.168.99-4.847 0-1.68-.325-3.315-.99-4.847a12.692 12.692 0 00-2.674-3.963c-.148-.147-.311-.294-.473-.442l-2.143 2.151c.163.133.311.28.473.442zM808.5 57a3.892 3.892 0 013.901 3.89c0 2.15-1.744 3.888-3.901 3.888a3.892 3.892 0 01-3.901-3.889c0-2.15 1.744-3.889 3.901-3.889z" fill="#000"/>
					<circle id="project1" data-title="<?php echo esc_attr( $projects[0] ) ?>" data-color="rgba<?php echo esc_attr( $colors[0] ) ?>" class="project-circle" cx="506.096" cy="198.9" r="11" fill="#000"/>
					<circle id="project2" data-title="<?php echo esc_attr( $projects[1] ) ?>" data-color="rgba<?php echo esc_attr( $colors[1] ) ?>" class="project-circle" cx="464.922" cy="356.349" r="11" fill="#000"/>
					<circle id="project3" data-title="<?php echo esc_attr( $projects[2] ) ?>" data-color="rgba<?php echo esc_attr( $colors[2] ) ?>" class="project-circle" cx="386.228" cy="278.228" r="11" fill="#000"/>
					<circle id="project4" data-title="<?php echo esc_attr( $projects[3] ) ?>" data-color="rgba<?php echo esc_attr( $colors[3] ) ?>" class="project-circle" cx="555.722" cy="268.502" r="11" fill="#000"/>
					<circle id="project5" data-title="<?php echo esc_attr( $projects[4] ) ?>" data-color="rgba<?php echo esc_attr( $colors[4] ) ?>" class="project-circle" cx="822.217" cy="241.188" r="11" fill="#000"/>
					<circle id="project6" data-title="<?php echo esc_attr( $projects[5] ) ?>" data-color="rgba<?php echo esc_attr( $colors[5] ) ?>" class="project-circle" cx="202.228" cy="159.228" r="11" fill="#000"/>
					<circle id="project7" data-title="<?php echo esc_attr( $projects[6] ) ?>" data-color="rgba<?php echo esc_attr( $colors[6] ) ?>" class="project-circle" cx="859.866" cy="306.151" r="11" fill="#000"/>
					<circle id="project8" data-title="<?php echo esc_attr( $projects[7] ) ?>" data-color="rgba<?php echo esc_attr( $colors[7] ) ?>" class="project-circle" cx="680.228" cy="307.228" r="11" fill="#000"/>
					<circle id="project9" data-title="<?php echo esc_attr( $projects[8] ) ?>" data-color="rgba<?php echo esc_attr( $colors[8] ) ?>" class="project-circle" cx="750.578" cy="372.226" r="11" fill="#000"/>
					<circle id="project10" data-title="<?php echo esc_attr( $projects[9] ) ?>" data-color="rgba<?php echo esc_attr( $colors[9] ) ?>" class="project-circle" cx="410.295" cy="200.587" r="11" fill="#000"/>
					<circle id="project11" data-title="<?php echo esc_attr( $projects[10] ) ?>" data-color="rgba<?php echo esc_attr( $colors[10] ) ?>" class="project-circle" cx="636.228" cy="237.228" r="11" fill="#000"/>
					<circle id="project12" data-title="<?php echo esc_attr( $projects[11] ) ?>" data-color="rgba<?php echo esc_attr( $colors[11] ) ?>" class="project-circle" cx="938.786" cy="345.276" r="11" fill="#000"/>
					<circle id="project13" data-title="<?php echo esc_attr( $projects[12] ) ?>" data-color="rgba<?php echo esc_attr( $colors[12] ) ?>" class="project-circle" cx="260.228" cy="254.228" r="11" fill="#000"/>
				</svg>
				<!-- <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 973 483"><path d="M268.859 102l7.671 13.288h-15.343L268.859 102z" fill="#72777D"/><path d="M12.859 221l7.671 13.288H5.187L12.859 221zm77 72l7.671 13.288H82.187L89.859 293z" fill="#A2A9B1"/><path d="M221.859 321l7.671 13.288h-15.343L221.859 321z" fill="#202122"/><path d="M179.859 227l7.671 13.288h-15.343L179.859 227zm643.465 120.86l7.672 13.288h-15.343l7.671-13.288z" fill="#C8CCD1"/><path d="M866.879 412.084l7.672 13.288h-15.344l7.672-13.288zM98.859 176l7.671 13.288H91.187L98.859 176z" fill="#A2A9B1"/><path d="M368.585 339.001l7.672 13.288h-15.343l7.671-13.288zm96.706-36.91l7.672 13.288H457.62l7.671-13.288z" fill="#72777D"/><path d="M393.685 126.396l7.671 13.288h-15.343l7.672-13.288zm-84.157 119.591l7.672 13.288h-15.343l7.671-13.288z" fill="#A2A9B1"/><path d="M738.859 105l7.671 13.288h-15.343L738.859 105z" fill="#EAECF0"/><path d="M563.474 413.561l7.671 13.288h-15.343l7.672-13.288z" fill="#C8CCD1"/><path d="M483.008 395.844l7.672 13.288h-15.343l7.671-13.288z" fill="#A2A9B1"/><path d="M903.051 82.103l7.672 13.288H895.38l7.671-13.288z" fill="#72777D"/><path d="M877.952 157.401l7.672 13.288H870.28l7.672-13.288zm-301.191-13.288h11.073v11.073h-11.073z" fill="#A2A9B1"/><path d="M958.811 247.335L952.906 237 947 247.335l5.906 10.335 5.905-10.335z" fill="#C8CCD1"/><path d="M650.811 383.335L644.906 373 639 383.335l5.906 10.335 5.905-10.335z" fill="#202122"/><path d="M688.811 185.335L682.906 175 677 185.335l5.906 10.335 5.905-10.335z" fill="#C8CCD1"/><path d="M27.811 130.335L21.906 120 16 130.335l5.906 10.335 5.905-10.335z" fill="#EAECF0"/><path d="M172.811 358.335L166.906 348 161 358.335l5.906 10.335 5.905-10.335z" fill="#202122"/><path d="M148.811 96.335L142.906 86 137 96.335l5.906 10.335 5.905-10.335z" fill="#72777D"/><path d="M470.811 154.335L464.906 144 459 154.335l5.906 10.335 5.905-10.335z" fill="#A2A9B1"/><circle cx="29.846" cy="334.846" r="1.846" fill="#72777D"/><circle cx="145.846" cy="283.846" r="1.846" fill="#A2A9B1"/><circle cx="667.192" cy="256.691" r="1.846" fill="#202122"/><circle cx="574.916" cy="375.543" r="1.846" fill="#EAECF0"/><circle cx="335.735" cy="171.796" r="1.846" fill="#202122"/><circle cx="746.181" cy="250.047" r="1.846" fill="#72777D"/><circle cx="848.055" cy="206.492" r="1.846" fill="#72777D"/><circle cx="370.431" cy="372.59" r="1.846" fill="#A2A9B1"/><circle cx="755.04" cy="213.136" r="1.846" fill="#C8CCD1"/><circle cx="326.876" cy="207.969" r="1.846" fill="#72777D"/><circle cx="288.489" cy="290.648" r="1.846" fill="#C8CCD1"/><circle cx="433.179" cy="303.936" r="1.846" fill="#C8CCD1"/><circle cx="396.268" cy="346.014" r="1.846" fill="#202122"/><circle cx="301.039" cy="151.865" r="1.846" fill="#A2A9B1"/><circle cx="777.924" cy="421.312" r="1.846" fill="#202122"/><circle cx="555.722" cy="348.967" r="1.846" fill="#72777D"/><circle cx="942.546" cy="300.245" r="1.846" fill="#EAECF0"/><circle cx="69.846" cy="108.846" r="1.846" fill="#202122"/><circle cx="73.846" cy="256.846" r="1.846" fill="#72777D"/><circle cx="752.825" cy="480.369" r="1.846" fill="#EAECF0"/><circle cx="190.846" cy="260.846" r="1.846" fill="#C8CCD1"/><circle cx="846.578" cy="413.192" r="1.846" fill="#72777D"/><circle cx="944.022" cy="133.409" r="1.846" fill="#C8CCD1"/><circle cx="722.558" cy="324.606" r="1.846" fill="#202122"/><circle cx="315.803" cy="391.784" r="1.846" fill="#72777D"/><circle cx="434.656" cy="427.956" r="1.846" fill="#C8CCD1"/><circle cx="1.846" cy="202.846" r="1.846" fill="#72777D"/><circle cx="761.684" cy="320.915" r="1.846" fill="#202122"/><circle cx="724.035" cy="180.655" r="1.846" fill="#202122"/><circle cx="356.405" cy="265.549" r="1.846" fill="#EAECF0"/><circle cx="892.347" cy="224.209" r="1.846" fill="#EAECF0"/><circle cx="109.846" cy="348.846" r="1.846" fill="#EAECF0"/><circle cx="666.846" cy="134.846" r="1.846" fill="#C8CCD1"/><circle cx="226.846" cy="384.846" r="1.846" fill="#EAECF0"/><circle cx="910.064" cy="387.354" r="1.846" fill="#C8CCD1"/><circle cx="828.861" cy="446.411" r="1.846" fill="#C8CCD1"/><circle cx="866.51" cy="479.631" r="1.846" fill="#EAECF0"/><circle cx="968.383" cy="299.507" r="1.846" fill="#C8CCD1"/><circle cx="539.482" cy="464.128" r="1.846" fill="#EAECF0"/><circle cx="91.846" cy="143.846" r="1.846" fill="#EAECF0"/><circle cx="765.375" cy="287.696" r="1.846" fill="#A2A9B1"/><circle cx="228.846" cy="202.846" r="1.846" fill="#202122"/><circle cx="791.212" cy="370.375" r="1.846" fill="#72777D"/><circle cx="797.856" cy="221.256" r="1.846" fill="#72777D"/><circle cx="890.871" cy="51.468" r="1.846" fill="#202122"/><circle cx="684.846" cy="432.846" r="1.846" fill="#202122"/><circle cx="166.846" cy="171.846" r="1.846" fill="#C8CCD1"/><circle cx="625.853" cy="311.318" r="1.846" fill="#202122"/><circle cx="569.748" cy="226.424" r="1.846" fill="#202122"/><circle cx="786.846" cy="126.846" r="1.846" fill="#C8CCD1"/><circle cx="970.598" cy="2.746" r="1.846" fill="#72777D"/><circle cx="844.363" cy="120.121" r="1.846" fill="#EAECF0"/><circle cx="281.846" cy="338.632" r="1.846" fill="#A2A9B1"/><circle cx="938.855" cy="229.377" r="1.846" fill="#72777D"/><circle cx="614.846" cy="460.846" r="1.846" fill="#202122"/><circle cx="970.598" cy="462.652" r="1.846" fill="#EAECF0"/><circle cx="484.116" cy="206.492" r="1.846" fill="#C8CCD1"/><circle cx="627.329" cy="207.969" r="1.846" fill="#A2A9B1"/><circle cx="522.503" cy="272.931" r="1.846" fill="#72777D"/><circle cx="431.703" cy="247.832" r="1.846" fill="#72777D"/></svg> -->
			</div>
			<div class="side-vis">
				<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 357 993"><path d="M82.076 953.771l7.672 13.287H74.404l7.672-13.287z" fill="#EAECF0"/><path d="M131.536 615.669l7.672 13.288h-15.343l7.671-13.288z" fill="#A2A9B1"/><path d="M302.063 121.067l7.672 13.288h-15.343l7.671-13.288z" fill="#202122"/><path d="M258.509 799.484l7.672 13.288h-15.344l7.672-13.288z" fill="#A2A9B1"/><path d="M268.844 549.23l7.672 13.288h-15.344l7.672-13.288z" fill="#202122"/><path d="M304.278 665.13l7.672 13.287h-15.344l7.672-13.287z" fill="#C8CCD1"/><path d="M291.728 368.368l7.672 13.288h-15.343l7.671-13.288z" fill="#72777D"/><path d="M234.886 138.784l7.672 13.288h-15.344l7.672-13.288zM138.918 682.847l7.672 13.288h-15.343l7.671-13.288z" fill="#C8CCD1"/><path d="M17.113 726.401l7.672 13.288H9.442l7.671-13.288z" fill="#202122"/><path d="M152.206 258.374l7.672 13.288h-15.344l7.672-13.288z" fill="#C8CCD1"/><path d="M231.933 608.287l7.672 13.288h-15.344l7.672-13.288zM316.089 270.186l7.672 13.288h-15.343l7.671-13.288z" fill="#EAECF0"/><path d="M286.561 740.427l7.672 13.288h-15.344l7.672-13.288zM91.673 744.118l7.671 13.288H84.001l7.672-13.288z" fill="#72777D"/><path d="M71.741 506.414l7.672 13.288H64.069l7.672-13.288z" fill="#A2A9B1"/><path d="M173.614 407.493l7.672 13.288h-15.343l7.671-13.288z" fill="#202122"/><path d="M84.859 828l7.671 13.288H77.187L84.859 828z" fill="#C8CCD1"/><path d="M8.255 970.011l7.671 13.288H.584l7.672-13.288z" fill="#EAECF0"/><path d="M240.792 470.979l7.671 13.288H233.12l7.672-13.288z" fill="#A2A9B1"/><path d="M206.834 324.813l7.672 13.288h-15.344l7.672-13.288z" fill="#C8CCD1"/><path d="M220.122 85.633l7.671 13.288H212.45l7.672-13.288z" fill="#72777D"/><path fill="#A2A9B1" d="M235.624 214.82h11.073v11.073h-11.073z"/><path d="M280.655 85.633l-5.906-10.335-5.905 10.335 5.905 10.335 5.906-10.335z" fill="#EAECF0"/><path d="M170.811 178.335L164.906 168 159 178.335l5.906 10.335 5.905-10.335z" fill="#C8CCD1"/><path d="M67.811 648.335L61.906 638 56 648.335l5.906 10.335 5.905-10.335z" fill="#72777D"/><path d="M200.811 724.335L194.906 714 189 724.335l5.906 10.335 5.905-10.335z" fill="#EAECF0"/><path d="M192.811 845.335L186.906 835 181 845.335l5.906 10.335 5.905-10.335zM212.811 527.335L206.906 517 201 527.335l5.906 10.335 5.905-10.335z" fill="#202122"/><path d="M106.811 400.335L100.906 390 95 400.335l5.906 10.335 5.905-10.335z" fill="#EAECF0"/><circle cx="235.993" cy="562.149" r="1.846" fill="#72777D"/><circle cx="182.842" cy="77.143" r="1.846" fill="#72777D"/><circle cx="82.445" cy="660.331" r="1.846" fill="#EAECF0"/><circle cx="45.846" cy="831.846" r="1.846" fill="#202122"/><circle cx="303.171" cy="595.368" r="1.846" fill="#202122"/><circle cx="160.696" cy="639.661" r="1.846" fill="#A2A9B1"/><circle cx="217.538" cy="899.512" r="1.846" fill="#EAECF0"/><circle cx="6.409" cy="624.159" r="1.846" fill="#202122"/><circle cx="241.846" cy="677.846" r="1.846" fill="#C8CCD1"/><circle cx="137.846" cy="902.846" r="1.846" fill="#72777D"/><circle cx="133.382" cy="940.852" r="1.846" fill="#EAECF0"/><circle cx="80.969" cy="886.962" r="1.846" fill="#202122"/><circle cx="297.265" cy="1.846" r="1.846" fill="#72777D"/><circle cx="275.119" cy="827.167" r="1.846" fill="#202122"/><circle cx="303.171" cy="436.653" r="1.846" fill="#EAECF0"/><circle cx="124.523" cy="976.286" r="1.846" fill="#202122"/><circle cx="63.99" cy="562.149" r="1.846" fill="#202122"/><circle cx="224.92" cy="813.141" r="1.846" fill="#202122"/><circle cx="55.87" cy="991.05" r="1.846" fill="#202122"/><circle cx="348.94" cy="516.38" r="1.846" fill="#72777D"/><circle cx="349.678" cy="421.888" r="1.846" fill="#EAECF0"/><circle cx="331.223" cy="537.05" r="1.846" fill="#A2A9B1"/><circle cx="348.202" cy="258.005" r="1.846" fill="#C8CCD1"/><circle cx="274.38" cy="616.038" r="1.846" fill="#202122"/><circle cx="155.846" cy="555.846" r="1.846" fill="#72777D"/><circle cx="317.197" cy="204.116" r="1.846" fill="#EAECF0"/><circle cx="56.608" cy="607.918" r="1.846" fill="#72777D"/><circle cx="193.177" cy="973.333" r="1.846" fill="#EAECF0"/><circle cx="281.024" cy="463.228" r="1.846" fill="#202122"/><circle cx="134.858" cy="587.248" r="1.846" fill="#A2A9B1"/><circle cx="255.925" cy="105.195" r="1.846" fill="#EAECF0"/><circle cx="327.532" cy="73.452" r="1.846" fill="#C8CCD1"/><circle cx="268.475" cy="929.779" r="1.846" fill="#C8CCD1"/><circle cx="300.956" cy="344.376" r="1.846" fill="#202122"/><circle cx="305.385" cy="508.259" r="1.846" fill="#202122"/><circle cx="351.154" cy="892.13" r="1.846" fill="#202122"/><circle cx="343.772" cy="439.606" r="1.846" fill="#C8CCD1"/><circle cx="291.359" cy="326.659" r="1.846" fill="#72777D"/><circle cx="288.406" cy="887.701" r="1.846" fill="#202122"/><circle cx="154.052" cy="294.916" r="1.846" fill="#A2A9B1"/><circle cx="75.801" cy="785.089" r="1.846" fill="#C8CCD1"/><circle cx="124.523" cy="751.131" r="1.846" fill="#72777D"/><circle cx="296.527" cy="58.688" r="1.846" fill="#EAECF0"/><circle cx="306.123" cy="861.125" r="1.846" fill="#202122"/><circle cx="222.706" cy="756.299" r="1.846" fill="#72777D"/><circle cx="197.606" cy="200.425" r="1.846" fill="#202122"/><circle cx="171.031" cy="800.591" r="1.846" fill="#C8CCD1"/><circle cx="176.198" cy="745.964" r="1.846" fill="#72777D"/><circle cx="269.951" cy="190.09" r="1.846" fill="#72777D"/><circle cx="296.527" cy="802.806" r="1.846" fill="#72777D"/><circle cx="258.14" cy="738.582" r="1.846" fill="#202122"/><circle cx="342.296" cy="481.684" r="1.846" fill="#A2A9B1"/><circle cx="317.935" cy="387.931" r="1.846" fill="#A2A9B1"/><circle cx="117.141" cy="790.995" r="1.846" fill="#A2A9B1"/><circle cx="277.333" cy="692.074" r="1.846" fill="#72777D"/><circle cx="157.743" cy="369.475" r="1.846" fill="#C8CCD1"/><circle cx="250.019" cy="335.518" r="1.846" fill="#EAECF0"/><circle cx="322.364" cy="734.891" r="1.846" fill="#A2A9B1"/><circle cx="341.558" cy="171.634" r="1.846" fill="#C8CCD1"/><circle cx="237.47" cy="26.207" r="1.846" fill="#202122"/><circle cx="169.554" cy="521.547" r="1.846" fill="#C8CCD1"/><circle cx="90.566" cy="688.383" r="1.846" fill="#72777D"/><circle cx="113.45" cy="540.741" r="1.846" fill="#202122"/><circle cx="141.502" cy="463.967" r="1.846" fill="#A2A9B1"/><circle cx="180.627" cy="460.275" r="1.846" fill="#202122"/><circle cx="114.846" cy="848.846" r="1.846" fill="#202122"/><circle cx="242.637" cy="423.365" r="1.846" fill="#72777D"/><circle cx="354.846" cy="649.846" r="1.846" fill="#A2A9B1"/><circle cx="239.684" cy="246.194" r="1.846" fill="#C8CCD1"/><circle cx="193.177" cy="687.645" r="1.846" fill="#C8CCD1"/><circle cx="275.119" cy="508.259" r="1.846" fill="#EAECF0"/><circle cx="291.359" cy="263.173" r="1.846" fill="#C8CCD1"/><circle cx="208.68" cy="369.475" r="1.846" fill="#202122"/><circle cx="140.026" cy="225.524" r="1.846" fill="#A2A9B1"/><circle cx="343.034" cy="595.368" r="1.846" fill="#EAECF0"/><circle cx="49.964" cy="946.758" r="1.846" fill="#EAECF0"/><circle cx="287.668" cy="563.625" r="1.846" fill="#C8CCD1"/><circle cx="236.732" cy="658.117" r="1.846" fill="#202122"/><circle cx="186.846" cy="931.846" r="1.846" fill="#202122"/></svg>
			</div>

		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'movement', 'wmf_movement_callback' );

/**
 * Define a [wmf_top_data] wrapper shortcode that renders wrapper for a wmf_top_data of milestones, see [year] shortcode.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_top_data_callback( $atts = [], $content = '' ) {
	$defaults = [
		'path_edits' => '/assets/src/shortcodejs/wp20edits.csv',
		'path_views' => '/assets/src/shortcodejs/wp20pageviews.csv',
		'lang' => 'en',
		'id' => 'top-data',
	];
	$atts = shortcode_atts( $defaults, $atts, 'wmf_top_data' );
	$atts['url_edits'] = get_stylesheet_directory_uri() . $atts['path_edits'];
	$atts['url_views'] = get_stylesheet_directory_uri() . $atts['path_views'];
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	$header = get_theme_mod( 'wmf_image_credit_header', __( 'Photo credits', 'shiro' ) );

	wp_enqueue_script( 'd3', get_stylesheet_directory_uri() . '/assets/src/datavisjs/libraries/d3.min.js', array( ), '0.0.1', true );
	wp_enqueue_script( 'top-data', get_stylesheet_directory_uri() . '/assets/dist/shortcode-top.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'top-data', "var topAtts = " . json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr( $atts['id'] ) ?>" class="top-data mw-980 mod-margin-bottom">
		<div>
			<div class="mod-margin-bottom_xs wysiwyg">
				<p><?php echo wp_kses_post( $content ) ?></p>
			</div>
			<div class="mod-margin-bottom_xs data-options">
				<p>
					<span>
						<input type="radio" id="views-radio" name="most" value="views" checked>
						<label for="views-radio">Most viewed articles</label>
					</span>
					<span class="p">or</span>
					<span>
						<input type="radio" id="edits-radio" name="most" value="edits">
						<label for="edits-radio">most edited articles</label>
					</span>
					<span class="p">in</span>
					<select class="p" name="year" id="year-select">
					    <option value="2020" selected="selected">2020</option>
					    <option value="2019">2019</option>
					    <option value="2018">2018</option>
					    <option value="2017">2017</option>
					    <option value="2016">2016</option>
					    <option value="2015">2015</option>
					    <option value="2014">2014</option>
					    <option value="2013">2013</option>
					    <option value="2012">2012</option>
					    <option value="2011">2011</option>
					    <option value="2010">2010</option>
					    <option value="2009">2009</option>
					    <option value="2008">2008</option>
					    <option value="2007">2007</option>
					    <option value="2006">2006</option>
					    <option value="2005">2005</option>
					    <option value="2004">2004</option>
					    <option value="2003">2003</option>
					    <option value="2002">2002</option>
					    <option value="2001">2001</option>
					</select>
				</p>
			</div>
		</div>
		<div class="no-data" style="display: none;"><p>There is not data for the options you chose. Please choose another year above.</p></div>
		<div id="top-data-container" class="mod-margin-bottom">
			<div id="enwiki" class="top-data-content flex flex-medium" style="display: none;">
				<div class="w-68p flex flex-all main-desc">
					<a class="article-image-link" href="/about" target="_blank"><div class="article-image"></div></a>
					<a href="" class="w-50p details"><div>
						<span class=lang-code>EN</span>
						<p class="heading"></p>
						<p class="desc"></p>
					</div></a>
				</div>
				<div class="w-32p data"><span class="total"></span><div id="enwiki-graph" class="graph"></div></div>
			</div>
			<div id="arwiki" class="top-data-content flex flex-medium" style="display: none;">
				<div class="w-68p flex flex-all main-desc">
					<a class="article-image-link" href="/about" target="_blank"><div class="article-image"></div></a>
					<a href="" class="w-50p details"><div>
						<span class=lang-code>AR</span>
						<p class="heading"></p>
						<p class="desc"></p>
					</div></a>
				</div>
				<div class="w-32p data"><span class="total"></span><div id="arwiki-graph" class="graph"></div></div>
			</div>
			<div id="dewiki" class="top-data-content flex flex-medium" style="display: none;">
				<div class="w-68p flex flex-all main-desc">
					<a class="article-image-link" href="/about" target="_blank"><div class="article-image"></div></a>
					<a href="" class="w-50p details"><div>
						<span class=lang-code>DE</span>
						<p class="heading"></p>
						<p class="desc"></p>
					</div></a>
				</div>
				<div class="w-32p data"><span class="total"></span><div id="dewiki-graph" class="graph"></div></div>
			</div>
			<div id="eswiki" class="top-data-content flex flex-medium" style="display: none;">
				<div class="w-68p flex flex-all main-desc">
					<a class="article-image-link" href="/about" target="_blank"><div class="article-image"></div></a>
					<a href="" class="w-50p details"><div>
						<span class=lang-code>ES</span>
						<p class="heading"></p>
						<p class="desc"></p>
					</div></a>
				</div>
				<div class="w-32p data"><span class="total"></span><div id="eswiki-graph" class="graph"></div></div>
			</div>
			<div id="frwiki" class="top-data-content flex flex-medium" style="display: none;">
				<div class="w-68p flex flex-all main-desc">
					<a class="article-image-link" href="/about" target="_blank"><div class="article-image"></div></a>
					<a href="" class="w-50p details"><div>
						<span class=lang-code>FR</span>
						<p class="heading"></p>
						<p class="desc"></p>
					</div></a>
				</div>
				<div class="w-32p data"><span class="total"></span><div id="frwiki-graph" class="graph"></div></div>
			</div>
			<div id="ruwiki" class="top-data-content flex flex-medium" style="display: none;">
				<div class="w-68p flex flex-all main-desc">
					<a class="article-image-link" href="/about" target="_blank"><div class="article-image"></div></a>
					<a href="" class="w-50p details"><div>
						<span class=lang-code>RU</span>
						<p class="heading"></p>
						<p class="desc"></p>
					</div></a>
				</div>
				<div class="w-32p data"><span class="total"></span><div id="ruwiki-graph" class="graph"></div></div>
			</div>
			<div id="zhwiki" class="top-data-content flex flex-medium" style="display: none;">
				<div class="w-68p flex flex-all main-desc">
					<a class="article-image-link" href="/about" target="_blank"><div class="article-image"></div></a>
					<a href="" class="w-50p details"><div>
						<span class=lang-code>ZH</span>
						<p class="heading"></p>
						<p class="desc"></p>
					</div></a>
				</div>
				<div class="w-32p data"><span class="total"></span><div id="zhwiki-graph" class="graph"></div></div>
			</div>
		</div>
		<div class="article-photo-credits-container">
			<h3 class="h2"><?php echo esc_html( $header ); ?></h3>
			<div class="article-photo-credits"></div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'wmf_top_data', 'wmf_top_data_callback' );

/**
 * Define a [wp20_easter_eggs] wrapper shortcode that creates a HTML wrapper for easter eggs and initializes js.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wp20_easter_eggs_shortcode_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'search' => 'Wikipedia',
		'index_list' => '4|5|6|9|10|11|12|14|19|21|22|23|25|26|27|29|31|32|33',
		'highlight_index' => '7|27',
	];
	$atts = shortcode_atts( $defaults, $atts, 'wp20_easter_eggs' );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );

	wp_enqueue_script( 'wp20_easter_eggs', get_stylesheet_directory_uri() . '/assets/dist/shortcode-easter-eggs.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'wp20_easter_eggs', "var eggsAtts = " . json_encode($atts) . ";");
	
	ob_start();
	?>

	<div class="easter-egg-container">
		<?php echo wp_kses_post( $content ); ?>
	</div>

	<?php 
	return (string) ob_get_clean();

}
add_shortcode( 'wp20_easter_eggs', 'wp20_easter_eggs_shortcode_callback' );

/**
 * Define a [egg] wrapper shortcode that creates a HTML wrapper for one easter egg in [wp20_easter_eggs].
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function egg_shortcode_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'egg' );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	
	ob_start();
	?>

	<div class="easter-egg-content wysiwyg">
		<?php echo wp_kses_post( $content ); ?>
	</div>

	<?php 
	return (string) ob_get_clean();

}
add_shortcode( 'egg', 'egg_shortcode_callback' );