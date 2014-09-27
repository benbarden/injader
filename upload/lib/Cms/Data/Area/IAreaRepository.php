<?php


namespace Cms\Data\Area;


interface IAreaRepository
{
    public function areaExists($areaId);
    public function getArea($areaId);
    public function saveArea(Area $area);
} 