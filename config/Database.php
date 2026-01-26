<?php
/**
 * Database Connection Handler
 */

require_once __DIR__ . '/Config.php';

class Database {
    private static $conn = null;

    public static function connect() {
        if (self::$conn === null) {
            self::$conn = new mysqli(
                Config::DB_HOST,
                Config::DB_USER,
                Config::DB_PASS,
                Config::DB_NAME
            );

            if (self::$conn->connect_error) {
                die("Database Connection Failed: " . self::$conn->connect_error);
            }

            self::$conn->set_charset(Config::CHARSET);
        }

        return self::$conn;
    }

    public static function getInstance() {
        return self::connect();
    }

    public static function closeConnection() {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}

// Get singleton instance
$conn = Database::getInstance();
?>
