<?php


namespace Cms\Data\Area;

use Cms\Data\IModel;


class Area extends IModel
{
    /**
     * @var integer
     */
    private $areaId;

    /**
     * varchar(125)
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $areaLevel;

    /**
     * @var integer
     */
    private $areaOrder;

    /**
     * @var integer
     */
    private $hierLeft;

    /**
     * @var integer
     */
    private $hierRight;

    /**
     * @var integer
     */
    private $parentId;

    /**
     * @var integer
     */
    private $permissionProfileId;

    /**
     * @var integer
     */
    private $areaGraphicId;

    /**
     * @var integer
     */
    private $contentPerPage;

    /**
     * varchar(100)
     * @var string
     */
    private $sortRule;

    /**
     * @var string
     */
    private $sortRuleField;

    /**
     * @var string
     */
    private $sortRuleDirection;

    /**
     * char(1)
     * @var string
     */
    private $includeInRssFeed;

    /**
     * varchar(20)
     * @var string
     */
    private $maxFileSize;

    /**
     * @var integer
     */
    private $maxFilesPerUser;

    /**
     * varchar(200)
     * @var string
     */
    private $areaUrl;

    /**
     * text
     * @var string
     */
    private $smartTags;

    /**
     * varchar(100)
     * @var string
     */
    private $seoName;

    /**
     * text
     * @var string
     */
    private $areaDescription;

    /**
     * varchar(45)
     * @var string
     */
    private $areaType;

    const AREA_TYPE_CONTENT = 'Content';
    const AREA_TYPE_LINKED = 'Linked';
    const AREA_TYPE_SMART = 'Smart';

    /**
     * text
     * @var string
     */
    private $themePath;

    /**
     * varchar(50)
     * @var string
     */
    private $layoutStyle;

    /**
     * varchar(1)
     * @var string
     */
    private $subareaContentOnIndex;

    public function __construct($dbData)
    {
        $this->dbData = $dbData;

        $this->areaId                = $dbData['id'];
        $this->name                  = $dbData['name'];
        $this->areaLevel             = $this->getFieldSafe('area_level');
        $this->areaOrder             = $this->getFieldSafe('area_order');
        $this->hierLeft              = $this->getFieldSafe('hier_left');
        $this->hierRight             = $this->getFieldSafe('hier_right');
        $this->parentId              = $this->getFieldSafe('parent_id');
        $this->permissionProfileId   = $this->getFieldSafe('permission_profile_id');
        $this->areaGraphicId         = $this->getFieldSafe('area_graphic_id');
        $this->contentPerPage        = $this->getFieldSafe('content_per_page');
        $this->sortRule              = $this->getFieldSafe('sort_rule');
        $this->includeInRssFeed      = $this->getFieldSafe('include_in_rss_feed');
        $this->maxFileSize           = $this->getFieldSafe('max_file_size');
        $this->maxFilesPerUser       = $this->getFieldSafe('max_files_per_user');
        $this->areaUrl               = $this->getFieldSafe('area_url');
        $this->smartTags             = $this->getFieldSafe('smart_tags');
        $this->seoName               = $this->getFieldSafe('seo_name');
        $this->areaDescription       = $this->getFieldSafe('area_description');
        $this->areaType              = $this->getFieldSafe('area_type');
        $this->themePath             = $this->getFieldSafe('theme_path');
        $this->layoutStyle           = $this->getFieldSafe('layout_style');
        $this->subareaContentOnIndex = $this->getFieldSafe('subarea_content_on_index');

        if ($this->sortRule) {
            $sortRuleArray = explode("|", $this->sortRule);
            $this->sortRuleField = $sortRuleArray[0];
            $this->sortRuleDirection = $sortRuleArray[1];
        } else {
            $this->sortRuleField = "create_date";
            $this->sortRuleDirection = "DESC";
        }
    }

    public function getAreaId()
    {
        return $this->areaId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAreaLevel()
    {
        return $this->areaLevel;
    }

    public function getAreaOrder()
    {
        return $this->areaOrder;
    }

    public function getHierLeft()
    {
        return $this->hierLeft;
    }

    public function getHierRight()
    {
        return $this->hierRight;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function getPermissionProfileId()
    {
        return $this->permissionProfileId;
    }

    public function getAreaGraphicId()
    {
        return $this->areaGraphicId;
    }

    public function getContentPerPage()
    {
        return $this->contentPerPage;
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

    public function getIncludeInRssFeed()
    {
        return $this->includeInRssFeed;
    }

    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    public function getMaxFilesPerUser()
    {
        return $this->maxFilesPerUser;
    }

    public function getAreaUrl()
    {
        return $this->areaUrl;
    }

    public function getSmartTags()
    {
        return $this->smartTags;
    }

    public function getSeoName()
    {
        return $this->seoName;
    }

    public function getAreaDescription()
    {
        return $this->areaDescription;
    }

    public function getAreaType()
    {
        return $this->areaType;
    }

    public function isContentArea()
    {
        return $this->areaType == self::AREA_TYPE_CONTENT;
    }

    public function isLinkedArea()
    {
        return $this->areaType == self::AREA_TYPE_LINKED;
    }

    public function isSmartArea()
    {
        return $this->areaType == self::AREA_TYPE_SMART;
    }

    public function getThemePath()
    {
        return $this->themePath;
    }

    public function getLayoutStyle()
    {
        return $this->layoutStyle;
    }

    public function getSubareaContentOnIndex()
    {
        return $this->subareaContentOnIndex;
    }
}