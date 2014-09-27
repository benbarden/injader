<?php


namespace Cms\Data\Area;


class Area
{
    private $areaId;
    private $name;

    public function __construct($areaData)
    {
        $this->areaId = $areaData['id'];
        $this->name = $areaData['name'];
    }

    public function getAreaId()
    {
        return $this->areaId;
    }

    public function getName()
    {
        return $this->name;
    }
} 