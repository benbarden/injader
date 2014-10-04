<?php


namespace Cms\Ia\Link;

use Cms\Data\Area\Area,
    Cms\Data\Article\Article;


class AreaLink extends Base
{
    /**
     * @var Area
     */
    private $area;

    public function __destruct()
    {
        unset($this->area);
        parent::__destruct();
    }

    public function setArea(Area $area)
    {
        $this->area = $area;
    }

    private function getOptimisedAreaUrl()
    {
        return $this->optimiser->optimise($this->area->getName());
    }

    /**
     * view.php/area/1/home/
     * @return string
     */
    protected function generateLinkStyleClassic()
    {
        return sprintf('view.php/area/%s/%s/',
            $this->area->getAreaId(), $this->getOptimisedAreaUrl());
    }

    /**
     * area/1/home/
     * @return string
     */
    protected function generateLinkStyleLong()
    {
        return sprintf('area/%s/%s/',
            $this->area->getAreaId(), $this->getOptimisedAreaUrl());
    }

    /**
     * area-name/
     * @return string
     */
    protected function generateLinkStyleTitleOnly()
    {
        return $this->getOptimisedAreaUrl().'/';
    }

    /**
     * area-name/
     * NOTE: as there is no article in the link, this is the same as Title Only
     * @return string
     */
    protected function generateLinkStyleAreaAndTitle()
    {
        return $this->getOptimisedAreaUrl().'/';
    }

    /**
     * area-name/
     * NOTE: as there is no article in the link, this is the same as Title Only
     * @return string
     */
    protected function generateLinkStyleDateAndTime()
    {
        return $this->getOptimisedAreaUrl().'/';
    }
}