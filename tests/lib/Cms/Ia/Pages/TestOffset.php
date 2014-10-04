<?php

class TestOffset extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Cms\Ia\Pages\Offset
     */
    private $engine;
    public function setUp()
    {
        $this->engine = new \Cms\Ia\Pages\Offset();
    }
    public function tearDown()
    {
        unset($this->engine);
    }
    public function testNotSet()
    {
        $expected = 0;
        $this->assertEquals($expected, $this->engine->calculate());
    }
    public function testPageZero()
    {
        $this->engine->setPageNo(1);
        $this->engine->setPerPage(10);
        $expected = 0;
        $this->assertEquals($expected, $this->engine->calculate());
    }
    public function testPageTwo()
    {
        $this->engine->setPageNo(2);
        $this->engine->setPerPage(5);
        $expected = 5;
        $this->assertEquals($expected, $this->engine->calculate());
    }
    public function testPageTen()
    {
        $this->engine->setPageNo(10);
        $this->engine->setPerPage(2);
        $expected = 18;
        $this->assertEquals($expected, $this->engine->calculate());
    }
}