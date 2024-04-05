<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';


$database = Database::getInstance();
$userInstance = User::getInstance($database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = $_POST['id'];

    $userInstance->softDeleteUser($userId);
    echo json_encode(array("status" => "success", "message" => "User deleted"));
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}