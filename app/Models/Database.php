<?php
namespace App\Config;

use PDO;

class Database
{
    private static ?PDO $conn = null;

    public static function getConnection(): PDO
    {
        if (self::$conn === null) {
            $cfg = require __DIR__ . '/../config/config.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $cfg['db_host'],
                $cfg['db_name'],
                $cfg['db_charset']
            );
            self::$conn = new PDO($dsn, $cfg['db_user'], $cfg['db_pass']);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}
