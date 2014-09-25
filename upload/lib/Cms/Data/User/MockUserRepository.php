<?php


namespace Cms\Data\User;


class MockUserRepository implements IUserRepository
{
    public function userExists($userId)
    {
        return $userId == 1;
    }
    public function getUser($userId)
    {
        if ($this->userExists($userId)) {
            return new User($userId, 'Ben');
        } else {
            throw new \Exception(sprintf('User %s does not exist.', $userId));
        }
    }
    public function saveUser(User $user)
    {
        // @todo
    }
} 