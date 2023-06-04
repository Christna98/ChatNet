<?php

require_once "../vendor/autoload.php";

use App\controllers\UserController;

$userController = new UserController();

if (isset($_POST["userName"]) && isset($_POST["password"])) {

    extract($_POST);

    $user = $userController->getUserByUsernameAndPassword($userName, $password);

    if (!$user) {
        header("location: ../pages/login.php", true);
    } else {

        $rowCount = $userController->connectAndDisconnectUser($user->id, "connected");

        $userConnected = $userController->getUser($user->id);

        if ($rowCount > 0) {
            session_start();
            $_SESSION["CurrentUser"] = $userConnected;
            header("location: ../index.php", true);
        } else {
            header("location: ../pages/login.php", true);
        }
    }

    exit();
}
