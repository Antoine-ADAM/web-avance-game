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

    const TYPES = [
        "string" => "s",
        "int" => "i",
        "integer" => "i",
        "double" => "d",
        "date" => "s",
        "bool" => "i",
        "boolean" => "i"
    ];

    /**
     * @param string $query
     * @param array $params
     * @return bool|mysqli_result
     */
    public static function query(string $string, array $array=[])
    {
        $db = self::getDB();
        $stmt = $db->prepare($string);
        $types = "";
        foreach ($array as $item) {
            $types .= self::TYPES[gettype($item)];
        }
        $stmt->bind_param($types,...$array);
        $stmt->execute();
        return $stmt->get_result();
    }
}