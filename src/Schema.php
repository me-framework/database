<?php
namespace me\database;
use me\core\Component;
use me\core\Container;
/**
 * @property-read \me\database\QueryBuilder $queryBuilder Query Builder
 * @property-read \me\database\TableSchema $tableSchema Table Schema
 */
abstract class Schema extends Component {
    /**
     * @var \me\database\DatabaseManager Database Manager
     */
    public $database;
    /**
     * @var \me\database\Connection Connection
     */
    public $connection;
    /**
     * @var \me\database\QueryBuilder Query Builder
     */
    protected $_queryBuilder;
    /**
     * @var \me\database\TableSchema Table Schema
     */
    protected $_tableSchema = [];
    /**
     * @return \me\database\QueryBuilder Query Builder
     */
    abstract public function getQueryBuilder();
    /**
     * @param string $table_name Table Name
     * @return \me\database\TableSchema Table Schema
     */
    abstract public function getTableSchema($table_name);
    /**
     * @param string $modelClass Model Class
     * @return \me\database\Query Query
     */
    public function createQuery($modelClass) {
        return Container::build(['class' => Query::class, 'schema' => $this, 'modelClass' => $modelClass]);
    }
}