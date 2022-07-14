<?php
namespace me\database;
use Exception;
use me\core\Component;
use me\core\components\Container;
use me\database\mysql\MysqlSchema;
use me\database\pgsql\PgsqlSchema;
use me\database\mssql\MssqlSchema;
/**
 * @property string $default Default Connection
 */
class DatabaseManager extends Component {
    /**
     * @var string Default Connection
     */
    private $_default;
    /**
     * @var array Database Connections
     */
    public $connections      = [];
    /**
     * @var array Connection Config
     */
    public $connectionConfig = ['class' => Connection::class];
    /**
     * @var \me\database\Command Command
     */
    private $_command;
    /**
     * @var \me\database\Schema[]
     */
    private $_schema  = [];
    /**
     * @var array
     */
    public $schemaMap = [
        'mysql'  => MysqlSchema::class, // MySQL
        'pgsql'  => PgsqlSchema::class, // PostgreSQL
        'mssql'  => MssqlSchema::class, // older MSSQL driver on MS Windows hosts
        'sqlsrv' => MssqlSchema::class, // newer MSSQL driver on MS Windows hosts
    ];
    /**
     * 
     */
    public function setDefault($value) {
        $this->_default = $value;
    }
    /**
     * 
     */
    public function getDefault() {
        return $this->_default;
    }
    /**
     * @param string $name Connection Name
     * @return \me\database\Connection Connection
     */
    public function getConnection($name = null) {
        if (is_null($name)) {
            $name = $this->getDefault();
        }
        if (!array_key_exists($name, $this->connections)) {
            throw new Exception("Database { $name } not exists.");
        }
        if (is_array($this->connections[$name])) {
            $this->connections[$name] = Container::build(array_merge($this->connectionConfig, $this->connections[$name]));
        }
        if (!($this->connections[$name] instanceof Connection)) {
            throw new Exception('Connection shuld be instanceof ' . Connection::class);
        }
        return $this->connections[$name];
    }
    /**
     * @return \me\database\Command Command
     */
    public function getCommand() {
        if ($this->_command === null) {
            $this->_command = Container::build(['class' => Command::class]);
        }
        return $this->_command;
    }
    /**
     * @param string $name Connection Name
     * @return \me\database\Schema Schema
     */
    public function getSchema($name) {
        if (!isset($this->_schema[$name])) {
            $connection = $this->getConnection($name);
            if (!isset($this->schemaMap[$connection->driver])) {
                throw new Exception("Schema { <b>$this->driver</b> } Not Found");
            }
            $this->_schema[$name] = Container::build([
                'class'      => $this->schemaMap[$connection->driver],
                'database'   => $this,
                'connection' => $connection
            ]);
        }
        return $this->_schema[$name];
    }
}