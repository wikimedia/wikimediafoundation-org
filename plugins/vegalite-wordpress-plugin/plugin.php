<?php
/**
 * Plugin Name: Vega Lite WordPress Plugin
 * Description: Provide a block to render graphics using Vega Lite.
 * Author: Human Made and the Wikimedia Foundation
 * Author URI: https://github.com/wikimedia/vegalite-wordpress-plugin/graphs/contributors
 * Version: 0.2.0
 */

namespace Vegalite_Plugin;

require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/blocks.php';
require_once __DIR__ . '/inc/datasets/endpoints.php';
require_once __DIR__ . '/inc/datasets/metadata.php';
require_once __DIR__ . '/inc/datasets/namespace.php';

Assets\bootstrap();
Blocks\bootstrap();
Datasets\bootstrap();
Datasets\Endpoints\bootstrap();
Datasets\Metadata\bootstrap();
