<?php


namespace Cms\Theme\User;


class Category
{
    /**
     * @var \Cms\Data\Area\Area
     */
    private $area;

    private $themeFile;

    private $bindings;

    public function __construct(\Cms\Core\Di\Container $container, $areaId)
    {
        $this->themeFile = 'core/category.twig';

        $areaRepo = $container->getService('Repo.Area');
        $this->area = $areaRepo->getArea($areaId);

        $this->setupBindings();
    }

    private function setupBindings()
    {
        $areaId = $this->area->getAreaId();

        $bindings = array();

        //$bindings['Area'] = $this->area;
        $bindings['Page']['Type'] = 'category';
        $bindings['Page']['Title'] = $this->area->getName();

        $bindings['Area']['Id'] = $areaId;
        $bindings['Area']['Name'] = $this->area->getName();
        $bindings['Area']['Desc'] = $this->area->getAreaDescription();

        $bindings['Area']['IsTypeContent'] = $this->area->isContentArea();
        $bindings['Area']['IsTypeLinked'] = $this->area->isLinkedArea();
        $bindings['Area']['IsTypeSmart'] = $this->area->isSmartArea();

        if ($this->area->isContentArea()) {
            $bindings['Area']['FeedUrl'] = sprintf('%s?name=articles&id=%s', FN_FEEDS, $areaId);
        }

        // Wrapper IDs and classes
        $bindings['Page']['WrapperId'] = sprintf('area-index-%s', $areaId);
        $bindings['Page']['WrapperClass'] = 'area-index';

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