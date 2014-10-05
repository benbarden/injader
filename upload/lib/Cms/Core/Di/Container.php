<?php


namespace Cms\Core\Di;


class Container
{
    private $serviceLocator;

    private $settings;

    public function __construct(IServiceLocator $serviceLocator)
    {
        $this->settings = array();
        $this->serviceLocator = $serviceLocator;
    }

    public function __destruct()
    {
        unset($this->settings);
        $this->serviceLocator->clear();
        unset($this->serviceLocator);
    }

    public function getService($service)
    {
        return $this->serviceLocator->get($service);
    }

    public function hasService($service)
    {
        return $this->serviceLocator->has($service);
    }

    public function saveSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function getSetting($key)
    {
        if (!array_key_exists($key, $this->settings)) {
            throw new \Exception(sprintf('Cannot find setting with key: %s', $key));
        }

        return $this->settings[$key];
    }

    public function hasSetting($key)
    {
        return array_key_exists($key, $this->settings);
    }
}