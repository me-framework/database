<?php
namespace me\database;
use me\core\Component;
class Query extends Component {
    use query\BaseTrait,
        query\RelationTrait,
        query\IndexByAsArray;
    /**
     * @var \me\database\Schema Schema
     */
    public $schema;
    /**
     * @var string Class Name
     */
    public $modelClass;
    /**
     * @return array|\me\database\RecordInterface[] rows | models
     */
    public function all() {

        $connection = $this->schema->connection;
        [$sql, $params] = $this->schema->getQueryBuilder()->build($this);
        $rows       = $this->schema->database->getCommand()->fetchAll($connection, $sql, $params);

        if ($rows === false || empty($rows)) {
            return [];
        }

        if ($this->asArray) {
            return $rows;
        }

        $models = [];
        if ($this->indexBy === null) {
            foreach ($rows as $row) {
                $models[] = (new $this->modelClass())->populate($row, $this);
            }
            return $models;
        }

        foreach ($rows as $row) {
            $modelClass   = (new $this->modelClass())->populate($row, $this);
            $key          = $modelClass->{$this->indexBy};
            $models[$key] = $modelClass;
        }
        return $models;
    }
    /**
     * @return array|\me\database\RecordInterface row | model
     */
    public function one() {

        $connection = $this->schema->connection;
        [$sql, $params] = $this->schema->getQueryBuilder()->build($this);
        $row        = $this->schema->database->getCommand()->fetchOne($connection, $sql, $params);

        if ($row === false) {
            return null;
        }

        if ($this->asArray) {
            return $row;
        }

        /* @var $modelClass \me\database\RecordInterface */
        $modelClass = new $this->modelClass;
        $modelClass->populate($row, $this);

        return $modelClass;
    }
    /**
     * 
     */
    public function count($column = '*') {
        return $this->queryScalar("COUNT($column)");
    }
    /**
     * 
     */
    public function sum($column) {
        return $this->queryScalar("SUM($column)");
    }
    /**
     * 
     */
    public function average($column) {
        return $this->queryScalar("AVG($column)");
    }
    /**
     * 
     */
    public function min($column) {
        return $this->queryScalar("MIN($column)");
    }
    /**
     * 
     */
    public function max($column) {
        return $this->queryScalar("MAX($column)");
    }
    /**
     * @param \me\database\QueryBuilder $builder Query Builder
     */
    public function prepare($builder) {
        if ($this->from === null) {
            /* @var $modelClass \me\database\RecordInterface */
            $modelClass = $this->modelClass;
            $this->from = [$modelClass::tableName()];
        }
    }
    /**
     * 
     */
    protected function queryScalar($select) {
        $q = new self([
            'schema'     => $this->schema,
            'modelClass' => $this->modelClass,
            'select'     => [$select],
            'from'       => $this->from,
            'where'      => $this->where
        ]);

        $connection = $this->schema->connection;
        [$sql, $params] = $this->schema->getQueryBuilder()->build($q);
        return $this->schema->database->getCommand()->queryScalar($connection, $sql, $params);
    }
}