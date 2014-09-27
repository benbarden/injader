<?php

use Cms\Core\Di\Factory,
    Cms\Core\Di\Config,
    Cms\Core\Di\Container;

class TestContainer extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $config = new Config('test-config.ini');
        $factory = new Factory();
        $this->container = $factory->buildContainer($config);
    }

    public function tearDown()
    {
        unset($this->container);
    }

    public function testGetTemplateVarHeadTitle()
    {
        $varExpected = 'Injader Test Site';
        $themeBinding = $this->container->getService('Theme.Binding');
        $themeBinding->set('Head.Title', $varExpected);
        $varActual = $themeBinding->get('Head.Title');
        $this->assertEquals($varExpected, $varActual);
    }

    public function testThemeEngineRender()
    {
        $varExpected = '<p>Hello Ben!</p>';
        $themeEngine = $this->container->getService('Theme.EngineUT');
        $varActual = $themeEngine->render('index.html', array('Name' => 'Ben'));
        $this->assertEquals($varExpected, $varActual);
    }
} 