<?php

namespace App\Core;

use App\Models\Database;

abstract class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function prepare($sql) {
        return $this->db->prepare($sql);
    }

    protected function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
