<?php


namespace Cms\Theme\User;

use Cms\Core\Di\Container;


class Archive
{
    /**
     * @var Container
     */
    private $container;

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
    private $params;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->themeFile = 'core/archive.twig';
        $this->params = array();
    }

    public function __destruct()
    {
        unset($this->container);
    }

    public function setParam($index, $value)
    {
        $this->params[$index] = $value;
    }

    public function setupBindings()
    {
        $bindings = array();

        $bindings['Page']['Type'] = 'archives';
        $bindings['Page']['Title'] = 'Archives';

        // Wrapper IDs and classes
        $bindings['Page']['WrapperId'] = 'archives-page';
        $bindings['Page']['WrapperClass'] = 'archives-page';

        // Content
        $repoArticle = $this->container->getService('Repo.Article');
        $dateFormat = $this->container->getSetting('DateFormat');

        if (array_key_exists(1, $this->params)) {
            $urlYear = $this->params[1];
        } else {
            $urlYear = "";
        }
        if (array_key_exists(2, $this->params)) {
            $urlMonth = $this->params[2];
        } else {
            $urlMonth = "";
        }

        if (!$urlYear || !$urlMonth) {

            // Overall summary
            $archivesSummary = $repoArticle->getArchivesSummary($urlYear, $urlMonth);

            if ($archivesSummary) {
                $archivesList = array();
                foreach ($archivesSummary as $item) {
                    $dateDesc  = $item['content_date_desc'];
                    $dateYear  = $item['content_yyyy'];
                    $dateMonth = $item['content_mm'];
                    $dateCount = $item['count'];
                    $dateLink  = URL_ROOT."cms/archives/$dateYear/$dateMonth";
                    $archivesList[] = array(
                        'Desc' => $dateDesc,
                        'Count' => $dateCount,
                        'Url' => $dateLink
                    );
                }
                $bindings['Archives']['SummaryList'] = $archivesList;
            }

        } else {

            // Articles for the given period
            $archivesContent = $repoArticle->getArchivesContent($urlYear, $urlMonth);

            if ($archivesContent) {
                $archivesList = array();
                foreach ($archivesContent as $item) {
                    $dateDesc  = $item['content_date_full'];
                    $itemTitle = $item['title'];
                    $permalink = $item['permalink'];
                    $archivesList[] = array(
                        'Date' => date($dateFormat, strtotime($dateDesc)),
                        'Title' => $itemTitle,
                        'Url' => $permalink
                    );
                }
                $bindings['Archives']['ContentList'] = $archivesList;
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