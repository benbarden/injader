<?php


namespace Cms\Theme;

use Cms\Exception\Theme\RendererException;


class Renderer
{
    const OBJECT_TYPE_HOMEPAGE = 'homepage';
    const OBJECT_TYPE_AREA     = 'area';
    const OBJECT_TYPE_CATEGORY = 'category';
    const OBJECT_TYPE_ARTICLE  = 'article';
    const OBJECT_TYPE_FILE     = 'file';
    const OBJECT_TYPE_USER     = 'user';
    const OBJECT_TYPE_ARCHIVES = 'archives';

    /**
     * @var \Cms\Core\Di\Container
     */
    private $container;

    /**
     * @var object
     */
    private $renderer;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var integer
     */
    private $itemId;

    /**
     * @var integer
     */
    private $pageNo;

    /**
     * @param \Cms\Core\Di\Container $container
     * @return void
     */
    public function __construct(\Cms\Core\Di\Container $container)
    {
        $this->container = $container;
    }

    public function __destruct()
    {
        unset($this->container);
        unset($this->renderer);
    }

    public function setObjectHomepage()
    {
        $this->objectType = self::OBJECT_TYPE_HOMEPAGE;
        $this->setupRenderer();
    }

    public function setObjectCategory()
    {
        $this->objectType = self::OBJECT_TYPE_CATEGORY;
        $this->setupRenderer();
    }

    public function setObjectArticle()
    {
        $this->objectType = self::OBJECT_TYPE_ARTICLE;
        $this->setupRenderer();
    }

    public function setObjectFile()
    {
        $this->objectType = self::OBJECT_TYPE_FILE;
        $this->setupRenderer();
    }

    public function setObjectUser()
    {
        $this->objectType = self::OBJECT_TYPE_USER;
        $this->setupRenderer();
    }

    public function setObjectArchives()
    {
        $this->objectType = self::OBJECT_TYPE_ARCHIVES;
        $this->setupRenderer();
    }

    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    public function setPageNo($pageNo)
    {
        $this->pageNo = $pageNo;
    }

    public function setRendererParam($index, $value)
    {
        $this->renderer->setParam($index, $value);
    }

    public function isObjectHomepage()
    {
        return $this->objectType == self::OBJECT_TYPE_HOMEPAGE;
    }

    public function isObjectCategory()
    {
        return $this->objectType == self::OBJECT_TYPE_CATEGORY;
    }

    public function isObjectArticle()
    {
        return $this->objectType == self::OBJECT_TYPE_ARTICLE;
    }

    public function isObjectFile()
    {
        return $this->objectType == self::OBJECT_TYPE_FILE;
    }

    public function isObjectUser()
    {
        return $this->objectType == self::OBJECT_TYPE_USER;
    }

