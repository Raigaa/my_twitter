<?php

require_once "Login.php";

$databaseInstance = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'password' => htmlspecialchars($_POST['password'])
    ];

    $login = new Login($databaseInstance);

    $result = $login->processFormData($formData);

    $userInstance = User::getInstance($databaseInstance);


    if ($result === "Identifiants corrects.") {
        echo json_encode(["status" => "success"]);
    } else {
        echo "error";
    }
} else {
    echo "error";
}
