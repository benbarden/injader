<?php


namespace Cms\Data;


use Cms\Exception\Data\DataException;

abstract class BaseRepository implements IRepository
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var array
     */
    private $dbFields;

    /**
     * @var array
     */
    private $dbValues;

    /**
     * @var array
     */
    private $dbObjectVars;

    /**
     * @var \PDOStatement
     */
    private $pdoStatement;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->dbFields = array();
        $this->dbValues = array();
        $this->dbObjectVars = array();
    }

    public function __destruct()
    {
        unset($this->db);
    }

    private function validateDefinitions($model)
    {
        if (!method_exists($model, 'getDbDefinitions')) {
            throw new DataException('Object does not implement method: getDbDefinitions');
        }

        $dbDefinitions = $model->getDbDefinitions();
        if (!isset($dbDefinitions['core'])) {
            throw new DataException('Missing definition: core');
        }
        if (!isset($dbDefinitions['core']['tableName'])) {
            throw new DataException('Missing definition: tableName');
        }
        if (!isset($dbDefinitions['core']['tableKey'])) {
            throw new DataException('Missing definition: tableKey');
        }
        if (!isset($dbDefinitions['fields'])) {
            throw new DataException('Missing definition: fields');
        }
    }

    private function buildInsertSql($table)
    {
        $pdoFields = '';
        foreach ($this->dbObjectVars as $value) {
            if ($pdoFields) {
                $pdoFields .= ',';
            }
            $pdoFields .= ':'.$value;
        }

        $insertSql = "
            INSERT INTO $table(".implode(', ', $this->dbFields).")
            VALUES($pdoFields)
        ";

        return $insertSql;
    }

    private function buildUpdateSql($table, $key, $updateVar)
    {
        $updateSql = "UPDATE $table SET ";
        $counter = 0;
        foreach ($this->dbValues as $item) {
            if ($counter > 0) {
                $updateSql .= ', ';
            }
            $field = $item['field'];
            $objectVar = $item['objectVar'];
            $updateSql .= sprintf('%s = :%s', $field, $objectVar);
            $counter++;
        }

        $updateSql .= sprintf(' WHERE %s = :%s', $key, $updateVar);

        return $updateSql;
    }

    private function buildDeleteSql($table, $key, $deleteVar)
    {
        $deleteSql = "DELETE FROM $table WHERE $key = :".$deleteVar;
        return $deleteSql;
    }

    private function bindParams()
    {
        foreach ($this->dbValues as $item) {
            $this->pdoStatement->bindParam(':'.$item['objectVar'], $item['value']);
        }
    }

    protected function addRecord($model)
    {
        $this->validateDefinitions($model);
        $dbDefinitions = $model->getDbDefinitions();
        $table = $dbDefinitions['core']['tableName'];
        $key = $dbDefinitions['core']['tableKey'];

        // Prepare db field names
        foreach ($dbDefinitions['fields'] as $field => $defs) {
            // for addRecord, ignore the key
            if ($field == $key) continue;

            $dbValue = call_user_func(array($model, $defs['classMethod']));
            $objectVar = $defs['objectVar'];

            $this->dbFields[] = $field;
            $this->dbValues[] = array('field' => $field, 'value' => $dbValue, 'objectVar' => $objectVar);
            $this->dbObjectVars[] = $objectVar;
        }

        $insertSql = $this->buildInsertSql($table);

        try {
            $this->pdoStatement = $this->db->prepare($insertSql);
            $this->bindParams();
            $this->pdoStatement->execute();
        } catch(\PDOException $e) {
            $errorInfo = $this->pdoStatement->errorInfo();
            if ($errorInfo) {
                $errorMsg = $errorInfo[2];
            }
            throw new DataException('Failed to run addRecord: '.$errorMsg);
        }
    }

    protected function updateRecord($model)
    {
        $this->validateDefinitions($model);
        $dbDefinitions = $model->getDbDefinitions();
        $table = $dbDefinitions['core']['tableName'];
        $key = $dbDefinitions['core']['tableKey'];

        // Prepare db field names
        $updateId = null;
        $updateVar = null;
        foreach ($dbDefinitions['fields'] as $field => $defs) {

            $dbValue = call_user_func(array($model, $defs['classMethod']));
            $objectVar = $defs['objectVar'];
            if ($field == $key) {
                $updateId = $dbValue;
                $updateVar = $objectVar;
                continue;
            }

            $this->dbFields[] = $field;
            $this->dbValues[] = array('field' => $field, 'value' => $dbValue, 'objectVar' => $objectVar);
            $this->dbObjectVars[] = $objectVar;
        }

        if (!$updateId) {
            throw new DataException('Cannot update: Row id not in array');
        }
        $updateSql = $this->buildUpdateSql($table, $key, $updateVar);

        try {
            $this->pdoStatement = $this->db->prepare($updateSql);
            $this->bindParams();
            $this->pdoStatement->bindParam(':'.$updateVar, $updateId);
            $this->pdoStatement->execute();
        } catch(\PDOException $e) {
            $errorInfo = $this->pdoStatement->errorInfo();
            if ($errorInfo) {
                $errorMsg = $errorInfo[2];
            }
            throw new DataException('Failed to run updateRecord: '.$errorMsg);
        }
    }

    protected function deleteRecord($model)
    {
        $this->validateDefinitions($model);
        $dbDefinitions = $model->getDbDefinitions();
        $table = $dbDefinitions['core']['tableName'];
        $key = $dbDefinitions['core']['tableKey'];

        // Prepare db field names
        $deleteId = null;
        $deleteVar = null;
        foreach ($dbDefinitions['fields'] as $field => $defs) {

            $dbValue = call_user_func(array($model, $defs['classMethod']));
            $objectVar = $defs['objectVar'];
            if ($field == $key) {
                $deleteId = $dbValue;
                $deleteVar = $objectVar;
            }
        }

        if (!$deleteId) {
            throw new DataException('Cannot delete: Row id not in array');
        }
        $deleteSql = $this->buildDeleteSql($table, $key, $deleteVar);

        try {
            $this->pdoStatement = $this->db->prepare($deleteSql);
            $this->pdoStatement->bindParam(':'.$deleteVar, $deleteId);
            $this->pdoStatement->execute();
        } catch(\PDOException $e) {
            $errorInfo = $this->pdoStatement->errorInfo();
            if ($errorInfo) {
                $errorMsg = $errorInfo[2];
            }
            throw new DataException('Failed to run deleteRecord: '.$errorMsg);
        }
    }
}