<?php


namespace Cms\Data\User;


class User
{
    private $userId;
    private $username;

    public function __construct($dbData)
    {
        $this->userId = $dbData['id'];
        $this->username = $dbData['username'];
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