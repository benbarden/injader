<?php


namespace Cms\Theme\User;


class Category
{
    /**
     * @var \Cms\Data\Area\Area
     */
    private $area;

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
    private $subareaData;

    public function __construct(\Cms\Core\Di\Container $container, $areaId)
    {
        $this->themeFile = 'core/category.twig';

        $areaRepo = $container->getService('Repo.Area');
        /* @var \Cms\Data\Area\AreaRepository $areaRepo */
        $this->area = $areaRepo->getArea($areaId);

        // Subareas
        $this->subareaData = $areaRepo->getSubareas($areaId);

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

        // Subareas
        if ($this->subareaData) {
            foreach ($this->subareaData as $subareaItem) {
                $subareaObject = new \Cms\Data\Area\Area($subareaItem);
                /* @var \Cms\Data\Area\Area $subareaObject */
                $subareaRow = array(
                    'Id' => $subareaObject->getAreaId(),
                    'Name' => $subareaObject->getName(),
                    'Desc' => $subareaObject->getAreaDescription()
                );
                $bindings['Area']['Subareas'][] = $subareaRow;
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