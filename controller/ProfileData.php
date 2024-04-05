<?php

session_start();

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';

$database = Database::getInstance();


if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $userModel = User::getInstance($database);

    $userData = $userModel->getUserById($userId);

    if ($userData) {
        //echo $userData;
        echo json_encode($userData);
    } else {
        echo "Erreur lors de la récupération des données de l'utilisateur.";
    }
} else {
    header("Location: ../view/login.html");
    exit;
}
