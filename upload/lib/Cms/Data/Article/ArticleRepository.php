<?php


namespace Cms\Data\Article;

use Cms\Data\BaseRepository,
    Cms\Exception\Data\DataException;


class ArticleRepository extends BaseRepository
{
    public function exists($id)
    {
        try {
            $pdoQuery = ('SELECT count(1) FROM Cms_Content WHERE id = :id');
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
                SELECT * FROM Cms_Content
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
                SELECT count(*) FROM Cms_Content
                WHERE content_area_id = :areaId
                AND content_status = 'Published'
            ");
            $pdoStatement->bindParam(':areaId', $areaId);
            $pdoStatement->execute();
            $count = $pdoStatement->fetchColumn();
            return $count;
        } catch(\PDOException $e) {
            throw new DataException('getByArea: Failed', 0, $e);
        }
    }

    public function getByAreaPublic($areaId, $limit = 25, $offset = 0, $sortField = "create_date", $sortDirection = "DESC")
    {
        return $this->getContent('public', $areaId, $limit, $offset, $sortField, $sortDirection);
    }

    public function getByAreaAll($areaId, $limit = 25, $offset = 0, $sortField = "create_date", $sortDirection = "DESC")
    {
        return $this->getContent('all', $areaId, $limit, $offset, $sortField, $sortDirection);
    }

    public function getRecentPublic($limit = 5)
    {
        return $this->getContent('public', 0, $limit);
    }

    private function getContent($mode, $areaId = 0, $limit = 25, $offset = 0, $sortField = "create_date", $sortDirection = "DESC")
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
            switch (strtolower($mode)) {
                case "public": $pdoAccessSql = "AND content_status = 'Published'"; break;
                case "all":    $pdoAccessSql = ""; break;
                default:       $pdoAccessSql = "AND content_status = 'Published'"; break;
            }

            if ($areaId) {
                $pdoAreaSql = 'AND content_area_id = :areaId';
            } else {
                $pdoAreaSql = '';
            }

            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM Cms_Content
                WHERE 1
                $pdoAreaSql
                $pdoAccessSql
                ORDER BY $pdoSortField $pdoSortDirection
                LIMIT :limit OFFSET :offset
            ");
            if ($areaId) {
                $pdoStatement->bindParam(':areaId', $areaId);
            }
            $pdoStatement->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
            $pdoStatement->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
            return $dbData;
        } catch(\PDOException $e) {
            throw new DataException('getByArea: Failed', 0, $e);
        }
    }

    public function getArchivesSummary($year = 0, $month = 0)
    {
        $year = (int) $year;
        $month = (int) $month;
        if ($month) {
            if ($month < 10) {
                $month = '0'.$month;
            }
        }

        if (($year > 0) && ($month > 0)) {
            $havingClause = "HAVING content_yyyy_mm = '$year-$month'";
        } elseif ($year > 0) {
            $havingClause = "HAVING content_yyyy = '$year'";
        } else {
            $havingClause = "";
        }

        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT DATE_FORMAT(create_date, '%Y-%m') AS content_yyyy_mm,
                DATE_FORMAT(create_date, '%Y') AS content_yyyy,
                DATE_FORMAT(create_date, '%m') AS content_mm,
                DATE_FORMAT(create_date, '%M %Y') AS content_date_desc,
                count(*) AS count
                FROM Cms_Content
                WHERE content_status = 'Published'
                GROUP BY content_yyyy_mm
                $havingClause
                ORDER BY create_date DESC
            ");
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
            return $dbData;
        } catch(\PDOException $e) {
            throw new DataException('getArchivesSummary: Failed', 0, $e);
        }
    }

    public function getArchivesContent($year = 0, $month = 0)
    {
        $year = (int) $year;
        $month = (int) $month;
        if ($month) {
            if ($month < 10) {
                $month = '0'.$month;
            }
        }

        if (($year > 0) && ($month > 0)) {
            $whereClause = "AND DATE_FORMAT(create_date, '%Y-%m') = '$year-$month'";
        } elseif ($year > 0) {
            $whereClause = "AND DATE_FORMAT(create_date, '%Y') = '$year'";
        } else {
            $whereClause = "";
        }

        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT DATE_FORMAT(create_date, '%M %Y') AS content_yyyy_mm,
                DATE_FORMAT(create_date, '%Y') AS content_yyyy,
                DATE_FORMAT(create_date, '%m') AS content_mm,
                id, content_area_id, title, permalink,
                create_date AS content_date_full
                FROM Cms_Content
                WHERE content_status = 'Published'
                $whereClause
                ORDER BY create_date DESC
            ");
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
            return $dbData;
        } catch(\PDOException $e) {
            throw new DataException('getArchivesContent: Failed', 0, $e);
        }
    }

}