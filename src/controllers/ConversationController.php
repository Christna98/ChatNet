<?php

namespace App\controllers;

use PDO;
use App\database\Database;

class ConversationController
{
    private Database $database;
    private PDO $connection;

    public function __construct()
    {
        $this->database = new Database();
        $this->connection = $this->database->getConnection();
    }

    public function getConversationsByUserId(int $userId)
    {
        $query = "SELECT c.*, MAX(m.timestamp) AS newestMessageTimestamp
              FROM conversations c
              INNER JOIN conversationmembers cm ON c.id = cm.conversationId
              INNER JOIN messages m ON c.id = m.conversationId
              WHERE cm.userId = :userId
              GROUP BY c.id
              ORDER BY newestMessageTimestamp DESC, c.createdAt DESC";

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "userId" => $userId
        ]);

        return $stmt->fetchAll();
    }

    public function getConversationById(int $id)
    {
        $query = "SELECT * FROM conversations WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "id" => $id
        ]);

        return $stmt->fetch();
    }

    public function createConversation(string $conversationName)
    {
        $query = "INSERT INTO conversations (name) VALUES (:conversationName)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "conversationName" => $conversationName
        ]);

        return $this->connection->lastInsertId();
    }
}
