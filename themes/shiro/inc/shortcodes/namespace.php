<?php
/**
 * Main file for including shortcodes.
 *
 * @package WMF
 */

namespace WMF\Shortcodes;

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/donate-cta.php';
require_once __DIR__ . '/facts-block.php';
require_once __DIR__ . '/featured-posts.php';
require_once __DIR__ . '/focus-blocks.php';
require_once __DIR__ . '/framing-copy.php';
require_once __DIR__ . '/grid.php';
require_once __DIR__ . '/intro-button.php';
require_once __DIR__ . '/list.php';
require_once __DIR__ . '/page-cta.php';
require_once __DIR__ . '/page-intro.php';
require_once __DIR__ . '/profiles.php';
require_once __DIR__ . '/projects.php';
require_once __DIR__ . '/related-pages.php';
require_once __DIR__ . '/stories.php';
require_once __DIR__ . '/support.php';
require_once __DIR__ . '/translation-bar.php';

/**
 * Bootstrap.
 */
function init() {
	Connect\init();
	Donate_CTA\init();
	Facts_Block\init();
	Featured_Posts\init();
	Focus\init();
	Framing\init();
	Grid\init();
	Intro_Button\init();
	Lists\init();
	Page_CTA\init();
	Page_Intro\init();
	Profiles\init();
	Projects\init();
	Related_Pages\init();
	Stories\init();
	Support\init();
	Translation_Bar\init();
}
