<?php
/**
 * Plugin Name: Simple Editorial Comments
 * Description: Provide a block where editors can leave feedback to writers which will not render on the frontend.
 * Author: Human Made Limited
 * Author URI: https://humanmade.com
 * Version: 0.1.0
 */

namespace Simple_Editorial_Comments;

require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/blocks.php';

Assets\bootstrap();
Blocks\bootstrap();
