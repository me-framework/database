<?php
namespace me\database\querybuilder;
use Exception;
use me\database\conditions\Condition;
use me\database\conditions\HashCondition;
trait WhereTrait {
    public $conditionClasses  = [
        'AND'         => 'me\database\conditions\AndCondition',
        'OR'          => 'me\database\conditions\OrCondition',
        'NOT'         => 'me\database\conditions\NotCondition',
        'IN'          => 'me\database\conditions\InCondition',
        'NOT IN'      => 'me\database\conditions\InCondition',
        'LIKE'        => 'me\database\conditions\LikeCondition',
        'NOT LIKE'    => 'me\database\conditions\LikeCondition',
        'BETWEEN'     => 'me\database\conditions\BetweenCondition',
        'NOT BETWEEN' => 'me\database\conditions\BetweenCondition',
    ];
    public $conditionBuilders = [
        'me\database\conditions\HashCondition'          => 'me\database\conditions\HashConditionBuilder',
        'me\database\conditions\SimpleCondition'        => 'me\database\conditions\SimpleConditionBuilder',
        'me\database\conditions\ConjunctionCondition'   => 'me\database\conditions\ConjunctionConditionBuilder',
        'me\database\conditions\AndCondition'           => 'me\database\conditions\ConjunctionConditionBuilder',
        'me\database\conditions\OrCondition'            => 'me\database\conditions\ConjunctionConditionBuilder',
        'me\database\conditions\NotCondition'           => 'me\database\conditions\NotConditionBuilder',
        'me\database\conditions\InCondition'            => 'me\database\conditions\InConditionBuilder',
        'me\database\conditions\LikeCondition'          => 'me\database\conditions\LikeConditionBuilder',
        'me\database\conditions\BetweenCondition'       => 'me\database\conditions\BetweenConditionBuilder',
        'me\database\conditions\BetweenColumnCondition' => 'me\database\conditions\BetweenColumnConditionBuilder',
    ];
    /**
     * @param array|string|\me\database\conditions\Condition $condition Conditions
     * @param array $params SQL Parameters
     * @return string
     */
    public function buildWhere($condition, &$params) {
        $where = $this->buildCondition($condition, $params);
        return $where === '' ? '' : 'WHERE ' . $where;
    }
    /**
     * @param array|string|\me\database\conditions\Condition $condition Condition
     * @param array $params SQL Parameters
     * @return string Raw Condition
     */
    public function buildCondition($condition, &$params) {
        if (is_array($condition)) {
            if (empty($condition)) {
                return '';
            }
            $condition = $this->createConditionFromArray($condition);
        }
        if ($condition instanceof Condition) {
            $builder = $this->getConditionBuilder($condition);
            return $builder->build($condition, $params);
        }
        return (string) $condition;
    }
    /**
     * @param array $condition Condition Array
     * @return \me\database\conditions\Condition Condition Object
     */
    public function createConditionFromArray($condition) {
        if (isset($condition[0])) {
            $operator  = strtoupper(array_shift($condition));
            /** @var \me\database\conditions\Condition $className */
            $className = 'me\database\conditions\SimpleCondition';
            if (isset($this->conditionClasses[$operator])) {
                $className = $this->conditionClasses[$operator];
            }
            return $className::fromArrayDefinition($operator, $condition);
        }
        return new HashCondition($condition);
    }
    /**
     * @param \me\database\conditions\Condition $condition Condition
     * @return \me\database\conditions\ConditionBuilder Condition Builder
     */
    public function getConditionBuilder($condition) {
        $className = get_class($condition);
        if (!isset($this->conditionBuilders[$className])) {
            throw new Exception('Condition of class ' . $className . ' can not be built in ' . get_class($this));
        }
        if (!is_object($this->conditionBuilders[$className])) {
            $this->conditionBuilders[$className] = new $this->conditionBuilders[$className]($this);
        }
        return $this->conditionBuilders[$className];
    }
}