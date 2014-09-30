<?php

class TestUser extends \PHPUnit_Framework_TestCase
{
    public function testMockUserId()
    {
        $mockUser = new \Cms\Data\User\MockUserRepository();
        $user = $mockUser->getUser(1);
        $this->assertEquals(1, $user->getUserId());
    }
    public function testMockUsername()
    {
        $mockUser = new \Cms\Data\User\MockUserRepository();
        $user = $mockUser->getUser(1);
        $this->assertEquals('Ben', $user->getUsername());
    }
}