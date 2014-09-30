<?php

use Cms\Core\Di\Factory,
    Cms\Core\Di\Config,
    Cms\Core\Di\Container;

abstract class ContainerBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @return void
     */
    protected function setUp()
    {
        $config = new Config('test-config.ini');
        $factory = new Factory();
        $this->container = $factory->buildContainer($config);
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        unset($this->container);
    }
}