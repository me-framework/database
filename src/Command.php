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
     * @return mixed
     */
    private function queryInternal($sql, $params, $method, $fetchMode) {
        /* @var $statement \PDOStatement */
        $statement = $this->connection->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->execute();
        $result = call_user_func_array([$statement, $method], (array) $fetchMode);
        $statement->closeCursor();
        return $result;
    }
    /**
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @return array records
     */
    public function fetchAll($sql, $params = []) {
        return $this->queryInternal($sql, $params, 'fetchAll', PDO::FETCH_ASSOC);
    }
    /**
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @return array record
     */
    public function fetchOne($sql, $params = []) {
        return $this->queryInternal($sql, $params, 'fetch', PDO::FETCH_ASSOC);
    }
    /**
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @return string|int|null|false the value of the first column in the first row of the query result.
     */
    public function queryScalar($sql, $params) {
        return $this->queryInternal($sql, $params, 'fetchColumn', 0);
    }
}