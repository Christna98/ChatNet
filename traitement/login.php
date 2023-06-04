<?php

require_once "../vendor/autoload.php";

use App\controllers\UserController;

$userController = new UserController();

// Vérifier si les champs "userName" et "password" sont définis
if (!isset($_POST["userName"]) || !isset($_POST["password"])) {
    header("Location: ../pages/login.php?error=missing_credentials");
    exit();
}

$userName = $_POST["userName"];
$password = $_POST["password"];

// Récupérer l'utilisateur en utilisant le nom d'utilisateur et le mot de passe
$user = $userController->getUserByUsernameAndPassword($userName, $password);

// Vérifier si l'utilisateur existe
if (!$user) {
    header("Location: ../pages/login.php?error=invalid_credentials");
    exit();
}

// Mettre à jour le statut de l'utilisateur à "connected"
if (!$userController->updateUserStatus($user->id, true)) {
    header("Location: ../pages/login.php?error=status_update_failed");
    exit();
}

// Récupérer à nouveau l'utilisateur pour s'assurer que le statut a été mis à jour
$updatedUser = $userController->getUserById($user->id);

// Vérifier si la récupération de l'utilisateur a réussi
if (!$updatedUser) {
    header("Location: ../pages/login.php?error=user_retrieval_failed");
    exit();
}

// Démarrer une session et stocker l'utilisateur dans la session
session_start();
$_SESSION["ConnectedUser"] = $updatedUser;

// Rediriger vers la page d'accueil
header("Location: ../index.php");
exit();
