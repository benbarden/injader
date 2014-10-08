<?php


namespace Cms\Data\Article;

use Cms\Data\BaseRepository,
    Cms\Exception\Data\DataException;


class ArticleRepository extends BaseRepository
{
    public function exists($id)
    {
        try {
            $pdoQuery = ('SELECT count(1) FROM maj_content WHERE id = :id');
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
                SELECT * FROM maj_content
                WHERE id = :id
            ");
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsSession = new Article($dbData);
            return $cmsSession;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if record exists for '. $id, 0, $e);
        }
    }

    public function countByArea($areaId)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT count(*) FROM maj_content
                WHERE content_area_id = :areaId
                AND content_status = 'Published'
            ");
            $pdoStatement->bindParam(':areaId', $areaId);
            $pdoStatement->execute();
            $count = $pdoStatement->fetch();
            return $count;
        } catch(\PDOException $e) {
            throw new DataException('getByArea: Failed', 0, $e);
        }
    }

    public function getByArea($areaId, $limit = 25, $offset = 0, $sortField = "create_date", $sortDirection = "DESC")
    {
        try {

            switch (strtolower($sortField)) {
                case "author_name":   $pdoSortField = "username";      break;
                case "create_date":   $pdoSortField = "create_date";   break;
                case "last_updated":  $pdoSortField = "last_updated";  break;
                case "article_title": $pdoSortField = "title";         break;
                case "random":        $pdoSortField = "rand()";        break;
                case "custom":        $pdoSortField = "article_order"; break;
                default:              $pdoSortField = "create_date";   break;
            }
            switch (strtolower($sortDirection)) {
                case "asc":  $pdoSortDirection = "ASC";  break;
                case "desc": $pdoSortDirection = "DESC"; break;
                default:     $pdoSortDirection = "DESC"; break;
            }

            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM maj_content
                WHERE content_area_id = :areaId
                ORDER BY $pdoSortField $pdoSortDirection
                LIMIT :limit OFFSET :offset
            ");
            $pdoStatement->bindParam(':areaId', $areaId);
            $pdoStatement->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
            $pdoStatement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
            return $dbData;
        } catch(\PDOException $e) {
            throw new DataException('getByArea: Failed', 0, $e);
        }
    }
} 