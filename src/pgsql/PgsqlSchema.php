<?php
namespace me\database\pgsql;
use me\database\Schema;
class PgsqlSchema extends Schema {
    public function getQueryBuilder() {
        if (is_null($this->_queryBuilder)) {
            $this->_queryBuilder = new PgsqlQueryBuilder();
        }
        return $this->_queryBuilder;
    }
    public function getTableSchema($table_name) {
        
    }
}