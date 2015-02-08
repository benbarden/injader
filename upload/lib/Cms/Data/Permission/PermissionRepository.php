<?php


namespace Cms\Data\Permission;

use Cms\Data\BaseRepository,
    Cms\Exception\Data\DataException;


class PermissionRepository extends BaseRepository
{
    const SYSTEM_ID = 1;

    public function exists($id) {}

    public function getById($id)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
            SELECT * FROM Cms_Permissions
            WHERE id = :id
        ");
        $pdoStatement->bindParam(':id', $id);
        $pdoStatement->execute();
        $dbData = $pdoStatement->fetch();
        $cmsPermission = new Permission($dbData);
        return $cmsPermission;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if record exists for '. $id, 0, $e);
        }
    }
    public function get()
    {
        return $this->getById(self::SYSTEM_ID);
    }
}