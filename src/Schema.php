<?php
namespace me\database;
use me\core\Component;
use me\core\components\Container;
/**
 * @property-read \me\database\QueryBuilder $queryBuilder Query Builder
 * @property-read \me\database\TableSchema $tableSchema Table Schema
 */
abstract class Schema extends Component {
    const TYPE_PK                 = 'pk';
    const TYPE_UPK                = 'upk';
    const TYPE_BIGPK              = 'bigpk';
    const TYPE_UBIGPK             = 'ubigpk';
    const TYPE_CHAR               = 'char';
    const TYPE_STRING             = 'string';
    const TYPE_TEXT               = 'text';
    const TYPE_TINYINT            = 'tinyint';
    const TYPE_SMALLINT           = 'smallint';
    const TYPE_INTEGER            = 'integer';
    const TYPE_BIGINT             = 'bigint';
    const TYPE_FLOAT              = 'float';
    const TYPE_DOUBLE             = 'double';
    const TYPE_DECIMAL            = 'decimal';
    const TYPE_DATETIME           = 'datetime';
    const TYPE_TIMESTAMP          = 'timestamp';
    const TYPE_TIME               = 'time';
    const TYPE_DATE               = 'date';
    const TYPE_BINARY             = 'binary';
    const TYPE_BOOLEAN            = 'boolean';
    const TYPE_MONEY              = 'money';
    const TYPE_JSON               = 'json';
    /**
     * @var \me\database\DatabaseManager Database Manager
     */
    public $database;
    /**
     * @var \me\database\Connection Connection
     */
    public $connection;
    /**
     * @param string $modelClass Model Class
     * @return \me\database\Query Query
     */
    public function createQuery($modelClass) {
        return Container::build(['class' => Query::class, 'schema' => $this, 'modelClass' => $modelClass]);
    }
    /**
     * @var \me\database\QueryBuilder Query Builder
     */
    protected $_queryBuilder;
    /**
     * @return \me\database\QueryBuilder Query Builder
     */
    abstract public function getQueryBuilder();
    /**
     * @var \me\database\TableSchema Table Schema
     */
    protected $_tableSchema = [];
    /**
     * @param string $table_name Table Name
     * @return \me\database\TableSchema Table Schema
     */
    abstract public function getTableSchema($table_name);
    /**
     * Extracts the PHP type from abstract DB type.
     * @param \me\database\ColumnSchema $column the column schema information
     * @return string PHP type name
     */
    protected function getColumnPhpType($column) {
        static $typeMap = [
            // abstract type => php type
            self::TYPE_TINYINT  => 'integer',
            self::TYPE_SMALLINT => 'integer',
            self::TYPE_INTEGER  => 'integer',
            self::TYPE_BIGINT   => 'integer',
            self::TYPE_BOOLEAN  => 'boolean',
            self::TYPE_FLOAT    => 'double',
            self::TYPE_DOUBLE   => 'double',
            self::TYPE_BINARY   => 'resource',
            self::TYPE_JSON     => 'array',
        ];
        if (isset($typeMap[$column->type])) {
            if ($column->type === 'bigint') {
                return PHP_INT_SIZE === 8 && !$column->unsigned ? 'integer' : 'string';
            }
            elseif ($column->type === 'integer') {
                return PHP_INT_SIZE === 4 && $column->unsigned ? 'string' : 'integer';
            }
            return $typeMap[$column->type];
        }
        return 'string';
    }
}