<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bio']) && isset($_POST['localisation']) && isset($_POST['website'])) {

    $bio = $_POST['bio'];
    $localisation = $_POST['localisation'];
    $website = $_POST['website'];

    $bio = $bio !== 'null' ? $bio : null;
    $localisation = $localisation !== 'null' ? $localisation : null;
    $website = $website !== 'null' ? $website : null;

    $databaseInstance = Database::getInstance();
    $userInstance = User::getInstance($databaseInstance);

    $userId = $_SESSION['user_id'];

    $userInstance->updatePublicData($userId, $bio, $localisation, $website);

    echo json_encode(array("status" => "success", "message" => "Public data updated successfully!"));
} else {
    echo json_encode(array("status" => "error", "message" => "Form data not received."));
}
