<?php

require_once "vendor/autoload.php";

use App\controllers\ConversationController;
use App\controllers\ConversationMemberController;
use App\controllers\MessageController;
use App\controllers\UserController;

session_start();

if (!isset($_SESSION["ConnectedUser"])) {
    session_destroy();
    header("location: ./pages/login.php", true);
    exit();
}

$connectedUser = $_SESSION["ConnectedUser"];

$userController = new UserController();
$conversationController = new ConversationController();
$conversationMemberController = new ConversationMemberController();
$messageController = new MessageController();

$users = $userController->getUsers($connectedUser->id);
$conversations = $conversationController->getConversationsByUserId($connectedUser->id);

$selectedConversationId = ((isset($_GET["selectedConversationId"])) ? $_GET["selectedConversationId"] : (count($conversations) > 0)) ? $conversations[0]->id : null;

if (count($conversations) > 0) {
    $selectedConversation = $conversationController->getConversationById($selectedConversationId);
    $membersOfSelectedConversation = $conversationMemberController->getMembersByConversationId($selectedConversation->id);
    $messages = $messageController->getMessagesByConversationId($selectedConversation->id);
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="garden">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatNet - <?= $connectedUser->userName; ?></title>
    <link rel="stylesheet" href="./styles/output.css">
</head>

<body class="overflow-hidden">
    <nav class="navbar flex justify-between">
        <a href="/" class="w-64 flex font-bold text-2xl">
            <span class="text-accent">Chat</span>
            <span>Net</span>
        </a>

        <div class="flex-1 flex gap-4 py-1 rounded-md">
            <?php if ((isset($membersOfSelectedConversation)) && (count($membersOfSelectedConversation) > 2)) : ?>
                <div class="w-12 h-12 flex items-center justify-center font-black bg-accent text-base-100 rounded-full cursor-pointer">
                    <?= substr(strtoupper($selectedConversation->name), 0, 1); ?>
                </div>
                <div class="flex flex-col rounded-md">
                    <p class="font-bold text-2xl"><?= $selectedConversation->name; ?></p>
                    <?php
                    $memberNames = "";

                    foreach ($membersOfSelectedConversation as $key => $member) :
                        $memberNames .= $member->userName . ($key >= (strlen($memberNames) - 1) ? ", " : "");
                    ?>
                    <?php endforeach; ?>
                    <span class="text-xs"><?= strlen($memberNames) > 25 ? substr($memberNames, 0, 25) . "..." : $memberNames; ?></span>
                </div>
                <p class="text-xs ml-auto font-bold">
                    <?= count($membersOfSelectedConversation); ?> Membre(s)
                </p>
            <?php else : ?>
                <?php if (isset($membersOfSelectedConversation)) : ?>
                    <?php foreach ($membersOfSelectedConversation as $member) : ?>
                        <?php if ($member->id !== $connectedUser->id) : ?>
                            <div class="w-12 h-12 flex items-center justify-center font-black bg-accent text-base-100 rounded-full cursor-pointer">
                                <?= substr(strtoupper($member->userName), 0, 1); ?>
                            </div>
                            <div class="flex flex-col rounded-md">
                                <p class="font-bold text-2xl">
                                    <?= $member->userName; ?>
                                </p>
                            </div>
                            <p class="text-xs ml-auto font-bold">
                                <?= $member->status ? "Online" : "Offline" ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <div class="w-72 flex items-center justify-end gap-2">
            <p class="w-10 h-10 flex justify-center items-center p-1 font-bold bg-accent text-base-100 cursor-pointer rounded-full">
                <?= substr(strtoupper($connectedUser->userName), 0, 1) ?>
            </p>
            <form action="./traitement/logout.php" method="post" class="flex items-center my-auto">
                <input type="hidden" name="id" value="<?= $connectedUser->id; ?>">
                <button type="submit" class="btn btn-sm">Logout</button>
            </form>
        </div>
    </nav>
    <main class="w-screen flex">
        <nav class="w-64 overflow-y-auto">
            <p class="font-bold text-2xl p-4">Conversations</p>

            <ul class="h-[40rem] overflow-y-auto">
                <?php foreach ($conversations as $conversation) : ?>
                    <li class="flex <?= intval($selectedConversation->id) === intval($conversation->id) ? "border-success border-r-4" : "" ?>">
                        <a href="./index.php?selectedConversationId=<?= $conversation->id; ?>" class="w-full flex py-2 px-4 flex-col hover:bg-base-200 relative">
                            <?php if (count($membersOfConversation = $conversationMemberController->getMembersByConversationId($conversation->id)) > 2) : ?>
                                <p class="font-bold text-lg">
                                    <?= $conversation->name; ?>
                                </p>
                            <?php else : ?>
                                <p class="font-bold text-lg">
                                    <?php foreach ($membersOfConversation as $member) : ?>
                                        <?php if ($member->id !== $connectedUser->id) : ?>
                                            <?= $member->userName ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>

                            <?php
                            $conversationMessages = $messageController->getMessagesByConversationId($conversation->id);

                            if (count($conversationMessages) > 0) :
                                $content = $conversationMessages[count($conversationMessages) - 1]->content;
                            ?>
                                <p class="text-sm whitespace-nowrap">
                                    <?= strlen($content) > 20 ? substr($content, 0, 20) . "..." : $content ?>
                                </p>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="h-[49rem] flex-1 flex flex-col px-8 pt-4 pb-20 bg-base-300">
            <div class="overflow-y-auto flex-1 flex flex-col px-8">
                <?php if (isset($messages)) : ?>
                    <?php foreach ($messages as $message) : ?>
                        <?php
                        $messageUser = $userController->getUserById($message->userId);
                        ?>
                        <div class="flex my-4 flex-col <?= $message->userId !== $connectedUser->id ? "self-start" : "self-end items-end"; ?>">
                            <p><?= $messageUser->userName; ?></p>
                            <div class=" w-74 py-2 px-4 my-1 rounded-md <?= $message->userId !== $connectedUser->id ? "bg-accent text-base-100" : "bg-secondary text-base-100"; ?>">
                                <p><?= $message->content ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <form action="./traitement/message.php" method="post" class="flex gap-4 items-center justify-between rounded-md">
                <input type="hidden" name="conversationId" value="<?= $selectedConversation->id ?? null; ?>">
                <input type="hidden" name="userId" value="<?= $connectedUser->id; ?>">
                <textarea <?= !(isset($selectedConversationId)) ? "disabled" : "";  ?> placeholder="Type your message here" required name="content" class="textarea textarea-bordered w-full resize-none text-lg"></textarea>
                <button class="btn bg-base-100" <?= !(isset($selectedConversationId)) ? "disabled" : "";  ?>>🚀</button>
            </form>
        </div>

        <nav class="w-72 flex flex-col">
            <p class="font-bold text-2xl p-4">Users</p>

            <ul class="h-[40rem] overflow-y-auto">
                <?php foreach ($users as $user) : ?>
                    <?php if ($user->id !== $connectedUser->id) : ?>
                        <li class="flex items-center justify-between py-2 px-4 gap-4 cursor-pointer hover:bg-base-200">
                            <div class="w-10 h-10 flex items-center justify-center bg-secondary rounded-full relative">
                                <p class="text-base-100"><?= substr(strtoupper($user->userName), 0, 1); ?></p>
                                <div class="w-3 h-3 rounded-full absolute bottom-0 right-0 <?= $user->status ? "bg-success" : "bg-base-300"; ?>"></div>
                            </div>
                            <form action="./traitement/conversation.php" method="post" class="flex-1 flex justify-between">
                                <p class="text-left"><?= $user->userName; ?></p>
                                <input type="hidden" name="userToConverseId" value="<?= $user->id; ?>">
                                <input type="hidden" name="connectedUserId" value="<?= $connectedUser->id; ?>">
                                <input type="hidden" name="message" value="You said hi to <?= $user->userName; ?>">
                                <button type="submit" class="btn btn-xs">Say Hi</button>
                            </form>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
    </main>
</body>

</html>