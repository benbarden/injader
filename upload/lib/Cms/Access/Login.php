<?php


namespace Cms\Access;

use Cms\Data\User\User;


class Login
{
    /**
     * @var User
     */
    private $loggedInUser;

    public function getCookie()
    {
        return isset($_COOKIE['IJ-Login']) ? $_COOKIE['IJ-Login'] : null;
    }

    public function getLoggedInUser()
    {
        return $this->loggedInUser;
    }
}