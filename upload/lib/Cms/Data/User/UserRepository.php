<?php


namespace Cms\Data\User;

use Cms\Data\BaseRepository,
    Cms\Exception\Data\DataException;


class UserRepository extends BaseRepository
{
    public function exists($id)
    {
        try {
            $pdoQuery = ('SELECT count(1) FROM Cms_Users WHERE id = :uid');
            $pdoStatement = $this->db->prepare($pdoQuery);
            $pdoStatement->bindParam(':uid', $id);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if row exists for '. $id, 0, $e);
        }
    }
    public function getById($id)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM Cms_Users
                WHERE id = :id
            ");
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsUser = new User($dbData);
            return $cmsUser;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if row exists for '. $id, 0, $e);
        }
    }
    public function saveUser(User $user)
    {

    }
} 