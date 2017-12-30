<?php

class Model {

    public $db;
    
    function __construct() {
        $this->db = $this->DB();
    }
    
    public function DB() {
        return new Database();
    }

}