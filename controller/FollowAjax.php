<?php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Bark.php';

$databaseInstance = Database::getInstance();
$barkInstance = Bark::getInstance($databaseInstance);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['followData'])) {
        if(isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $followUserArray = $_POST['followData'];
            $followUser = $followUserArray['userId'];

            $barkInstance->followUser($userId, $followUser);

            echo json_encode(array("status" => "success", "message" => "Add Follow"));
        } else {
            echo json_encode(array("status" => "error", "message" => "User not logged in"));
        }
    } elseif (isset($_POST['unfollowData'])) {
        if(isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $unfollowUser = $_POST['unfollowData'];
            $unfollowUserId = $unfollowUser['userId'];

            $barkInstance->unfollowUser($userId, $unfollowUserId);

            echo json_encode(array("status" => "success", "message" => "Data received for unfollow", "unfollowData" => $unfollowUserId, "userId" => $userId));
        } else {
            echo json_encode(array("status" => "error", "message" => "User not logged in"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Invalid data received"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
