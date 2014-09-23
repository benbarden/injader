<?php


namespace Cms\Theme;


use Cms\Interfaces\Renderer;

abstract class Base implements Renderer
{
    // Theme locations
    const THEME_LOC_INDEX   = 'ThemeLocationIndex';
    const THEME_LOC_PAGE    = 'ThemeLocationPage';
    const THEME_LOC_PROFILE = 'ThemeLocationProfile';
    const THEME_LOC_DEFAULT = 'ThemeLocationDefault';

    // Storage
    protected $themeFile;
}