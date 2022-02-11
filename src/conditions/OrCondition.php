<?php
namespace me\database\conditions;
class OrCondition extends ConjunctionCondition {
    public function getOperator() {
        return 'OR';
    }
}