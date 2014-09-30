<?php

use Cms\Theme\Renderer;

class TestRenderer extends ContainerBase
{
    public function testObjectCategory()
    {
        $renderer = new Renderer($this->container);
        $renderer->setObjectCategory();
        $this->assertTrue($renderer->isObjectCategory());
    }
    public function testObjectArticle()
    {
        $renderer = new Renderer($this->container);
        $renderer->setObjectArticle();
        $this->assertTrue($renderer->isObjectArticle());
    }
    public function testObjectFile()
    {
        $renderer = new Renderer($this->container);
        $renderer->setObjectFile();
        $this->assertTrue($renderer->isObjectFile());
    }
    public function testObjectUser()
    {
        $renderer = new Renderer($this->container);
        $renderer->setObjectUser();
        $this->assertTrue($renderer->isObjectUser());
    }
    public function testItemId()
    {
        $renderer = new Renderer($this->container);
        $renderer->setItemId(1);
        $this->assertEquals(1, $renderer->getItemId());
    }
    public function testCategoryWithId()
    {
        $renderer = new Renderer($this->container);
        $renderer->setObjectCategory();
        $renderer->setItemId(5);
        $this->assertTrue($renderer->isObjectCategory());
        $this->assertEquals(5, $renderer->getItemId());
    }
}