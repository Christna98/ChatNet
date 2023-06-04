<?php

require_once "../vendor/autoload.php";

use App\controllers\UserController;

$userController = new UserController();

if (isset($_POST["id"])) {

    extract($_POST);

    $rowCount = $userController->connectAndDisconnectUser($id, "disconnected");

    if ($rowCount > 0) {
        session_start();
        unset($_SESSION["CurrentUser"]);
        session_destroy();

        header("location: ../pages/login.php", true);
    } else {
        header("location: ../index.php", true);
    }
}

exit();
