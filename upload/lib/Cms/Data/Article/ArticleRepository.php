<?php


namespace Cms\Data\Article;

use Cms\Data\IRepository;


class ArticleRepository implements IRepository
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }
    public function exists($id)
    {
        try {
            $pdoQuery = ('SELECT count(1) FROM maj_content WHERE id = :id');
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
                SELECT * FROM maj_content
                WHERE id = :id
            ");
            $pdoStatement->bindParam(':id', $id);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsSession = new Article($dbData);
            return $cmsSession;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if record exists for '. $id, 0, $e);
        }
    }
} 