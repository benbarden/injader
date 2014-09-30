<?php

class TestLink extends \PHPUnit_Framework_TestCase
{
    public function testLinkStyle1()
    {
        $expected = 'yoursite.com/view.php/article/1/hello-world';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getArea(1);
        $mockArticleRepo = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticleRepo->getById(1);
        $iaLink = new \Cms\Ia\Link(1);
        $iaLink->setArea($area);
        $iaLink->setArticle($article);
        $this->assertEquals($expected, $iaLink->generate());
    }
}