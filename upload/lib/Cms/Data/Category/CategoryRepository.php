<?php


namespace Cms\Data\Category;

use Cms\Data\BaseRepository,
    Cms\Exception\Data\DataException;


class CategoryRepository extends BaseRepository
{
    const TABLE_NAME = 'Cms_Categories';
    const TABLE_KEY = 'id';

    public function exists($id)
    {
        try {
            $pdoQuery = ("SELECT count(1) FROM ".self::TABLE_NAME." WHERE id = :id");
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
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM ".self::TABLE_NAME."
                WHERE id = :id
            ");
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            if ($dbData['id'] == null) {
                return null;
            } else {
                $model = new Category($dbData);
                return $model;
            }
        } catch(\PDOException $e) {
            throw new DataException('Failed to getById for '. $id, 0, $e);
        }
    }

    public function getTopLevel()
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM ".self::TABLE_NAME."
                WHERE parent_id IS NULL
                ORDER BY name
            ");
            $pdoStatement->execute();
            return $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            throw new DataException('Failed: getTopLevel', 0, $e);
        }
    }

    public function getByParent($parentId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM ".self::TABLE_NAME."
                WHERE parent_id = :parentId
                ORDER BY name
            ");
            $pdoStatement->bindParam(':parentId', $parentId);
            $pdoStatement->execute();
            return $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            throw new DataException('Failed: getTopLevel', 0, $e);
        }
    }

    public function save(Category $category)
    {
        if ($category->getCategoryId()) {
            parent::updateRecord($category);
        } else {
            return parent::addRecord($category);
        }
    }

    public function delete(Category $category)
    {
        $categoryId = $category->getCategoryId();
        if (!$categoryId) {
            throw new DataException('Cannot delete record - id not set');
        }

        parent::deleteRecord($category);
    }
} 