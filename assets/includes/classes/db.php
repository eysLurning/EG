<?php

// //  require_once '../conf.php';
// class Database {
//     public $con;

//     function __construct(){
//         $this->open_db_connection();
//     }
//     public function open_db_connection(){
//         try{
//             $this->con = new PDO( "mysql:host=".DB_HOST.";dbname=".DB_NAME.";", DB_USER, DB_PASS);
//             // echo 'DB up';
//         }catch(Exception $e){
//             die("Connection failed: ". $e->getMessage());
//         }
//     }

//     public function latest_id(){
//         return $this->con->lastInsertId();
//     }

// }
// $database = new Database();
class Database {
    private static $instance; // Singleton instance
    public $con;

    private function __construct() {
        $this->open_db_connection();
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function open_db_connection() {
        try {
            $this->con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";", DB_USER, DB_PASS);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // You can set other attributes as needed
        } catch (PDOException $e) {
            // Log the error or handle it appropriately
            die("Connection failed: " . $e->getMessage());
        }
    }
    public function latest_id() {
        return $this->con->lastInsertId();
    }
}

$database = Database::getInstance();


