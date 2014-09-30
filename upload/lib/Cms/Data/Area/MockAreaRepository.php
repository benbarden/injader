<?php


namespace Cms\Data\Area;


class MockAreaRepository implements IAreaRepository
{
    public function areaExists($areaId)
    {
        return $areaId == 1;
    }
    public function getArea($areaId)
    {
        if ($this->areaExists($areaId)) {
            return new Area($areaId, 'Home');
        } else {
            throw new \Exception(sprintf('Area %s does not exist.', $areaId));
        }
    }
    public function saveArea(Area $area)
    {
        // @todo
    }
} 