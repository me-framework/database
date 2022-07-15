<?php
namespace me\database\querybuilder;
use Exception;
use me\database\querybuilder\conditions;
trait WhereTrait {
    public $conditionClasses  = [
        'AND'         => conditions\AndCondition::class,
        'OR'          => conditions\OrCondition::class,
        'NOT'         => conditions\NotCondition::class,
        'IN'          => conditions\InCondition::class,
        'NOT IN'      => conditions\InCondition::class,
        'LIKE'        => conditions\LikeCondition::class,
        'NOT LIKE'    => conditions\LikeCondition::class,
        'BETWEEN'     => conditions\BetweenCondition::class,
        'NOT BETWEEN' => conditions\BetweenCondition::class,
    ];
    public $conditionBuilders = [
        conditions\HashCondition::class           => conditions\HashConditionBuilder::class,
        conditions\SimpleCondition::class         => conditions\SimpleConditionBuilder::class,
        conditions\ConjunctionCondition::class    => conditions\ConjunctionConditionBuilder::class,
        conditions\AndCondition::class            => conditions\ConjunctionConditionBuilder::class,
        conditions\OrCondition::class             => conditions\ConjunctionConditionBuilder::class,
        conditions\NotCondition::class            => conditions\NotConditionBuilder::class,
        conditions\InCondition::class             => conditions\InConditionBuilder::class,
        conditions\LikeCondition::class           => conditions\LikeConditionBuilder::class,
        conditions\BetweenCondition::class        => conditions\BetweenConditionBuilder::class,
        conditions\BetweenColumnsCondition::class => conditions\BetweenColumnsConditionBuilder::class,
    ];
    /**
     * @param array|string|\me\database\querybuilder\conditions\Condition $condition Conditions
     * @param array $params SQL Parameters
     * @return string
     */
    public function buildWhere($condition, &$params) {
        $where = $this->buildCondition($condition, $params);
        return $where === '' ? '' : 'WHERE ' . $where;
    }
    /**
     * @param array|string|\me\database\querybuilder\conditions\Condition $condition Condition
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
        if ($condition instanceof conditions\Condition) {
            $builder = $this->getConditionBuilder($condition);
            return $builder->build($condition, $params);
        }
        return (string) $condition;
    }
    /**
     * @param array $condition Condition Array
     * @return \me\database\querybuilder\conditions\Condition Condition Object
     */
    public function createConditionFromArray($condition) {
        if (isset($condition[0])) {
            $operator  = strtoupper(array_shift($condition));
            /** @var \me\database\querybuilder\conditions\Condition $className */
            $className = conditions\SimpleCondition::class;
            if (isset($this->conditionClasses[$operator])) {
                $className = $this->conditionClasses[$operator];
            }
            return $className::fromArrayDefinition($operator, $condition);
        }
        return new conditions\HashCondition($condition);
    }
    /**
     * @param \me\database\querybuilder\conditions\Condition $condition Condition
     * @return \me\database\querybuilder\conditions\ConditionBuilder Condition Builder
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