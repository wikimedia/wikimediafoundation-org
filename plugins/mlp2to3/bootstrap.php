<?php

use Dhii\Di\ContainerAwareCachingContainer;
use Dhii\Cache\MemoryMemoizer;
use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;

/**
 * The function that bootstraps the application.
 *
 * @param array $defaults Default configuration for the app.
 * See services factory definition for required data.
 *
 * @return HandlerInterface
 */
return function (array $defaults) {
    $appRootDir = dirname($defaults['base_path']);

    if (file_exists($autoload = "$appRootDir/vendor/autoload.php")) {
        require_once($autoload);
    }

    $servicesFactory = require_once("$appRootDir/includes/services.php");
    $c = new ContainerAwareCachingContainer(
        $servicesFactory($defaults),
        new MemoryMemoizer()
    );

    $adminDir = $c->get('admin_dir');
    require_once "$adminDir/includes/upgrade.php";

    $handler = $c->get('handler_main');
    assert($handler instanceof HandlerInterface);

    return $handler;
};
