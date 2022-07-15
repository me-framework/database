<?php
namespace me\database\querybuilder\conditions;
use me\core\Component;
abstract class Condition extends Component {
    abstract public static function fromArrayDefinition($operator, $operands);
}