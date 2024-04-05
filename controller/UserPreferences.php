<?php

session_start();

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';

$database = Database::getInstance();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $userModel = User::getInstance($database);

    $userData = $userModel->getUserById($userId);

    $userPreferences = $userModel->getUsersPreferences($userId);

    if ($userPreferences) {
        ob_clean();

        header('Content-Type: application/json');

        echo json_encode($userPreferences);
    } else {
        echo json_encode(array('error' => 'Erreur lors de la récupération des données de l\'utilisateur.'));
    }
} else {
    echo json_encode(array('error' => 'Session utilisateur non trouvée.'));
}
