<?php 
class Database {     
    private static $host = "localhost";     
    private static $user = "root";     
    private static $pass = "";     
    private static $dbname = "fixline_db";     
    private static $conn = null;      

    public static function getConnection() {         
        if (self::$conn === null) {             
            try {                 
                self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4", self::$user, self::$pass);                 
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                 
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);             
            } catch (PDOException $e) {                 
                // Database fallback to session if DB connection fails                 
                self::$conn = false;             
            }         
        }         
        return self::$conn;     
    } 
}