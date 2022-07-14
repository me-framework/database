<?php
namespace me\database;
use PDO;
use me\core\Component;
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
     * 
     */
    protected function init() {
        if ($this->pdo === null) {
            $this->pdo = new PDO("$this->driver:host=$this->host;port=$this->port;dbname=$this->database", $this->username, $this->password, $this->options);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL); // for pgsql to get rowCount
        }
    }
    /**
     * @return \PDOStatement PDO Statement
     */
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    /**
     * @param string $name Sequence Name
     * @return string Row ID
     */
    public function lastInsertId($name = null) {
        return $this->pdo->lastInsertId($name);
    }
}