<?php


namespace Cms\Data\User;

use Cms\Data\IRepository,
    Cms\Exception\Data\DataException;


class MockUserRepository implements IRepository
{
    public function exists($id)
    {
        return $id == 1;
    }
    public function getById($id)
    {
        if ($this->exists($id)) {
            return new User(array('id' => $id, 'username' => 'Ben'));
        } else {
            throw new DataException(sprintf('User %s does not exist.', $id));
        }
    }
    public function saveUser(User $user)
    {
        // @todo
    }
} 