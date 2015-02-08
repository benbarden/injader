<?php


namespace Cms\Data\Article;

use Cms\Data\IModel;


class Article extends IModel
{
    protected $dbDefinitions = array(
        'core' => array('tableName' => 'Cms_Content', 'tableKey' => 'id'),
        'fields' => array(
            'id' => array('objectVar' => 'id'),
            'title' => array('objectVar' => 'title'),
            'permalink' => array('objectVar' => 'permalink'),
            'content' => array('objectVar' => 'content'),
            'author_id' => array('objectVar' => 'authorId'),
            'category_id' => array('objectVar' => 'categoryId'),
            'create_date' => array('objectVar' => 'createDate'),
            'last_updated' => array('objectVar' => 'lastUpdated'),
            'tags' => array('objectVar' => 'tags'),
            'link_url' => array('objectVar' => 'linkUrl'),
            'content_status' => array('objectVar' => 'status'),
            'tags_deleted' => array('objectVar' => 'tagsDeleted'),
            'article_order' => array('objectVar' => 'articleOrder'),
            'article_excerpt' => array('objectVar' => 'excerpt'),
        )
    );

    public function getDbDefinitions()
    {
        return $this->dbDefinitions;
    }

    private $id;
    private $title;
    private $permalink;
    private $content;
    private $authorId;
    private $categoryId;
    private $createDate;
    private $lastUpdated;
    private $tags;
    private $linkUrl;
    private $status;
    private $tagsDeleted;
    private $articleOrder;
    private $excerpt;

    public function __construct($dbData)
    {
        foreach ($dbData as $key => $value) {
            if (isset($this->dbDefinitions['fields'][$key])) {
                $objectVar = $this->dbDefinitions['fields'][$key]['objectVar'];
                $this->$objectVar = $value;
            }
        }
        /*
        $this->dbData = $dbData;

        $this->id = $dbData['id'];
        $this->title = $dbData['title'];
        $this->permalink = $this->getFieldSafe('permalink');
        $this->content = $this->getFieldSafe('content');
        $this->authorId = $this->getFieldSafe('author_id');
        $this->categoryId = $this->getFieldSafe('categoryId');
        $this->createDate = $this->getFieldSafe('create_date');
        $this->lastUpdated = $this->getFieldSafe('last_updated');
        $this->tags = $this->getFieldSafe('tags');
        $this->linkUrl = $this->getFieldSafe('link_url');
        $this->status = $this->getFieldSafe('content_status');
        $this->tagsDeleted = $this->getFieldSafe('tags_deleted');
        $this->articleOrder = $this->getFieldSafe('article_order');
        $this->excerpt = $this->getFieldSafe('article_excerpt');
        */
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getPermalink()
    {
        return $this->permalink;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getAuthorId()
    {
        return $this->authorId;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getTagsDeleted()
    {
        return $this->tagsDeleted;
    }

    public function getArticleOrder()
    {
        return $this->articleOrder;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

}