<?php


namespace Cms\Access;

use Cms\Core\Di\Container;
use Cms\Data\User\User;
use Cms\Data\Permission\Permission as DataPermission;


class Permission
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var User
     */
    private $user;

    /**
     * @var DataPermission
     */
    private $permission;

    /**
     * @param Container $container
     * @param User $user
     * @return void
     */
    public function __construct(Container $container, User $user = null)
    {
        $this->container = $container;
        if ($user) {
            $this->user = $user;
        }

        $repoPermission = $this->container->getService('Repo.Permission');
        $this->permission = $repoPermission->get();
    }

    public function __destruct()
    {
        unset($this->container);
        unset($this->user);
    }

    private function userGroupMatch($allowedGroups)
    {
        if (!$this->user) return false;

        $userGroupsArray = explode("|", $this->user->getUserGroups());
        $allowedGroupsArray = explode("|", $allowedGroups);
        $isMatch = false;

        foreach ($userGroupsArray as $ug) {
            foreach ($allowedGroupsArray as $ag) {
                if ($ug == $ag) {
                    $isMatch = true;
                    break;
                }
            }
        }

        return $isMatch;
    }

    public function canCreateArticle()
    {
        return $this->userGroupMatch($this->permission->getCreateArticle());
    }

    public function canPublishArticle()
    {
        return $this->userGroupMatch($this->permission->getPublishArticle());
    }

    public function canEditAllArticles()
    {
        return $this->userGroupMatch($this->permission->getEditArticle());
    }

    public function canDeleteArticle()
    {
        return $this->userGroupMatch($this->permission->getDeleteArticle());
    }

    public function canAttachFile()
    {
        return $this->userGroupMatch($this->permission->getAttachFile());
    }
} 