<?php


namespace Cms\Data\AccessLog;

use Cms\Data\BaseRepository,
    Cms\Exception\Data\DataException;


class AccessLogRepository extends BaseRepository
{
    public function exists($id) {}
    public function getById($id) {}

    public function purgeEntries($logLimit)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT id FROM Cms_AccessLog
                ORDER BY id DESC LIMIT $logLimit, 1
            ");
            $pdoStatement->execute();
            $maxRowId = (int) $pdoStatement->fetchColumn();
            if (!$maxRowId) return null;
            // delete with this row id
            $pdoStatement = $this->db->prepare("
                DELETE FROM Cms_AccessLog
                WHERE id BETWEEN 1 AND :maxRowId
            ");
            $pdoStatement->bindParam(':maxRowId', $maxRowId, \PDO::PARAM_INT);
            $pdoStatement->execute();
        } catch(\PDOException $e) {
            throw new DataException('Failed to purge access log entries', 0, $e);
        }
    }

    public function saveAccessLog(AccessLog $accessLog)
    {

    }
} 