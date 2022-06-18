<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit47f1412b5d94bfec1930bc5e8361e78d
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit47f1412b5d94bfec1930bc5e8361e78d', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit47f1412b5d94bfec1930bc5e8361e78d', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit47f1412b5d94bfec1930bc5e8361e78d::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
