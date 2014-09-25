<?php


namespace Cms\Data\User;


class User
{
    private $userId;
    private $username;

    public function __construct($userId, $username)
    {
        $this->userId = $userId;
        $this->username = $username;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }
} 