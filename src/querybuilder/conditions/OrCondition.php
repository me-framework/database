<?php
namespace me\database\querybuilder\conditions;
class OrCondition extends ConjunctionCondition {
    public function getOperator() {
        return 'OR';
    }
}