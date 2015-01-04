<?php


namespace Cms\Theme\User;

use Cms\Core\Di\Container;


class Homepage
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $themeFile;

    /**
     * @var array
     */
    private $bindings;

    /**
     * @var array
     */
    private $params;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->themeFile = 'core/homepage.twig';
        $this->params = array();
    }

    public function __destruct()
    {
        unset($this->container);
    }

    public function setupBindings()
    {
        $bindings = array();

        $bindings['Page']['Type'] = 'homepage';
        $bindings['Page']['Title'] = 'Home';

        // Wrapper IDs and classes
        $bindings['Page']['WrapperId'] = 'homepage';
        $bindings['Page']['WrapperClass'] = 'homepage';

        $this->bindings = $bindings;
    }

    public function getFile()
    {
        return $this->themeFile;
    }

    public function getBindings()
    {
        return $this->bindings;
    }
} 