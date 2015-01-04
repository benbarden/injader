<?php


namespace Cms\Data\Category;


class Category
{
    private $categoryId;
    private $name;
    private $permalink;
    private $description;
    private $parentId;
    private $itemsPerPage;
    private $sortRule;
    private $sortRuleField;
    private $sortRuleDirection;

    public function __construct($logData)
    {
        $this->categoryId = $logData['id'];
        $this->name = $logData['name'];
        $this->permalink = $logData['permalink'];
        $this->description = $logData['description'];
        $this->parentId = $logData['parent_id'];
        $this->itemsPerPage = $logData['items_per_page'];
        $this->sortRule = $logData['sort_rule'];

        if ($this->sortRule) {
            $sortRuleArray = explode("|", $this->sortRule);
            $this->sortRuleField = $sortRuleArray[0];
            $this->sortRuleDirection = $sortRuleArray[1];
        } else {
            $this->sortRuleField = "create_date";
            $this->sortRuleDirection = "DESC";
        }
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPermalink()
    {
        return $this->permalink;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    public function getSortRule()
    {
        return $this->sortRule;
    }

    public function getSortRuleField()
    {
        return $this->sortRuleField;
    }

    public function getSortRuleDirection()
    {
        return $this->sortRuleDirection;
    }
}