<?php


namespace Cms\Core\Di;


class ServiceLocator implements IServiceLocator
{
    protected $services = array();

    public function set($name, $service)
    {
        if (!is_object($service)) {
            throw new \Exception("ServiceLocator only supports objects.");
        }
        if (!in_array($service, $this->services, true)) {
            $this->services[$name] = $service;
        }
        return $this;
    }

    public function get($name)
    {
        if (!isset($this->services[$name])) {
            throw new \Exception("The service $name has not been registered.");
        }
        return $this->services[$name];
    }

    public function has($name)
    {
        return isset($this->services[$name]);
    }

    public function remove($name)
    {
        if (isset($this->services[$name])) {
            unset($this->services[$name]);
        }
        return $this;
    }

    public function clear()
    {
        $this->services = array();
        return $this;
    }
} 