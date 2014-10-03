<?php


namespace Cms\Data;


abstract class BaseRepository implements IRepository
{
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function __destruct()
    {
        unset($this->db);
    }
}