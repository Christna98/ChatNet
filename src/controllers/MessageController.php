<?php

namespace App\controllers;

use PDO;
use App\database\Database;

class MessageController
{
    private Database $database;
    private PDO $connection;

    public function __construct()
    {
        $this->database = new Database();
        $this->connection = $this->database->getConnection();
    }

    public function getMessagesByConversationId(int $conversationId)
    {
        $query = "SELECT * FROM messages WHERE conversationId = :conversationId";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "conversationId" => $conversationId
        ]);

        return $stmt->fetchAll();
    }

    public function createMessage(int $conversationId, int $userId, string $content)
    {
        $query = "INSERT INTO messages (conversationId, userId, content) VALUES (:conversationId, :userId, :content)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "conversationId" => $conversationId,
            "userId" => $userId,
            "content" => $content
        ]);

        return $this->connection->lastInsertId();
    }

    public function deleteMessage(int $messageId)
    {
        $query = "DELETE FROM messages WHERE id = :messageId";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "messageId" => $messageId
        ]);

        return $stmt->rowCount();
    }
}
