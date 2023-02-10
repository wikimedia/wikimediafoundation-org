<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit13f9eea4c51ed974b05f7fa9fbc092ca
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit13f9eea4c51ed974b05f7fa9fbc092ca::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit13f9eea4c51ed974b05f7fa9fbc092ca::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit13f9eea4c51ed974b05f7fa9fbc092ca::$classMap;

        }, null, ClassLoader::class);
    }
}
