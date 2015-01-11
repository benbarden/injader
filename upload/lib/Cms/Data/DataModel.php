<?php


namespace Cms\Data;


class DataModel
{
    protected $dbDefinitions = array();

    public function getDbDefinitions()
    {
        return $this->dbDefinitions;
    }

    public function setupObjectFromArray($dbData)
    {
        foreach ($dbData as $key => $value) {
            if (isset($this->dbDefinitions['fields'][$key])) {
                $classMethod = $this->dbDefinitions['fields'][$key]['classMethod'];
                if (substr($classMethod, 0, 3) == 'get') {
                    $classMethod = 'set'.substr($classMethod, 3);
                }
                call_user_func(array($this, $classMethod), $value);
            }
        }
    }

} 