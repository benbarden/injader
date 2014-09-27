<?php


namespace Cms\Theme;


class Engine
{
    /**
     * @var array
     */
    private $pathsArray;

    /**
     * @var array
     */
    private $envArray;

    /**
     * @param string $current
     * @param integer $cache
     * @throws \Exception
     */
    public function __construct($current = "", $cache = 1)
    {
        // Validate theme path
        if (!$current) {
            throw new \Exception('Current theme not defined!');
        }
        $userThemePath = sprintf('%sthemes/user/%s', ABS_ROOT, $current);
        if (!is_dir($userThemePath)) {
            throw new \Exception(sprintf('Cannot find theme: %s', $userThemePath));
        }
        $this->pathsArray = array(
            $userThemePath,
            ABS_ROOT.'themes/system',
        );

        // Set up caching
        if ($cache == 1) {
            $this->envArray = array('cache' => ABS_ROOT.'data/cache');
        } else {
            $this->envArray = array();
        }

        // Instantiate Twig
        require_once ABS_ROOT.'/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
    }

    /**
     * @return \Twig_Environment
     */
    public function getEngine()
    {
        $loader = new \Twig_Loader_Filesystem($this->pathsArray);
        return new \Twig_Environment($loader, $this->envArray);
    }

    /**
     * @return \Twig_Environment
     */
    public function getEngineUnitTesting()
    {
        $loader = new \Twig_Loader_Array(array(
            'index.html' => '<p>Hello {{ Name }}!</p>',
        ));
        return new \Twig_Environment($loader);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        unset($this->engine);
    }
} 