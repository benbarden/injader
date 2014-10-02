<?php


namespace Cms\Ia\Tools;


class OptimiseUrl
{
    public function optimise($input)
    {
        $output = $input;
        $output = strtolower($output);
        $output = preg_replace('/[^a-z\- ]/i', '', $output);
        $output = trim($output);
        $output = str_replace(' ', '-', $output);
        return $output;
    }
} 