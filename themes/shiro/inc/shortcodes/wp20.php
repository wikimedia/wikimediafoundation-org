<?php
/**
 * Define the shortcodes for the Wikipedia 20th birthday page.
 * Author: Hang Do Thi Duc
 *
 * @package shiro
 */

/**
 * Define a [symbol_grid] shortcode that renders a grid of images and text.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_symbols_grid_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'text' => '',
		'id' => 'symbols-grid',
	];
	$atts = shortcode_atts( $defaults, $atts, 'symbols_grid' );
	$texts = preg_split('/\|/', $atts['text']);
	$text1Class = sizeof($texts) >= 1 ? "grid-item grid-text grid-text-1" : "grid-item";
	$text2Class = sizeof($texts) >= 2 ? "grid-item grid-text grid-text-2" : "grid-item";
	$text3Class = sizeof($texts) >= 3 ? "grid-item grid-text grid-text-3" : "grid-item";

	wp_enqueue_script( 'symbols_grid', get_template_directory_uri() . '/assets/dist/shortcode-symbol-grid.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'symbols_grid', "var gridAtts = " . wp_json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr( $atts['id'] ) ?>" class="symbols-grid mod-margin-bottom">
		<?php for ($i=0; $i < 24; $i++) { ?>
			<div class="grid-item grid-symbol"><div></div><div></div></div>
		<?php } ?>
		<div class="<?php echo esc_attr($text1Class) ?>"><div class="wp20"><h2><?php echo esc_html($texts[0]) ?></h2></div></div>
		<div class="<?php echo esc_attr($text2Class) ?>"><div class="wp20"><h2><?php echo esc_html($texts[1]) ?></h2></div></div>
		<div class="<?php echo esc_attr($text3Class) ?>"><div class="wp20"><h2><?php echo esc_html($texts[2]) ?></h2></div></div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'symbols_grid', 'wmf_symbols_grid_callback' );

/**
 * Define a [story_carousel] shortcode that includes [story].
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_story_carousel_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'id' => 'volunteer-stories',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'story_carousel' );
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text( $content );

	wp_enqueue_script( 'story_carousel', get_template_directory_uri() . '/assets/dist/shortcode-stories.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'story_carousel', "var storiesAtts = " . wp_json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr($atts['id']) ?>" class="mod-margin-bottom_sm story-carousel-container mod-padding-vertical_sm <?php echo esc_attr( $atts['class'] ) ?>">
		<div class="mw-980 story-carousel-inner">
			<div class="story-carousel w-68p">
				<div class="story-content-container"><?php echo wp_kses_post( $content ) ?></div>
				<div class="story-nav flex flex-medium flex-space-between" aria-hidden="true">
					<div class="prev-story"><span>←</span></div>
					<span class="index"></span>
					<div class="next-story"><span>→</span></div>
				</div>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'story_carousel', 'wmf_story_carousel_callback' );

/**
 * Define a [story] shortcode that creates a (volunteer) story.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_story_shortcode_callback( $atts = [], $content = '' ) {
	$defaults = [
		'name' => '',
		'since' => '',
		'location' => '',
		'img' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'story' );
	$image_id = custom_get_attachment_id_by_slug( $atts['img'] );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, array( 400, 400 ) ) : null;

	ob_start();
	?>
	<div class="story-content" style="display: none;">
		<?php if ( isset($image_url) ) { ?>
			<div class="story-image" style="background-image: url(<?php echo esc_attr($image_url) ?>);"></div>
		<?php } ?>
		<h2><?php echo esc_html( $atts['name'] ); ?></h2>

		<?php if ( !empty($atts['location'] ) ) { ?>
			<p class="story-location flex flex-all">
				<span><?php echo esc_html( $atts['location'] ); ?></span>
			</p>
		<?php } ?>

		<?php if ( !empty($atts['since'] ) ) { ?>
			<p class="story-since flex flex-all">
				<span><?php echo esc_html( $atts['since'] ); ?></span>
			</p>
		<?php } ?>

		<p class="story-desc p"><?php echo wp_kses_post( $content ) ?></p>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'story', 'wmf_story_shortcode_callback' );

/**
 * Define a [recent_edits] shortcode.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_recent_edits_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'id' => 'recent-edits',
		'lang_list' => 'en|ar|es|de|fr|ru|zh',
		'label' => 'One human just edited',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'recent_edits' );
	$atts['lang_list'] = preg_split('/\|/', $atts['lang_list']);
	$atts['lang_list_long'] = [];
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text( $content );

	for ($i=0; $i < count($atts['lang_list']); $i++) {
		array_push( $atts['lang_list_long'], get_theme_mod( $atts['lang_list'][$i] . '_wikipedia', strtoupper($atts['lang_list'][$i]) . ' Wikipedia' ) );
	}

	wp_enqueue_script( 'recent_edits', get_template_directory_uri() . '/assets/dist/shortcode-recent-edits.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'recent_edits', "var recentEditsAtts = " . wp_json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr($atts['id'] . '-container') ?>" class="mod-margin-bottom mod-padding-vertical <?php echo esc_attr( $atts['class'] ) ?>">
		<div class="mod-margin-bottom_xs"><?php echo wp_kses_post( $content ) ?></div>
		<div id="<?php echo esc_attr($atts['id']) ?>" class="recent-edits mw-980">
			<div class="rounded">
				<div class="label-container"><span class="label"></span> <span class="wiki wp20-color-7"></span></div>
				<div class="accent"><svg width="129" height="107" viewBox="0 0 129 107" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M126.08 1C118.84 27.21 97.28 49 71.14 56.48C66.06 57.93 60.4 58.83 55.66 56.48C50.92 54.13 48.22 47.33 51.66 43.33C54.66 39.86 60.66 40.48 63.94 43.62C67.22 46.76 68.49 51.55 68.83 56.11C69.3303 64.1928 67.6724 72.2633 64.0261 79.4943C60.3799 86.7252 54.8765 92.8566 48.08 97.26C41.1726 101.488 33.2655 103.804 25.1686 103.971C17.0716 104.138 9.07579 102.15 2 98.21" stroke="#000000" stroke-width="6" stroke-miterlimit="10"/></svg>
				</div>
				<div class="title"></div>
				<div class="box-accent box-accent-top">
					<svg width="240" height="150" viewBox="0 0 240 150" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M46 101.215L19 21.7153" stroke="black" stroke-width="20"/>
						<path d="M111 98L139 20" stroke="black" stroke-width="20"/>
						<path d="M162 130L229 90" stroke="black" stroke-width="20"/>
					</svg>
				</div>
				<div class="box-accent box-accent-bottom">
					<svg width="240" height="150" viewBox="0 0 240 150" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M190.398 43.073L225.047 119.55" stroke="black" stroke-width="20"/>
						<path d="M126.025 52.6325L105.791 132.998" stroke="black" stroke-width="20"/>
						<path d="M72.1384 25.7759L9.37354 72.1394" stroke="black" stroke-width="20"/>
					</svg>
				</div>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'recent_edits', 'wmf_recent_edits_callback' );

/**
 * Define a [timeline] wrapper shortcode that renders wrapper for a timeline of milestones, see [year] shortcode.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_timeline_callback( $atts = [], $content = '' ) {
	$defaults = [
		'id' => 'timeline',
		'title' => '',
		'margin' => '0',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'timeline' );
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text($content);
	$margin = $atts['margin'] === '0' ? '' : ' mod-margin-bottom';
	$classes = "timeline " . $atts['class'] . $margin;

	wp_enqueue_script( 'timeline', get_template_directory_uri() . '/assets/dist/shortcode-timeline.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'timeline', "var timelineAtts = " . wp_json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr($atts['id']) ?>" class="<?php echo esc_attr($classes) ?>">
		<div class="timeline-container">
			<div class="milestones-window">
				<div class="milestones flex flex-all">
					<?php echo wp_kses_post( $content ) ?>
				</div>
			</div>
			<div class="milestone-nav flex flex-all flex-space-between" aria-hidden="true">
				<div id="prev-milestone" class="prev hidden"><span>←</span></div>
				<div id="next-milestone" class="next hidden"><span>→</span></div>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'timeline', 'wmf_timeline_callback' );

/**
 * Define a [milestone] wrapper shortcode that renders one year for the timeline, see [timeline].
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_milestone_callback( $atts = [], $content = '' ) {
	$defaults = [
		'img' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'milestone' );
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text( $content );
	$classes = "milestone";
	$image_id = custom_get_attachment_id_by_slug( $atts['img'] );

	ob_start();
	?>

	<div class="<?php echo esc_attr($classes) ?>">
		<div class="milestone-container">
			<?php if ( $image_id ) echo wp_get_attachment_image( $image_id, array( 500, 500 ) ); ?>
			<div class="milestone-desc"><?php echo wp_kses_post( $content ) ?></div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'milestone', 'wmf_milestone_callback' );

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
		'bg_color' => '0',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'wmf_section' );
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text( $content );

	$margin = $atts['margin'] === '1' ? 'mod-margin-bottom ' : '';
	$classes = $atts['bg_color'] === '1' ? "section mod-padding-vertical bg-ltgray " . $margin : "mw-980 section " . $margin;
	$atts['class'] = $atts['bg_color'] === '1' ? $atts['class'] . ' mw-980' :  $atts['class'];
	$id = strtolower( str_replace(" ", "-", $atts['title']) );
	$image_id = custom_get_attachment_id_by_slug( $atts['img'] );
	$image = $image_id ? wp_get_attachment_image( $image_id, array( 600, 400 ) ) : null;
	$atts['columns'] = $image === null && strlen($atts['title']) === 0 ? '1' : $atts['columns'];
	$confetti_opt = random_int(1, 10);

	ob_start();
	?>

	<?php if ( $atts['columns'] === '1' ) { ?>
		<div id="<?php echo esc_attr( $id ) ?>" class="<?php echo esc_attr($classes) ?>" data-confetti-option="<?php echo esc_attr( $confetti_opt ) ?>">
			<div class="<?php echo esc_attr($atts['class']) ?>">
				<?php echo esc_html( $atts['title'] )  . wp_kses_post( $content ) ?>
			</div>
		</div>
	<?php } else {
		if ( $atts['reverse'] === '0') { ?>
			<div id="<?php echo esc_attr( $id ) ?>" class="<?php echo esc_attr($classes) ?>">
				<div class="flex flex-medium flex-space-between <?php echo esc_attr($atts['class']) ?>">
					<div class="w-48p mod-margin-bottom_xs"><?php echo wp_kses_post( $content ) ?></div>
					<div class="w-48p mod-margin-bottom_xs">
						<?php if ( $image_id ) {
							echo wp_get_attachment_image( $image_id, array( 600, 400 ) );
						} else { ?>
						<h1><?php echo esc_html( $atts['title'] ); ?></h1>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div id="<?php echo esc_attr( $id ) ?>" class="<?php echo esc_attr($classes) ?>">
				<div class="flex flex-medium flex-space-between flex-column-reverse-small <?php echo esc_attr($atts['class']) ?>">
					<div class="w-48p mod-margin-bottom_xs">
						<?php if ( $image_id ) {
							echo wp_get_attachment_image( $image_id, array( 600, 400 ) );
						} else { ?>
						<h1><?php echo esc_html( $atts['title'] ); ?></h1>
						<?php } ?>
					</div>
					<div class="w-48p mod-margin-bottom_xs"><?php echo wp_kses_post( $content ) ?></div>
				</div>
			</div>
	<?php }
	}
	return (string) ob_get_clean();
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
		'id' => 'movement-content',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'movement' );
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text( $content );

	wp_enqueue_script( 'movement', get_template_directory_uri() . '/assets/dist/shortcode-movement.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'movement', "var movementAtts = " . wp_json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr($atts['id']) ?>" class="movement section mod-margin-bottom <?php echo esc_attr($atts['class']) ?>">
		<div class="mw-980">
			<div class="w-68p">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		</div>
		<div class="movement-vis">
			<div class="main-vis">
				<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 973 483"><path d="M268.859 102l7.671 13.288h-15.343L268.859 102z" fill="#72777D"/><path d="M12.859 221l7.671 13.288H5.187L12.859 221zM89.859 293l7.671 13.288H82.187L89.859 293z" fill="#A2A9B1"/><path d="M221.859 321l7.671 13.288h-15.343L221.859 321z" fill="#202122"/><path d="M179.859 227l7.671 13.288h-15.343L179.859 227zM823.324 347.86l7.672 13.288h-15.343l7.671-13.288z" fill="#C8CCD1"/><path d="M866.879 412.084l7.672 13.288h-15.344l7.672-13.288zM98.859 176l7.671 13.288H91.187L98.859 176z" fill="#A2A9B1"/><path d="M368.585 339.001l7.672 13.288h-15.343l7.671-13.288zM465.291 302.091l7.672 13.288H457.62l7.671-13.288z" fill="#72777D"/><path d="M393.685 126.396l7.671 13.288h-15.343l7.672-13.288zM309.528 245.987l7.672 13.288h-15.343l7.671-13.288z" fill="#A2A9B1"/><path d="M738.859 105l7.671 13.288h-15.343L738.859 105z" fill="#EAECF0"/><path d="M563.474 413.561l7.671 13.288h-15.343l7.672-13.288z" fill="#C8CCD1"/><path d="M483.008 395.844l7.672 13.288h-15.343l7.671-13.288z" fill="#A2A9B1"/><path d="M903.051 82.103l7.672 13.288H895.38l7.671-13.288z" fill="#72777D"/><path d="M877.952 157.401l7.672 13.288H870.28l7.672-13.288zM576.761 144.113h11.073v11.073h-11.073z" fill="#A2A9B1"/><path d="M958.811 247.335L952.906 237 947 247.335l5.906 10.335 5.905-10.335z" fill="#C8CCD1"/><path d="M650.811 383.335L644.906 373 639 383.335l5.906 10.335 5.905-10.335z" fill="#202122"/><path d="M688.811 185.335L682.906 175 677 185.335l5.906 10.335 5.905-10.335z" fill="#C8CCD1"/><path d="M27.811 130.335L21.906 120 16 130.335l5.906 10.335 5.905-10.335zM172.811 358.335L166.906 348 161 358.335l5.906 10.335 5.905-10.335z" fill="#202122"/><path d="M148.811 96.335L142.906 86 137 96.335l5.906 10.335 5.905-10.335z" fill="#72777D"/><path d="M470.811 154.335L464.906 144 459 154.335l5.906 10.335 5.905-10.335z" fill="#A2A9B1"/><circle cx="29.846" cy="334.846" r="1.846" fill="#72777D"/><circle cx="145.846" cy="283.846" r="1.846" fill="#A2A9B1"/><circle cx="667.192" cy="256.691" r="1.846" fill="#202122"/><circle cx="574.916" cy="375.543" r="1.846" fill="#EAECF0"/><circle cx="335.735" cy="171.796" r="1.846" fill="#202122"/><circle cx="746.181" cy="250.047" r="1.846" fill="#72777D"/><circle cx="848.055" cy="206.492" r="1.846" fill="#72777D"/><circle cx="370.431" cy="372.59" r="1.846" fill="#A2A9B1"/><circle cx="755.04" cy="213.136" r="1.846" fill="#C8CCD1"/><circle cx="326.876" cy="207.969" r="1.846" fill="#72777D"/><circle cx="288.489" cy="290.648" r="1.846" fill="#C8CCD1"/><circle cx="433.179" cy="303.936" r="1.846" fill="#C8CCD1"/><circle cx="396.268" cy="346.014" r="1.846" fill="#202122"/><circle cx="301.039" cy="151.865" r="1.846" fill="#A2A9B1"/><circle cx="777.924" cy="421.312" r="1.846" fill="#202122"/><circle cx="555.722" cy="348.967" r="1.846" fill="#72777D"/><circle cx="942.546" cy="300.245" r="1.846" fill="#EAECF0"/><circle cx="69.846" cy="108.846" r="1.846" fill="#202122"/><circle cx="73.846" cy="256.846" r="1.846" fill="#72777D"/><circle cx="752.825" cy="480.369" r="1.846" fill="#EAECF0"/><circle cx="190.846" cy="260.846" r="1.846" fill="#C8CCD1"/><circle cx="846.578" cy="413.192" r="1.846" fill="#72777D"/><circle cx="944.022" cy="133.409" r="1.846" fill="#C8CCD1"/><circle cx="722.558" cy="324.606" r="1.846" fill="#202122"/><circle cx="315.803" cy="391.784" r="1.846" fill="#72777D"/><circle cx="434.656" cy="427.956" r="1.846" fill="#C8CCD1"/><circle cx="1.846" cy="202.846" r="1.846" fill="#72777D"/><circle cx="761.684" cy="320.915" r="1.846" fill="#202122"/><circle cx="724.035" cy="180.655" r="1.846" fill="#202122"/><circle cx="356.405" cy="265.549" r="1.846" fill="#EAECF0"/><circle cx="892.347" cy="224.209" r="1.846" fill="#EAECF0"/><circle cx="109.846" cy="348.846" r="1.846" fill="#EAECF0"/><circle cx="666.846" cy="134.846" r="1.846" fill="#C8CCD1"/><circle cx="226.846" cy="384.846" r="1.846" fill="#EAECF0"/><circle cx="910.064" cy="387.354" r="1.846" fill="#C8CCD1"/><circle cx="828.861" cy="446.411" r="1.846" fill="#C8CCD1"/><circle cx="866.51" cy="479.631" r="1.846" fill="#EAECF0"/><circle cx="968.383" cy="299.507" r="1.846" fill="#C8CCD1"/><circle cx="539.482" cy="464.128" r="1.846" fill="#EAECF0"/><circle cx="91.846" cy="143.846" r="1.846" fill="#EAECF0"/><circle cx="765.375" cy="287.696" r="1.846" fill="#A2A9B1"/><circle cx="228.846" cy="202.846" r="1.846" fill="#202122"/><circle cx="791.212" cy="370.375" r="1.846" fill="#72777D"/><circle cx="797.856" cy="221.256" r="1.846" fill="#72777D"/><circle cx="890.871" cy="51.468" r="1.846" fill="#202122"/><circle cx="684.846" cy="432.846" r="1.846" fill="#202122"/><circle cx="166.846" cy="171.846" r="1.846" fill="#C8CCD1"/><circle cx="625.853" cy="311.318" r="1.846" fill="#202122"/><circle cx="569.748" cy="226.424" r="1.846" fill="#202122"/><circle cx="786.846" cy="126.846" r="1.846" fill="#C8CCD1"/><circle cx="970.598" cy="2.746" r="1.846" fill="#72777D"/><circle cx="844.363" cy="120.121" r="1.846" fill="#EAECF0"/><circle cx="281.846" cy="338.632" r="1.846" fill="#A2A9B1"/><circle cx="938.855" cy="229.377" r="1.846" fill="#72777D"/><circle cx="614.846" cy="460.846" r="1.846" fill="#202122"/><circle cx="970.598" cy="462.652" r="1.846" fill="#EAECF0"/><circle cx="484.116" cy="206.492" r="1.846" fill="#C8CCD1"/><circle cx="627.329" cy="207.969" r="1.846" fill="#A2A9B1"/><circle cx="522.503" cy="272.931" r="1.846" fill="#72777D"/><circle cx="431.703" cy="247.832" r="1.846" fill="#72777D"/><path d="M809.593 77.138c3.739-.53 6.605-3.727 6.605-7.586 0-2.358-1.064-4.45-2.733-5.864l-3.872 3.86v9.59zm-2.172 0v-9.59l-3.871-3.874a7.632 7.632 0 00-2.733 5.863c0 3.874 2.866 7.071 6.604 7.601zm7.772-14.26a9.347 9.347 0 012.763 6.659c0 2.504-.99 4.89-2.763 6.659a9.402 9.402 0 01-6.678 2.755 9.44 9.44 0 01-6.679-2.755 9.371 9.371 0 01-2.777-6.66c0-2.518.989-4.89 2.763-6.658.147-.147.31-.295.472-.442l-2.157-2.15c-.162.146-.31.294-.473.441a12.216 12.216 0 00-2.674 3.963 12.367 12.367 0 00-.99 4.847c0 1.68.325 3.314.99 4.847a12.693 12.693 0 002.674 3.963 12.255 12.255 0 003.975 2.666c1.537.648 3.177.987 4.861.987 1.684 0 3.324-.324 4.861-.987a12.74 12.74 0 003.975-2.666 12.217 12.217 0 002.674-3.963c.65-1.532.99-3.168.99-4.847 0-1.68-.325-3.315-.99-4.847a12.692 12.692 0 00-2.674-3.963c-.148-.147-.311-.294-.473-.442l-2.143 2.151c.163.133.311.28.473.442zM808.5 57a3.892 3.892 0 013.901 3.89c0 2.15-1.744 3.888-3.901 3.888a3.892 3.892 0 01-3.901-3.889c0-2.15 1.744-3.889 3.901-3.889z" fill="#000"/>
					<circle id="project1" class="project-circle wp20-fill-color-11" cx="506.096" cy="198.9" r="11" fill="#000"/>
					<circle id="project2" class="project-circle wp20-fill-color-1" cx="464.922" cy="356.349" r="11" fill="#000"/>
					<circle id="project3" class="project-circle wp20-fill-color-2" cx="386.228" cy="278.228" r="11" fill="#000"/>
					<circle id="project4" class="project-circle wp20-fill-color-3" cx="555.722" cy="268.502" r="11" fill="#000"/>
					<circle id="project5" class="project-circle wp20-fill-color-4" cx="822.217" cy="241.188" r="11" fill="#000"/>
					<circle id="project6" class="project-circle wp20-fill-color-5" cx="202.228" cy="159.228" r="11" fill="#000"/>
					<circle id="project7" class="project-circle wp20-fill-color-6" cx="859.866" cy="306.151" r="11" fill="#000"/>
					<circle id="project8" class="project-circle wp20-fill-color-7" cx="680.228" cy="307.228" r="11" fill="#000"/>
					<circle id="project9" class="project-circle wp20-fill-color-8" cx="750.578" cy="372.226" r="11" fill="#000"/>
					<circle id="project10" class="project-circle wp20-fill-color-9" cx="410.295" cy="200.587" r="11" fill="#000"/>
					<circle id="project11" class="project-circle wp20-fill-color-10" cx="636.228" cy="237.228" r="11" fill="#000"/>
					<circle id="project12" class="project-circle wp20-fill-color-11" cx="938.786" cy="345.276" r="11" fill="#000"/>
					<circle id="project13" class="project-circle wp20-fill-color-1" cx="260.228" cy="254.228" r="11" fill="#000"/>
				</svg>
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
 * Define a [movement_tooltip] shortcode that wraps all tooltips.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_movement_tooltip_callback( $atts = [], $content = '' ) {
	$content = custom_filter_shortcode_text( $content );
	ob_start();
	?>

	<div class="tooltip hidden">
		<?php echo wp_kses_post( $content ) ?>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'movement_tooltip', 'wmf_movement_tooltip_callback' );


/**
 * Define a [wmf_top_data] wrapper shortcode that renders wrapper.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_top_data_callback( $atts = [], $content = '' ) {
	$defaults = [
		'path_edits' => '/assets/src/foundation-assets/wikipedia20/data/wp20edits.csv',
		'path_views' => '/assets/src/foundation-assets/wikipedia20/data/wp20pageviews.csv',
		'lang' => 'en',
		'id' => 'top-data',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'wmf_top_data' );
	$atts['directory'] = get_template_directory_uri() . "/assets/src/foundation-assets/wikipedia20/data/thumbnails/";
	$atts['url_edits'] = get_template_directory_uri() . $atts['path_edits'];
	$atts['url_views'] = get_template_directory_uri() . $atts['path_views'];
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text( $content );
	$header = get_theme_mod( 'wmf_image_credit_header', __( 'Photo credits', 'shiro-admin' ) );
	$most_viewed_label = get_theme_mod( 'wikipedia_article_most_viewed', __( 'Most viewed articles', 'shiro-admin' ) );
	$most_edited_label = get_theme_mod( 'wikipedia_article_most_edited', __( 'most edited articles', 'shiro-admin' ) );
	$in_label = get_theme_mod( 'wikipedia_article_in', __( 'in', 'shiro-admin' ) );
	$or_label = get_theme_mod( 'wikipedia_article_or', __( 'or', 'shiro-admin' ) );
	$no_data_label = get_theme_mod( 'wikipedia_article_no_data', __( 'There is not data for the options you selected. Please change the options above.', 'shiro-admin' ) );
	// "views" and "edits" needs to be same as the inout values below
	$atts['views_label'] = get_theme_mod( 'wikipedia_article_views', __( 'views', 'shiro-admin' ) );
	$atts['edits_label'] = get_theme_mod( 'wikipedia_article_edits', __( 'edits', 'shiro-admin' ) );

	wp_enqueue_script( 'd3', get_template_directory_uri() . '/assets/src/datavisjs/libraries/d3.min.js', array( ), '0.0.1', true );
	wp_enqueue_script( 'top-data', get_template_directory_uri() . '/assets/dist/shortcode-top.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'top-data', "var topAtts = " . wp_json_encode($atts) . ";");

	ob_start();
	?>

	<div id="<?php echo esc_attr( $atts['id'] ) ?>" class="top-data mw-980 mod-margin-bottom <?php echo esc_attr($atts['class']) ?>">
		<div>
			<div class="mod-margin-bottom_xs">
				<?php echo wp_kses_post( $content ) ?>
			</div>
			<div class="mod-margin-bottom_xs data-options">
				<p>
					<span>
						<input type="radio" id="views-radio" name="most" value="views" checked>
						<label for="views-radio"><?php echo esc_html( $most_viewed_label ) ?></label>
					</span>
					<span class="p"><?php echo esc_html( $or_label ) ?></span>
					<span>
						<input type="radio" id="edits-radio" name="most" value="edits">
						<label for="edits-radio"><?php echo esc_html( $most_edited_label ) ?></label>
					</span>
					<span class="p"><?php echo esc_html( $in_label ) ?></span>
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
		<div class="no-data" style="display: none;"><p><?php echo esc_html( $no_data_label ) ?></p></div>
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
 * Define a [wp20_easter_eggs] wrapper shortcode that creates a HTML wrapper for easter eggs and initializes js
 * which searches for <strong class="easter-egg"> in .section and .movement.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wp20_easter_eggs_shortcode_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'target_search' => '.section strong.easter-egg, .movement strong.easter-egg',
	];
	$atts = shortcode_atts( $defaults, $atts, 'wp20_easter_eggs' );
	$content = do_shortcode( $content );
	$content = custom_filter_shortcode_text( $content );

	wp_enqueue_script( 'wp20_easter_eggs', get_template_directory_uri() . '/assets/dist/shortcode-easter-eggs.min.js', array( 'jquery' ), '0.0.1', true );
	wp_add_inline_script( 'wp20_easter_eggs', "var eggsAtts = " . wp_json_encode($atts) . ";");

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
	$content = custom_filter_shortcode_text( $content );

	ob_start();
	?>

	<div class="easter-egg-content">
		<?php echo wp_kses_post( $content ); ?>
	</div>

	<?php
	return (string) ob_get_clean();

}
add_shortcode( 'egg', 'egg_shortcode_callback' );

/*
 * Utility function to deal with the way
 * WordPress auto formats text in a shortcode.
 */
function custom_filter_shortcode_text($text = "") {
	// Replace all the poorly formatted P tags that WP adds by default.
	$tags = array("<p>", "</p>");
	$text = str_replace($tags, "\n", $text);

	// Remove any BR tags
	$tags = array("<br>", "<br/>", "<br />");
	$text = str_replace($tags, "", $text);

	// Add back in the P and BR tags again, remove empty ones
	return apply_filters("the_content", $text);
}
