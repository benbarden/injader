<?php

class TestArea extends \PHPUnit_Framework_TestCase
{
    public function testMockAreaId()
    {
        $mockArea = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockArea->getById(1);
        $this->assertEquals(1, $area->getAreaId());
    }
    public function testMockArticleTitle()
    {
        $mockArea = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockArea->getById(1);
        $this->assertEquals('Home', $area->getName());
    }
}