<?php
namespace me\database\mysql;
use PDO;
use me\database\Schema;
use me\database\TableSchema;
use me\database\ColumnSchema;
class MysqlSchema extends Schema {
    /**
     * 
     */
    public function getQueryBuilder() {
        if (is_null($this->_queryBuilder)) {
            $this->_queryBuilder = new MysqlQueryBuilder();
        }
        return $this->_queryBuilder;
    }
    /**
     * 
     */
    public function getTableSchema($table_name) {
        if (!isset($this->_tableSchema[$table_name])) {
            $this->_tableSchema[$table_name] = $this->loadTableSchema($table_name);
        }
        return $this->_tableSchema[$table_name];
    }
    /**
     * 
     */
    protected function loadTableSchema($name) {
        $table = new TableSchema();
        $this->getQueryBuilder()->resolveTableNames($table, $name);
        $this->findColumns($table);
        $this->findConstraints($table);
        return $table;
    }
    /**
     * @param \me\database\TableSchema $table Table Schema
     */
    protected function findColumns($table) {
        $connection = $this->connection;
        [$sql, $params] = $this->getQueryBuilder()->findColumns($table);
        $columns    = $this->database->getCommand()->fetchAll($connection, $sql, $params);
        foreach ($columns as $info) {
            if ($connection->pdo->getAttribute(PDO::ATTR_CASE) !== PDO::CASE_LOWER) {
                $info = array_change_key_case($info, CASE_LOWER);
            }
            $column = $this->loadColumnSchema($info);

            $table->columns[$column->name] = $column;
            if (!$column->isPrimaryKey) {
                continue;
            }
            $table->primaryKey[] = $column->name;
            if ($column->autoIncrement) {
                $table->sequenceName = '';
            }
        }
    }
    /**
     * @param \me\database\TableSchema $table Table Schema
     */
    protected function findConstraints($table) {
        
    }
    /**
     * @param array $info Column Info
     * @return \me\database\ColumnSchema Column Schema
     */
    protected function loadColumnSchema($info) {
        $column                = new ColumnSchema();
        $column->name          = $info['field'];
        $column->allowNull     = $info['null'] === 'YES';
        $column->isPrimaryKey  = strpos($info['key'], 'PRI') !== false;
        $column->autoIncrement = stripos($info['extra'], 'auto_increment') !== false;
        $column->comment       = $info['comment'];
        $column->dbType        = $info['type'];
        $column->unsigned      = stripos($column->dbType, 'unsigned') !== false;
        $column->type          = self::TYPE_STRING;
        $column->phpType       = self::TYPE_STRING;
        if (!$column->isPrimaryKey) {
            if (($column->type === 'timestamp' || $column->type === 'datetime') && preg_match('/^current_timestamp(?:\(([0-9]*)\))?$/i', $info['default'], $matches)) {
                $column->defaultValue = new Expression('CURRENT_TIMESTAMP' . (!empty($matches[1]) ? '(' . $matches[1] . ')' : ''));
            }
            elseif (isset($type) && $type === 'bit') {
                $column->defaultValue = bindec(trim($info['default'], 'b\''));
            }
            else {
                $column->defaultValue = $column->phpTypecast($info['default']);
            }
        }
        return $column;
    }
}