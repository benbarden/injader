<?php


namespace Cms\Core\Di;


interface IServiceLocator
{
    public function set($name, $service);
    public function get($name);
    public function has($name);
    public function remove($name);
    public function clear();
}