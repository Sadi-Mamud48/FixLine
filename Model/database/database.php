<?php
/**
 * FixLine - Model / Database
 * -----------------------------------
 * Single PDO connection used across the whole app.
 * Update the credentials below to match your local MySQL setup.
 */

class Database
{
    private static ?PDO $connection = null;

    private const HOST    = "127.0.0.1";
    private const DB_NAME = "fixline_db";
    private const USER    = "root";
    private const PASS    = "";
    private const CHARSET = "utf8mb4";

    // Returns a single shared PDO instance (Singleton pattern)
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, self::USER, self::PASS, $options);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
