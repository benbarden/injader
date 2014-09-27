<?php


namespace Cms\Theme;


class Renderer
{
    const OBJECT_TYPE_AREA     = 'area';
    const OBJECT_TYPE_CATEGORY = 'category';
    const OBJECT_TYPE_ARTICLE  = 'article';
    const OBJECT_TYPE_FILE     = 'file';
    const OBJECT_TYPE_USER     = 'user';

    /**
     * @var \Cms\Core\Di\Container
     */
    private $container;

    /**
     * @var object
     */
    private $renderer;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var integer
     */
    private $itemId;

    public function __construct(
        \Cms\Core\Di\Container $container, $objectType, $itemId
    )
    {
        $this->container = $container;
        $this->objectType = $objectType;
        $this->itemId = $itemId;
    }

    public function __destruct()
    {
        unset($this->container);
        unset($this->renderer);
    }

    public function render()
    {
        $this->getRenderer();
        $themeFile = $this->renderer->getFile();
        $themeBindings = $this->renderer->getBindings();

        $engine = $this->container->getService('Theme.Engine');
        $outputHtml = $engine->render($themeFile, $themeBindings);
        print($outputHtml);
        exit;
    }

    private function getRenderer()
    {
        switch ($this->objectType) {
            case self::OBJECT_TYPE_AREA:
            case self::OBJECT_TYPE_CATEGORY:
                $this->renderer = new \Cms\Theme\User\Category($this->container, $this->itemId);
                break;
            case self::OBJECT_TYPE_ARTICLE:
                //$this->renderer = new \Cms\Theme\User\Article();
                //break;
            case self::OBJECT_TYPE_FILE:
                //$this->renderer = new \Cms\Theme\User\File();
                //break;
            case self::OBJECT_TYPE_USER:
                //$this->renderer = new \Cms\Theme\User\User();
                //break;
            default:
                throw new \Exception(sprintf('Unknown object type: %s', $this->objectType));
                break;
        }
    }
}