<?php
namespace me\database\conditions;
class AndCondition extends ConjunctionCondition {
   public function getOperator() {
        return 'AND';
    }
}