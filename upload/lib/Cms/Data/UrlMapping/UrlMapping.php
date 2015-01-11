<?php

namespace Cms\Data\UrlMapping;

use Cms\Data\DataModel;

class UrlMapping extends DataModel
{
    private $definitions = array(
        'core' => array('tableName' => 'Cms_UrlMapping', 'tableKey' => 'id'),
        'fields' => array(
            'id' => array('objectVar' => 'id', 'classMethod' => 'getId'),
            'relative_url' => array('objectVar' => 'relativeUrl', 'classMethod' => 'getRelativeUrl'),
            'is_active' => array('objectVar' => 'isActive', 'classMethod' => 'getIsActive'),
            'article_id' => array('objectVar' => 'articleId', 'classMethod' => 'getArticleId'),
            'category_id' => array('objectVar' => 'categoryId', 'classMethod' => 'getCategoryId'),
        )
    );

    public function __construct()
    {
        $this->dbDefinitions = $this->definitions;
    }

    private $id;
    private $relativeUrl;
    private $isActive;
    private $articleId;
    private $categoryId;

    public function getId()
    {
        return $this->id;
    }

    protected function setId($value)
    {
        $this->id = $value;
    }

    public function getRelativeUrl()
    {
        return $this->relativeUrl;
    }

    public function setRelativeUrl($value)
    {
        $this->relativeUrl = $value;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($value)
    {
        $this->isActive = $value;
    }

    public function getArticleId()
    {
        return $this->articleId;
    }

    public function setArticleId($value)
    {
        $this->articleId = $value;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function setCategoryId($value)
    {
        $this->categoryId = $value;
    }
}