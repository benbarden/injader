<?php


namespace Cms\Theme\User;

use Cms\Data\Article\Article as DataArticle,
    Cms\Core\Di\Container;


class Article
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Cms\Data\Article\Article
     */
    private $article;

    /**
     * @var string
     */
    private $themeFile;

    /**
     * @var array
     */
    private $bindings;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->themeFile = 'core/article.twig';
    }

    public function __destruct()
    {
        unset($this->container);
    }

    public function setArticle(DataArticle $article)
    {
        $this->article = $article;
    }

    public function setupBindings()
    {
        $articleId = $this->article->getId();
        $articleTitle = stripslashes($this->article->getTitle());

        $bindings = array();

        $bindings['Page']['Type'] = 'article';
        $bindings['Page']['Title'] = $articleTitle;

        $bindings['Article']['FeedUrl'] = sprintf('%s?name=comments&id=%s', FN_FEEDS, $articleId);

        // Wrapper IDs and classes
        $bindings['Page']['WrapperId'] = sprintf('article-page-%s', $articleId);
        $bindings['Page']['WrapperClass'] = 'article-page';

        // Date
        $dateFormat = $this->container->getSetting('DateFormat');
        $iaLink = $this->container->getService('IA.LinkArticle');

        // Current page
        $contentArticle = new \Cms\Content\Article($this->article, $iaLink);
        $bindings['Article']['Id'] = $articleId;
        $bindings['Article']['Title'] = $articleTitle;
        $bindings['Article']['Body'] = $contentArticle->getFullBody();
        $bindings['Article']['Date'] = date($dateFormat, strtotime($this->article->getCreateDate()));

        $repoUser = $this->container->getService('Repo.User');
        $articleAuthor = $repoUser->getById($this->article->getAuthorId());
        /* @var \Cms\Data\User\User $articleAuthor */
        $bindings['Article']['Author']['Id'] = $articleAuthor->getUserId();
        $bindings['Article']['Author']['Username'] = $articleAuthor->getUsername();

        $this->bindings = $bindings;
    }

    public function getFile()
    {
        return $this->themeFile;
    }

    public function getBindings()
    {
        return $this->bindings;
    }
} 