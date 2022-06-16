<?php # -*- coding: utf-8 -*-

(static function () {
    $vendor = dirname(__DIR__, 2) . '/vendor/';
    $autoloadPath = $vendor . 'autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception("The autoload file({$autoloadPath}) doesn't exist");
    }

    require_once $vendor . 'brain/monkey/inc/patchwork-loader.php';
    require_once $autoloadPath;

    require_once __DIR__ . '/stubs.php';
})();
