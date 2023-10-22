<?php

namespace ChatBooster;

use PDO;
use PDOException;

class Conection
{
    protected $connection;

    public function __construct()
    {
        if(file_exists(__DIR__.'/../../../../chatbooster.settings.php'))
            require_once __DIR__.'/../../../../chatbooster.settings.php';
            
        $db_host = DB_HOST;
        $db_user = DB_USERNAME;
        $db_password = DB_PASSWORD;
        $db_name = DB_DATABASE_NAME;

        try {
            $connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection = $connection;

        } catch(PDOException $e) {
            echo "Query error: " . $e->getMessage();
        }
    }
}