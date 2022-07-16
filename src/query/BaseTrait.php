<?php
namespace me\database\query;
trait BaseTrait {
    public $select;
    public $from;
    public $where;
    public $groupBy;
    public $orderBy;
    public $limit;
    public $offset;
    /**
     * 
     */
    public function select($columns) {
        $this->select = $this->normalizeSelect($columns);
        return $this;
    }
    /**
     * 
     */
    public function from($tables) {
        $this->from = $this->normalizeFrom($tables);
        return $this;
    }
    /**
     * 
     */
    public function where($where) {
        $this->where = $where;
        return $this;
    }
    /**
     * 
     */
    public function andWhere($where) {
        if ($this->where === null) {
            $this->where = $where;
        }
        elseif (is_array($this->where) && isset($this->where[0]) && strcasecmp($this->where[0], 'and') === 0) {
            $this->where[] = $where;
        }
        else {
            $this->where = ['and', $this->where, $where];
        }
        return $this;
    }
    /**
     * 
     */
    public function orWhere($where) {
        if ($this->where === null) {
            $this->where = $where;
        }
        else {
            $this->where = ['or', $this->where, $where];
        }
        return $this;
    }
    /**
     * 
     */
    public function groupBy($columns) {
        $this->groupBy = $this->normalizeGroupBy($columns);
        return $this;
    }
    /**
     * 
     */
    public function orderBy($columns) {
        $this->orderBy = $this->normalizeOrderBy($columns);
        return $this;
    }
    /**
     * 
     */
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }
    /**
     * 
     */
    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }
    //
    protected function normalizeSelect($columns) {
        if (!is_array($columns)) {
            $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        }
        $select = [];
        foreach ($columns as $alias => $columnName) {
            if (is_string($alias)) {
                $select[$columnName] = $alias;
                continue;
            }
            if (is_string($columnName) && preg_match('/^(.*?)(?i:\s+as\s+|\s+)([\w\-_\.]+)$/', $columnName, $matches)) {
                $select[$matches[2]] = $matches[1];
                continue;
            }
            if (is_string($columnName) && strpos($columnName, '(') === false) {
                $select[] = $columnName;
                continue;
            }
            $select[] = $columnName;
        }
        return $select;
    }
    protected function normalizeFrom($tables) {
        if (is_array($tables)) {
            return $tables;
        }
        return preg_split('/\s*,\s*/', trim($tables), -1, PREG_SPLIT_NO_EMPTY);
    }
    protected function normalizeGroupBy($columns) {
        if (is_array($columns)) {
            return $columns;
        }
        return preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
    }
    protected function normalizeOrderBy($columns) {
        if (is_array($columns)) {
            return $columns;
        }
        $fields = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        $result  = [];
        foreach ($fields as $field) {
            if (preg_match('/^(.*?)\s+(asc|desc)$/i', $field, $matches)) {
                $result[$matches[1]] = strcasecmp($matches[2], 'desc') ? SORT_ASC : SORT_DESC;
            }
            else {
                $result[$field] = SORT_ASC;
            }
        }
        return $result;
    }
}