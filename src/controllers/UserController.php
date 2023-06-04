<?php

namespace App\controllers;

use PDO;
use App\database\Database;

class UserController
{
    private Database $database;
    private PDO $connection;

    public function __construct()
    {
        $this->database = new Database();
        $this->connection = $this->database->getConnection();
    }

    public function getUsers()
    {
        $query = "SELECT * FROM users";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUser(int $id)
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "id" => $id
        ]);

        return $stmt->fetch();
    }

    public function getUserByUsernameAndPassword(string $userName, string $password)
    {
        $query = "SELECT * FROM users WHERE userName = :userName AND password = :password";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "userName" => $userName,
            "password" => $password
        ]);

        return $stmt->fetch();
    }

    public function createUser(string $userName, string $password)
    {
        $query = "INSERT INTO users(userName, password) VALUES(:userName, :password)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "userName" => $userName,
            "password" => $password
        ]);;

        return $stmt->rowCount();
    }

    public function connectAndDisconnectUser(int $id, string $status)
    {
        $query = "UPDATE users SET status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "status" => $status,
            "id" => $id
        ]);

        return $stmt->rowCount();
    }
}
