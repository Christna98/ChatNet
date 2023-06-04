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

    public function getUsers(int $currentUserId)
    {
        $query = "SELECT u.*
              FROM users u
              WHERE u.id != :currentUserId
              AND u.id NOT IN (
                SELECT cm.userId
                FROM conversationmembers cm
                INNER JOIN conversations c ON cm.conversationId = c.id
                WHERE cm.conversationId IN (
                  SELECT cm.conversationId
                  FROM conversationmembers cm
                  WHERE cm.userId = :currentUserId
                )
              )
              ORDER BY u.status DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "currentUserId" => $currentUserId
        ]);

        return $stmt->fetchAll();
    }


    public function getUserById(int $id)
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "id" => $id
        ]);

        return $stmt->fetch();
    }

    public function getUserByUsername(string $userName)
    {
        $query = "SELECT * FROM users WHERE userName = :userName";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "userName" => $userName
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

    public function createUser(string $userName, string $password, bool $connected = true)
    {
        $query = "INSERT INTO users (userName, password, status) VALUES (:userName, :password, :status)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "userName" => $userName,
            "password" => $password,
            "status" => $connected,
        ]);

        return $stmt->rowCount();
    }

    public function updateUserStatus(int $id, bool $status)
    {
        $query = "UPDATE users SET status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "status" => boolval($status) ? 1 : 0,
            "id" => $id
        ]);

        return $stmt->rowCount();
    }

    public function updateUser(int $id, array $userData)
    {
        $query = "UPDATE users SET userName = :userName, password = :password WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "userName" => $userData['userName'],
            "password" => $userData['password'],
            "id" => $id
        ]);

        return $stmt->rowCount();
    }

    public function deleteUser(int $id)
    {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "id" => $id
        ]);

        return $stmt->rowCount();
    }

    public function searchUsers(string $keyword)
    {
        $query = "SELECT * FROM users WHERE userName LIKE :keyword";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "keyword" => "%$keyword%"
        ]);

        return $stmt->fetchAll();
    }

    public function countUsers()
    {
        $query = "SELECT COUNT(*) FROM users";
        $stmt = $this->connection->query($query);
        return $stmt->fetchColumn();
    }
}
