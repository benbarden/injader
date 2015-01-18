<?php


namespace Cms\Data\UrlMapping;

use Cms\Data\BaseRepository,
    Cms\Exception\Data\DataException;


class UrlMappingRepository extends BaseRepository
{
    const TABLE_NAME = 'Cms_UrlMapping';
    const TABLE_KEY = 'id';

    public function exists($id)
    {
        try {
            $pdoQuery = "SELECT count(1) FROM ".self::TABLE_NAME." WHERE id = :id";
            $pdoStatement = $this->db->prepare($pdoQuery);
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if record exists for '. $id, 0, $e);
        }
    }

    public function getById($id)
    {

    }

    public function urlExists($url)
    {
        try {
            $pdoQuery = "SELECT count(1) FROM ".self::TABLE_NAME." WHERE relative_url = :relative_url";
            $pdoStatement = $this->db->prepare($pdoQuery);
            $pdoStatement->bindParam(':relative_url', $url);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if record exists for '. $url, 0, $e);
        }
    }

    public function getByUrl($url)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM ".self::TABLE_NAME."
                WHERE relative_url = :relative_url
            ");

            $pdoStatement->bindParam(':relative_url', $url);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            if ($dbData['id'] == null) {
                return null;
            } else {
                $model = new UrlMapping();
                $model->setupObjectFromArray($dbData);
                return $model;
            }
        } catch(\PDOException $e) {
            throw new DataException('Failed to getById for '. $url, 0, $e);
        }
    }

    public function deactivateByCategory($categoryId, $excludeRowId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                UPDATE ".self::TABLE_NAME."
                SET is_active = 'N'
                WHERE category_id = :categoryId
                AND id != :excludeRowId
            ");
            $pdoStatement->bindParam(':categoryId', $categoryId);
            $pdoStatement->bindParam(':excludeRowId', $excludeRowId);
            $pdoStatement->execute();
        } catch(\PDOException $e) {
            throw new DataException('Failed to run: deactivateByCategory', 0, $e);
        }
    }

    public function activateById($rowId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                UPDATE ".self::TABLE_NAME."
                SET is_active = 'Y'
                WHERE id = :rowId
            ");
            $pdoStatement->bindParam(':rowId', $rowId);
            $pdoStatement->execute();
        } catch(\PDOException $e) {
            throw new DataException('Failed to run: activateById', 0, $e);
        }
    }

    public function deleteAllByCategory($categoryId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                DELETE FROM ".self::TABLE_NAME."
                WHERE category_id = :category_id
            ");
            $pdoStatement->bindParam(':category_id', $categoryId);
            $pdoStatement->execute();
        } catch(\PDOException $e) {
            throw new DataException('Failed to run: deleteAllByCategory', 0, $e);
        }
    }

    public function create(UrlMapping $urlMapping)
    {
        return parent::addRecord($urlMapping);
    }

    public function update(UrlMapping $urlMapping)
    {
        parent::updateRecord($urlMapping);
    }

    public function delete(UrlMapping $urlMapping)
    {
        parent::deleteRecord($urlMapping);
    }
} 