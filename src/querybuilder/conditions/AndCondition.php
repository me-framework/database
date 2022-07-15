<?php
namespace me\database\querybuilder\conditions;
class AndCondition extends ConjunctionCondition {
   public function getOperator() {
        return 'AND';
    }
}