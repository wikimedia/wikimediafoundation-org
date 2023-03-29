<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita01a9bccf71b9b420b77a658e4eb9047
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPCompatibility\\' => 17,
        ),
        'D' => 
        array (
            'Dealerdirect\\Composer\\Plugin\\Installers\\PHPCodeSniffer\\' => 55,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPCompatibility\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpcompatibility/php-compatibility/PHPCompatibility',
        ),
        'Dealerdirect\\Composer\\Plugin\\Installers\\PHPCodeSniffer\\' => 
        array (
            0 => __DIR__ . '/..' . '/dealerdirect/phpcodesniffer-composer-installer/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita01a9bccf71b9b420b77a658e4eb9047::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita01a9bccf71b9b420b77a658e4eb9047::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita01a9bccf71b9b420b77a658e4eb9047::$classMap;

        }, null, ClassLoader::class);
    }
}
