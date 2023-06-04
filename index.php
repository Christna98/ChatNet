<?php

require_once "vendor/autoload.php";

use App\controllers\ConversationController;
use App\controllers\UserController;

session_start();

if (!isset($_SESSION["CurrentUser"])) {
    session_destroy();
    header("location: ./pages/login.php", true);
    exit();
}

$userController = new UserController();
$conversationController = new ConversationController();

$currentUser = $_SESSION["CurrentUser"];

$users = $userController->getUsers();
$conversations = $conversationController->getConversationsByUserId($currentUser->id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatNet - <?= $currentUser->userName; ?></title>
    <link rel="stylesheet" href="./styles/output.css">
</head>

<body class="overflow-hidden">
    <nav class="navbar flex justify-between bg-neutral text-neutral-content">
        <a href="/" class="font-bold text-2xl">ChatNet</a>

        <div class="flex items-center gap-2">
            <p class="w-10 h-10 flex justify-center items-center p-1 font-bold bg-neutral-700 cursor-pointer rounded-full">
                <?= substr(strtoupper($currentUser->userName), 0, 1) ?>
            </p>
            <form action="./traitement/logout.php" method="post" class="flex items-center my-auto">
                <input type="hidden" name="id" value="<?= $currentUser->id; ?>">
                <button type="submit" class="btn btn-sm">Logout</button>
            </form>
        </div>
    </nav>
    <main class="w-screen h-screen flex">
        <ul class="flex flex-col bg-base-300">
            <li class="font-bold text-xl p-4">Conversations</li>
            <?php foreach ($conversations as $conversation) : ?>
                <li class="flex items-center py-2 px-4 gap-4 cursor-pointer hover:bg-base-200">
                    <div class="avatar">
                        <div class="w-6 rounded-xl">
                            <img src="../../../../../Users/etien/Pictures/e.png" />
                        </div>
                    </div>
                    <p><?= $conversation->conversationName; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="flex-1 flex flex-col p-4">
            <div class="flex-1">
                <div>
                    <p>Message 1</p>
                </div>
                <div>
                    <p>Message 2</p>
                </div>
            </div>
            <form action="" method="post" class="flex">
                <textarea placeholder="Type your message here" class="textarea textarea-bordered textarea-xs w-full max-w-xs"></textarea>
            </form>
        </div>

        <ul class="flex flex-col bg-base-300">
            <li class="font-bold text-xl p-4">Users</li>
            <?php foreach ($users as $user) : ?>
                <?php if ($user->id !== $currentUser->id) : ?>
                    <li class="flex items-center py-2 px-4 gap-4 cursor-pointer hover:bg-base-200">
                        <div class="w-10 h-10 flex items-center justify-center bg-neutral rounded-full relative">
                            <?= substr(strtoupper($user->userName), 0, 1); ?>
                            <div class="w-2 h-2 rounded-full absolute bottom-0 right-0 <?= $user->status === "connected" ? "bg-green-500" : "bg-gray-300"; ?>"></div>
                        </div>
                        <p><?= $user->userName; ?></p>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </main>
</body>

</html>