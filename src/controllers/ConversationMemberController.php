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

    public function getOtherMemberOfConversationByUserId(int $userId, int $conversationId)
    {
        // Requête pour sélectionner les informations de l'autre membre de la conversation
        $query = "SELECT u.* FROM users u
    INNER JOIN conversationmembers cm ON u.id != :userId
    WHERE cm.conversationId = :conversationId";

        // Préparation de la requête
        $stmt = $this->connection->prepare($query);

        // Exécution de la requête avec les valeurs des paramètres
        $stmt->execute([
            "userId" => $userId,
            "conversationId" => $conversationId
        ]);

        // Récupération du résultat (un seul enregistrement)
        return $stmt->fetch();
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
