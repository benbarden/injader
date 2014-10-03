<?php

class TestArticle extends \PHPUnit_Framework_TestCase
{
    public function testMockArticleId()
    {
        $mockArticle = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticle->getById(1);
        $this->assertEquals(1, $article->getId());
    }
    public function testMockArticleTitle()
    {
        $mockArticle = new \Cms\Data\Article\MockArticleRepository();
        $article = $mockArticle->getById(1);
        $this->assertEquals('Test Article', $article->getTitle());
    }
}