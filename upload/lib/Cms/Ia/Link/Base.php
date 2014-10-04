<?php


namespace Cms\Ia\Link;

use Cms\Ia\Tools\OptimiseUrl;


abstract class Base implements ILink
{
    // yoursite.com/view.php/article/1/hello-world
    const STYLE_CLASSIC = 1;

    // yoursite.com/article/1/hello-world
    const STYLE_LONG = 2;

    // yoursite.com/hello-world
    const STYLE_TITLE_ONLY = 3;

    // yoursite.com/area-name/hello-world
    const STYLE_AREA_AND_TITLE = 4;

    // yoursite.com/2009/12/31/hello-world
    const STYLE_DATE_AND_TIME = 5;

    /**
     * @var integer
     */
    protected $linkStyle;

    /**
     * @var OptimiseUrl
     */
    protected $optimiser;

    public function __construct($linkStyle, OptimiseUrl $optimiser)
    {
        $this->linkStyle = $linkStyle;
        $this->optimiser = $optimiser;
    }

    public function __destruct()
    {
        unset($this->optimiser);
    }

    protected function getGenerateFunctionName()
    {
        switch ($this->linkStyle) {
            case self::STYLE_CLASSIC;
                $functionName = 'generateLinkStyleClassic';
                break;
            case self::STYLE_LONG:
                $functionName = 'generateLinkStyleLong';
                break;
            case self::STYLE_TITLE_ONLY:
                $functionName = 'generateLinkStyleTitleOnly';
                break;
            case self::STYLE_AREA_AND_TITLE:
                $functionName = 'generateLinkStyleAreaAndTitle';
                break;
            case self::STYLE_DATE_AND_TIME:
                $functionName = 'generateLinkStyleDateAndTime';
                break;
            default:
                throw new \Exception(sprintf('Unknown link style: %s', $this->linkStyle));
                break;
        }

        return $functionName;
    }

    public function generate()
    {
        $funcGenerate = $this->getGenerateFunctionName();
        return call_user_func(array($this, $funcGenerate));
    }

    /**
     * @return string
     */
    abstract protected function generateLinkStyleClassic();

    /**
     * @return string
     */
    abstract protected function generateLinkStyleLong();

    /**
     * @return string
     */
    abstract protected function generateLinkStyleTitleOnly();

    /**
     * @return string
     */
    abstract protected function generateLinkStyleAreaAndTitle();

    /**
     * @return string
     */
    abstract protected function generateLinkStyleDateAndTime();
}