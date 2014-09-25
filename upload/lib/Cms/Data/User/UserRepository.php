<?php


namespace Cms\Data\User;


class UserRepository implements IUserRepository
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }
    public function userExists($userId)
    {
        try {
            $pdoQuery = ('SELECT count(1) FROM maj_users WHERE id = :uid');
            $podStatement = $this->dbh->prepare($pdoQuery);
            $podStatement->bindParam(':uid', $userId);
            $podStatement->execute();
            return $podStatement->fetchColumn() >0;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if profile exists for '. $userId, 0, $e);
        }
    }
    public function getUser($userId)
    {

    }
    public function saveUser(User $user)
    {

    }
} 