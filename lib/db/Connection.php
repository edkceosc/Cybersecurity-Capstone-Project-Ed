<?php

namespace lib\db;

use Exception;
use PDO;
use PDOException;

/**
 * Provides a reusable PDO connection
 */
class Connection {

    /**
     * @var \PDO
     */
    private static $pdo;

    /**
     * Connect to the database and return a PDO.
     *
     * @return PDO A PDO that can be used to interact with the database.
     * @throws Exception
     */
    static function get_db_pdo(): PDO
    {
        if (isset(self::$pdo)) {
            return self::$pdo;
        }

        // Get the database URL from the environment
        $url = getenv("DATABASE_URL");
        if (!$url) {
            throw new Exception('Cannot connect to the database (1)');
        }
        $db = parse_url($url);
        if(!$db || empty($db["path"])) {
            throw new Exception('Failed to connect to database (2)');
        }
        $db["path"] = ltrim($db["path"], "/");

        // Connect to the database
        try {
            // Data Source Name (DSN)
            $dsn = "pgsql:" . sprintf(
                    "host=%s;port=%s;user=%s;password=%s;dbname=%s",
                    $db["host"], $db["port"], $db["user"], $db["pass"], $db["path"]
                );

            // PHP Data Object (PDO)
            self::$pdo = new PDO($dsn);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Failed to initialize database connection (3)', 0, $e);
        }

        return self::$pdo;
    }

}
