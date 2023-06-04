<?php
require_once '../vendor/autoload.php';

use App\controllers\MessageController;

// Créer une instance de la classe MessageController
$messageController = new MessageController();

// Vérifier si conversationId, userId et content ne sont pas manquants dans $_POST
if (!isset($_POST['conversationId'], $_POST['userId'], $_POST['content'])) {

    // Rediriger l'utilisateur vers la page index.php si conversationId, userId ou content est manquant
    header("Location: ../index.php?error=missing_fields");
    exit();
}

$conversationId = $_POST['conversationId'];
$userId = $_POST['userId'];
$content = $_POST['content'];

// Insérer le nouveau message dans la base de données
$messageId = $messageController->createMessage(
    intval($conversationId),
    intval($userId),
    $content
);

if (!$messageId) {
    // Rediriger l'utilisateur vers la page index.php si le message n'est pas envoyé
    header("Location: ../index.php?error=database");
    exit();
}

header("Location: ../index.php?selectedConversationId=" . $conversationId);
exit();
