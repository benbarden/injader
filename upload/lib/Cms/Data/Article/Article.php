<?php


namespace Cms\Data\Article;

use Cms\Data\IModel;


class Article extends IModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * varchar(125)
     * @var string
     */
    private $title;

    /**
     * text
     * @var string
     */
    private $content;

    /**
     * @var integer
     */
    private $authorId;

    /**
     * @var integer
     */
    private $contentAreaId;

    /**
     * @var datetime
     */
    private $createDate;

    /**
     * text
     * @var string
     */
    private $excerpt;

    public function __construct($dbData)
    {
        $this->dbData = $dbData;

        $this->id = $dbData['id'];
        $this->title = $dbData['title'];
        $this->content = $this->getFieldSafe('content');
        $this->authorId = $this->getFieldSafe('author_id');
        $this->contentAreaId = $this->getFieldSafe('content_area_id');
        $this->createDate = $this->getFieldSafe('create_date');
        $this->excerpt = $this->getFieldSafe('article_excerpt');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getAuthorId()
    {
        return $this->authorId;
    }

    public function getContentAreaId()
    {
        return $this->contentAreaId;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

}