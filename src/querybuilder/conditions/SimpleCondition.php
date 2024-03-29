<?php
namespace me\database\querybuilder\conditions;
use me\exceptions\Exception;
class SimpleCondition extends Condition {
    /**
     * @var string $operator the operator to use. Anything could be used e.g. `>`, `<=`, etc.
     */
    private $operator;
    /**
     * @var mixed the column name to the left of [[operator]]
     */
    private $column;
    /**
     * @var mixed the value to the right of the [[operator]]
     */
    private $value;
    /**
     * SimpleCondition constructor
     *
     * @param mixed $column the literal to the left of $operator
     * @param string $operator the operator to use. Anything could be used e.g. `>`, `<=`, etc.
     * @param mixed $value the literal to the right of $operator
     */
    public function __construct($column, $operator, $value) {
        parent::__construct();
        $this->column   = $column;
        $this->operator = $operator;
        $this->value    = $value;
    }
    /**
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }
    /**
     * @return mixed
     */
    public function getColumn() {
        return $this->column;
    }
    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
    /**
     * {@inheritdoc}
     * @throws Exception if wrong number of operands have been given.
     */
    public static function fromArrayDefinition($operator, $operands) {
        if (count($operands) !== 2) {
            throw new Exception("Operator '$operator' requires two operands.");
        }
        return new static($operands[0], $operator, $operands[1]);
    }
}