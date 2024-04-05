<?php


include "./Signup.php";



$database = Database::getInstance();

$user = new Signup($database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formData = $_POST;
    $createUser = $user->processFormData($formData);

    if ($createUser) {
        echo json_encode(["status" => "success"]);
    } else {
        echo "error";
    }
} else {
    echo "Error: " . "Méthode non autorisée.";
    return false;
}