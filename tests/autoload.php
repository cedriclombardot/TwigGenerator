<?php

require_once __DIR__.'/../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'TwigGenerator' => array(__DIR__.'/../src', __DIR__),
    'Symfony' => __DIR__.'/../vendor/',
));
$loader->registerPrefixes(array(
    'Twig_' => __DIR__.'/../vendor/twig/lib',
));
$loader->register();
