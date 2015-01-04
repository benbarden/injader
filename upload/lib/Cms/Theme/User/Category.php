<?php


namespace Cms\Theme\User;

use Cms\Data\Area\Area,
    Cms\Core\Di\Container;


class Category
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Cms\Data\Category\Category
     */
    private $category;

    /**
     * @var string
     */
    private $themeFile;

    /**
     * @var array
     */
    private $bindings;

    /**
     * @var array
     */
    private $subareas;

    /**
     * @var array
     */
    private $areaContent;

    /**
     * @var integer
     */
    private $currentPageNo;

    /**
     * @var integer
     */
    private $lastPageNo;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->themeFile = 'core/category.twig';
    }

    public function __destruct()
    {
        unset($this->container);
    }

    public function setCategory(\Cms\Data\Category\Category $category)
    {
        $this->category = $category;
    }

    public function setSubareas($subareas)
    {
        $this->subareas = $subareas;
    }

    public function setAreaContent($areaContent)
    {
        $this->areaContent = $areaContent;
    }

    public function setCurrentPageNo($pageNo)
    {
        $this->currentPageNo = $pageNo;
    }

    public function setLastPageNo($pageNo)
    {
        $this->lastPageNo = $pageNo;
    }

    public function setupBindings()
    {
        $categoryId = $this->category->getCategoryId();

        $bindings = array();

        //$bindings['Area'] = $this->area;
        $bindings['Page']['Type'] = 'category';
        $bindings['Page']['Title'] = $this->category->getName();

        $bindings['Area']['Id'] = $categoryId;
        $bindings['Area']['Name'] = $this->category->getName();
        $bindings['Area']['Desc'] = $this->category->getDescription();

        $areaUrl = $this->category->getPermalink();
        $bindings['Area']['Url'] = $areaUrl;

        /*
        $bindings['Area']['IsTypeContent'] = $this->area->isContentArea();
        $bindings['Area']['IsTypeLinked'] = $this->area->isLinkedArea();
        $bindings['Area']['IsTypeSmart'] = $this->area->isSmartArea();
        */

        $bindings['Area']['FeedUrl'] = sprintf('%s?name=articles&id=%s', FN_FEEDS, $categoryId);

        // Wrapper IDs and classes
        $bindings['Page']['WrapperId'] = sprintf('area-index-%s', $categoryId);
        $bindings['Page']['WrapperClass'] = 'area-index';

        // Subareas
        /*
        if ($this->subareas) {
            foreach ($this->subareas as $subareaItem) {
                $subareaObject = new \Cms\Data\Area\Area($subareaItem);
                $subareaRow = array(
                    'Id' => $subareaObject->getAreaId(),
                    'Name' => $subareaObject->getName(),
                    'Desc' => $subareaObject->getAreaDescription()
                );
                $bindings['Area']['Subareas'][] = $subareaRow;
            }
        }
        */

        // Date
        $dateFormat = $this->container->getSetting('DateFormat');
        $iaLink = $this->container->getService('IA.LinkArticle');

        // Content
        $repoUser = $this->container->getService('Repo.User');

        if ($this->areaContent) {

            // Area URL
            //$area = $this->container->getService('Repo.Area')->getById($areaId);
            //$iaLinkArea = $this->container->getService('IA.LinkArea');
            //$iaLinkArea->setArea($area);

            if ($this->currentPageNo == 1) {
                $bindings['Page']['CanonicalUrl'] = $areaUrl;
            } else {
                $bindings['Page']['CanonicalUrl'] = sprintf('%s?page=%s', $areaUrl, $this->currentPageNo);
            }

            // Pagination
            $bindings['Area']['Page']['Current'] = $this->currentPageNo;
            $bindings['Area']['Page']['Last'] = $this->lastPageNo;

            $prevPageNo = 0;
            if ($this->currentPageNo > 1) {
                $prevPageNo = $this->currentPageNo - 1;
                $bindings['Area']['Page']['Prev'] = $prevPageNo;
                if ($prevPageNo == 1) {
                    $bindings['Page']['PrevUrl'] = $areaUrl;
                } else {
                    $bindings['Page']['PrevUrl'] = sprintf('%s?page=%s', $areaUrl, $prevPageNo);
                }
            }

            if ($this->currentPageNo < $this->lastPageNo) {
                $nextPageNo = $this->currentPageNo + 1;
                $bindings['Area']['Page']['Next'] = $nextPageNo;
                $bindings['Page']['NextUrl'] = sprintf('%s?page=%s', $areaUrl, $nextPageNo);
            }

            // only use numbered links if 10 pages or less
            if ($this->lastPageNo <= 10) {
                $pageNumberArray = array();
                for ($i=1; $i<=$this->lastPageNo; $i++) {
                    $pageNumberArray[] = $i;
                }
                $bindings['Area']['Page']['List'] = $pageNumberArray;
            }

            foreach ($this->areaContent as $contentItem) {

                $contentObject = new \Cms\Data\Article\Article($contentItem);
                $contentArticle = new \Cms\Content\Article($contentObject, $iaLink);
                $articleId = $contentObject->getId();
                // Author
                $articleAuthor = $repoUser->getById($contentObject->getAuthorId());
                /* @var \Cms\Data\User\User $articleAuthor */
                $authorId = $articleAuthor->getUserId();
                $authorUsername = $articleAuthor->getUsername();
                // Setup array
                $contentRow = array(
                    'Id' => $articleId,
                    'Title' => stripslashes($contentObject->getTitle()),
                    'Permalink' => $contentObject->getPermalink(),
                    'Body' => $contentArticle->getCategoryBody(),
                    'Date' => date($dateFormat, strtotime($contentObject->getCreateDate())),
                    'AuthorId' => $authorId,
                    'AuthorUsername' => $authorUsername
                );
                $bindings['Area']['Content'][] = $contentRow;

            }

        }

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