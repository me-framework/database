<?php
namespace me\database;
use PDO;
use Exception;
use me\core\Component;
use me\core\components\Container;
use me\database\mysql\MysqlSchema;
use me\database\pgsql\PgsqlSchema;
use me\database\mssql\MssqlSchema;
/**
 * @property-read \me\database\Schema $schema Schema
 */
class Connection extends Component {
    /**
     * @var string
     */
    public $driver;
    /**
     * @var string
     */
    public $host;
    /**
     * @var string
     */
    public $port;
    /**
     * @var string
     */
    public $database;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var \PDO
     */
    public $pdo;
    /**
     * @var \me\database\Schema
     */
    private $_schema;
    /**
     * 
     */
    public function init() {
        if ($this->pdo === null) {
            $this->pdo = new PDO("$this->driver:host=$this->host;port=$this->port;dbname=$this->database", $this->username, $this->password, $this->options);
        }
    }
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
     * @return \me\database\Schema Schema
     */
    public function getSchema() {
        if ($this->_schema === null) {
            if (!isset($this->schemaMap[$this->driver])) {
                throw new Exception("Schema { <b>$this->driver</b> } Not Found");
            }
            $this->_schema = Container::build(['class' => $this->schemaMap[$this->driver], 'connection' => $this]);
        }
        return $this->_schema;
    }
    /**
     * @param string $sql
     * @param array $params
     * @return Command
     */
    public function createCommand(string $sql = null, array $params = []) {
        return $this->getSchema()->createCommand($sql, $params);
    }
    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() {
        return $this->getSchema()->createQueryBuilder();
    }
}