<?php


namespace Cms\Data\UserSession;


class UserSession
{
    private $id;
    private $sessionId;
    private $userId;
    private $ipAddress;
    private $userAgent;
    private $loginDate;
    private $expiryDate;

    public function __construct($dbData)
    {
        $this->sessionId = $dbData['session_id'];
        $this->userId = $dbData['user_id'];
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

}