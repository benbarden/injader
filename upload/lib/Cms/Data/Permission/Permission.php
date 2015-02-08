<?php


namespace Cms\Data\Permission;


class Permission
{
    private $permissionId;
    private $name;
    private $isSystem;
    private $createArticle;
    private $publishArticle;
    private $editArticle;
    private $deleteArticle;
    private $attachFile;

    public function __construct($logData)
    {
        $this->permissionId = $logData['id'];
        $this->name = $logData['name'];
        $this->isSystem = $logData['is_system'];
        $this->createArticle = $logData['create_article'];
        $this->publishArticle = $logData['publish_article'];
        $this->editArticle = $logData['edit_article'];
        $this->deleteArticle = $logData['delete_article'];
        $this->attachFile = $logData['attach_file'];
    }

    public function getPermissionId()
    {
        return $this->permissionId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIsSystem()
    {
        return $this->isSystem;
    }

    public function getCreateArticle()
    {
        return $this->createArticle;
    }

    public function getPublishArticle()
    {
        return $this->publishArticle;
    }

    public function getEditArticle()
    {
        return $this->editArticle;
    }

    public function getDeleteArticle()
    {
        return $this->deleteArticle;
    }

    public function getAttachFile()
    {
        return $this->attachFile;
    }
}