<?php

if ((!$loader = @include __DIR__.'/../vendor/autoload.php')) {
    die('You must set up the project dependencies, run the following command:'.PHP_EOL.PHP_EOL.
        '    php composer.phar install'.PHP_EOL);
}

$loader->add('TwigGenerator\\Tests', 'tests');
