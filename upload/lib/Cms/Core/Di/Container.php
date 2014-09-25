<?php


namespace Cms\Core\Di;


class Container
{
    private $serviceLocator;

    public function __construct(IServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __destruct()
    {
        $this->serviceLocator->clear();
        unset($this->serviceLocator);
    }

    public function getService($service)
    {
        return $this->serviceLocator->get($service);
    }

}