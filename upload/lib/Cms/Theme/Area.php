<?php


namespace Cms\Theme;


class Area extends Base
{
    public function generateOutput($currentTheme)
    {
        $themeFile = sprintf('%s/core/area.twig', $currentTheme);
        $this->themeFile = $themeFile;

        $bindings = array('Cms' => array('Seo' => array('Title' => 'Demo')));
        global $cmsTemplateEngine;
        $htmlOutput = $cmsTemplateEngine->render($themeFile, $bindings);
        return $htmlOutput;
    }
} 