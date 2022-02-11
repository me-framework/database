<?php
namespace me\database;
use me\core\Component;
abstract class QueryBuilder extends Component {
    use querybuilder\BaseTrait;
    use querybuilder\WhereTrait;
    public $separator    = ' ';
    public $param_prefix = ':me';
    /**
     * @param \me\database\TableSchema $table Table Schema
     * @param string $name Table Name
     */
    abstract public function resolveTableNames($table, $name);
    /**
     * @param \me\database\TableSchema $table Table Schema
     * @return array [string $sql, array $params]
     */
    abstract public function findColumns($table);
    /**
     * @param \me\database\Query $query Query
     * @return array [string $sql, array $params]
     */
    public function build($query) {

        $query->prepare($this);

        $params  = [];
        $clauses = [
            $this->buildSelect($query->select),
            $this->buildFrom($query->from),
            $this->buildWhere($query->where, $params),
            $this->buildGroupBy($query->groupBy),
            $this->buildOrderBy($query->orderBy),
            $this->buildLimit($query->limit, $query->offset),
        ];

        $sql = implode($this->separator, array_filter($clauses));

        return [$sql, $params];
    }
    /**
     * @param mixed $value Value
     * @param array $params SQL Parameters
     * @return string phName
     */
    public function bindParam($value, &$params) {
        $phName          = $this->param_prefix . count($params);
        $params[$phName] = $value;
        return $phName;
    }
    /**
     * @param string $table_name Table Name
     * @param array $values Values
     * @return array [string $sql, array $params]
     */
    public function insert($table_name, $values) {
        $names        = [];
        $params       = [];
        $placeholders = [];
        foreach ($values as $name => $value) {
            $names[]        = $this->quote($name);
            $placeholders[] = $this->bindParam($value, $params);
        }
        $sql = 'INSERT INTO ' . $this->quote($table_name) . ' (' . implode(', ', $names) . ') VALUES (' . implode(', ', $placeholders) . ')';
        return [$sql, $params];
    }
    /**
     * @param string $table_name Table Name
     * @param array $values Values
     * @param array $condition Condition
     * @return array [string $sql, array $params]
     */
    public function update($table_name, $values, $condition) {
        $params  = [];
        $clauses = [
            'UPDATE ' . $this->quote($table_name),
            'SET ' . $this->buildUpdateSets($values, $params),
            $this->buildWhere($condition, $params)
        ];
        $sql     = implode($this->separator, array_filter($clauses));
        return [$sql, $params];
    }
    public function buildUpdateSets($values, &$params) {
        $sets = [];
        foreach ($values as $name => $value) {
            $sets[] = $this->quote($name) . '=' . $this->bindParam($value, $params);
        }
        return implode(', ', $sets);
    }
    /**
     * @param string $table_name Table Name
     * @param array $condition Condition
     * @return array [string $sql, array $params]
     */
    public function delete($table_name, $condition) {
        $params  = [];
        $clauses = [
            'DELETE FROM ' . $this->quote($table_name),
            $this->buildWhere($condition, $params)
        ];
        $sql     = implode($this->separator, array_filter($clauses));
        return [$sql, $params];
    }
}