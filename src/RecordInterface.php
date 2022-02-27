<?php
namespace me\database;
interface RecordInterface {
    public static function tableName();
    public function populate($row, $query);
}