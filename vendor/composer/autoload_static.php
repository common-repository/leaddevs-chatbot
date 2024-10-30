<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb5a4937f4e03ff5fa2eea27e2e03f800
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Leaddevs\\WPFBMessenger\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Leaddevs\\WPFBMessenger\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb5a4937f4e03ff5fa2eea27e2e03f800::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb5a4937f4e03ff5fa2eea27e2e03f800::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}