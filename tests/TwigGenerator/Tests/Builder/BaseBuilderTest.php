<?php

namespace TwigGenerator\Tests\Builder;

use TwigGenerator\Tests\Builder\Fixtures\Builder\DemoBuilder;

class BaseBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSimpleClassName()
    {
        $builder = new DemoBuilder();
        $this->assertEquals('DemoBuilder', $builder->getSimpleClassName(), 'getSimpleClassName remove the namespaced part of get_class');

        $this->assertEquals('Bar', $builder->getSimpleClassName('\\Foo\\Bar'), 'getSimpleClassName remove the namespaced part of get_class');
    }

    public function testGetDefaultTemplateName()
    {
        $builder = new DemoBuilder();
        $this->assertEquals('DemoBuilder.php.twig', $builder->getDefaultTemplateName(), 'getDefaultTemplateName return the twig file path');
    }

    public function testSetVariables()
    {
        $builder = new DemoBuilder();
        $builder->setVariables(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $builder->getVariables(), 'setVariables accept an array');
    }

    public function testGetVariable()
    {
        $builder = new DemoBuilder();
        $builder->setVariables(array('foo' => 'bar'));
        $this->assertEquals('bar', $builder->getVariable('foo','default'));
        $this->assertEquals('default', $builder->getVariable('nonexistant','default'));
    }

    public function testSetVariable()
    {
        $builder = new DemoBuilder();
        $builder->setVariables(array('foo' => 'bar'));

        $builder->setVariable('foo', 'demo');
        $this->assertEquals('demo', $builder->getVariable('foo'), 'setVariable overwrite the default variables setted');

        $builder->setVariable('add', 'bar');
        $this->assertEquals('bar', $builder->getVariable('add'), 'setVariable can also add a variable');
    }

    public function testHasVariable()
    {
        $builder = new DemoBuilder();
        $builder->setVariables(array('foo' => 'bar'));
        $this->assertTrue($builder->hasVariable('foo'), 'hasVariable return true on a valid key');
        $this->assertFalse($builder->hasVariable('var'), 'hasVariable return false on a invalid key');
    }

    public function testGetCode()
    {
        $builder = $this->initBuilder();

        $this->assertEquals('Hello cedric !', $builder->getCode());

        $builder->setVariables(array('name' => 'Tux'));
        $this->assertEquals('Hello Tux !', $builder->getCode(), 'If i change variables code is changed');
    }

    public function testWriteOnDisk()
    {
        $builder = $this->initBuilder();

        $builder->writeOnDisk(sys_get_temp_dir());
        $this->assertTrue(file_exists(sys_get_temp_dir() . '/test.php'));
        $this->assertEquals('Hello cedric !', file_get_contents(sys_get_temp_dir() . '/test.php'));

        $builder->setVariables(array('name' => 'Tux'));
        $builder->writeOnDisk(sys_get_temp_dir());
        $this->assertTrue($builder->mustOverwriteIfExists());
        $this->assertTrue(file_exists(sys_get_temp_dir() . '/test.php'));
        $this->assertEquals('Hello Tux !', file_get_contents(sys_get_temp_dir() . '/test.php'), 'If i change variables code is changed');

        $builder->setVariables(array('name' => 'cedric'));
        $builder->setMustOverwriteIfExists(false);
        $builder->writeOnDisk(sys_get_temp_dir());
        $this->assertFalse($builder->mustOverwriteIfExists());
        $this->assertTrue(file_exists(sys_get_temp_dir() . '/test.php'));
        $this->assertEquals('Hello Tux !', file_get_contents(sys_get_temp_dir() . '/test.php'), 'If i change variables on an existant files code is not generated');

        unlink(sys_get_temp_dir() . '/test.php');
        $this->assertFalse(file_exists(sys_get_temp_dir() . '/test.php'));
        $builder->writeOnDisk(sys_get_temp_dir());
        $this->assertEquals('Hello cedric !', file_get_contents(sys_get_temp_dir() . '/test.php'), 'If i change variables on a non existant files code is generated');
    }

    protected function initBuilder()
    {
        $builder = new DemoBuilder();
        $generator = $this->getMockBuilder('TwigGenerator\Builder\Generator')
                          ->disableOriginalConstructor()
                          ->getMock();

        $builder->setGenerator($generator);
        $builder->setMustOverwriteIfExists(true);
        $builder->setOutputName('test.php');
        $builder->setTemplateDirs(array(__DIR__.'/Fixtures/Templates'));
        $builder->setVariables(array('name' => 'cedric'));
        $builder->setTemplateName($builder->getDefaultTemplateName());

        return $builder;
    }
}