    public function isObjectArchives()
    {
        return $this->objectType == self::OBJECT_TYPE_ARCHIVES;
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function render()
    {
        $this->setupBindings();
        $themeFile = $this->renderer->getFile();
        $userBindings = $this->renderer->getBindings();

        if ($userBindings) {
            if (array_key_exists('CMS', $userBindings)) {
                throw new RendererException('Illegal binding key - Cannot override: CMS');
            } elseif (array_key_exists('URL', $userBindings)) {
                throw new RendererException('Illegal binding key - Cannot override: URL');
            } elseif (array_key_exists('Nav', $userBindings)) {
                throw new RendererException('Illegal binding key - Cannot override: Nav');
            } elseif (array_key_exists('Login', $userBindings)) {
                throw new RendererException('Illegal binding key - Cannot override: Login');
            }
        }

        // we may allow certain overrides here
        $globalBindings = $this->getGlobalBindings();

        $bindings = array_merge($globalBindings, $userBindings);
        $engine = $this->container->getService('Theme.Engine');
        $outputHtml = $engine->render($themeFile, $bindings);
        print($outputHtml);
        exit;
    }

    private function setupBindings()
    {
        $this->renderer->setupBindings();
    }

    private function setupRenderer()
    {
        switch ($this->objectType) {
            case self::OBJECT_TYPE_HOMEPAGE:
                $this->renderer = new \Cms\Theme\User\Homepage($this->container);
                break;
            case self::OBJECT_TYPE_AREA:
            case self::OBJECT_TYPE_CATEGORY:

                $categoryRepo = $this->container->getService('Repo.Category');
                /* @var \Cms\Data\Category\CategoryRepository $categoryRepo */
                $articleRepo = $this->container->getService('Repo.Article');
                /* @var \Cms\Data\Article\ArticleRepository $articleRepo */

                // Category setup
                $category = $categoryRepo->getById($this->itemId);
                //$subareas = $areaRepo->getSubareas($this->itemId);

                // Sort Rule setup
                $sortField = $category->getSortRuleField();
                $sortDirection = $category->getSortRuleDirection();

                // Content setup
                $perPage = $category->getItemsPerPage();
                $totalCount = $articleRepo->countByCategory($this->itemId);

                // Pagination
                $iaPagesOffset = new \Cms\Ia\Pages\Offset();
                $iaPagesOffset->setPageNo($this->pageNo);
                $iaPagesOffset->setPerPage($perPage);
                $offset = $iaPagesOffset->calculate();

                $iaPagesLast = new \Cms\Ia\Pages\LastPage();
                $iaPagesLast->setItemCount($totalCount);
                $iaPagesLast->setPerPage($perPage);
                $lastPageNo = $iaPagesLast->calculate();

                $areaContent = $articleRepo->getByCategoryPublic(
                    $this->itemId, $perPage, $offset, $sortField, $sortDirection
                );

                // Renderer setup
                $this->renderer = new \Cms\Theme\User\Category($this->container);
                $this->renderer->setCategory($category);
                if ($areaContent) {
                    $this->renderer->setAreaContent($areaContent);
                    $this->renderer->setCurrentPageNo($this->pageNo);
                    $this->renderer->setLastPageNo($lastPageNo);
                }
                /*
                if ($subareas) {
                    $this->renderer->setSubareas($subareas);
                }
                */
            break;
            case self::OBJECT_TYPE_ARTICLE:

                $articleRepo = $this->container->getService('Repo.Article');
                /* @var \Cms\Data\Article\ArticleRepository $articleRepo */
                $article = $articleRepo->getById($this->itemId);

                $this->renderer = new \Cms\Theme\User\Article($this->container);
                $this->renderer->setArticle($article);

                break;
            case self::OBJECT_TYPE_FILE:
                //$this->renderer = new \Cms\Theme\User\File();
                //break;
            case self::OBJECT_TYPE_USER:
                //$this->renderer = new \Cms\Theme\User\User();
                //break;
            case self::OBJECT_TYPE_ARCHIVES:
                $this->renderer = new \Cms\Theme\User\Archive($this->container);
                break;
            default:
                throw new RendererException(sprintf('Unknown object type: %s', $this->objectType));
                break;
        }
    }

    private function getGlobalBindings()
    {
        $bindings = array();

        $cmsThemeEngine = $this->container->getService('Cms.ThemeEngine');
        $publicThemePath = $cmsThemeEngine->getPublicThemePath();

        $repoSetting = $this->container->getService('Repo.Setting');
        /* @var \Cms\Data\Setting\SettingRepository $repoSetting */
        $siteTitle = $repoSetting->getSettingSiteTitle();
        $siteDesc = $repoSetting->getSettingSiteDesc();
        $siteKeywords = $repoSetting->getSettingSiteKeywords();
        $siteCustomHeader = $repoSetting->getSettingSiteHeader();
        $siteDisqusId = $repoSetting->getSettingDisqusId();

        $repoArea = $this->container->getService('Repo.Area');
        /* @var \Cms\Data\Area\AreaRepository $repoArea */
        $areasTopLevel = $repoArea->getTopLevel();

        $repoArticle = $this->container->getService('Repo.Article');
        /* @var \Cms\Data\Article\ArticleRepository $repoArticle */
        $bindings['Content']['Recent'] = $repoArticle->getRecentPublic(5);

        $repoCategory = $this->container->getService('Repo.Category');
        /* @var \Cms\Data\Category\CategoryRepository $repoCategory */
        $categoriesTopLevel = $repoCategory->getTopLevel();

        // Default RSS URL
        $siteRSSArticlesUrl = FN_FEEDS."?name=articles";

        // Core styles URL
        $siteStylesCoreUrl = URL_ROOT."sys/core.css";
        $siteScriptsCoreUrl = URL_ROOT."sys/scripts.js";
        $siteScriptsInitUrl = URL_ROOT."sys/init.js";

        $settingsArray = array(
            'SiteTitle' => $siteTitle,
            'SiteDesc' => $siteDesc,
            'SiteKeywords' => $siteKeywords,
            'SiteRSSArticlesUrl' => $siteRSSArticlesUrl,
            'SiteStylesCoreUrl' => $siteStylesCoreUrl,
            'SiteScriptsCoreUrl' => $siteScriptsCoreUrl,
            'SiteScriptsInitUrl' => $siteScriptsInitUrl,
            'SiteCustomHeader' => $siteCustomHeader,
            'SiteDisqusId' => $siteDisqusId
        );

        $bindings['CMS']['Settings'] = $settingsArray;

        $bindings['URL']['SiteRoot'] = URL_ROOT;
        $bindings['URL']['ThemeRoot'] = $publicThemePath;

        //$bindings['Nav']['TopLevelAreas'] = $this->processAreaData($areasTopLevel);
        $bindings['Nav']['TopLevelAreas'] = $this->processCategoryData($categoriesTopLevel);

        // User access
        if ($this->container->hasService('Auth.CurrentUser')) {
            $authCurrentUser = $this->container->getService('Auth.CurrentUser');
            $userArray = array(
                'Name' => $authCurrentUser->getUsername()
            );
            $bindings['Login']['User'] = $userArray;
        }

        return $bindings;
    }

    private function processAreaData($areaData)
    {
        $areaArray = array();

        if ($areaData) {
            foreach ($areaData as $areaItem) {
                $areaObject = new \Cms\Data\Area\Area($areaItem);
                /* @var \Cms\Data\Area\Area $areaObject */
                $areaRow = array(
                    'Id' => $areaObject->getAreaId(),
                    'Name' => $areaObject->getName(),
                    'Desc' => $areaObject->getAreaDescription()
                );
                $areaArray[] = $areaRow;
            }
        }

        return $areaArray;
    }

    private function processCategoryData($catData)
    {
        $catArray = array();

        if ($catData) {
            foreach ($catData as $catItem) {
                $catObject = new \Cms\Data\Category\Category($catItem);
                $catArray[] = array(
                    'Id' => $catObject->getCategoryId(),
                    'Name' => $catObject->getName(),
                    'Desc' => $catObject->getDescription(),
                    'Url' => $catObject->getPermalink()
                );;
            }
        }

        return $catArray;
    }
}