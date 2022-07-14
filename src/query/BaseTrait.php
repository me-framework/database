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
        foreach ($columns as $columnAlias => $columnDefinition) {
            if (is_string($columnAlias)) {
                $select[$columnAlias] = $columnDefinition;
                continue;
            }
            if (is_string($columnDefinition)) {
                if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)([\w\-_\.]+)$/', $columnDefinition, $matches) && !preg_match('/^\d+$/', $matches[2]) && strpos($matches[2], '.') === false) {
                    $select[$matches[2]] = $matches[1];
                    continue;
                }
                if (strpos($columnDefinition, '(') === false) {
                    $select[$columnDefinition] = $columnDefinition;
                    continue;
                }
            }
            $select[] = $columnDefinition;
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
        $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
        $result  = [];
        foreach ($columns as $column) {
            if (preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches)) {
                $result[$matches[1]] = strcasecmp($matches[2], 'desc') ? SORT_ASC : SORT_DESC;
            }
            else {
                $result[$column] = SORT_ASC;
            }
        }
        return $result;
    }
}