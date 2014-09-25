<?php


namespace Cms\Theme;


class Binding
{
    private $bindings;

    public function set($key, $value)
    {
        $this->bindings[$key] = $value;
    }

    public function get($key)
    {
        return $this->bindings[$key];
    }

    public function has($key)
    {
        return isset($this->bindings[$key]);
    }

    public function remove($key)
    {
        unset($this->bindings[$key]);
    }
} 