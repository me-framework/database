<?php
namespace me\database;
use me\core\Component;
use me\core\components\Container;
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
        if (!($this->connections[$name] instanceof Connection)) {
            //if (!is_array($this->connections[$name])) {
            //    throw new Exception("");
            //}
            $this->connections[$name] = Container::build(array_merge($this->connectionConfig, $this->connections[$name]));
        }
        return $this->connections[$name];
    }
    /**
     * @param string $connection Connection Name
     * @return \me\database\Command Command
     */
    public function getCommand($connection = null) {
        return $this->getConnection($connection)->getCommand();
    }
}