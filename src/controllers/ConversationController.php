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
        $query = "SELECT c.* FROM conversations c
              INNER JOIN conversationmembers cm ON c.id = cm.conversationId
              WHERE cm.userId = :userId";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "userId" => $userId
        ]);

        return $stmt->fetchAll();
    }
}
