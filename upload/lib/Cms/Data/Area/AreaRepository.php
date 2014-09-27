<?php


namespace Cms\Data\Area;


class AreaRepository implements IAreaRepository
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function areaExists($areaId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("SELECT count(1) FROM maj_areas WHERE id = :id");
            $pdoStatement->bindParam(':id', $areaId);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if row exists for '. $areaId, 0, $e);
        }
    }

    public function getArea($areaId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("SELECT * FROM maj_areas WHERE id = :id");
            $pdoStatement->bindParam(':id', $areaId);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsArea = new Area($dbData);
            return $cmsArea;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if row exists for '. $areaId, 0, $e);
        }
    }

    public function saveArea(Area $area)
    {

    }
} 