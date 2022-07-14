<?php
namespace me\database\conditions;
use me\core\Component;
abstract class Condition extends Component {
    abstract public static function fromArrayDefinition($operator, $operands);
}