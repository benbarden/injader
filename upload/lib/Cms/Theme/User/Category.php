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
        //$this->bindings = array('Cms' => array('Head' => array('Title' => 'Demo')));

        $areaRepo = $container->getService('Repo.Area');
        $this->area = $areaRepo->getArea($areaId);

        $this->setupBindings();
    }

    private function setupBindings()
    {
        $bindings = array();

        //$bindings['Area'] = $this->area;
        $bindings['Area']['Id'] = $this->area->getAreaId();
        $bindings['Area']['Name'] = $this->area->getName();

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