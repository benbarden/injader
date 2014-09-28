<?php


namespace Cms\Ia;


class Link
{
    const STYLE_CLASSIC = 1;
    const STYLE_LONG = 2;
    const STYLE_TITLE_ONLY = 3;
    const STYLE_AREA_AND_TITLE = 4;
    const STYLE_DATE_AND_TIME = 5;

    /**
     * @var integer
     */
    private $linkStyle;

    public function __construct($linkStyle)
    {
        $this->linkStyle = $linkStyle;
    }
} 