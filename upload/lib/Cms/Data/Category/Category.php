<?php


namespace Cms\Data\Category;


class Category
{
    protected $dbDefinitions = array(
        'core' => array('tableName' => 'Cms_Categories', 'tableKey' => 'id'),
        'fields' => array(
            'id' => array('objectVar' => 'categoryId', 'classMethod' => 'getCategoryId'),
            'name' => array('objectVar' => 'name', 'classMethod' => 'getName'),
            'permalink' => array('objectVar' => 'permalink', 'classMethod' => 'getPermalink'),
            'description' => array('objectVar' => 'description', 'classMethod' => 'getDescription'),
            'parent_id' => array('objectVar' => 'parentId', 'classMethod' => 'getParentId'),
            'items_per_page' => array('objectVar' => 'itemsPerPage', 'classMethod' => 'getItemsPerPage'),
            'sort_rule' => array('objectVar' => 'sortRule', 'classMethod' => 'getSortRule'),
        )
    );

    public function getDbDefinitions()
    {
        return $this->dbDefinitions;
    }

    private $categoryId;
    private $name;
    private $permalink;
    private $description;
    private $parentId;
    private $itemsPerPage;
    private $sortRule;
    private $sortRuleField;
    private $sortRuleDirection;

    public function __construct($dbData)
    {
        foreach ($dbData as $key => $value) {
            if (isset($this->dbDefinitions['fields'][$key])) {
                $objectVar = $this->dbDefinitions['fields'][$key]['objectVar'];
                $this->$objectVar = $value;
            }
        }
        /*
        $this->categoryId = $dbData['id'];
        $this->name = $dbData['name'];
        $this->permalink = $dbData['permalink'];
        $this->description = $dbData['description'];
        $this->parentId = $dbData['parent_id'];
        $this->itemsPerPage = $dbData['items_per_page'];
        $this->sortRule = $dbData['sort_rule'];
        */

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