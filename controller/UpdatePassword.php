<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';

$databaseInstance = Database::getInstance();
$userInstance = User::getInstance($databaseInstance);

$formData = $_POST['formPassword'];

$id = $formData['id'];

$oldPassword = $formData['old_password'];
$newPassword = $formData['new_password'];

if(isset($id, $oldPassword, $newPassword)) {
    $userData = $userInstance->getUserById($id);
    if($userData) {
        $dbPassword = $userData['password_hash'];
        $hashedOldPassword = $userInstance->hashpwd($oldPassword);
        
        if($hashedOldPassword === $dbPassword) {
            $hashedNewPassword = $userInstance->hashpwd($newPassword);
            $updateResult = $userInstance->updatePassword($id, $hashedNewPassword);
            if($updateResult) {
                echo json_encode(true);
            } else {
                echo json_encode(false);
            }
        } else {
            echo json_encode(false);
        }
    } else {
        echo json_encode(false);
    }
} else {
    echo json_encode(false);
}
