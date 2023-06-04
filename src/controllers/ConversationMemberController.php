<?php

namespace App\controllers;

use PDO;
use App\database\Database;

class ConversationMemberController
{
    private Database $database;
    private PDO $connection;

    public function __construct()
    {
        $this->database = new Database();
        $this->connection = $this->database->getConnection();
    }

    public function getMembersByConversationId(int $conversationId)
    {
        $query = "SELECT u.* FROM users u
              INNER JOIN conversationmembers cm ON u.id = cm.userId
              WHERE cm.conversationId = :conversationId";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "conversationId" => $conversationId
        ]);

        return $stmt->fetchAll();
    }


    public function addMemberToConversation(int $conversationId, int $userId)
    {
        $query = "INSERT INTO conversationmembers (conversationId, userId) VALUES (:conversationId, :userId)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "conversationId" => $conversationId,
            "userId" => $userId
        ]);

        return $stmt->rowCount();
    }

    public function removeMemberFromConversation(int $conversationId, int $userId)
    {
        $query = "DELETE FROM conversationmembers WHERE conversationId = :conversationId AND userId = :userId";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            "conversationId" => $conversationId,
            "userId" => $userId
        ]);

        return $stmt->rowCount();
    }
}
