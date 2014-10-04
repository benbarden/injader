<?php

class TestOptimiseUrl extends \PHPUnit_Framework_TestCase
{
    private $optimiser;
    public function setUp()
    {
        $this->optimiser = new \Cms\Ia\Tools\OptimiseUrl();
    }
    public function tearDown()
    {
        unset($this->optimiser);
    }
    public function testSimpleLink()
    {
        $input = 'Hello World';
        $expected = 'hello-world';
        $this->assertEquals($expected, $this->optimiser->optimise($input));
    }
    public function testNumbers()
    {
        $input = 'Hello World 237492347';
        $expected = 'hello-world-237492347';
        $this->assertEquals($expected, $this->optimiser->optimise($input));
    }
    public function testSpaces()
    {
        $input = 'Hello World XX      ??';
        $expected = 'hello-world-xx';
        $this->assertEquals($expected, $this->optimiser->optimise($input));
    }
    public function testSymbols()
    {
        $input = "No No No... IT IS NOT GOOD";
        $expected = 'no-no-no-it-is-not-good';
        $this->assertEquals($expected, $this->optimiser->optimise($input));
    }
}