<?php
namespace me\database\mssql;
use me\database\QueryBuilder;
class MssqlQueryBuilder extends QueryBuilder {
    public $quoteCharacter  = ['[', ']'];
    public function resolveTableNames($table, $name) {
        
    }
    public function findColumns($table) {
        
    }
}