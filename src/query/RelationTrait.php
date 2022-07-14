<?php
namespace me\database\query;
trait RelationTrait {
    public $with;
    /**
     * @param array|string $names relations
     */
    public function with($names) {
        $this->with = $names;
        return $this;
    }
}