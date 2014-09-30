<?php

class TestBinding extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $themeBinding = new \Cms\Theme\Binding;
        $themeBinding->set('abc', 'xyz');
        $this->assertEquals('xyz', $themeBinding->get('abc'));
    }
    public function testGetNull()
    {
        $themeBinding = new \Cms\Theme\Binding;
        $this->assertEquals(null, $themeBinding->get('abc'));
    }
}