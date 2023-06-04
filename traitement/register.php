<?php

require_once "../vendor/autoload.php";

use App\controllers\UserController;

$userController = new UserController();

if (isset($_POST["userName"]) && isset($_POST["password"])) {

    extract($_POST);

    $rowCount = $userController->createUser($userName, $password);

    if ($rowCount > 0) {
        session_start();
        $userCreated = $userController->getUserByUsernameAndPassword($userName, $password);
        $_SESSION["CurrentUser"] = $userCreated;
        header("location: ../index.php", true);
        exit();
    } else {
        header("location: ../pages/register.php", true);
        exit();
    }
}

exit();
