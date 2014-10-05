<?php

class TestUserLink extends \PHPUnit_Framework_TestCase
{
    public function testLinkStyle1()
    {
        $expected = '/index.php/profile/1/ben/';
        $mockUserRepo = new \Cms\Data\User\MockUserRepository();
        $user = $mockUserRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\UserLink(1, $iaOptimiser);
        $iaLink->setUser($user);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle2()
    {
        $expected = '/profile/1/ben/';
        $mockUserRepo = new \Cms\Data\User\MockUserRepository();
        $user = $mockUserRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\UserLink(2, $iaOptimiser);
        $iaLink->setUser($user);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle3()
    {
        $expected = '/profile/1/ben/';
        $mockUserRepo = new \Cms\Data\User\MockUserRepository();
        $user = $mockUserRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\UserLink(3, $iaOptimiser);
        $iaLink->setUser($user);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle4()
    {
        $expected = '/profile/1/ben/';
        $mockUserRepo = new \Cms\Data\User\MockUserRepository();
        $user = $mockUserRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\UserLink(4, $iaOptimiser);
        $iaLink->setUser($user);
        $this->assertEquals($expected, $iaLink->generate());
    }
    public function testLinkStyle5()
    {
        $expected = '/profile/1/ben/';
        $mockUserRepo = new \Cms\Data\User\MockUserRepository();
        $user = $mockUserRepo->getById(1);
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLink = new \Cms\Ia\Link\UserLink(5, $iaOptimiser);
        $iaLink->setUser($user);
        $this->assertEquals($expected, $iaLink->generate());
    }
}