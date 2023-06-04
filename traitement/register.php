<?php

require_once "../vendor/autoload.php";

use App\controllers\UserController;

$userController = new UserController();

// Vérifier si les champs "userName" et "password" sont définis
if (!isset($_POST["userName"]) || !isset($_POST["password"])) {
    header("Location: ../pages/register.php?error=missing_credentials");
    exit();
}

$userName = $_POST["userName"];
$password = $_POST["password"];

// Vérifier si l'utilisateur existe déjà
if ($userController->getUserByUsername($userName)) {
    header("Location: ../pages/register.php?error=user_exists");
    exit();
}

// Créer un nouvel utilisateur
$rowCount = $userController->createUser($userName, $password);

if (!($rowCount > 0)) {
    header("Location: ../pages/register.php?error=registration_failed");
    exit();
}

// Récupérer l'utilisateur créé
$userCreated = $userController->getUserByUsername($userName);

// Vérifier si la récupération de l'utilisateur a réussi
if (!$userCreated) {
    header("Location: ../pages/register.php?error=user_retrieval_failed");
    exit();
}

// Démarrer une session et stocker l'utilisateur dans la session
session_start();
$_SESSION["CurrentUser"] = $userCreated;

// Rediriger vers la page d'accueil
header("Location: ../index.php");
exit();
