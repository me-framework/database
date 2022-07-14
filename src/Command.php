<?php
namespace me\database;
use PDO;
use me\core\Cache;
use me\core\Component;
class Command extends Component {
    /**
     * @param \me\database\Connection $connection Connection
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @param string $method Method
     * @return mixed
     */
    private function queryInternal($connection, $sql, $params, $method, $fetchMode) {
        $this->cache($sql, $params);
        $statement = $connection->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->execute();
        $result = call_user_func_array([$statement, $method], (array) $fetchMode);
        $statement->closeCursor();
        return $result;
    }
    /**
     * 
     */
    private function cache($sql, $params) {
        $command = Cache::getCache(['command', 'command']);
        if ($command === null) {
            Cache::setCache(['command', 'command'], [[$sql, $params]]);
        }
        else {
            $command[] = [$sql, $params];
            Cache::setCache(['command', 'command'], $command);
        }
    }
    /**
     * @param \me\database\Connection $connection Connection
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @return array records
     */
    public function fetchAll($connection, $sql, $params = []) {
        return $this->queryInternal($connection, $sql, $params, 'fetchAll', PDO::FETCH_ASSOC);
    }
    /**
     * @param \me\database\Connection $connection Connection
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @return array record
     */
    public function fetchOne($connection, $sql, $params = []) {
        return $this->queryInternal($connection, $sql, $params, 'fetch', PDO::FETCH_ASSOC);
    }
    /**
     * @param \me\database\Connection $connection Connection
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @return string|int|null|false the value of the first column in the first row of the query result.
     */
    public function queryScalar($connection, $sql, $params = []) {
        return $this->queryInternal($connection, $sql, $params, 'fetchColumn', 0);
    }
    /**
     * @param \me\database\Connection $connection Connection
     * @param string $sql Raw SQL
     * @param array $params SQL Parameters
     * @return int Row Count
     */
    public function execute($connection, $sql, $params = []) {
        return $this->queryInternal($connection, $sql, $params, 'rowCount', []);
    }
}