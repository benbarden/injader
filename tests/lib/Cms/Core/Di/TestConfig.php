<?php

use Cms\Core\Di\Config;

class TestConfig extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->config = new Config(__DIR__.'/TestConfigDummy.ini');
    }

    public function tearDown()
    {
        unset($this->config);
    }

    public function testGetDatabaseDSN()
    {
        $expected = 'ABC';
        $actual = $this->config->getByKey('Database.DSN');
        $this->assertEquals($expected, $actual);
    }

    public function testGetDatabaseUser()
    {
        $expected = 'DEF';
        $actual = $this->config->getByKey('Database.User');
        $this->assertEquals($expected, $actual);
    }

    public function testGetDatabasePass()
    {
        $expected = 'GHI';
        $actual = $this->config->getByKey('Database.Pass');
        $this->assertEquals($expected, $actual);
    }
}