<?php


namespace Cms\Data\Area;


class Area
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
     * varchar(20)
     * @var string
     */
    private $navType;

    /**
     * varchar(1)
     * @var string
     */
    private $subareaContentOnIndex;

    public function __construct($areaData)
    {
        $this->areaId = $areaData['id'];
        $this->name = $areaData['name'];
        $this->areaLevel = $areaData['area_level'];
        $this->areaOrder = $areaData['area_order'];
        $this->hierLeft = $areaData['hier_left'];
        $this->hierRight = $areaData['hier_right'];
        $this->parentId = $areaData['parent_id'];
        $this->permissionProfileId = $areaData['permission_profile_id'];
        $this->areaGraphicId = $areaData['area_graphic_id'];
        $this->contentPerPage = $areaData['content_per_page'];
        $this->sortRule = $areaData['sort_rule'];
        $this->includeInRssFeed = $areaData['include_in_rss_feed'];
        $this->maxFileSize = $areaData['max_file_size'];
        $this->maxFilesPerUser = $areaData['max_files_per_user'];
        $this->areaUrl = $areaData['area_url'];
        $this->smartTags = $areaData['smart_tags'];
        $this->seoName = $areaData['seo_name'];
        $this->areaDescription = $areaData['area_description'];
        $this->areaType = $areaData['area_type'];
        $this->themePath = $areaData['theme_path'];
        $this->layoutStyle = $areaData['layout_style'];
        $this->navType = $areaData['nav_type'];
        $this->subareaContentOnIndex = $areaData['subarea_content_on_index'];
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

    public function getNavType()
    {
        return $this->navType;
    }

    public function getSubareaContentOnIndex()
    {
        return $this->subareaContentOnIndex;
    }
}