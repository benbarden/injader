<?php


namespace Cms\Ia;

use Cms\Data\Area\Area,
    Cms\Data\Article\Article;


class Link
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
     * @var Area
     */
    private $area;

    public function __construct($linkStyle)
    {
        $this->linkStyle = $linkStyle;
    }

    public function __destruct()
    {
        unset($this->article);
        unset($this->area);
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

    // yoursite.com/view.php/article/1/hello-world
    private function generateLinkStyleClassic()
    {
        return sprintf('view.php/article/%s/%s', $this->article->getId(), $this->article->getTitle());
    }

    // yoursite.com/article/1/hello-world
    private function generateLinkStyleLong()
    {

    }

    // yoursite.com/hello-world
    private function generateLinkStyleTitleOnly()
    {

    }

    // yoursite.com/area-name/hello-world
    private function generateLinkStyleAreaAndTitle()
    {

    }

    // yoursite.com/2009/12/31/hello-world
    private function generateLinkStyleDateAndTime()
    {

    }
}