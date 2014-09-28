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
     * @var string
     */
    private $publicThemePath;

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

        // Save the theme path
        $this->publicThemePath = sprintf('%sthemes/user/%s/', URL_ROOT, $current);

        // Instantiate Twig
        require_once ABS_ROOT.'/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
    }

    /**
     * @return string
     */
    public function getPublicThemePath()
    {
        return $this->publicThemePath;
    }

    private function setupFunctions($twig)
    {
        $function = new \Twig_SimpleFunction('cmsBlock', array($this, 'cmsBlock'), array(
            'is_safe' => array('html'),
            'needs_environment' => true,
            'needs_context' => true
        ));
        $twig->addFunction($function);
        return $twig;
    }

    public function cmsBlock(\Twig_Environment $twig, $context, $blockFile)
    {
        $urlThemeRoot = $context['URL']['ThemeRoot'];
        $absSysBlockPath  = sprintf(ABS_ROOT.'themes/system/blocks/%s.twig', $blockFile);
        $absUserBlockPath = sprintf(ABS_ROOT.$urlThemeRoot.'blocks/%s.twig', $blockFile);
        $relBlockPath = sprintf('blocks/%s.twig', $blockFile);
        if (file_exists($absSysBlockPath) || file_exists($absUserBlockPath)) {
            return $twig->render($relBlockPath, $context);
        } else {
            return sprintf('<p><strong>MISSING: %s</strong></p>', $blockFile);
        }
    }

    /**
     * @return \Twig_Environment
     */
    public function getEngine()
    {
        $loader = new \Twig_Loader_Filesystem($this->pathsArray);
        $twig = new \Twig_Environment($loader, $this->envArray);
        $twig = $this->setupFunctions($twig);
        return $twig;
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