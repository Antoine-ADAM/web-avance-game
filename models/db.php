<?php

class MyDB{
    static private $instance=null;
    /**
     * @var mysqli
     */
    public $db;

    function __construct(){
        require_once "config.php";
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }
    /**
     * @return mysqli
     */
    static public function getDB(){
        if(self::$instance==null){
            self::$instance = new MyDB();
        }
        return self::$instance->db;
    }
}