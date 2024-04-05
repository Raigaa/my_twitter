<?php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Bark.php';

$databaseInstance = Database::getInstance();
$barkInstance = Bark::getInstance($databaseInstance);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tweetId'])) {
        session_start(); 
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $barkId = $_POST['tweetId']; 

            $barkInstance->deleteBark($userId, $barkId);

            echo json_encode(array("status" => "success", "message" => "Bark deleted"));
        } else {
            echo json_encode(array("status" => "error", "message" => "User not logged in"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Invalid data received"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}

