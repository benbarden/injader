<?php

class TestEngine extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $cmsThemeEngine = new \Cms\Theme\Engine("injader");
        $utEngine = $cmsThemeEngine->getEngineUnitTesting();
        $varExpected = '<p>Hello Ben!</p>';
        $varActual = $utEngine->render('index.html', array('Name' => 'Ben'));
        $this->assertEquals($varExpected, $varActual);
    }

}