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
				<div class="story-nav">
					<span class="btn btn-blue next-story">Next story</span>
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

	if ( $atts['img'] !== '' ) {
		$attachment = get_page_by_title($atts['img'], OBJECT, 'attachment');
		$img_id = $attachment->ID;
		$image_url = wp_get_attachment_image_url($img_id, array(400, 400));
	}

	ob_start();
	?>
	<div class="story-content" style="display: none;">
		<h2><?php echo esc_html( $atts['name'] ); ?></h2>
		<?php if ( isset($image_url) ) { ?>
			<div class="story-image" style="background-image: url(<?php echo $image_url ?>);"></div>
		<?php } ?>

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
				<p><?php echo wp_kses_post( $content ) ?></p>
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
	$wp_h1_style = 'style="font-family: Linux Libertine, Charis SIL, serif;"';

	if ( $atts['img'] !== '' ) {
		$attachment = get_page_by_title($atts['img'], OBJECT, 'attachment');
		$img_id = $attachment->ID;
		$image = wp_get_attachment_image($img_id, array(600, 400));
	}

	if ( $atts['columns'] === '1' ) {
		$o = '<div class="mw-980' . $margin . '"><h1 ' . $wp_h1_style . '>' . esc_html($atts['title']) . '</h1><p>' . wp_kses_post( $content ) . '</p></div>';
		return $o;
	} else {
		if ( empty($image) ) {
			$col_1 = '<div class="w-48p"><h1 ' . $wp_h1_style . '>' . esc_html($atts['title']) . '</h1></div>';
			$col_2 = '<div class="w-48p"><p>' . wp_kses_post( $content ) . '</p></div>';
		} else {
			$col_1 = '<div class="w-48p"><h1 ' . $wp_h1_style . '>' . esc_html($atts['title']) . '</h1><p>' . wp_kses_post( $content ) . '</p></div>';
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
 * Define a [movement] wrapper shortcode that renders Wikimedia projects and affiliates.
 *
 * @param array $atts Shortcode attributes array.
 * @return string Rendered shortcode output.
 */
function wmf_movement_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'movement' );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	ob_start();
	?>

	<div class="movement mod-margin-bottom">
		<div class="mw-980">
			<div class="w-68p">
				<h1 style="font-family: Linux Libertine, Charis SIL, serif;"><?php echo esc_html( $atts['title'] ); ?></h1>
				<p><?php echo wp_kses_post( $content ); ?></p>
			</div>
		</div>
		<div class="svg-projects">
			<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 770 345" width="770">
				<text x="520" y="70">
					<tspan x="520">13</tspan>
					<tspan x="520" dy="1.2em">Wikimedia</tspan>
					<tspan x="520" dy="1.2em">Projects</tspan>
				</text>
				<circle cx="111.251" cy="65.191" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="177.158" cy="149.222" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="252.127" cy="92.378" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="299.909" cy="184.647" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="388.883" cy="141.808" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="421.836" cy="213.481" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="544.665" cy="235.803" r="24.303" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="349.339" cy="279.388" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="442.432" cy="299.984" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="17.334" cy="16.585" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="713.473" cy="117.093" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="664.043" cy="219.248" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><circle cx="728.928" cy="318.928" r="16.065" fill="#fff" stroke="#000" stroke-width=".824"/><path d="M520.952 206.734a.413.413 0 00.439-.383l.25-3.699a.413.413 0 00-.822-.055l-.223 3.287-3.287-.222a.412.412 0 00-.056.822l3.699.25zm.338-.682c-12.728-14.575-20.811-31.998-21.679-46.969-.434-7.48.933-14.329 4.399-19.91 3.462-5.576 9.038-9.922 17.089-12.368l-.239-.788c-8.22 2.497-13.971 6.956-17.55 12.721-3.576 5.759-4.963 12.787-4.522 20.393.882 15.202 9.07 32.792 21.881 47.463l.621-.542z" fill="#202122"/><path d="M281.29 249.262l-2.246-12.78c-.089-.506-.586-.838-1.116-.745l-13.07 2.296c-2.112.372-3.538 2.308-3.183 4.324l2.138 12.171c.354 2.016 2.355 3.35 4.467 2.979l13.069-2.296c.53-.093.885-.575.796-1.081l-.107-.608a.917.917 0 00-.479-.649c-.271-.557-.564-2.226-.5-2.842a.903.903 0 00.231-.769zm-14.261-6.356a.238.238 0 01.199-.27l8.447-1.484a.237.237 0 01.279.186l.134.761a.239.239 0 01-.199.27l-8.447 1.484a.239.239 0 01-.28-.186l-.133-.761zm.427 2.434a.239.239 0 01.199-.27l8.448-1.484a.238.238 0 01.279.186l.134.761a.239.239 0 01-.199.27l-8.448 1.484a.238.238 0 01-.279-.186l-.134-.761zm11.768 7.735l-11.372 1.998c-.705.124-1.371-.32-1.489-.993-.118-.67.36-1.318 1.061-1.441l11.372-1.998c.039.663.238 1.797.428 2.434zM196.662 42.418l-14.28 3.595c-.91.23-1.851-.39-2.101-1.383l-2.715-10.785c-.25-.993.285-1.983 1.196-2.212l14.28-3.595c.91-.23 1.85.39 2.1 1.383l2.715 10.785c.25.993-.285 1.983-1.195 2.212zm-15.176-9.241c-1.061.267-1.686 1.423-1.394 2.58.292 1.159 1.389 1.881 2.45 1.614 1.062-.267 1.686-1.423 1.395-2.581-.292-1.158-1.389-1.88-2.451-1.613zm.841 10.301l13.182-3.318-1.055-4.195-3.83-2.52a.4.4 0 00-.582.146l-3.375 6.246-2.429-1.6a.4.4 0 00-.582.147l-1.781 3.297.452 1.797zM592.044 327.207l-2.11 3.897-3.155.598c-.41.078-.665.549-.569 1.052l1.037 5.463c.095.503.505.848.914.77l3.156-.599 3.391 2.853c.572.482 1.298-.071 1.144-.885l-2.419-12.746c-.155-.814-1.033-1.061-1.389-.403zm6.845-3.307c-.398-.213-.84-.007-.986.46-.146.467.058 1.018.456 1.231 2.362 1.261 4.111 3.803 4.68 6.799.569 2.997-.128 6.002-1.863 8.041-.293.343-.281.932.026 1.312.295.365.783.424 1.086.067 2.093-2.459 2.933-6.087 2.247-9.704-.687-3.617-2.798-6.685-5.646-8.206zm2.678 8.769c-.458-2.41-1.869-4.438-3.777-5.424-.397-.206-.832.007-.97.477-.138.469.072 1.016.47 1.222 1.411.73 2.455 2.228 2.793 4.007.338 1.779-.085 3.555-1.131 4.751-.295.337-.29.923.011 1.309.276.355.762.451 1.078.089 1.413-1.616 1.984-4.02 1.526-6.431zm-4.936-2.085c-.404-.172-.825.072-.94.55-.114.478.122 1.007.527 1.182.45.193.794.653.897 1.198.104.545-.048 1.099-.396 1.443-.312.312-.338.89-.057 1.293.283.405.764.476 1.076.167.761-.755 1.091-1.976.861-3.185-.229-1.21-.983-2.224-1.968-2.648zM765.819 185.665l-2.766-.61.532-2.414c.294-1.331 1.524-2.194 2.745-1.925l.276.06c.46.102.919-.22 1.029-.722l.399-1.81c.111-.501-.17-.987-.63-1.088l-.276-.061c-3.056-.673-6.127 1.481-6.861 4.815l-1.995 9.051c-.221.999.344 1.974 1.26 2.176l4.425.975c.916.202 1.838-.445 2.058-1.444l1.064-4.828c.22-.999-.344-1.974-1.26-2.175zm-9.956-2.195l-2.766-.61.532-2.413c.294-1.331 1.524-2.195 2.745-1.926l.276.061c.46.101.919-.221 1.029-.722l.399-1.81c.111-.502-.17-.987-.63-1.088l-.276-.061c-3.056-.674-6.127 1.481-6.861 4.814l-1.995 9.051c-.221 1 .344 1.974 1.26 2.176l4.425.975c.916.202 1.838-.445 2.058-1.444l1.064-4.827c.22-.999-.344-1.974-1.26-2.176zM338.333 88.074l-4.442-.892c-.92-.184-1.83.48-2.031 1.483l-.973 4.846c-.202 1.004.381 1.968 1.301 2.152l2.776.558-.486 2.423c-.269 1.336-1.483 2.223-2.708 1.977l-.278-.056c-.461-.093-.914.238-1.015.742l-.365 1.817c-.101.504.189.983.65 1.076l.278.056c3.068.616 6.097-1.596 6.769-4.943l1.825-9.087c.201-1.003-.381-1.967-1.301-2.152zm-9.995-2.007l-4.443-.892c-.92-.184-1.829.48-2.031 1.483l-.973 4.846c-.201 1.004.382 1.968 1.301 2.152l2.777.558-.487 2.423c-.268 1.336-1.482 2.223-2.708 1.977l-.277-.056c-.462-.093-.914.238-1.016.742l-.365 1.817c-.101.504.189.983.651 1.076l.278.056c3.068.616 6.097-1.596 6.769-4.943l1.825-9.087c.201-1.003-.382-1.967-1.301-2.152zM22.455 75.328c-2.1-.621-4.42.964-5.182 3.54-.617 2.084.744 6.616 1.288 8.276.11.337.455.439.73.216 1.36-1.096 4.969-4.158 5.585-6.241.763-2.577-.321-5.17-2.421-5.79zm-1.84 6.221c-.7-.207-1.062-1.071-.808-1.93.254-.86 1.028-1.388 1.728-1.18.7.207 1.06 1.07.807 1.93-.255.859-1.028 1.387-1.728 1.18zm-8.609-.616a1.077 1.077 0 00-.57.305 1.442 1.442 0 00-.363.615l-2.743 9.27c-.124.419.136.807.5.746l4.854-1.094 2.554-8.631c-.092-.671-.14-1.312-.133-1.909l-4.099.698zm6.508 7.714c-.425-.126-.759-.474-.916-.954-.339-1.035-.68-2.2-.954-3.373l-1.995 6.74 5.092 4.084 2.696-9.109a40.837 40.837 0 01-2.636 2.31c-.393.317-.863.427-1.287.302zm10.203-4.976l-4.854 1.095-3.156 10.664 4.833-.823c.205-.034.404-.14.57-.305.166-.164.293-.378.363-.615l2.743-9.27c.124-.419-.136-.807-.5-.746z" fill="#000"/></svg>
		</div>
		<div class="svg-user-groups">
			<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 438 1046" width="438">
				<text x="190" y="720">
					<tspan x="190">135</tspan>
					<tspan x="190" dy="1.2em">Wikimedia User</tspan>
					<tspan x="190" dy="1.2em">Groups</tspan>
				</text>
				<text x="140" y="950">
					<tspan x="140">2</tspan>
					<tspan x="140" dy="1.2em">Wikimedia Thematic</tspan>
					<tspan x="140" dy="1.2em">Organizations</tspan>
				</text>
				<circle cx="139.149" cy="879.071" r="7.5" transform="rotate(103.783 139.149 879.071)" fill="#C8CCD1"/><circle cx="88.071" cy="904.61" r="7.5" transform="rotate(103.783 88.07 904.61)" fill="#777"/><circle cx="384.702" cy="657.137" r="2.5" fill="#CCC"/><circle cx="169.215" cy="177.447" r="2.5" fill="#777"/><circle cx="377.288" cy="914.173" r="2.5" fill="#777"/><circle cx="239.807" cy="103.917" r="2.5" fill="#777"/><circle cx="391.411" cy="335.082" r="2.5" fill="#777"/><circle cx="244.271" cy="218.917" r="2.5" fill="#777"/><circle cx="382.791" cy="307.272" r="2.5" fill="#777"/><circle cx="257.795" cy="244.7" r="2.5" fill="#777"/><circle cx="382.319" cy="279.841" r="2.5" fill="#777"/><circle cx="263.239" cy="271.59" r="2.5" fill="#DDD"/><circle cx="333.076" cy="197.655" r="2.5" fill="#DDD"/><circle cx="326.581" cy="343.473" r="2.5" fill="#777"/><circle cx="304.242" cy="77.93" r="2.5" fill="#777"/><circle cx="376.668" cy="455.976" r="2.5" fill="#DDD"/><circle cx="314.741" cy="153.064" r="2.5" fill="#222"/><circle cx="352.706" cy="383.995" r="2.5" fill="#222"/><circle cx="348.073" cy="126.04" r="2.5" fill="#CCC"/><circle cx="314.048" cy="865.629" r="2.5" fill="#777"/><circle cx="378.076" cy="95.362" r="2.5" fill="#222"/><circle cx="300.896" cy="452.234" r="2.5" fill="#DDD"/><circle cx="386.109" cy="248.527" r="2.5" fill="#EEE"/><circle cx="265.195" cy="303.072" r="2.5" fill="#EEE"/><circle cx="394.818" cy="48.905" r="2.5" fill="#222"/><circle cx="292.865" cy="500.959" r="2.5" fill="#222"/><circle cx="369.952" cy="219.783" r="2.5" fill="#CCC"/><circle cx="286.301" cy="328.406" r="2.5" fill="#CCC"/><circle cx="290.807" cy="3.242" r="2.5" fill="#CCC"/><circle cx="403.436" cy="526.984" r="2.5" fill="#777"/><circle cx="345.045" cy="40.757" r="2.5" fill="#777"/><circle cx="343.29" cy="499.938" r="2.5" fill="#EEE"/><circle cx="312.205" cy="633.246" r="2.5" fill="#DDD"/><circle cx="194.042" cy="410.413" r="2.5" fill="#DDD"/><circle cx="372.281" cy="169.739" r="2.5" fill="#CCC"/><circle cx="293.094" cy="378.042" r="2.5" fill="#EEE"/><circle cx="422.598" cy="116.096" r="2.5" fill="#DDD"/><circle cx="253.35" cy="439.926" r="2.5" fill="#DDD"/><circle cx="343.51" cy="664.551" r="2.5" fill="#222"/><circle cx="348.452" cy="266.984" r="2.5" fill="#222"/><circle cx="341.938" cy="245.304" r="2.5" fill="#DDD"/><circle cx="309.217" cy="298.224" r="2.5" fill="#DDD"/><circle cx="333.276" cy="281.058" r="2.5" fill="#CCC"/><circle cx="290.157" cy="962.841" r="2.5" fill="#222"/><circle cx="400.355" cy="589.583" r="2.5" fill="#EEE"/><circle cx="182.12" cy="224.482" r="2.5" fill="#222"/><circle cx="359.163" cy="666.199" r="2.5" fill="#DDD"/><circle cx="116.032" cy="227.059" r="2.5" fill="#CCC"/><circle cx="435.159" cy="410.015" r="2.5" fill="#EEE"/><circle cx="187.648" cy="153.17" r="2.5" fill="#DDD"/><circle cx="430.803" cy="378.321" r="2.5" fill="#222"/><circle cx="197.684" cy="183.546" r="2.5" fill="#222"/><circle cx="426.447" cy="346.627" r="2.5" fill="#DDD"/><circle cx="207.721" cy="213.923" r="2.5" fill="#DDD"/><circle cx="430.345" cy="312.985" r="2.5" fill="#CCC"/><circle cx="209.994" cy="247.714" r="2.5" fill="#222"/><circle cx="417.734" cy="283.239" r="2.5" fill="#222"/><circle cx="227.795" cy="274.676" r="2.5" fill="#EEE"/><circle cx="307.262" cy="714.805" r="2.5" fill="#777"/><circle cx="160.796" cy="292.077" r="2.5" fill="#222"/><circle cx="365.754" cy="721.396" r="2.5" fill="#222"/><circle cx="201.732" cy="307.642" r="2.5" fill="#EEE"/><circle cx="309.122" cy="236.196" r="2.5" fill="#777"/><circle cx="343.142" cy="301.225" r="2.5" fill="#DDD"/><circle cx="233.638" cy="179.031" r="2.5" fill="#777"/><circle cx="387.369" cy="753.588" r="2.5" fill="#CCC"/><circle cx="292.221" cy="185.84" r="2.5" fill="#222"/><circle cx="287.685" cy="906.821" r="2.5" fill="#222"/><circle cx="279.692" cy="129.272" r="2.5" fill="#DDD"/><circle cx="391.491" cy="401.03" r="2.5" fill="#777"/><circle cx="413.377" cy="251.545" r="2.5" fill="#777"/><circle cx="237.832" cy="305.053" r="2.5" fill="#777"/><circle cx="280.075" cy="637.365" r="2.5" fill="#DDD"/><circle cx="322.286" cy="1039.46" r="2.5" fill="#222"/><circle cx="420.375" cy="213.965" r="2.5" fill="#222"/><circle cx="237.771" cy="343.279" r="2.5" fill="#DDD"/><circle cx="404.665" cy="188.158" r="2.5" fill="#CCC"/><circle cx="257.905" cy="365.807" r="2.5" fill="#CCC"/><circle cx="430.242" cy="164.855" r="2.5" fill="#222"/><circle cx="236.982" cy="393.365" r="2.5" fill="#CCC"/><circle cx="344.646" cy="545.121" r="2.5" fill="#222"/><circle cx="56.169" cy="333.081" r="2.5" fill="#EEE"/><circle cx="356.512" cy="603.407" r="2.5" fill="#CCC"/><circle cx="126.761" cy="259.552" r="2.5" fill="#CCC"/><circle cx="278.365" cy="490.717" r="2.5" fill="#DDD"/><circle cx="131.225" cy="374.551" r="2.5" fill="#DDD"/><circle cx="269.745" cy="462.907" r="2.5" fill="#CCC"/><circle cx="144.749" cy="400.335" r="2.5" fill="#222"/><circle cx="269.273" cy="435.476" r="2.5" fill="#222"/><circle cx="150.193" cy="427.224" r="2.5" fill="#DDD"/><circle cx="373.992" cy="698.329" r="2.5" fill="#DDD"/><circle cx="213.535" cy="499.107" r="2.5" fill="#CCC"/><circle cx="354.22" cy="793.893" r="2.5" fill="#222"/><circle cx="263.622" cy="611.61" r="2.5" fill="#DDD"/><circle cx="201.695" cy="308.698" r="2.5" fill="#222"/><circle cx="239.66" cy="539.629" r="2.5" fill="#777"/><circle cx="235.027" cy="281.674" r="2.5" fill="#777"/><circle cx="211.786" cy="572.254" r="2.5" fill="#777"/><circle cx="268.542" cy="834.261" r="2.5" fill="#DDD"/><circle cx="187.85" cy="607.868" r="2.5" fill="#DDD"/><circle cx="273.063" cy="404.161" r="2.5" fill="#DDD"/><circle cx="152.149" cy="458.707" r="2.5" fill="#DDD"/><circle cx="281.772" cy="204.539" r="2.5" fill="#222"/><circle cx="179.819" cy="656.593" r="2.5" fill="#CCC"/><circle cx="324.562" cy="815.313" r="2.5" fill="#222"/><circle cx="173.255" cy="484.041" r="2.5" fill="#222"/><circle cx="214.992" cy="937.24" r="2.5" fill="#777"/><circle cx="290.391" cy="682.619" r="2.5" fill="#DDD"/><circle cx="231.999" cy="196.391" r="2.5" fill="#EEE"/><circle cx="230.244" cy="655.572" r="2.5" fill="#777"/><circle cx="362.518" cy="311.519" r="2.5" fill="#DDD"/><circle cx="80.996" cy="566.047" r="2.5" fill="#777"/><circle cx="259.236" cy="325.373" r="2.5" fill="#777"/><circle cx="180.048" cy="533.676" r="2.5" fill="#DDD"/><circle cx="309.552" cy="271.731" r="2.5" fill="#CCC"/><circle cx="140.304" cy="595.56" r="2.5" fill="#EEE"/><circle cx="184.64" cy="424.538" r="2.5" fill="#222"/><circle cx="235.406" cy="422.618" r="2.5" fill="#DDD"/><circle cx="284.194" cy="751.054" r="2.5" fill="#222"/><circle cx="196.171" cy="453.858" r="2.5" fill="#EEE"/><circle cx="220.23" cy="436.692" r="2.5" fill="#777"/><circle cx="253.084" cy="841.738" r="2.5" fill="#CCC"/><circle cx="340.494" cy="496.525" r="2.5" fill="#DDD"/><circle cx="69.074" cy="380.117" r="2.5" fill="#777"/><circle cx="361.635" cy="856.505" r="2.5" fill="#CCC"/><circle cx="2.986" cy="382.693" r="2.5" fill="#EEE"/><circle cx="322.114" cy="565.649" r="2.5" fill="#DDD"/><circle cx="74.602" cy="308.804" r="2.5" fill="#777"/><circle cx="317.757" cy="533.955" r="2.5" fill="#DDD"/><circle cx="84.639" cy="339.181" r="2.5" fill="#777"/><circle cx="313.401" cy="502.261" r="2.5" fill="#777"/><circle cx="94.675" cy="369.557" r="2.5" fill="#DDD"/><circle cx="317.299" cy="468.619" r="2.5" fill="#CCC"/><circle cx="96.948" cy="403.349" r="2.5" fill="#DDD"/><circle cx="304.688" cy="438.874" r="2.5" fill="#777"/><circle cx="114.749" cy="430.311" r="2.5" fill="#DDD"/><circle cx="373.732" cy="433.923" r="2.5" fill="#222"/><circle cx="47.75" cy="447.711" r="2.5" fill="#EEE"/><circle cx="336.301" cy="411.187" r="2.5" fill="#CCC"/><circle cx="88.686" cy="463.276" r="2.5" fill="#222"/><circle cx="196.076" cy="391.83" r="2.5" fill="#CCC"/><circle cx="230.096" cy="456.859" r="2.5" fill="#DDD"/><circle cx="120.592" cy="334.665" r="2.5" fill="#777"/><circle cx="355.938" cy="467.523" r="2.5" fill="#EEE"/><circle cx="179.175" cy="341.474" r="2.5" fill="#222"/><circle cx="255.856" cy="503.311" r="2.5" fill="#CCC"/><circle cx="285.842" cy="767.531" r="2.5" fill="#CCC"/><circle cx="278.445" cy="556.664" r="2.5" fill="#222"/><circle cx="328.682" cy="903.463" r="2.5" fill="#777"/><circle cx="124.786" cy="460.688" r="2.5" fill="#222"/><circle cx="331.977" cy="725.515" r="2.5" fill="#777"/><circle cx="91.907" cy="503.993" r="2.5" fill="#777"/><circle cx="307.329" cy="369.599" r="2.5" fill="#CCC"/><circle cx="124.726" cy="498.914" r="2.5" fill="#EEE"/><circle cx="291.619" cy="343.792" r="2.5" fill="#DDD"/><circle cx="144.86" cy="521.441" r="2.5" fill="#EEE"/><circle cx="317.197" cy="320.489" r="2.5" fill="#777"/><circle cx="123.936" cy="548.999" r="2.5" fill="#EEE"/><path d="M240.517 673.609a.411.411 0 00-.52.262l-1.157 3.523a.412.412 0 00.783.257l1.028-3.131 3.131 1.028a.412.412 0 00.257-.783l-3.522-1.156zm-.497.577c2.782 5.502 4.176 14.598 1.966 22.705-1.102 4.046-3.098 7.831-6.25 10.802-3.149 2.968-7.473 5.144-13.269 5.944l.113.816c5.948-.821 10.436-3.064 13.721-6.161 3.283-3.094 5.346-7.022 6.48-11.184 2.264-8.309.846-17.615-2.025-23.294l-.736.372zM140.088 924.513a.413.413 0 00.425.399l3.706-.12a.411.411 0 10-.027-.823l-3.294.106-.106-3.294a.411.411 0 10-.823.027l.119 3.705zm.713.269c7.643-8.154 8.122-19.342 3.577-29.944l-.757.324c4.457 10.398 3.935 21.21-3.422 29.056l.602.564z" fill="#202122"/></svg>
		</div>
		<div class="svg-chapters">
			<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 658 470" width="658">
				<text x="310" y="260">
					<tspan x="310">39</tspan>
					<tspan x="310" dy="1.2em">Wikimedia</tspan>
					<tspan x="310" dy="1.2em">Chapters</tspan>
				</text>
				<circle cx="290.35" cy="452.473" r="6.179" fill="#EEE"/><circle cx="155.179" cy="395.179" r="6.179" fill="#222"/><circle cx="209.907" cy="422.775" r="6.179" fill="#DDD"/><circle cx="146.104" cy="456.87" r="6.179" fill="#DDD"/><circle cx="420.179" cy="367.179" r="6.179" fill="#DDD"/><circle cx="77.179" cy="289.179" r="6.179" fill="#DDD"/><circle cx="43.332" cy="208.348" r="6.179" fill="#CCC"/><circle cx="99.179" cy="134.179" r="6.179" fill="#EEE"/><circle cx="71.179" cy="102.179" r="6.179" fill="#777"/><circle cx="118.804" cy="200.641" r="6.179" fill="#DDD"/><circle cx="176.552" cy="342.728" r="6.179" fill="#CCC"/><circle cx="87.179" cy="181.179" r="6.179" fill="#777"/><circle cx="420.179" cy="439.179" r="6.179" fill="#CCC"/><circle cx="6.179" cy="142.179" r="6.179" fill="#EEE"/><circle cx="43.179" cy="67.179" r="6.179" fill="#CCC"/><circle cx="261.179" cy="212.179" r="6.179" fill="#EEE"/><circle cx="137.685" cy="272.813" r="6.179" fill="#EEE"/><circle cx="167.661" cy="205.651" r="6.179" fill="#DDD"/><circle cx="99.677" cy="390.939" r="6.179" fill="#CCC"/><circle cx="476.179" cy="383.179" r="6.179" fill="#777"/><circle cx="532.179" cy="403.179" r="6.179" fill="#CCC"/><circle cx="602.179" cy="405.179" r="6.179" fill="#222"/><circle cx="335.458" cy="395.421" r="6.179" fill="#222"/><circle cx="348.179" cy="463.179" r="6.179" fill="#CCC"/><circle cx="553.179" cy="354.179" r="6.179" fill="#EEE"/><circle cx="246.791" cy="422.21" r="6.179" fill="#CCC"/><circle cx="65.104" cy="416.87" r="6.179" fill="#222"/><circle cx="651.179" cy="383.179" r="6.179" fill="#DDD"/><circle cx="278.179" cy="358.179" r="6.179" fill="#EEE"/><circle cx="238.604" cy="328.658" r="6.179" fill="#DDD"/><circle cx="99.772" cy="356.289" r="6.179" fill="#777"/><circle cx="9.73" cy="326.996" r="6.179" fill="#DDD"/><circle cx="20.104" cy="373.87" r="6.179" fill="#CCC"/><circle cx="6.179" cy="253.179" r="6.179" fill="#777"/><circle cx="232.179" cy="375.179" r="6.179" fill="#222"/><circle cx="229.291" cy="288.889" r="6.179" fill="#222"/><circle cx="26.179" cy="6.179" r="6.179" fill="#777"/><circle cx="174.315" cy="279.612" r="6.179" fill="#CCC"/><circle cx="210.179" cy="187.179" r="6.179" fill="#222"/><path d="M281.706 212.976a.413.413 0 00.005.583l2.642 2.6a.412.412 0 10.578-.587l-2.348-2.311 2.311-2.349a.411.411 0 10-.587-.578l-2.601 2.642zm.297.701c9.615-.076 18.781 2.522 24.678 7.023 2.944 2.247 5.059 4.957 6.027 8.031.967 3.068.805 6.537-.869 10.335l.754.332c1.745-3.96 1.934-7.635.901-10.915-1.032-3.273-3.269-6.114-6.313-8.438-6.081-4.642-15.442-7.269-25.184-7.192l.006.824z" fill="#202122"/></svg>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'movement', 'wmf_movement_callback' );