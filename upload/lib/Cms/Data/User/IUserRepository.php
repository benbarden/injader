<?php


namespace Cms\Data\User;


interface IUserRepository
{
    public function userExists($userId);
    public function getUser($userId);
    public function saveUser(User $user);
} 