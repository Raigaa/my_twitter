<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Bark.php';

$databaseInstance = Database::getInstance();
$barkInstance = Bark::getInstance($databaseInstance);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['barkText'])) {

    $userId = $_SESSION['user_id'];

    $message = $_POST['barkText'];


    $barkInstance->newBark($userId, $message);

    echo json_encode(array("status" => "success", "message" => "New bark is here!"));
} else {
    echo json_encode(array("status" => "error", "message" => "Data not received"));
}
