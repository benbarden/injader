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
            $pdoStatement = $this->db->prepare($pdoQuery);
            $pdoStatement->bindParam(':uid', $userId);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if profile exists for '. $userId, 0, $e);
        }
    }
    public function getUser($userId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM maj_users
                WHERE id = :id
            ");
            $pdoStatement->bindParam(':id', $userId);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsUser = new User($dbData);
            return $cmsUser;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if record exists for '. $userId, 0, $e);
        }
    }
    public function saveUser(User $user)
    {

    }
} 