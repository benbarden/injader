<?php

class TestLogin extends \PHPUnit_Framework_TestCase
{
    public function testSetUser()
    {
        $mockUser = new \Cms\Data\User\MockUserRepository();
        $user = $mockUser->getById(1);
        $accessLogin = new \Cms\Access\Login;
        $accessLogin->setLoggedInUser($user);
        $this->assertEquals('Ben', $accessLogin->getLoggedInUser()->getUsername());
    }
}