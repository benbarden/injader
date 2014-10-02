<?php


namespace Cms\Ia\Link;

use Cms\Data\Area\Area,
    Cms\Data\Article\Article,
    Cms\Ia\Tools\OptimiseUrl;


class ArticleLink
{
    // yoursite.com/view.php/article/1/hello-world
    const STYLE_CLASSIC = 1;

    // yoursite.com/article/1/hello-world
    const STYLE_LONG = 2;

    // yoursite.com/hello-world
    const STYLE_TITLE_ONLY = 3;

    // yoursite.com/area-name/hello-world
    const STYLE_AREA_AND_TITLE = 4;

    // yoursite.com/2009/12/31/hello-world
    const STYLE_DATE_AND_TIME = 5;

    /**
     * @var integer
     */
    private $linkStyle;

    /**
     * @var Article
     */
    private $article;

    /**
     * @var OptimiseUrl
     */
    private $optimiser;

    /**
     * @var Area
     */
    private $area;

    public function __construct($linkStyle, OptimiseUrl $optimiser)
    {
        $this->linkStyle = $linkStyle;
        $this->optimiser = $optimiser;
    }

    public function __destruct()
    {
        unset($this->article);
        unset($this->area);
        unset($this->optimiser);
    }

    public function setArticle(Article $article)
    {
        $this->article = $article;
    }

    public function setArea(Area $area)
    {
        $this->area = $area;
    }

    public function generate()
    {
        switch ($this->linkStyle) {
            case self::STYLE_CLASSIC;
                return $this->generateLinkStyleClassic();
                break;
            case self::STYLE_LONG:
                return $this->generateLinkStyleLong();
                break;
            case self::STYLE_TITLE_ONLY:
                return $this->generateLinkStyleTitleOnly();
                break;
            case self::STYLE_AREA_AND_TITLE:
                return $this->generateLinkStyleAreaAndTitle();
                break;
            case self::STYLE_DATE_AND_TIME:
                return $this->generateLinkStyleDateAndTime();
                break;
            default:
                throw new \Exception(sprintf('Unknown link style: %s', $this->linkStyle));
                break;
        }
    }

    // view.php/article/1/hello-world
    private function generateLinkStyleClassic()
    {
        $optimisedUrl = $this->optimiser->optimise($this->article->getTitle());
        return sprintf('view.php/article/%s/%s', $this->article->getId(), $optimisedUrl);
    }

    // article/1/hello-world
    private function generateLinkStyleLong()
    {
        $optimisedUrl = $this->optimiser->optimise($this->article->getTitle());
        return sprintf('article/%s/%s', $this->article->getId(), $optimisedUrl);
    }

    // hello-world
    private function generateLinkStyleTitleOnly()
    {
        $optimisedUrl = $this->optimiser->optimise($this->article->getTitle());
        return $optimisedUrl;
    }

    // area-name/hello-world
    private function generateLinkStyleAreaAndTitle()
    {
        $optimisedUrl = $this->optimiser->optimise($this->article->getTitle());
        $areaName = $this->optimiser->optimise($this->area->getName());
        return sprintf('%s/%s', $areaName, $optimisedUrl);
    }

    // 2009/12/31/hello-world
    private function generateLinkStyleDateAndTime()
    {
        $optimisedUrl = $this->optimiser->optimise($this->article->getTitle());
        return sprintf('%s/%s/%s/%s', '??', '??', '??', $optimisedUrl);
    }
}