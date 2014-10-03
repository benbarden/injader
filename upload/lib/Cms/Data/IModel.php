<?php


namespace Cms\Data;


class IModel
{
    protected $dbData;

    protected function getFieldSafe($key, $default = "")
    {
        return isset($this->dbData[$key]) ? $this->dbData[$key] : $default;
    }
} 