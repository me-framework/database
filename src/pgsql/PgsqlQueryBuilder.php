<?php
namespace me\database\pgsql;
use me\database\QueryBuilder;
class PgsqlQueryBuilder extends QueryBuilder {
    public $quoteCharacter = '"';
    public function resolveTableNames($table, $name) {
        
    }
    public function findColumns($table) {
        
    }
}