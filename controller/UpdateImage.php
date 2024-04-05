<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';

session_start();

$databaseInstance = Database::getInstance();

$userInstance = User::getInstance($databaseInstance);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];

    function moveUploadedFile($file, $destination) {
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            echo json_encode(array("status" => "error", "message" => "Unable to move uploaded file."));
            exit;
        }
    }

    if (isset($_FILES['profilePictureFile'])) {
        $profilePictureFile = $_FILES['profilePictureFile'];

        if ($profilePictureFile['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(array("status" => "error", "message" => "An error occurred while uploading the profile picture."));
            exit;
        }

        $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($profilePictureFile['type'], $allowedTypes)) {
            echo json_encode(array("status" => "error", "message" => "The profile picture file format is not allowed."));
            exit;
        }

        $uploadsDirectory = "../uploads/";
        $profilePictureFilename = 'profilePicture_' . $userId . '.' . pathinfo($profilePictureFile['name'], PATHINFO_EXTENSION);
        $profilePicturePath = $uploadsDirectory . $profilePictureFilename;
        moveUploadedFile($profilePictureFile, $profilePicturePath);
        $userInstance->updatePfp($userId, $profilePicturePath);

    }

    if (isset($_FILES['profileBannerFile'])) {
        $profileBannerFile = $_FILES['profileBannerFile'];

        if ($profileBannerFile['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(array("status" => "error", "message" => "An error occurred while uploading the profile banner."));
            exit;
        }

        $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($profileBannerFile['type'], $allowedTypes)) {
            echo json_encode(array("status" => "error", "message" => "The profile picture file format is not allowed."));
            exit;
        }

        $uploadsDirectory = "../uploads/";
        $profileBannerFilename = 'profileBanner_' . $userId . '.' . pathinfo($profileBannerFile['name'], PATHINFO_EXTENSION);
        $profileBannerPath = $uploadsDirectory . $profileBannerFilename;
        moveUploadedFile($profileBannerFile, $profileBannerPath);
        $userInstance->updateProfileBanner($userId, $profileBannerPath);
    }

    echo json_encode(array("status" => "success", "message" => "Upload successful."));
    exit;
}
