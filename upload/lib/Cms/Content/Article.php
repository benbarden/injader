<?php


namespace Cms\Content;

use Cms\Data\Article\Article as DataArticle,
    Cms\Ia\Link\ArticleLink;


class Article
{
    /**
     * @var DataArticle
     */
    private $article;

    /**
     * @var ArticleLink
     */
    private $iaLink;

    public function __construct(DataArticle $article, ArticleLink $iaLink)
    {
        $this->article = $article;
        $this->iaLink = $iaLink;
        $this->iaLink->setArticle($this->article);
    }

    public function __destruct()
    {
        unset($this->article);
        unset($this->iaLink);
    }

    public function getFullBody()
    {
        // @todo Remove stripslashes once article editor is rebuilt
        return stripslashes($this->article->getContent());
    }

    /**
     * Read More - code to use in TinyMCE
     * @return string
     */
    public function getReadMoreEditor()
    {
        return sprintf('<img src="%ssys/images/icons/application_tile_vertical.png" alt="" />',
            URL_ROOT);
    }

    /**
     * Read More - code to use on the site
     * @return string
     */
    public function getReadMorePublic()
    {
        return "<!-- Injader: Read More -->";
    }

    public function getBodyExcerpt()
    {
        $excerpt = $this->article->getExcerpt();
        if ($excerpt) return $excerpt;

        $fullBody = $this->getFullBody();
        $autoExcerpt = strip_tags($fullBody);
        $autoExcerpt = str_replace("\r", " ", $autoExcerpt);
        $autoExcerpt = str_replace("\n", " ", $autoExcerpt);
        $autoExcerpt = substr($autoExcerpt, 0, 200);
        $autoExcerpt .= $this->getReadMoreLink();
        return $autoExcerpt;
    }

    public function getCategoryBody()
    {
        $fullBody = $this->getFullBody();

        $readMoreEditor = $this->getReadMoreEditor();
        $readMorePublic = $this->getReadMorePublic();

        if (strpos($fullBody, $readMorePublic) !== false) {
            $dataToReplace = $readMorePublic;
        } elseif (strpos($fullBody, $readMoreEditor) !== false) {
            $dataToReplace = $readMoreEditor;
        } else {
            $categoryBody = $this->getBodyExcerpt();
            return $categoryBody;
        }

        $bodyData = explode($dataToReplace, $fullBody);
        $categoryBody = $bodyData[0];
        $categoryBody .= $this->getReadMoreLink();
        return $categoryBody;
    }

    public function getReadMoreLink()
    {
        $articleLink = $this->iaLink->generate();
        $readMore = sprintf(' <span class="read-more">[<a href="%s">Read more...</a>]</span>',
            $articleLink);
        return $readMore;
    }
} 