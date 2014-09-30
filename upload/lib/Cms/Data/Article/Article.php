<?php


namespace Cms\Data\Article;


class Article
{
    private $id;
    private $title;

    public function __construct($dbData)
    {
        $this->id = $dbData['id'];
        $this->title = $dbData['title'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

}