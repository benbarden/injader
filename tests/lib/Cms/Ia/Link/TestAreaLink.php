<?php

class TestAreaLink extends \PHPUnit_Framework_TestCase
{
    public function testLinkStyle1()
    {
        $expected = '/index.php/area/1/home/';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\AreaLink(1, $iaOptimiser);
        $iaLink->setArea($area);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle2()
    {
        $expected = '/area/1/home/';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\AreaLink(2, $iaOptimiser);
        $iaLink->setArea($area);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle3()
    {
        $expected = '/home/';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\AreaLink(3, $iaOptimiser);
        $iaLink->setArea($area);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle4()
    {
        $expected = '/home/';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\AreaLink(4, $iaOptimiser);
        $iaLink->setArea($area);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle5()
    {
        $expected = '/home/';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\AreaLink(5, $iaOptimiser);
        $iaLink->setArea($area);
        $this->assertEquals($expected, $iaLink->generate());
    }
}