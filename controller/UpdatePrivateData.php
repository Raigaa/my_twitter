<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Database.php';


$databaseInstance = Database::getInstance();
$userInstance = User::getInstance($databaseInstance);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['formPrivateData'])) {
    $formData = $_POST['formPrivateData'];

    if(isset($formData['id'], $formData['username'], $formData['email'], $formData['firstname'], $formData['lastname'], $formData['dob'])) {
        $id = $formData['id'];
        $username = $formData['username'];
        $email = $formData['email'];
        $firstname = $formData['firstname'];
        $lastname = $formData['lastname'];
        $birthdate = $formData['dob'];

        try {
            $loggedInUser = $userInstance->getUserById($id);

            $emailExists = $userInstance->getUserByEmail($email);
            if ($emailExists && $emailExists['id'] !== $loggedInUser['id']) {
                echo json_encode(array('status' => false, 'message' => 'Email already exists.'));
                return;
            }

            $usernameExists = $userInstance->getUserByUsername($username);
            if ($usernameExists && $usernameExists['id'] !== $loggedInUser['id']) {
                echo json_encode(array('status' => false, 'message' => 'Username already exists.'));
                return;
            }

            $updateResult = $userInstance->updateProfile($id, $username, $email, $firstname, $lastname, $birthdate);
            if($updateResult) {
                echo json_encode(array('status' => true, 'message' => 'Personal data updated successfully!'));
            } else {
                echo json_encode(array('status' => false, 'message' => 'Error updating personal data.'));
            }
        } catch (PDOException $e) {
            echo json_encode(array('status' => false, 'message' => 'Database error: ' . $e->getMessage()));
        }
    } else {
        echo json_encode(array('status' => false, 'message' => 'Incomplete data provided.'));
    }
} else {
    echo json_encode(array('status' => false, 'message' => 'Invalid request.'));
}
