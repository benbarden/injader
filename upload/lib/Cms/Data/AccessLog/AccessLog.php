<?php


namespace Cms\Data\AccessLog;


class AccessLog
{
    private $logId;
    private $userId;
    private $detail;
    private $tag;
    private $logDate;
    private $ipAddress;

    public function __construct($logData)
    {
        $this->logId  = $logData['id'];
        $this->userId = $logData['user_id'];
        $this->detail = $logData['detail'];
        $this->tag = $logData['tag'];
        $this->logDate = $logData['log_date'];
        $this->ipAddress = $logData['ip_address'];
    }

    public function getLogId()
    {
        return $this->logId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getDetail()
    {
        return $this->detail;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getLogDate()
    {
        return $this->logDate;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }
}