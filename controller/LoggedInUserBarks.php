<?php

require_once __DIR__ . '/../models/Bark.php';
require_once __DIR__ . '/../models/Database.php';

$database = Database::getInstance();
$barkInstance = Bark::getInstance($database);


$userData = $barkInstance->getLoggedInUserBarks();

echo json_encode($userData);