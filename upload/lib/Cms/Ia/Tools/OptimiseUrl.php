<?php


namespace Cms\Ia\Tools;


class OptimiseUrl
{
    /**
     * @param string $input
     * @return string
     */
    public function optimise($input)
    {
        $output = $input;
        $output = strtolower($output);
        $output = preg_replace('/[^a-z0-9\- ]/i', '', $output);
        $output = trim($output);
        $output = str_replace(' ', '-', $output);
        return $output;
    }
} 