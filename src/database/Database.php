<?php

namespace App\database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private string $host;
    private string $dbname;
    private string $username;
    private string $password;
    private string $dsn;
    private PDO $connection;
    private array $options;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $this->host = $_ENV["DB_HOST"];
        $this->dbname = $_ENV["DB_NAME"];
        $this->username = $_ENV["DB_USERNAME"];
        $this->password = $_ENV["DB_PASSWORD"];

        $this->dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname;

        $this->options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
    }

    public function getConnection(): PDO
    {
        try {
            $this->connection = new PDO(
                $this->dsn,
                $this->username,
                $this->password,
                $this->options
            );

            return $this->connection;
        } catch (PDOException $e) {
            die($e);
        }
    }

    public function closeConnection(): void
    {
        $this->connection = null;
    }
}
