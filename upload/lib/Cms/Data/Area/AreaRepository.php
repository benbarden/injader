<?php


namespace Cms\Data\Area;

use Cms\Data\IRepository,
    Cms\Exception\Data\DataException;



class AreaRepository implements IRepository
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }
    public function exists($id)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("SELECT count(1) FROM maj_areas WHERE id = :id");
            $pdoStatement->bindParam(':id', $id);
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
            $pdoStatement = $this->db->prepare("SELECT * FROM maj_areas WHERE id = :id");
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsArea = new Area($dbData);
            return $cmsArea;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if row exists for '. $id, 0, $e);
        }
    }

    public function getSubareas($areaId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM maj_areas
                WHERE parent_id = :id
                ORDER BY hier_left
            ");
            $pdoStatement->bindParam(':id', $areaId);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
            return $dbData;
        } catch(\PDOException $e) {
            throw new DataException("Couldn't get subareas for: ". $areaId, 0, $e);
        }
    }

    public function getTopLevel()
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM maj_areas
                WHERE parent_id = 0
                ORDER BY hier_left
            ");
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
            return $dbData;
        } catch(\PDOException $e) {
            throw new DataException("Couldn't get navigation for: ". $navType, 0, $e);
        }
    }

    public function saveArea(Area $area)
    {

    }
} 