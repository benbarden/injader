<?php


namespace Cms\Theme;


abstract class Base implements IRenderer
{
    // Theme locations
    const THEME_LOC_INDEX   = 'ThemeLocationIndex';
    const THEME_LOC_PAGE    = 'ThemeLocationPage';
    const THEME_LOC_PROFILE = 'ThemeLocationProfile';
    const THEME_LOC_DEFAULT = 'ThemeLocationDefault';

    // Storage
    protected $themeFile;
}