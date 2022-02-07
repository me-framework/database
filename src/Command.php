<?php
namespace me\database;
use PDO;
use me\core\Component;
class Command extends Component {
    /**
     * @var Connection Connection
     */
    public $connection;
    /**
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     */
    public function fetchAll($sql, $params = []) {
        /* @var $statement \PDOStatement */
        $statement = $this->connection->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     */
    public function fetchOne($sql, $params) {
        /* @var $statement \PDOStatement */
        $statement = $this->connection->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}