<?php

require_once '../vendor/autoload.php';

use App\controllers\ConversationController;
use App\controllers\ConversationMemberController;
use App\controllers\MessageController;

$conversationMemberController = new ConversationMemberController();
$conversationController = new ConversationController();
$messageController = new MessageController();

// Vérifier si conversationId, userId et content ne sont pas manquants dans $_POST
if (!isset($_POST['userToConverseId'], $_POST['connectedUserId'], $_POST['message'])) {

    // Rediriger l'utilisateur vers la page index.php si conversationId, userId ou content est manquant
    header("Location: ../index.php?error=missing_fields");
    exit();
}

extract($_POST);

$conversationId = $conversationController->createConversation("New Conversation");

$conversationMemberController->addMemberToConversation($conversationId, $userToConverseId);
$conversationMemberController->addMemberToConversation($conversationId, $connectedUserId);

$messageController->createMessage($conversationId, $connectedUserId, $message);


header("Location: ../index.php?selectedConversationId=" . $conversationId);
exit();
