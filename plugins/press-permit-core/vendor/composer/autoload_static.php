<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit510533fedad4afa88f5557c13743bb8c
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit510533fedad4afa88f5557c13743bb8c::$classMap;

        }, null, ClassLoader::class);
    }
}