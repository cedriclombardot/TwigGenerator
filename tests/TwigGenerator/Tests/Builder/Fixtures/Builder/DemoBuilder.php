<?php

namespace TwigGenerator\Tests\Builder\Fixtures\Builder;

use TwigGenerator\Builder\BaseBuilder;

class DemoBuilder extends BaseBuilder
{

    public function getDefaultTemplateDirs()
    {
        return array(__DIR__.'/../Templates');
    }
}
