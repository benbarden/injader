<?php


namespace Cms\Data\User;


class User
{
    private $userId;
    private $username;
    private $userGroups;

    public function __construct($dbData)
    {
        $this->userId = $dbData['id'];
        $this->username = $dbData['username'];
        $this->userGroups = $dbData['user_groups'];
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getUserGroups()
    {
        return $this->userGroups;
    }
} 