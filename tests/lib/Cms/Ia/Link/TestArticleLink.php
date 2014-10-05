<?php

class TestArticleLink extends \PHPUnit_Framework_TestCase
{
    public function testLinkStyle1()
    {
        $expected = '/index.php/article/1/test-article';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $mockArticleRepo = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticleRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\ArticleLink(1, $iaOptimiser);
        $iaLink->setArea($area);
        $iaLink->setArticle($article);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle2()
    {
        $expected = '/article/1/test-article';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $mockArticleRepo = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticleRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\ArticleLink(2, $iaOptimiser);
        $iaLink->setArea($area);
        $iaLink->setArticle($article);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle3()
    {
        $expected = '/test-article';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $mockArticleRepo = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticleRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\ArticleLink(3, $iaOptimiser);
        $iaLink->setArea($area);
        $iaLink->setArticle($article);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle4()
    {
        $expected = '/home/test-article';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $mockArticleRepo = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticleRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\ArticleLink(4, $iaOptimiser);
        $iaLink->setArea($area);
        $iaLink->setArticle($article);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle5()
    {
        $expected = '/2009/12/31/test-article';
        $mockAreaRepo = new \Cms\Data\Area\MockAreaRepository();
        $area = $mockAreaRepo->getById(1);
        $mockArticleRepo = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticleRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\ArticleLink(5, $iaOptimiser);
        $iaLink->setArea($area);
        $iaLink->setArticle($article);
        $this->assertEquals($expected, $iaLink->generate());
    }
}