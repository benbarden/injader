<?php


namespace Cms\Data\UserSession;

use Cms\Data\BaseRepository;


class UserSessionRepository extends BaseRepository
{
    public function exists($id)
    {
        try {
            $pdoQuery = ('SELECT count(1) FROM maj_user_sessions WHERE session_id = :id');
            $pdoStatement = $this->db->prepare($pdoQuery);
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if record exists for '. $id, 0, $e);
        }
    }
    public function getById($id)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM maj_user_sessions
                WHERE session_id = :id
            ");
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsSession = new UserSession($dbData);
            return $cmsSession;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if record exists for '. $id, 0, $e);
        }
    }

    public function getValidUserId($id)
    {
        $session = $this->getById($id);
        if (!$session) return null;

        return $session->getUserId();
    }
} 