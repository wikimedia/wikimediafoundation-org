<?php
/**
 * Main file for including shortcodes.
 *
 * @package WMF
 */

namespace WMF\Shortcodes;

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/facts-block.php';
require_once __DIR__ . '/featured-posts.php';
require_once __DIR__ . '/focus-blocks.php';
require_once __DIR__ . '/framing-copy.php';
require_once __DIR__ . '/list.php';
require_once __DIR__ . '/profiles.php';
require_once __DIR__ . '/projects.php';
require_once __DIR__ . '/support.php';
require_once __DIR__ . '/translation-bar.php';

/**
 * Bootstrap.
 */
function init() {
	Connect\init();
	Facts_Block\init();
	Featured_Posts\init();
	Focus\init();
	Framing\init();
	Lists\init();
	Profiles\init();
	Projects\init();
	Support\init();
	Translation_Bar\init();
}
