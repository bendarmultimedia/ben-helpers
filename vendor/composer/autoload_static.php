<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6d70786123469d588cd63598bc6732af
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6d70786123469d588cd63598bc6732af::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6d70786123469d588cd63598bc6732af::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6d70786123469d588cd63598bc6732af::$classMap;

        }, null, ClassLoader::class);
    }
}
