<?php

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = getenv('FIXLINE_DB_HOST') ?: '127.0.0.1';
            $database = getenv('FIXLINE_DB_NAME') ?: 'fixline_db';
            $username = getenv('FIXLINE_DB_USER') ?: 'root';
            $password = getenv('FIXLINE_DB_PASSWORD') ?: '';
            $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}

function createDatabaseConnection(): PDO
{
    return Database::getConnection();
}
