<?php

require_once __DIR__ . '/../models/User.php';
class Login
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function hashpwd($password)
    {
        $salt = "vive le projet tweet_academy";
        $noHashedPwd = $salt . $password;
        $newPwd = hash('ripemd160', $noHashedPwd);
        return $newPwd;
    }

    public function processFormData($formData)
{
    if (!isset($formData['email']) || !isset($formData['password'])) {
        return "Veuillez remplir tous les champs du formulaire.";
    }

    $email = $formData['email'];
    $password = $formData['password'];

    if (empty($email) || empty($password)) {
        return "Veuillez remplir tous les champs du formulaire.";
    }

    $userModel = User::getInstance($this->db);
    $userData = $userModel->getUserByEmail($email);

    if (!$userData) {
        return "Adresse e-mail incorrecte.";
    }

    if ($userData['isDeleted'] == 1) {
        return "Votre compte a été supprimé.";
    }

    $storedPasswordHash = $userData['password_hash'];
    $enteredPasswordHash = $this->hashpwd($password);

    if ($enteredPasswordHash !== $storedPasswordHash) {
        return "Mot de passe incorrect.";
    }

    session_start();
    $_SESSION['user_id'] = $userData['id'];

    return "Identifiants corrects.";
}

}
