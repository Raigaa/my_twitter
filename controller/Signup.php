<?php

require_once __DIR__ . '/../models/User.php';

class Signup
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
        try {

            $email = filter_var($formData['email'], FILTER_SANITIZE_EMAIL);
            $birthdate = filter_var($formData['dob'], FILTER_SANITIZE_STRING);
            $username = filter_var($formData['username'], FILTER_SANITIZE_STRING);
            $firstname = filter_var($formData['firstname'], FILTER_SANITIZE_STRING);
            $lastname = filter_var($formData['lastname'], FILTER_SANITIZE_STRING);
            $password = $this->hashpwd($formData['password']);

            if ($this->emailExists($email)) {
                echo "Cette adresse e-mail est déjà utilisée.";
                return false;
            }

            $this->filterInput($email, $birthdate, $username, $firstname, $lastname, $password);

            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    function emailExists($email)
    {
        try {
            $query = "SELECT COUNT(*) FROM users WHERE email = ?";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(1, $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    function verifyAge($birthdate, $minAge)
    {
        $date = new DateTime($birthdate);
        $today = new DateTime();
        $age = $today->diff($date)->y;

        return $age >= $minAge;
    }



    function filterInput($email, $birthdate, $username, $firstname, $lastname, $password)
    {
        $inputIsValid = true;

        $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        //YYYY-MM-DD
        $birthdateRegex = '/^\d{4}-\d{2}-\d{2}$/';
        //3-16 characters, letters, numbers, underscores and hyphens
        $usernameRegex = '/^[a-zA-Z0-9_-]{3,16}$/';
        //Only letters
        $nameRegex = '/^[a-zA-Z]+$/';
        //At least 8 characters
        $passwordRegex = '/^.{8,}$/';

        if (!preg_match($emailRegex, $email)) {
            $inputIsValid = false;
            echo "Invalid email.<br>";
        }
        if (!preg_match($birthdateRegex, $birthdate)) {
            $inputIsValid = false;
            echo "Date de naissance invalide.<br>";
        }
        if (!preg_match($usernameRegex, $username)) {
            $inputIsValid = false;
            echo "Nom d'utilisateur invalide.<br>";
        }
        if (!preg_match($nameRegex, $firstname)) {
            $inputIsValid = false;
            echo "Prénom invalide.<br>";
        }
        if (!preg_match($nameRegex, $lastname)) {
            $inputIsValid = false;
            echo "Nom invalide.<br>";
        }
        if (!preg_match($passwordRegex, $password)) {
            $inputIsValid = false;
            echo "Mot de passe invalide. Il doit faire au moins 8 caractères.<br>";
        }

        if (!$this->verifyAge($birthdate, 15)) {
            $inputIsValid = false;
            echo "Vous devez avoir au moins 15 ans pour vous inscrire.";
        }

        if ($inputIsValid === true) {
            $userHandler = User::getInstance($this->db);
            $userHandler->createUser($email, $birthdate, $username, $firstname, $lastname, $password);
            return true;
        }

        return false;
    }

}