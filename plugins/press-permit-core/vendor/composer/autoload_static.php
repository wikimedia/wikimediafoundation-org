<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1363d7ee3d130deac771b2b1940e5d24
{
    public static $files = array (
        '52b96848dd058dbb598947f65241cdd8' => __DIR__ . '/..' . '/publishpress/vendor-locator-permissions/includes.php',
        '41c664bd04a95c2d6a2f2a3e00f06593' => __DIR__ . '/..' . '/publishpress/wordpress-reviews/ReviewsController.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit1363d7ee3d130deac771b2b1940e5d24::$classMap;

        }, null, ClassLoader::class);
    }
}