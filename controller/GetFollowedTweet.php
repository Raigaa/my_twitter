<?php

require_once __DIR__ . '/../models/Bark.php';
require_once __DIR__ . '/../models/Database.php';

$database = Database::getInstance();
$barkInstance = Bark::getInstance($database);

$loggedInUserId = $_SESSION['user_id'];

$followUserTweets = $barkInstance->getFollowingTweets($loggedInUserId);

$objectName = "followUserTweets";

$response = array(
    $objectName => $followUserTweets
);

echo json_encode($response, JSON_FORCE_OBJECT);
