<?php


namespace Cms\Ia\Link;

use Cms\Data\Area\Area,
    Cms\Data\Article\Article;


class ArticleLink extends Base
{
    /**
     * @var Article
     */
    private $article;

    /**
     * @var Area
     */
    private $area;

    public function __destruct()
    {
        unset($this->article);
        unset($this->area);
        parent::__destruct();
    }

    public function setArticle(Article $article)
    {
        $this->article = $article;
    }

    public function setArea(Area $area)
    {
        $this->area = $area;
    }

    private function getOptimisedArticleUrl()
    {
        return $this->optimiser->optimise($this->article->getTitle());
    }

    private function getOptimisedAreaUrl()
    {
        return $this->optimiser->optimise($this->area->getName());
    }

    /**
     * view.php/article/1/hello-world
     * @return string
     */
    protected function generateLinkStyleClassic()
    {
        return URL_ROOT.sprintf('view.php/article/%s/%s',
            $this->article->getId(), $this->getOptimisedArticleUrl());
    }

    /**
     * article/1/hello-world
     * @return string
     */
    protected function generateLinkStyleLong()
    {
        return URL_ROOT.sprintf('article/%s/%s',
            $this->article->getId(), $this->getOptimisedArticleUrl());
    }

    /**
     * hello-world
     * @return string
     */
    protected function generateLinkStyleTitleOnly()
    {
        return URL_ROOT.$this->getOptimisedArticleUrl();
    }

    /**
     * area-name/hello-world
     * @return string
     */
    protected function generateLinkStyleAreaAndTitle()
    {
        return URL_ROOT.sprintf('%s/%s',
            $this->getOptimisedAreaUrl(), $this->getOptimisedArticleUrl());
    }

    /**
     * 2009/12/31/hello-world
     * @return string
     */
    protected function generateLinkStyleDateAndTime()
    {
        $createDate = $this->article->getCreateDate();
        $articleDate = new \DateTime($createDate);
        $articleYY = $articleDate->format('Y');
        $articleMM = $articleDate->format('m');
        $articleDD = $articleDate->format('d');
        return URL_ROOT.sprintf('%s/%s/%s/%s',
            $articleYY, $articleMM, $articleDD, $this->getOptimisedArticleUrl());
    }
}