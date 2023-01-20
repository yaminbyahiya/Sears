<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitWiloke6771291f88954ed887baf715029a0a4c
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '25072dd6e2470089de65ae7bf11d3109' => __DIR__ . '/..' . '/symfony/polyfill-php72/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' =>
        array (
            'WilokeHeroSlider\\' => 20,
        ),
        'T' =>
        array (
            'Twig\\' => 5,
            'Timber\\' => 7,
        ),
        'S' =>
        array (
            'Symfony\\Polyfill\\Php72\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'C' =>
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WilokeHeroSlider\\' =>
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Twig\\' =>
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Timber\\' =>
        array (
            0 => __DIR__ . '/..' . '/timber/timber/lib',
        ),
        'Symfony\\Polyfill\\Php72\\' =>
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php72',
        ),
        'Symfony\\Polyfill\\Mbstring\\' =>
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' =>
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Composer\\Installers\\' =>
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/..' . '/twig/cache-extension/lib',
    );

    public static $prefixesPsr0 = array (
        'T' =>
        array (
            'Twig_' =>
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
        'R' =>
        array (
            'Routes' =>
            array (
                0 => __DIR__ . '/..' . '/upstatement/routes',
            ),
        ),
    );

    public static $classMap = array (
        'AltoRouter' => __DIR__ . '/..' . '/altorouter/altorouter/AltoRouter.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitWiloke6771291f88954ed887baf715029a0a4c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitWiloke6771291f88954ed887baf715029a0a4c::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInitWiloke6771291f88954ed887baf715029a0a4c::$fallbackDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitWiloke6771291f88954ed887baf715029a0a4c::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitWiloke6771291f88954ed887baf715029a0a4c::$classMap;

        }, null, ClassLoader::class);
    }
}