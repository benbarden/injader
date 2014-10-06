<?php


namespace Cms\Data\Area;

use Cms\Data\IRepository,
    Cms\Exception\Data\DataException;


class MockAreaRepository implements IRepository
{
    public function exists($id)
    {
        return $id == 1;
    }
    public function getById($id)
    {
        if ($this->exists($id)) {
            return new Area(array('id' => $id, 'name' => 'Home'));
        } else {
            throw new DataException(sprintf('Area %s does not exist.', $id));
        }
    }
    public function saveArea(Area $area)
    {
        // @todo
    }
} 